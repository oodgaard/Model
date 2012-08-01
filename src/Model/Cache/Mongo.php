<?php

namespace Model\Cache;

/**
 * The Memcache driver.
 * 
 * @category Cache
 * @package  Model
 * @author   Trey Shugart <treshugart@gmail.com>
 * @license  Copyright (c) 2011 Trey Shugart http://europaphp.org/license
 */
class Mongo implements CacheInterface
{
    /**
     * The default Memcache configuration.
     * 
     * @var array
     */
    private $config = [
        'db'         => 'cache',
        'collection' => 'cache',
        'dsn'        => null,
        'lifetime'   => null,
        'options'    => []
    ];

    /**
     * The memcache instance to use.
     * 
     * @var MongoDb
     */
    private $mongo;

    /**
     * Constructs a new memcache cache driver and sets its configuration.
     * 
     * @param array $config The Memcache configuration.
     * 
     * @return Mongo
     */
    public function __construct(array $config = [])
    {
        $this->config = array_merge($this->config, $config);
        
        $mongo   = new \Mongo($this->config['dsn'], $this->config['options']);
        $mongodb = $mongo->selectDB($this->config['db']);
        
        $this->collection = $mongodb->selectCollection($this->config['collection']);
        $this->collection->ensureIndex([
            '_id'     => 1,
            'expires' => 1
        ], [
            'background' => true
        ]);
    }

    /**
     * Sets an item in the cache.
     * 
     * @param string $key      The cache key.
     * @param mixed  $value    The cached value.
     * @param mixed  $lifetime The max lifetime of the item in the cache.
     * 
     * @return Mongo
     */
    public function set($key, $value, $lifetime = null)
    {
        $this->collection->save([
            '_id'     => $key,
            'value'   => serialize($value),
            'expires' => $this->config['lifetime'] ?: time() + ($lifetime ?: 1000)
        ]);
        return $this;
    }

    /**
     * Returns an item from the cache.
     * 
     * @param string $key The cache key.
     * 
     * @return mixed
     */
    public function get($key)
    {
        $value = $this->collection->findOne(['_id' => $key, 'expires' => ['$gte' => time()]]);
        
        if ($value) {
            $value = $value['value'];
            $value = unserialize($value);
        }
        
        return $value ?: false;
    }

    /**
     * Checks to see if the specified cache item exists.
     * 
     * @param string $key The key to check for.
     * 
     * @return bool
     */
    public function exists($key)
    {
        return $this->get($key) !== null;
    }

    /**
     * Removes the item with the specified key.
     * 
     * @param string $key The key of the item to remove.
     * 
     * @return Mongo
     */
    public function remove($key)
    {
        $this->collection->remove(['_id' => $key]);
        return $this;
    }
}