<?php


namespace HalloVerden\ApplicationInsightsBundle\Interfaces;


interface TelemetryTrackerInterface {

  public function getContext();

  public function getChannel();

  public function trackException(\Throwable $getThrowable);

  public function isEnabled();

  public function isExceptionEnabled();

  public function flush();
}
