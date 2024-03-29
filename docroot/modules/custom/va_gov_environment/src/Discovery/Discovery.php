<?php

namespace Drupal\va_gov_environment\Discovery;

use Drupal\Core\Site\Settings;
use Drupal\va_gov_environment\Environment\Environment;
use Drupal\va_gov_environment\Environment\EnvironmentInterface;

/**
 * Builds and sends metrics to Datadog.
 */
class Discovery implements DiscoveryInterface {

  /**
   * The Settings service.
   *
   * @var \Drupal\Core\Site\Settings
   */
  protected $settings;

  /**
   * The environment.
   *
   * @var \Drupal\va_gov_environment\Environment\EnvironmentInterface
   */
  protected $environment;

  /**
   * Constructor.
   *
   * @param \Drupal\Core\Site\Settings $settings
   *   The Drupal settings manager.
   */
  public function __construct(Settings $settings) {
    $this->settings = $settings;
    $this->environment = Environment::fromSettings($this->settings);
  }

  /**
   * {@inheritDoc}
   */
  public function getRawEnvironment() : string {
    return $this->settings->get('va_gov_environment')['environment_raw'];
  }

  /**
   * {@inheritDoc}
   */
  public function getEnvironment() : EnvironmentInterface {
    return $this->environment;
  }

  /**
   * {@inheritDoc}
   */
  public function getRawValue(): string {
    return $this->getEnvironment()->getRawValue();
  }

  /**
   * {@inheritDoc}
   */
  public function isLocalDev() : bool {
    return $this->getEnvironment()->isLocalDev();
  }

  /**
   * {@inheritDoc}
   */
  public function isTugboat() : bool {
    return $this->getEnvironment()->isTugboat();
  }

  /**
   * {@inheritDoc}
   */
  public function isDev() : bool {
    return $this->getEnvironment()->isDev();
  }

  /**
   * {@inheritDoc}
   */
  public function isStaging() : bool {
    return $this->getEnvironment()->isStaging();
  }

  /**
   * {@inheritDoc}
   */
  public function isProduction() : bool {
    return $this->getEnvironment()->isProduction();
  }

  /**
   * {@inheritDoc}
   */
  public function isBrd() : bool {
    return $this->getEnvironment()->isBrd();
  }

  /**
   * {@inheritDoc}
   */
  public function isCmsTest() : bool {
    return (bool) $this->settings->get('va_gov_environment')['is_cms_test'];
  }

}
