<?php

namespace Drupal\va_gov_migrate\Plugin\migrate\source;

use Drupal\migrate\MigrateException;
use Drupal\migrate\Plugin\MigrationInterface;

/**
 * Source to read from sidebar.json.
 *
 * @MigrateSource(
 *   id = "va_benefits_menu_source"
 * )
 */
class VaBenefitsMenu extends VaMenuBase {
  protected $sections;

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, MigrationInterface $migration) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $migration);

    // Path is required.
    if (empty($this->configuration['sections'])) {
      throw new MigrateException('You must declare the "sections" in your source settings.');
    }

    $this->sections = $configuration['sections'];
  }

  /**
   * Return a string representing the source file path.
   *
   * @return string
   *   The file path.
   */
  public function __toString() {
    return 'sidebar.json';
  }

  /**
   * {@inheritdoc}
   */
  public function initializeIterator() {
    $contents = file_get_contents("modules/custom/va_gov_migrate/data/sidebar.json");
    $json_sidebar = json_decode($contents, TRUE);
    // Get top level menus.
    $menus = [];
    foreach ($json_sidebar as $page) {
      if (!empty($page['sidebarTitle']) && in_array($page['sidebarTitle'], $this->sections)) {
        if (empty($menus[$page['sidebarTitle']])) {
          $menu_name = strtolower(str_replace(' ', '-', $page['sidebarTitle'])) . '-benefits-hub';
          $menus[$page['sidebarTitle']] = [
            'title' => $page['sidebarTitle'],
            'href' => 'route:<nolink>',
            'items' => [],
            'menu' => $menu_name,
          ];
        }
        if (!empty($page['menus'])) {
          $menus[$page['sidebarTitle']]['items'] = $this->mergeMenus($page['menus'], $menus[$page['sidebarTitle']]['items']);
        }
        $menus[$page['sidebarTitle']]['menu'] = strtolower(str_replace(' ', '-', $page['sidebarTitle'])) . '-benefits-hub';
      }
    }

    foreach ($json_sidebar as $page) {
      if (!empty($page['sidebarTitle']) && in_array($page['sidebarTitle'], $this->sections)) {
        if (!empty($page['items'])) {
          $this->findMergeMenus($page, $menus[$page['sidebarTitle']]['items']);
        }
      }
    }

    return new \ArrayIterator($this->process($menus));
  }

  /**
   * {@inheritdoc}
   */
  public function fields() {
    $fields['href'] = parent::fields();
    $fields['menu'] = 'Menu machine name';

    return $fields;
  }

  /**
   * {@inheritdoc}
   */
  protected function sanitizeDomain($href) {
    return str_replace('http://localhost:3001', 'https://www.va.gov', $href);
  }

}
