<?php

namespace Drupal\va_gov_migrate\Plugin\migrate\source;

use Drupal\Core\Site\Settings;
use Drupal\migration_tools\Message;
use Drupal\migration_tools\Plugin\migrate\source\UrlList;
use Drupal\va_gov_migrate\AnomalyMessage;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Yaml\Exception\ParseException;
use Drupal\migrate\Plugin\MigrationInterface;
use GuzzleHttp\Exception\RequestException;

/**
 * Gets frontmatter and page urls from metalsmith files.
 *
 * This source requires a list of urls that are either metalsmith files
 * or a github directory listing of metalsmith files.
 *
 * @MigrateSource(
 *  id = "metalsmith_source"
 * )
 */
class MetalsmithSource extends UrlList {
  /**
   * Array of assoc arrays of frontmatter data and website url.
   *
   * @var array
   */
  protected $rows;

  /**
   * Name of the template file to filter for in this migration.
   *
   * @var array
   */
  protected $templates;

  /**
   * The server where the pages that will be scraped live.
   *
   * @var string
   */
  protected $server;
  /**
   * Holds values of github directory listings.
   *
   * @var array
   */
  protected $dirLists;

  const CONTENT_URL = "https://api.github.com/repos/department-of-veterans-affairs/vagov-content/contents";
  const PAGES_URL = "https://api.github.com/repos/department-of-veterans-affairs/vagov-content/contents/pages";

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, MigrationInterface $migration) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $migration);

    $this->templates = empty($configuration['templates']) ? '' : $configuration['templates'];
    $this->server = empty($configuration['server']) ? 'https://www.va.gov' : $configuration['server'];
    $this->dirLists = [];
  }

  /**
   * {@inheritdoc}
   *
   * @throws \Drupal\migrate\MigrateException
   * @throws \Drupal\migrate\MigrateSkipRowException
   */
  public function initializeIterator() {
    $this->rows = [];

    foreach ($this->urls as $url) {
      // If it's a markdown file, process it.
      if (substr($url, -3) == '.md') {
        $contents = $this->readUrl(self::PAGES_URL . $url);
        if (!empty($contents)) {
          $file_info = json_decode($contents);
          $this->addRow($file_info->git_url, $url);
        }
      }
      // Otherwise, assume it's a directory listing.
      else {
        $this->readDirectory($url);

      }
    }

    $this->validateRows();
    return new \ArrayIterator($this->rows);
  }

  /**
   * Removes any rows with duplicate key fields (urls).
   */
  protected function validateRows() {
    $unique_rows = [];
    $urls = [];
    foreach ($this->rows as $row) {
      if (in_array($row['url'], $urls)) {
        $key = array_search($row['url'], array_column($this->rows, 'url'));
        $this->rows[$key] = array_merge($row, $this->rows[$key]);
        Message::make('Found duplicate entry for @url', ['@url' => $row['url']], Message::DEBUG);
      }
      else {
        if (empty($row['lastupdate'])) {
          $row['lastupdate'] = 0;
        }
        $unique_rows[] = $row;
        $urls[] = $row['url'];
      }
    }

    $this->rows = $unique_rows;
  }

  /**
   * Read github directory recursively to find and parse all .md files in it.
   *
   * @param string $url
   *   The url of the github directory.
   *
   * @throws \Drupal\migrate\MigrateException
   * @throws \Drupal\migrate\MigrateSkipRowException
   */
  protected function readDirectory($url) {

    if (strrpos($url, '/') === FALSE) {
      Message::make('Url must start with a slash (/) @url', ['@url' => $url], Message::ERROR);
      return;
    }

    if ($url == '/') {
      $parent = self::CONTENT_URL;
      $dir = 'pages';
    }
    else {
      $path_parts = explode('/', $url);
      $dir = array_pop($path_parts);

      $parent = self::PAGES_URL . implode('/', $path_parts);
    }
    if (empty($this->dirLists[$parent])) {
      // Read the page at the url.
      if (!($parent_tree = $this->readUrl($parent))) {
        return;
      }

      $this->dirLists[$parent] = $parent_tree;
    }
    else {
      $parent_tree = $this->dirLists[$parent];

    }
    $parent_tree = json_decode($parent_tree);
    foreach ($parent_tree as $branch) {
      if ($branch->name == $dir) {
        $git_url = $branch->git_url;
        break;
      }
    }
    if (empty($git_url)) {
      Message::make('Problem with @url', ['@url' => $url], Message::ERROR);
      return;
    }

    $tree = $this->readUrl($git_url . '?recursive=1');
    $tree = json_decode($tree);

    foreach ($tree->tree as $branch) {
      if ($branch->type == 'blob') {
        $this->addRow($branch->url, $url . '/' . $branch->path);
      }
    }
  }

  /**
   * Add a row with frontmatter and the url of the associated web page.
   *
   * @param string $url
   *   The url of the markdown file to get fields from.
   * @param string $path
   *   The root relative path.
   *
   * @throws \Drupal\migrate\MigrateException
   */
  protected function addRow($url, $path) {
    if (!($row = $this->readMetalsmithFile($url))) {
      return;
    }

    // Migrate metalsmith only and no fully react pages.
    if (empty($row['layout']) || 'page-react.html' == $row['layout']) {
      return;
    }

    // Filter for 'templates' field defined in migration yml, if any.
    if (!empty($this->templates) && (empty($row['template']) || !in_array($row['template'], $this->templates))) {
      return;
    }

    $this->setPagePath($path, $row);
    if (empty($row['url'])) {
      return;
    }

    // Make sure title isn't too long.
    if (!empty($row['title']) && strlen($row['title']) > 51) {
      AnomalyMessage::make("Some <title> tags are longer than 51 characters", $row['heading'], $row['url'], $row['title']);
    }

    // Extract the plainlanguage date, if any.
    if (!empty($row['plainlanguage']) && preg_match('/0?(\d+)[-\.]0?(\d+)[-\.](\d+)/', $row['plainlanguage'], $matches)) {
      $row['plainlanguage_date'] = $matches[1] . '/' . $matches[2] . '/' . $matches[3];
    }

    $this->rows[] = $row;
  }

  /**
   * Parse Metalsmith file and return an array of front matter values.
   *
   * @param string $url
   *   The file's url (used for error message).
   * @param string $page_content
   *   Gets populated with the page content section of the file (optional)
   *
   * @return array
   *   The front matter values, keyed on field names.
   *
   * @throws \Drupal\migrate\MigrateException
   */
  public function readMetalsmithFile($url, &$page_content = NULL) {
    if (!($response = $this->readUrl($url))) {
      return [];
    }
    $response = json_decode($response);
    $markdown = base64_decode($response->content);

    /*
     * Metalsmith source pages look like this:
     *  ---
     *  [front matter in yaml format]
     *  ---
     *  [page content in markup and html]
     */

    // Add asterisks to markdown line breaks so we don't explode on them.
    $markdown = str_replace('------', '**--*--*--**', $markdown);
    $page_part = explode('---', $markdown);

    if (count($page_part) < 2) {
      Message::make('No front matter in @url', ['@url' => parse_url($url, PHP_URL_PATH)], Message::DEBUG);
      return [];
    }
    else {
      if ($page_content !== NULL && count($page_part) > 2) {
        $page_content = str_replace('**--*--*--**', '------', $page_part[2]);
      }

      try {
        return Yaml::parse($page_part[1]);
      }
      catch (ParseException $exception) {
        Message::make('Unable to parse the YAML string: @message', ['@message' => $exception->getMessage()], Message::ERROR);
        return [];
      }
    }
  }

  /**
   * Get the page from github.
   *
   * @param string $url
   *   The url of the page to read.
   *
   * @return bool|string
   *   The contents of the page or FALSE on failure.
   *
   * @throws \Drupal\migrate\MigrateException
   */
  public function readUrl($url) {
    $cache = $this->getCache()->get('github_url:' . $url);

    $accessToken = Settings::get('va_cms_bot_github_auth_token');
    $httpClient = \Drupal::httpClient();
    $headers = [
      'User-Agent'    => Settings::get('va_cms_bot_github_username'),
      'Accept'        => 'application/json',
      'Authorization' => 'Bearer ' . $accessToken,
    ];
    if (!empty($cache->data['etag'])) {
      $headers['If-None-Match'] = $cache->data['etag'];
    }

    try {
      $response = $httpClient->get($url, ['headers' => $headers]);
      if (empty($response)) {
        Message::make('No response at @url.', ['@url' => $url], Message::ERROR);
        return FALSE;
      }
    }
    catch (RequestException $e) {
      Message::make('Error message: @message at @url.', [
        '@message' => $e->getMessage(),
        '@url' => $url,
      ], Message::ERROR);
      return FALSE;
    }

    switch ($response->getStatusCode()) {
      case 304:
        return $cache->data['contents'];

      case 200:
        $contents = $response->getBody()->getContents();

        $next_month = mktime(0, 0, 0, date("m") + 1, date("d"), date("Y"));
        $this->getCache()->set('github_url:' . $url,
          [
            'etag' => $response->getHeader("ETag"),
            'contents' => $contents,
          ], $next_month);

        return $contents;

      default:
        return FALSE;
    }
  }

  /**
   * Set the 'url' key to the va.gov path that corresponds to a metalsmith file.
   *
   * Also set the 'path' key, used for drupal node alias.
   *
   * @param string $path
   *   The path of the metalsmith file.
   * @param array $row
   *   The row to set the url on.
   */
  protected function setPagePath($path, array &$row) {
    // Get the path without the file name for index pages.
    if (preg_match('/([^\.]+)\/index\.md/', $path, $matches)) {
      $site_path = $matches[1];
    }
    // Get the path with the file name for all others.
    elseif (preg_match('/([^\.]+)\.md/', $path, $matches)) {
      $site_path = $matches[1];
    }
    if (!empty($site_path)) {
      $row['url'] = $this->server . $site_path . '/';
      $row['path'] = $site_path;
    }

  }

}
