<?php

interface BeSimple_Memcached_MemcachedInterface
{
    /**
     * @return bool
     */
    public function add($key, $value, $expiration = null);

    /**
     * @return bool
     */
    public function addByKey($serverKey, $key, $value, $expiration = null);

    /**
     * @return bool
     */
    public function addServer($host, $port, $weight = 0);

    /**
     * @return bool
     */
    public function addServers(array $servers);

    /**
     * @return bool
     */
    public function append($key, $value);

    /**
     * @return bool
     */
    public function appendByKey($serverKey, $key, $value);

    /**
     * @return bool
     */
    public function cas($casToken, $key, $value, $expiration = null);

    /**
     * @return bool
     */
    public function casByKey($casToken, $serverKey, $key, $value, $expiration = null);

    /**
     * @return int
     */
    public function decrement($key, $offset = 1);

    /**
     * @return bool
     */
    public function delete($key, $time = 0);

    /**
     * @return bool
     */
    public function deleteByKey($serverKey, $key, $time = 0);

    /**
     * @return array
     */
    public function fetch();

    /**
     * @return array
     */
    public function fetchAll();

    /**
     * @return bool
     */
    public function flush($delay = 0);

    /**
     * @return mixed
     */
    public function get($key, $cacheCb = null, &$casToken = null);

    /**
     * @return mixed
     */
    public function getByKey($serverKey, $key, $cacheCb = null, &$casToken = null);

    /**
     * @return bool
     */
    public function getDelayed(array $keys, $withCas = false, $cacheCb = null);

    /**
     * @return bool
     */
    public function getDelayedByKey($serverKey, array $keys, $withCas = false, $cacheCb = null);

    /**
     * @return mixed
     */
    public function getMulti(array $keys, &$casToken = null, $flags = null);

    /**
     * @return array
     */
    public function getMultiByKey($serverKey, array $keys, &$casToken = null, $flags = null);

    /**
     * @return mixed
     */
    public function getOption($option);

    /**
     * @return int
     */
    public function getResultCode();

    /**
     * @return string
     */
    public function getResultMessage();

    /**
     * @return array
     */
    public function getServerByKey($serverKey);

    /**
     * @return array
     */
    public function getServerList();

    /**
     * @return array
     */
    public function getStats();

    /**
     * @return array
     */
    public function getVersion();

    /**
     * @return int
     */
    public function increment($key, $offset = 1);

    /**
     * @return bool
     */
    public function prepend($key, $value);

    /**
     * @return bool
     */
    public function prependByKey($serverKey, $key, $value);

    /**
     * @return bool
     */
    public function replace($key, $value, $expiration = null);

    /**
     * @return bool
     */
    public function replaceByKey($serverKey, $key, $value, $expiration = null);

    /**
     * @return bool
     */
    public function set($key, $value, $expiration = null);

    /**
     * @return bool
     */
    public function setByKey($serverKey, $key, $value, $expiration = null);

    /**
     * @return bool
     */
    public function setMulti(array $items, $expiration = null);

    /**
     * @return bool
     */
    public function setMultiByKey($serverKey, array $items, $expiration = null);

    /**
     * @return bool
     */
    public function setOption($option, $value);
}
