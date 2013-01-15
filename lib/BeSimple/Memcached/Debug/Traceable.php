<?php

class BeSimple_Memcached_Debug_Traceable implements BeSimple_Memcached_TraceableInterface, BeSimple_Memcached_MemcachedInterface
{
    protected $memcached;

    protected $actions;

    public function __construct($memcached)
    {
        if (!$memcached instanceof Memcached && !$memcached instanceof BeSimple_Memcached_MemcachedInterface) {
            throw new \InvalidArgumentException('The memcached must be an instance of Memcached or BeSimple_Memcached_MemcachedInterface');
        }

        $this->memcached = $memcached;
        $this->actions = array();
    }

    public function getActions()
    {
        return $this->actions;
    }

    public function add($key, $value, $expiration = null)
    {
        $this->startTimer();
        $returnedValue = $this->memcached->add($key, $value, $expiration);

        return $this->addAction('add', $returnedValue, $this->stopTimer(), array(
            'key' => $key,
            'value' => $value,
            'expiration' => $expiration,
        ));
    }

    public function addByKey($serverKey, $key, $value, $expiration = null)
    {
        $this->startTimer();
        $returnedValue = $this->memcached->addByKey($serverKey, $key, $value, $expiration);

        return $this->addAction('addByKey', $returnedValue, $this->stopTimer(), array(
            'server_key' => $serverKey,
            'key' => $key,
            'value' => $value,
            'expiration' => $expiration,
        ));
    }

    public function addServer($host, $port, $weight = null)
    {
        $this->startTimer();
        $returnedValue = $this->memcached->addServer($host, $port, $weight);

        return $this->addAction('addServer', $returnedValue, $this->stopTimer(), array(
            'host' => $host,
            'port' => $port,
            'weight' => $weight,
        ));
    }

    public function addServers(array $servers)
    {
        $this->startTimer();
        $returnedValue = $this->memcached->addServers($servers);

        return $this->addAction('addServers', $returnedValue, $this->stopTimer(), array(
            'servers' => $servers,
        ));
    }

    public function append($key, $value)
    {
        $this->startTimer();
        $returnedValue = $this->memcached->append($key, $value);

        return $this->addAction('append', $returnedValue, $this->stopTimer(), array(
            'key' => $key,
            'value' => $value,
        ));
    }

    public function appendByKey($serverKey, $key, $value)
    {
        $this->startTimer();
        $returnedValue = $this->memcached->appendByKey($serverKey, $key, $value);

        return $this->addAction('appendByKey', $returnedValue, $this->stopTimer(), array(
            'server_key' => $serverKey,
            'key' => $key,
            'value' => $value,
        ));
    }

    public function cas($casToken, $key, $value, $expiration = null)
    {
        $this->startTimer();
        $returnedValue = $this->memcached->cas($casToken, $key, $value, $expiration);

        return $this->addAction('cas', $returnedValue, $this->stopTimer(), array(
            'cas_token' => $casToken,
            'key' => $key,
            'value' => $value,
            'expiration' => $expiration,
        ));
    }

    public function casByKey($casToken, $serverKey, $key, $value, $expiration = null)
    {
        $this->startTimer();
        $returnedValue = $this->memcached->casByKey($casToken, $serverKey, $key, $value, $expiration);

        return $this->addAction('casByKey', $returnedValue, $this->stopTimer(), array(
            'cas_token' => $casToken,
            'server_key' => $serverKey,
            'key' => $key,
            'value' => $value,
            'expiration' => $expiration,
        ));
    }

    public function decrement($key, $offset = 1)
    {
        $this->startTimer();
        $returnedValue = $this->memcached->decrement($key, $offset);

        return $this->addAction('decrement', $returnedValue, $this->stopTimer(), array(
            'key' => $key,
            'offset' => $offset,
        ));
    }

    public function delete($key, $time = 0)
    {
        $this->startTimer();
        $returnedValue = $this->memcached->delete($key, $time);

        return $this->addAction('delete', $returnedValue, $this->stopTimer(), array(
            'key' => $key,
            'time' => $time,
        ));
    }

    public function deleteByKey($serverKey, $key, $time = 0)
    {
        $this->startTimer();
        $returnedValue = $this->memcached->deleteByKey($serverKey, $key, $time);

        return $this->addAction('deleteByKey', $returnedValue, $this->stopTimer(), array(
            'server_key' => $serverKey,
            'key' => $key,
            'time' => $time,
        ));
    }

