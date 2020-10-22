<?php


namespace HalloVerden\ApplicationInsightsBundle\Tracker;


use ApplicationInsights\Channel\Contracts\Envelope;
use Psr\Cache\InvalidArgumentException;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

/**
 * Class FailureCache
 * @package HalloVerden\ApplicationInsightsBundle\Tracker
 */
class FailureCache {

  public const CACHE_CHANNEL_KEY = 'app_insights_php.failure_cache';
  public const CACHE_CHANNEL_TTL_SEC = 86400;

  private $cache;

  /**
   * FailureCache constructor.
   * @param CacheInterface $cache
   */
  public function __construct(CacheInterface $cache) {
    $this->cache = $cache;
  }

  /**
   * @param Envelope ...$envelopes
   *
   * @throws InvalidArgumentException
   */
  public function add(Envelope ...$envelopes): void {
    if ($this->cache->get(self::CACHE_CHANNEL_KEY, function (){return false;})) {
      $envelopes = \array_merge(
        $this->all(),
        $envelopes
      );
    }
    $this->purge();
    $this->cache->get(self::CACHE_CHANNEL_KEY, function (ItemInterface $item, $envelopes){
      $item->set($envelopes);
      $item->expiresAfter(self::CACHE_CHANNEL_TTL_SEC);
      return$this->cache->save($item);
    });
  }

  /**
   * @throws InvalidArgumentException
   */
  public function purge(): void {
    $this->cache->delete(self::CACHE_CHANNEL_KEY);
  }

  /**
   * @return array
   * @throws InvalidArgumentException
   */
  public function all(): array {
    $cacheData = $this->cache->get(self::CACHE_CHANNEL_KEY, function (){return [];});
    return unserialize($cacheData);
  }

  /**
   * @return bool
   * @throws InvalidArgumentException
   */
  public function empty(): bool {
    return \count($this->all()) === 0;
  }

}
