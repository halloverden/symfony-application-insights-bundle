<?php

/*
 * This file is inspired by the app-insights-php/app-insights-php-bundle package.
 *
 * (c) 2019 App Insights PHP
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
    if ( !$this->tracker->isEnabled() ||
         !$this->tracker->getContext()->getInstrumentationKey() ||
         !$this->tracker->isExceptionEnabled() ||
          $this->exceptionLogged
    ) {
      return;
    }

    $this->tracker->trackException($event->getThrowable());
    $this->exceptionLogged = true;
  }

  public static function getSubscribedEvents() {
    return array(
      KernelEvents::EXCEPTION => [ 'onKernelException', 255 ]
    );
  }

}
