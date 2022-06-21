<?php
namespace PhpMemoryCache\MemoryCache;

class ClientCode
{
    private const CACHE_KEY = 'someCacheKey';
    const CACHE_TIME = 180;

    /** @var MemoryCache */
    private $cache;

    public function __construct(MemoryCache $cache)
    {
        $this->cache = $cache;
    }

    public function someFunction()
    {
        $this->cache->add(
            self::CACHE_KEY,
            'data of cache',
            self::CACHE_TIME
        );
    }
}