    public function fetch()
    {
        $this->startTimer();
        $returnedValue = $this->memcached->fetch();

        return $this->addAction('fetch', $returnedValue, $this->stopTimer());
    }

    public function fetchAll()
    {
        $this->startTimer();
        $returnedValue = $this->memcached->fetchAll();

        return $this->addAction('fetchAll', $returnedValue, $this->stopTimer());
    }

    public function flush($delay = 0)
    {
        $this->startTimer();
        $returnedValue = $this->memcached->flush($delay);

        return $this->addAction('flush', $returnedValue, $this->stopTimer(), array(
            'delay' => $delay,
        ));
    }

    public function get($key, $cacheCb = null, &$casToken = null)
    {
        $this->startTimer();
        $returnedValue = $this->memcached->get($key, $cacheCb, $casToken);

        return $this->addAction('get', $returnedValue, $this->stopTimer(), array(
            'key' => $key,
            'cache_cb' => $cacheCb,
            'cas_token' => $casToken,
        ));
    }

    public function getByKey($serverKey, $key, $cacheCb = null, &$casToken = null)
    {
        $this->startTimer();
        $returnedValue = $this->memcached->getByKey($serverKey, $key, $cacheCb, $casToken);

        return $this->addAction('getByKey', $returnedValue, $this->stopTimer(), array(
            'server_key' => $serverKey,
            'key' => $key,
            'cache_cb' => $cacheCb,
            'cas_token' => $casToken,
        ));
    }

    public function getDelayed(array $keys, $withCas = false, $cacheCb = null)
    {
        $this->startTimer();
        $returnedValue = $this->memcached->getDelayed($keys, $withCas, $cacheCb);

        return $this->addAction('getDelayed', $returnedValue, $this->stopTimer(), array(
            'keys' => $keys,
            'with_cas' => $withCas,
            'cache_cb' => $cacheCb,
        ));
    }

    public function getDelayedByKey($serverKey, array $keys, $withCas = false, $cacheCb = null)
    {
        $this->startTimer();
        $returnedValue = $this->memcached->getDelayedByKey($serverKey, $keys, $withCas, $cacheCb);

        return $this->addAction('getByKey', $returnedValue, $this->stopTimer(), array(
            'server_key' => $serverKey,
            'keys' => $keys,
            'with_cas' => $withCas,
            'cache_cb' => $cacheCb,
        ));
    }

    public function getMulti(array $keys, &$casToken = null, $flags = null)
    {
        $this->startTimer();
        $returnedValue = $this->memcached->getMulti($keys, $casToken, $flags);

        return $this->addAction('getMulti', $returnedValue, $this->stopTimer(), array(
            'keys' => $keys,
            'cas_token' => $casToken,
            'flags' => $flags,
        ));
    }

    public function getMultiByKey($serverKey, array $keys, &$casToken = null, $flags = null)
    {
        $this->startTimer();
        $returnedValue = $this->memcached->getMultiByKey($serverKey, $keys, $casToken, $flags);

        return $this->addAction('getMultiByKey', $returnedValue, $this->stopTimer(), array(
            'server_key' => $serverKey,
            'keys' => $keys,
            'cas_token' => $casToken,
            'flags' => $flags,
        ));
    }

    public function getOption($option)
    {
        $this->startTimer();
        $returnedValue = $this->memcached->getOption($option);

        return $this->addAction('getOption', $returnedValue, $this->stopTimer(), array(
            'option' => $option,
        ));
    }

    public function getResultCode()
    {
        $this->startTimer();
        $returnedValue = $this->memcached->getResultCode();

        return $this->addAction('getResultCode', $returnedValue, $this->stopTimer());
    }

    public function getResultMessage()
    {
        $this->startTimer();
        $returnedValue = $this->memcached->getResultMessage();

        return $this->addAction('getResultMessage', $returnedValue, $this->stopTimer());
    }

    public function getServerByKey($serverKey)
    {
        $this->startTimer();
        $returnedValue = $this->memcached->getServerByKey($serverKey);

        return $this->addAction('getServerByKey', $returnedValue, $this->stopTimer(), array(
            'server_key' => $serverKey,
        ));
    }

    public function getServerList()
    {
        $this->startTimer();
        $returnedValue = $this->memcached->getServerList();

        return $this->addAction('getServerList', $returnedValue, $this->stopTimer());
    }

