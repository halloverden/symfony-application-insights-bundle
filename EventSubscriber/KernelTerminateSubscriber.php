<?php


namespace HalloVerden\ApplicationInsightsBundle\EventSubscriber;


use HalloVerden\ApplicationInsightsBundle\Interfaces\TelemetryTrackerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;


class KernelTerminateSubscriber implements EventSubscriberInterface {

  /**
   * @var TelemetryTrackerInterface
   */
  private $tracker;

  public function __construct(TelemetryTrackerInterface $tracker) {
    $this->tracker = $tracker;
  }


  public function onKernelTerminate() {
    if (!$this->tracker->getContext()->getInstrumentationKey()) {
      // instrumentation key is empty
      return;
    }

    if (!count($this->tracker->getChannel()->getQueue())) {
      // telemetry client queue is empty
      return;
    }

    $this->tracker->flush();
  }

  public static function getSubscribedEvents() {
    return [
      KernelEvents::TERMINATE => 'onKernelTerminate'
    ];
  }
}
