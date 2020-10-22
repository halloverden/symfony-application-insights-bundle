<?php


namespace HalloVerden\ApplicationInsightsBundle\Tracker\Configuration;


class ExceptionConfiguration {

  /**
   * @var bool
   */
  private $enabled;
  /**
   * @var array
   */
  private $ignoredExceptions;

  /**
   * Exception constructor.
   * @param bool $enabled
   * @param array $ignoredExceptions
   */
  public function __construct(bool $enabled, array $ignoredExceptions = []) {

    foreach ($ignoredExceptions as $exceptionClass) {
      if (!\class_exists($exceptionClass)) {
        throw new \RuntimeException(sprintf('Exception class "%s" flagged to be ignored in ignored_exceptions option does not exists', $exceptionClass));
      }
    }

    $this->enabled = $enabled;
    $this->ignoredExceptions = $ignoredExceptions;
  }

  /**
   * @param string $exceptionClass
   * @return bool
   */
  public function isIgnored(string $exceptionClass) : bool {
    return (bool) array_filter(
      $this->ignoredExceptions,
      function(string $ignoredExceptionClass) use ($exceptionClass) {
        return $ignoredExceptionClass === $exceptionClass;
      }
    );
  }

  public function disable() : void {
    $this->enabled = false;
  }

  public function enable() : void {
    $this->enabled = true;
  }

  /**
   * @return bool
   */
  public function isEnabled(): bool {
    return $this->enabled;
  }

  /**
   * @param string $exceptionClass
   */
  public function ignore(string $exceptionClass) : void {
    if (!\class_exists($exceptionClass)) {
      throw new \RuntimeException(sprintf('Exception class "%s" flagged to be ignored in ignored_exceptions option does not exists', $exceptionClass));
    }

    if (\in_array($exceptionClass, $this->ignoredExceptions)) {
      return ;
    }
    $this->ignoredExceptions[] = $exceptionClass;
  }

}
