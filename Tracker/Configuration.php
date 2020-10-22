<?php


namespace HalloVerden\ApplicationInsightsBundle\Tracker;


use HalloVerden\ApplicationInsightsBundle\Tracker\Configuration\ExceptionConfiguration;

class Configuration {

  /**
   * @var string
   */
  private $key;

  /**
   * @var ExceptionConfiguration
   */
  private $exception;

  public function __construct(string $key, ExceptionConfiguration $exception) {
    $this->exception = $exception;
    $this->key = $key;
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
  public function getException(): ExceptionConfiguration {
    return $this->exception;
  }

}
