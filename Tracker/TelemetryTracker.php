<?php

/*
 * This file is inspired by the app-insights-php/client package.
 *
 * (c) 2019 App Insights PHP
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace HalloVerden\ApplicationInsightsBundle\Tracker;


use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Promise\PromiseInterface;
use HalloVerden\ApplicationInsights\Channel\TelemetryChannel;
use HalloVerden\ApplicationInsights\TelemetryClient;
use HalloVerden\ApplicationInsights\TelemetryContext;
use HalloVerden\ApplicationInsightsBundle\Interfaces\TelemetryTrackerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Throwable;

class TelemetryTracker implements TelemetryTrackerInterface {

  /**
   * @var TelemetryClient
   */
  private $client;

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
   * @param TelemetryClient $client
   * @param LoggerInterface $logger
   * @param Configuration $configuration
   */
  public function __construct(TelemetryClient $client, LoggerInterface $logger, Configuration $configuration) {
    $client->getContext()->setInstrumentationKey($configuration->getKey());
    $this->client = $client;
    $this->logger = $logger;
    $this->configuration = $configuration;
  }

  /**
   * @return bool
   */
  public function isEnabled() {
    return $this->configuration->isEnabled();
  }

  /**
   * @return bool
   */
  public function isExceptionEnabled() {
    return $this->configuration->getExceptionConfiguration()->isEnabled();
  }

  /**
   * @return TelemetryContext
   */
  public function getContext() {
    return $this->client->getContext();
  }

  /**
   * @return TelemetryChannel
   */
  public function getChannel() {
    return $this->client->getChannel();
  }

  /**
   * @param Throwable $exception
   * @param array|null $properties
   * @param array|null $measurements
   */
  public function trackException(Throwable $exception, array $properties = NULL, array $measurements = NULL) {
    if (!$this->isExceptionEnabled() || $this->configuration->getExceptionConfiguration()->isIgnored(get_class($exception))) {
      return;
    }

    TelemetryData::exception($exception, $properties, $measurements)->validate();
    $this->client->trackException($exception, $properties, $measurements);
  }

  /**
   * @return PromiseInterface|ResponseInterface|null
   */
  public function flush() {
    if (!$this->isExceptionEnabled()) {
      return null;
    }

    try {
      $response = $this->client->flush();
    } catch (Throwable $e) {
      $this->logger->error(
        sprintf('Exception occurred while flushing App Insights Telemetry Client: %s', $e->getMessage()),
        json_decode($this->client->getChannel()->getSerializedQueue(), true)
      );

      return $e instanceof RequestException ? $e->getResponse() : null;
    }
    return $response;
  }

}
