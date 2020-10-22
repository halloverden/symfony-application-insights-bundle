<?php


namespace HalloVerden\ApplicationInsightsBundle\Tracker;


use ApplicationInsights\Channel\Telemetry_Channel;
use ApplicationInsights\Telemetry_Client;
use ApplicationInsights\Telemetry_Context;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Promise\PromiseInterface;
use HalloVerden\ApplicationInsightsBundle\Interfaces\TelemetryTrackerInterface;
use Psr\Cache\InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;

class TelemetryTracker implements TelemetryTrackerInterface {

  /**
   * @var Telemetry_Client
   */
  private $client;

  /**
   * @var FailureCache
   */
  private $cache;

  /**
   * @var LoggerInterface
   */
  private $logger;

  /**
   * @var Configuration
   */
  private $configuration;

  /**
   * TelemetryTracker constructor.
   * @param Telemetry_Client $client
   * @param FailureCache $cache
   * @param LoggerInterface $logger
   * @param Configuration $configuration
   */
  public function __construct(Telemetry_Client $client, FailureCache $cache, LoggerInterface $logger, Configuration $configuration) {
    $client->getContext()->setInstrumentationKey($configuration->getKey());
    $this->client = $client;
    $this->cache = $cache;
    $this->logger = $logger;
    $this->configuration = $configuration;
  }

  /**
   * @return bool
   */
  public function isExceptionEnabled() {
    return $this->configuration->getException()->isEnabled();
  }

  /**
   * @return Telemetry_Context
   */
  public function getContext() {
    return $this->client->getContext();
  }

  /**
   * @return Telemetry_Channel
   */
  public function getChannel() {
    return $this->client->getChannel();
  }

  /**
   * @param \Throwable $exception
   * @param array|null $properties
   * @param array|null $measurements
   */
  public function trackException(\Throwable $exception, array $properties = NULL, array $measurements = NULL) {
    if (!$this->isExceptionEnabled() || $this->configuration->getException()->isIgnored(\get_class($exception))) {
      return;
    }

    TelemetryData::exception($exception, $properties, $measurements)->validate();
    $this->client->trackException($exception, $properties, $measurements);
  }

  /**
   * @return PromiseInterface|ResponseInterface|null
   * @throws InvalidArgumentException
   */
  public function flush() {
    if (!$this->isExceptionEnabled()) {
      return null;
    }

    try {
      $response = $this->client->flush();
    } catch (\Throwable $e) {
      $this->cache->add(...$this->client->getChannel()->getQueue());
      $this->logger->error(
        sprintf('Exception occurred while flushing App Insights Telemetry Client: %s', $e->getMessage()),
        \json_decode($this->client->getChannel()->getSerializedQueue(), true)
      );

      return $e instanceof RequestException ? $e->getResponse() : null;
    }

    try {
      if ($this->cache->empty()) {
        return $response;
      }

      $failures = [];
      foreach ($this->cache->all() as $item) {
        try {
          (new SendOne)($this->client, $item);
        } catch (\Throwable $e) {
          $this->logger->error(
            sprintf('Exception occurred while flushing App Insights Telemetry Client: %s', $e->getMessage()),
            [
              'item' => \json_encode($item),
              'exception' => $e
            ]
          );

          $failures[] = $item;
        }
      }

      $this->cache->purge();

      if (\count($failures) > 0) {
        $this->cache->add(...$failures);
      }
    } catch (\Throwable $e) {
      $this->logger->error(
        sprintf('Exception occurred while flushing App Insights Failure Cache: %s', $e->getMessage()),
        ['exception' => $e]
      );
    }
    return null;
  }

}
