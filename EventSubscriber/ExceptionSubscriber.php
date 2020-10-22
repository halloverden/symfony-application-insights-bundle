<?php


namespace HalloVerden\ApplicationInsightsBundle\EventSubscriber;


use HalloVerden\ApplicationInsightsBundle\Interfaces\TelemetryTrackerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class ExceptionSubscriber implements EventSubscriberInterface {

  /**
   * @var TelemetryTrackerInterface
   */
  private $tracker;
  private $exceptionLogged = false;

  /**
   * ExceptionSubscriber constructor.
   * @param TelemetryTrackerInterface $tracker
   */
  public function __construct(TelemetryTrackerInterface $tracker) {
    $this->tracker = $tracker;
  }

  /**
   * @param ExceptionEvent $event
   */
  public function onKernelException(ExceptionEvent $event) {
    // instrumentation key is empty
    if (!$this->tracker->getContext()->getInstrumentationKey()) {
      return;
    }

    if (!$this->tracker->isExceptionEnabled()) {
      return;
    }

    if ($this->exceptionLogged) {
      return;
    }

    $this->tracker->trackException($event->getThrowable());
    $this->exceptionLogged = true;
  }

  public static function getSubscribedEvents() {
    return [
      KernelEvents::EXCEPTION => 'onKernelException'
    ];
  }

}