    public function getStats()
    {
        $this->startTimer();
        $returnedValue = $this->memcached->getStats();

        return $this->addAction('getStats', $returnedValue, $this->stopTimer());
    }

    public function getVersion()
    {
        $this->startTimer();
        $returnedValue = $this->memcached->getVersion();

        return $this->addAction('getVersion', $returnedValue, $this->stopTimer());
    }

    public function increment($key, $offset = 1)
    {
        $this->startTimer();
        $returnedValue = $this->memcached->increment($key, $offset);

        return $this->addAction('increment', $returnedValue, $this->stopTimer(), array(
            'key' => $key,
            'offset' => $offset,
        ));
    }

    public function prepend($key, $value)
    {
        $this->startTimer();
        $returnedValue = $this->memcached->prepend($key, $value);

        return $this->addAction('prepend', $returnedValue, $this->stopTimer(), array(
            'key' => $key,
            'value' => $value,
        ));
    }

    public function prependByKey($serverKey, $key, $value)
    {
        $this->startTimer();
        $returnedValue = $this->memcached->prependByKey($serverKey, $key, $value);

        return $this->addAction('prependByKey', $returnedValue, $this->stopTimer(), array(
            'server_key' => $serverKey,
            'key' => $key,
            'value' => $value,
        ));
    }

    public function replace($key, $value, $expiration = null)
    {
        $this->startTimer();
        $returnedValue = $this->memcached->replace($key, $value, $expiration);

        return $this->addAction('replace', $returnedValue, $this->stopTimer(), array(
            'key' => $key,
            'value' => $value,
            'expiration' => $expiration,
        ));
    }

    public function replaceByKey($serverKey, $key, $value, $expiration = null)
    {
        $this->startTimer();
        $returnedValue = $this->memcached->replaceByKey($serverKey, $key, $value, $expiration);

        return $this->addAction('replaceByKey', $returnedValue, $this->stopTimer(), array(
            'server_key' => $serverKey,
            'key' => $key,
            'value' => $value,
            'expiration' => $expiration,
        ));
    }

    public function set($key, $value, $expiration = null)
    {
        $this->startTimer();
        $returnedValue = $this->memcached->set($key, $value, $expiration);

        return $this->addAction('set', $returnedValue, $this->stopTimer(), array(
            'key' => $key,
            'value' => $value,
            'expiration' => $expiration,
        ));
    }

    public function setByKey($serverKey, $key, $value, $expiration = null)
    {
        $this->startTimer();
        $returnedValue = $this->memcached->setByKey($serverKey, $key, $value, $expiration);

        return $this->addAction('setByKey', $returnedValue, $this->stopTimer(), array(
            'server_key' => $serverKey,
            'key' => $key,
            'value' => $value,
            'expiration' => $expiration,
        ));
    }

    public function setMulti(array $items, $expiration = null)
    {
        $this->startTimer();
        $returnedValue = $this->memcached->setMulti($items, $expiration);

        return $this->addAction('setMulti', $returnedValue, $this->stopTimer(), array(
            'items' => $items,
            'expiration' => $expiration,
        ));
    }

    public function setMultiByKey($serverKey, array $items, $expiration = null)
    {
        $this->startTimer();
        $returnedValue = $this->memcached->setMultiByKey($serverKey, $items, $expiration);

        return $this->addAction('setMultiByKey', $returnedValue, $this->stopTimer(), array(
            'server_key' => $serverKey,
            'items' => $items,
            'expiration' => $expiration,
        ));
    }

    public function setOption($option, $value)
    {
        $this->startTimer();
        $returnedValue = $this->memcached->setOption($option, $value);

        return $this->addAction('setOption', $returnedValue, $this->stopTimer(), array(
            'option' => $option,
            'value' => $value,
        ));
    }

    protected function addAction($action, $returnedValue, $time, array $params = array())
    {
        if ('getResultCode' === $action || 'getResultMessage' === $action) {
            $resultMessage = null;
        } else {
            $resultMessage = $this->memcached->getResultMessage();
        }

        $this->actions[] = array(
            'name' => $action,
            'params' => $params,
            'returned_value' => $returnedValue,
            'time' => $time,
            'result_message' => $resultMessage,
        );

        return $returnedValue;
    }

    protected function startTimer()
    {
        $this->timer = microtime(true);
    }

    protected function stopTimer()
    {
        return microtime(true) - $this->timer;
    }
}
