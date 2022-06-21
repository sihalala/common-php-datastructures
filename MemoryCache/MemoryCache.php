<?php
namespace PhpMemoryCache\MemoryCache;

class MemoryCache
{
    const EXPIRATION_TIMESTAMP = 'expirationTimestamp';
    const DATA = 'data';

    /** @var MemoryCache */
    private static $instance;

    /** @var [] */
    private $indexedCache = [];

    private function __construct()
    {
    }

    public static function getInstance(): MemoryCache
    {
        if (null === static::$instance) {
            static::$instance = new self();
        }

        return static::$instance;
    }

    public static function getStandAloneInstance(): MemoryCache
    {
        return new self();
    }

    public function add(string $index, $data, int $lifeTimeInSeconds = 3600)
    {
        $this->indexedCache[$index][self::EXPIRATION_TIMESTAMP] = time() + $lifeTimeInSeconds;
        $this->indexedCache[$index][self::DATA] = $data;
    }

    /**
     * @param string $index
     *
     * @throws \Exception
     *
     * @return mixed
     */
    public function get(string $index)
    {
        $this->assertIndexExists($index);

        return $this->indexedCache[$index][self::DATA];
    }

    /**
     * @param string $index
     *
     * @throws \Exception
     *
     * @return mixed
     */
    public function pop(string $index)
    {
        $this->assertIndexExists($index);

        $value = $this->indexedCache[$index][self::DATA];
        unset($this->indexedCache[$index]);

        return $value;
    }

    public function exists(string $index): bool
    {
        return array_key_exists($index, $this->indexedCache)
            && array_key_exists(self::DATA, $this->indexedCache[$index])
            && !$this->hasLifeTimeExpired($index);
    }

    public function clear()
    {
        $this->indexedCache = [];
    }

    private function hasLifeTimeExpired(string $index): bool
    {
        $hasLifeTimeExpired = ($this->indexedCache[$index][self::EXPIRATION_TIMESTAMP] ?? 0) <= time();

        if ($hasLifeTimeExpired) {
            unset($this->indexedCache[$index]);
        }

        return $hasLifeTimeExpired;
    }

    /**
     * @param string $index
     *
     * @throws \Exception
     */
    private function assertIndexExists(string $index)
    {
        if (!$this->exists($index)) {
            throw new \Exception(sprintf('Cache not present or expired for index %s', $index));
        }
    }

    public function getOrLoad(string $index, int $lifeTimeInSeconds, \Closure $function)
    {
        try {
            $value = $this->get($index);
        } catch (\Exception $e) {
            $value = $function();
            $this->add($index, $value, $lifeTimeInSeconds);
        }

        return $value;
    }
}
