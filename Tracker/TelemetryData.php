<?php


namespace HalloVerden\ApplicationInsightsBundle\Tracker;


class TelemetryData {

  private $data;

  public function __construct(...$data) {
    $this->data = $data;
  }

  public static function exception(\Throwable $exception, array $properties = NULL, array $measurements = NULL): self {
    return new self($exception->getTraceAsString(), $properties, $measurements);
  }

  public function exceededMaximumSize(): bool {
    return \strlen((string) \json_encode($this->data)) > 65000;
  }

  public function validate(): void {
    if ($this->exceededMaximumSize()) {
      throw new \RuntimeException('Telemetry exceeded the maximum size of 65kb: '.\json_encode($this->data));
    };
  }
}
