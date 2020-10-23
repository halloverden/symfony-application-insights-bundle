<?php


namespace HalloVerden\ApplicationInsightsBundle\Tracker;


use HalloVerden\ApplicationInsightsBundle\Tracker\Configuration\ExceptionConfiguration;

class Configuration {

  /**
   * @var bool
   */
  private $enabled;

  /**
   * @var string
   */
  private $key;

  /**
   * @var ExceptionConfiguration
   */
  private $exceptionConfiguration;

  public function __construct(bool $enabled, string $key, ExceptionConfiguration $exceptionConfiguration) {
    $this->enabled = $enabled;
    $this->key = $key;
    $this->exceptionConfiguration = $exceptionConfiguration;
  }

  /**
   * @return bool
   */
  public function isEnabled(): bool {
    return $this->enabled;
  }

  /**
   * @return string
   */
  public function getKey(): string {
    return $this->key;
  }

  /**
   * @return ExceptionConfiguration
   */
  public function getExceptionConfiguration(): ExceptionConfiguration {
    return $this->exceptionConfiguration;
  }

}
