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
    return array(
      KernelEvents::TERMINATE => [ 'onKernelTerminate', -255 ]
    );
  }
}
