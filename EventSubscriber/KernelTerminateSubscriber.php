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
    if ( !$this->tracker->isEnabled() ||
         !$this->tracker->getContext()->getInstrumentationKey() ||
         !count($this->tracker->getChannel()->getQueue())
    ) {
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
