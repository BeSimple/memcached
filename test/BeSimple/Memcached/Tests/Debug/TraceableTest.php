<?php

class BeSimple_Memcached_Tests_Debug_TraceableTest extends PHPUnit_Framework_TestCase
{
    public function testConstructWithMemcachedInterface()
    {
        new BeSimple_Memcached_Debug_Traceable($this->getMemcachedInterfaceMock());
    }

    public function testConstructWithMemcached()
    {
        if (!extension_loaded('Memcached')) {
            $this->markTestSkipped('The Memcached extension is not available.');
        }

        new BeSimple_Memcached_Debug_Traceable($this->getMock('Memcached'));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testConstructWithInvalidMemcachedInstance()
    {
        new BeSimple_Memcached_Debug_Traceable($this);
    }

    public function testGetActions()
    {
        $memcached = new BeSimple_Memcached_Debug_Traceable($this->getMemcachedInterfaceMock());

        $this->assertEquals(array(), $memcached->getActions());
    }

    public function testAdd()
    {
        $memcached = $this->getMemcachedInterfaceMock(array('SUCCESS', 'NOT STORED'), array('add' => array(true, false)));
        $traceable = new BeSimple_Memcached_Debug_Traceable($memcached);

        $return1 = $traceable->add('foo', 'bar', 42);
        $this->assertCount(1, $actions = $traceable->getActions());

        $return2 = $traceable->add('foo', 'foo');
        $actions = $traceable->getActions();
        $this->assertCount(2, $actions);

        $this->checkAction(array_pop($actions), 'add', array(
            'key' => 'foo',
            'value' => 'foo',
            'expiration' => null,
        ), false, 'NOT STORED', $return2);

        $this->checkAction(array_pop($actions), 'add', array(
            'key' => 'foo',
            'value' => 'bar',
            'expiration' => 42,
        ), true, 'SUCCESS', $return1);
    }

    public function testAddByKey()
    {
        $memcached = $this->getMemcachedInterfaceMock(array('SUCCESS', 'NOT STORED'), array('addByKey' => array(true, false)));
        $traceable = new BeSimple_Memcached_Debug_Traceable($memcached);

        $return1 = $traceable->addByKey('foobar', 'foo', 'bar', 42);
        $this->assertCount(1, $traceable->getActions());

        $return2 = $traceable->addByKey('barfoo', 'foo', 'foo', 120);
        $actions = $traceable->getActions();
        $this->assertCount(2, $actions);

        $this->checkAction(array_pop($actions), 'addByKey', array(
            'server_key' => 'barfoo',
            'key' => 'foo',
            'value' => 'foo',
            'expiration' => 120,
        ), false, 'NOT STORED', $return2);

        $this->checkAction($action = array_pop($actions), 'addByKey', array(
            'server_key' => 'foobar',
            'key' => 'foo',
            'value' => 'bar',
            'expiration' => 42,
        ), true, 'SUCCESS', $return1);
    }

    public function testAddServer()
    {
        $memcached = $this->getMemcachedInterfaceMock(array('SUCCESS', 'HOST LOOKUP FAILURE'), array('addServer' => array(true, false)));
        $traceable = new BeSimple_Memcached_Debug_Traceable($memcached);

        $return1 = $traceable->addServer('127.0.0.1', 11211, 30);
        $this->assertCount(1, $traceable->getActions());

        $return2 = $traceable->addServer('foo.bar', 11212);
        $actions = $traceable->getActions();
        $this->assertCount(2, $actions);

        $this->checkAction(array_pop($actions), 'addServer', array(
            'host' => 'foo.bar',
            'port' => 11212,
            'weight' => null,
        ), false, 'HOST LOOKUP FAILURE', $return2);

        $this->checkAction(array_pop($actions), 'addServer', array(
            'host' => '127.0.0.1',
            'port' => 11211,
            'weight' => 30,
        ), true, 'SUCCESS', $return1);
    }

    public function testAddServers()
    {
        $memcached = $this->getMemcachedInterfaceMock(array('SUCCESS', 'FAILURE'), array('addServers' => array(true, false)));
        $traceable = new BeSimple_Memcached_Debug_Traceable($memcached);

        $return1 = $traceable->addServers($servers1 = array(
            array('127.0.0.1', 11211, 30),
            array('127.0.0.1', 11212, 20),
        ));
        $this->assertCount(1, $traceable->getActions());

        $return2 = $traceable->addServers($servers2 = array(
            array('foo.bar', 11211),
            array('bar.foo', 11211, 20),
        ));
        $actions = $traceable->getActions();
        $this->assertCount(2, $actions);

        $this->checkAction(array_pop($actions), 'addServers', array(
            'servers' => $servers2,
        ), false, 'FAILURE', $return2);

        $this->checkAction(array_pop($actions), 'addServers', array(
            'servers' => $servers1,
        ), true, 'SUCCESS', $return1);
    }

    public function testAppend()
    {
        $memcached = $this->getMemcachedInterfaceMock(array('SUCCESS', 'NOT STORED'), array('append' => array(true, false)));
        $traceable = new BeSimple_Memcached_Debug_Traceable($memcached);

        $return1 = $traceable->append('foo', 'bar');
        $this->assertCount(1, $traceable->getActions());

        $return2 = $traceable->append('bar', 'foo');
        $actions = $traceable->getActions();
        $this->assertCount(2, $actions);

        $this->checkAction(array_pop($actions), 'append', array(
            'key' => 'bar',
            'value' => 'foo',
        ), false, 'NOT STORED', $return2);

        $this->checkAction(array_pop($actions), 'append', array(
            'key' => 'foo',
            'value' => 'bar',
        ), true, 'SUCCESS', $return1);
    }

    public function testAppendByKey()
    {
        $memcached = $this->getMemcachedInterfaceMock(array('SUCCESS', 'NOT STORED'), array('appendByKey' => array(true, false)));
        $traceable = new BeSimple_Memcached_Debug_Traceable($memcached);

        $return1 = $traceable->appendByKey('foobar', 'foo', 'bar');
        $this->assertCount(1, $traceable->getActions());

        $return2 = $traceable->appendByKey('barfoo', 'bar', 'foo');
        $actions = $traceable->getActions();
        $this->assertCount(2, $actions);

        $this->checkAction(array_pop($actions), 'appendByKey', array(
            'server_key' => 'barfoo',
            'key' => 'bar',
            'value' => 'foo',
        ), false, 'NOT STORED', $return2);

        $this->checkAction(array_pop($actions), 'appendByKey', array(
            'server_key' => 'foobar',
            'key' => 'foo',
            'value' => 'bar',
        ), true, 'SUCCESS', $return1);
    }

    public function testCas()
    {
        $memcached = $this->getMemcachedInterfaceMock(array('SUCCESS', 'DATA EXISTS'), array('cas' => array(true, false)));
        $traceable = new BeSimple_Memcached_Debug_Traceable($memcached);

        $return1 = $traceable->cas((double) 1, 'foo', 'bar', 42);
        $this->assertCount(1, $traceable->getActions());

        $return2 = $traceable->cas((double) 2, 'foo', 'foo');
        $actions = $traceable->getActions();
        $this->assertCount(2, $actions);

        $this->checkAction(array_pop($actions), 'cas', array(
            'cas_token' => (double) 2,
            'key' => 'foo',
            'value' => 'foo',
            'expiration' => null,
        ), false, 'DATA EXISTS', $return2);

        $this->checkAction(array_pop($actions), 'cas', array(
            'cas_token' => (double) 1,
            'key' => 'foo',
            'value' => 'bar',
            'expiration' => 42,
        ), true, 'SUCCESS', $return1);
    }

    public function testCasByKey()
    {
        $memcached = $this->getMemcachedInterfaceMock(array('SUCCESS', 'DATA EXISTS'), array('casByKey' => array(true, false)));
        $traceable = new BeSimple_Memcached_Debug_Traceable($memcached);

        $return1 = $traceable->casByKey((double) 1, 'foobar', 'foo', 'bar', 42);
        $this->assertCount(1, $traceable->getActions());

        $return2 = $traceable->casByKey((double) 2, 'barfoo', 'foo', 'foo');
        $actions = $traceable->getActions();
        $this->assertCount(2, $actions);

        $this->checkAction(array_pop($actions), 'casByKey', array(
            'cas_token' => (double) 2,
            'server_key' => 'barfoo',
            'key' => 'foo',
            'value' => 'foo',
            'expiration' => null,
        ), false, 'DATA EXISTS', $return2);

        $this->checkAction(array_pop($actions), 'casByKey', array(
            'cas_token' => (double) 1,
            'server_key' => 'foobar',
            'key' => 'foo',
            'value' => 'bar',
            'expiration' => 42,
        ), true, 'SUCCESS', $return1);
    }

    public function testDecrement()
    {
        $memcached = $this->getMemcachedInterfaceMock(array('SUCCESS', 'NOT FOUND'), array('decrement' => array(true, false)));
        $traceable = new BeSimple_Memcached_Debug_Traceable($memcached);

        $return1 = $traceable->decrement('foo', 42);
        $this->assertCount(1, $traceable->getActions());

        $return2 = $traceable->decrement('bar');
        $actions = $traceable->getActions();
        $this->assertCount(2, $actions);

        $this->checkAction(array_pop($actions), 'decrement', array(
            'key' => 'bar',
            'offset' => 1,
        ), false, 'NOT FOUND', $return2);

        $this->checkAction(array_pop($actions), 'decrement', array(
            'key' => 'foo',
            'offset' => 42,
        ), true, 'SUCCESS', $return1);
    }

    public function testDelete()
    {
        $memcached = $this->getMemcachedInterfaceMock(array('SUCCESS', 'NOT FOUND'), array('delete' => array(true, false)));
        $traceable = new BeSimple_Memcached_Debug_Traceable($memcached);

        $return1 = $traceable->delete('foo', 42);
        $this->assertCount(1, $traceable->getActions());

        $return2 = $traceable->delete('bar');
        $actions = $traceable->getActions();
        $this->assertCount(2, $actions);

        $this->checkAction(array_pop($actions), 'delete', array(
            'key' => 'bar',
            'time' => 0,
        ), false, 'NOT FOUND', $return2);

        $this->checkAction(array_pop($actions), 'delete', array(
            'key' => 'foo',
            'time' => 42,
        ), true, 'SUCCESS', $return1);
    }

    public function testDeleteByKey()
    {
        $memcached = $this->getMemcachedInterfaceMock(array('SUCCESS', 'NOT FOUND'), array('deleteByKey' => array(true, false)));
        $traceable = new BeSimple_Memcached_Debug_Traceable($memcached);

        $return1 = $traceable->deleteByKey('foobar', 'foo', 42);
        $this->assertCount(1, $traceable->getActions());

        $return2 = $traceable->deleteByKey('barfoo', 'bar');
        $actions = $traceable->getActions();
        $this->assertCount(2, $actions);

        $this->checkAction(array_pop($actions), 'deleteByKey', array(
            'server_key' => 'barfoo',
            'key' => 'bar',
            'time' => 0,
        ), false, 'NOT FOUND', $return2);

        $this->checkAction(array_pop($actions), 'deleteByKey', array(
            'server_key' => 'foobar',
            'key' => 'foo',
            'time' => 42,
        ), true, 'SUCCESS', $return1);
    }

    public function testFetch()
    {
        $memcached = $this->getMemcachedInterfaceMock(array('SUCCESS', 'END'), array('fetch' => array($result1 = array(
            'key' => 'foo',
            'value' => 'bar',
            'cas' => (double) 42,
        ), false)));
        $traceable = new BeSimple_Memcached_Debug_Traceable($memcached);

        $return1 = $traceable->fetch();
        $this->assertCount(1, $traceable->getActions());

        $return2 = $traceable->fetch();
        $actions = $traceable->getActions();
        $this->assertCount(2, $actions);

        $this->checkAction(array_pop($actions), 'fetch', array(), false, 'END', $return2);

        $this->checkAction(array_pop($actions), 'fetch', array(), $result1, 'SUCCESS', $return1);
    }

    public function testFetchAll()
    {
        $memcached = $this->getMemcachedInterfaceMock(array('SUCCESS', 'END'), array('fetchAll' => array($result1 = array(
            'key' => 'foo',
            'value' => 'bar',
            'cas' => (double) 42,
        ), false)));
        $traceable = new BeSimple_Memcached_Debug_Traceable($memcached);

        $return1 = $traceable->fetchAll();
        $this->assertCount(1, $traceable->getActions());

        $return2 = $traceable->fetchAll();
        $actions = $traceable->getActions();
        $this->assertCount(2, $actions);

        $this->checkAction(array_pop($actions), 'fetchAll', array(), false, 'END', $return2);

        $this->checkAction(array_pop($actions), 'fetchAll', array(), $result1, 'SUCCESS', $return1);
    }

    public function testFlush()
    {
        $memcached = $this->getMemcachedInterfaceMock(array('SUCCESS', 'FAILURE'), array('flush' => array(true, false)));
        $traceable = new BeSimple_Memcached_Debug_Traceable($memcached);

        $return1 = $traceable->flush(42);
        $this->assertCount(1, $traceable->getActions());

        $return2 = $traceable->flush();
        $actions = $traceable->getActions();
        $this->assertCount(2, $actions);

        $this->checkAction(array_pop($actions), 'flush', array(
            'delay' => 0,
        ), false, 'FAILURE', $return2);

        $this->checkAction(array_pop($actions), 'flush', array(
            'delay' => 42,
        ), true, 'SUCCESS', $return1);
    }

    public function testGet()
    {
        $this->markTestSkipped();
        $memcached = $this->getMemcachedInterfaceMock(array('SUCCESS', 'NOT FOUND'), array('get' => array(true, false)));
        $traceable = new BeSimple_Memcached_Debug_Traceable($memcached);

        $test = null;
        $return1 = $traceable->get('foo', null, $test);
        $this->assertCount(1, $traceable->getActions());

        $test = 10;
    }

    public function testGetOption()
    {
        $memcached = $this->getMemcachedInterfaceMock(array('SUCCESS', 'SOME ERRORS WERE REPORTED'), array('getOption' => array(true, 1)));
        $traceable = new BeSimple_Memcached_Debug_Traceable($memcached);

        $return1 = $traceable->getOption(-1001); // Memcached::OPT_COMPRESSION
        $this->assertCount(1, $traceable->getActions());

        $return2 = $traceable->getOption(-1003); // Memcached::OPT_SERIALIZER
        $actions = $traceable->getActions();
        $this->assertCount(2, $actions);

        $this->checkAction(array_pop($actions), 'getOption', array(
            'option' => -1003,
        ), 1, 'SOME ERRORS WERE REPORTED', $return2);

        $this->checkAction(array_pop($actions), 'getOption', array(
            'option' => -1001,
        ), true, 'SUCCESS', $return1);
    }

    public function testGetResultCode()
    {
        $memcached = $this->getMemcachedInterfaceMock(null, array('getResultCode' => array(
            0, // Memcached::SUCCESS
            14, // Memcached::RES_NOTSTORED
        )));
        $traceable = new BeSimple_Memcached_Debug_Traceable($memcached);

        $return1 = $traceable->getResultCode();
        $this->assertCount(1, $traceable->getActions());

        $return2 = $traceable->getResultCode();
        $actions = $traceable->getActions();
        $this->assertCount(2, $actions);

        $this->checkAction(array_pop($actions), 'getResultCode', array(), 14, null, $return2);

        $this->checkAction(array_pop($actions), 'getResultCode', array(), 0, null, $return1);
    }

    public function testGetResultMessage()
    {
        $memcached = $this->getMemcachedInterfaceMock(array('SUCCESS', 'NOT STORED'));
        $traceable = new BeSimple_Memcached_Debug_Traceable($memcached);

        $return1 = $traceable->getResultMessage();
        $this->assertCount(1, $traceable->getActions());

        $return2 = $traceable->getResultMessage();
        $actions = $traceable->getActions();
        $this->assertCount(2, $actions);

        $this->checkAction(array_pop($actions), 'getResultMessage', array(), 'NOT STORED', null, $return2);

        $this->checkAction(array_pop($actions), 'getResultMessage', array(), 'SUCCESS', null, $return1);
    }

    public function testGetServerList()
    {
        $memcached = $this->getMemcachedInterfaceMock(array('SUCCESS', 'SOME ERRORS WERE REPORTED'), array('getServerList' => array(
            $servers1 = array(array('host' => 'localhost', 'port' => 11211), array('host' => 'localhost', 'port' => 11212)),
            $servers2 = array(array('host' => 'localhost', 'port' => 11212)),
        )));
        $traceable = new BeSimple_Memcached_Debug_Traceable($memcached);

        $return1 = $traceable->getServerList();
        $this->assertCount(1, $traceable->getActions());

        $return2 = $traceable->getServerList();
        $actions = $traceable->getActions();
        $this->assertCount(2, $actions);

        $this->checkAction(array_pop($actions), 'getServerList', array(), $servers2, 'SOME ERRORS WERE REPORTED', $return2);

        $this->checkAction(array_pop($actions), 'getServerList', array(), $servers1, 'SUCCESS', $return1);
    }

    public function testGetStats()
    {
        $memcached = $this->getMemcachedInterfaceMock(array('SUCCESS', 'SOME ERRORS WERE REPORTED'), array('getStats' => array(
            $stats1 = array('localhost:11211' => array(
                'pid' => 83833,
                'curr_items' => 5033,
                'total_items' => 6302,
                // ...
            )),
            $stats2 = array('localhost:11211' => array(
                'pid' => -1,
                'curr_items' => 0,
                'total_items' => 0,
                // ...
            )),
        )));
        $traceable = new BeSimple_Memcached_Debug_Traceable($memcached);

        $return1 = $traceable->getStats();
        $this->assertCount(1, $traceable->getActions());

        $return2 = $traceable->getStats();
        $actions = $traceable->getActions();
        $this->assertCount(2, $actions);

        $this->checkAction(array_pop($actions), 'getStats', array(), $stats2, 'SOME ERRORS WERE REPORTED', $return2);

        $this->checkAction(array_pop($actions), 'getStats', array(), $stats1, 'SUCCESS', $return1);
    }

    public function testGetVersion()
    {
        $memcached = $this->getMemcachedInterfaceMock(array('SUCCESS', 'SOME ERRORS WERE REPORTED', 'NO SERVERS DEFINED'), array('getVersion' => array(
            $version1 = array('localhost:11211' => '1.2.6'),
            $version2 = array('localhost:11211' => '255.255.255.255'),
            false,
        )));
        $traceable = new BeSimple_Memcached_Debug_Traceable($memcached);

        $return1 = $traceable->getVersion();
        $this->assertCount(1, $traceable->getActions());

        $return2 = $traceable->getVersion();
        $this->assertCount(2, $traceable->getActions());

        $return3 = $traceable->getVersion();
        $actions = $traceable->getActions();
        $this->assertCount(3, $actions);

        $this->checkAction(array_pop($actions), 'getVersion', array(), false, 'NO SERVERS DEFINED', $return3);

        $this->checkAction(array_pop($actions), 'getVersion', array(), $version2, 'SOME ERRORS WERE REPORTED', $return2);

        $this->checkAction(array_pop($actions), 'getVersion', array(), $version1, 'SUCCESS', $return1);
    }

    public function testIncrement()
    {
        $memcached = $this->getMemcachedInterfaceMock(array('SUCCESS', 'NOT FOUND'), array('increment' => array(true, false)));
        $traceable = new BeSimple_Memcached_Debug_Traceable($memcached);

        $return1 = $traceable->increment('foo', 42);
        $this->assertCount(1, $traceable->getActions());

        $return2 = $traceable->increment('bar');
        $actions = $traceable->getActions();
        $this->assertCount(2, $actions);

        $this->checkAction(array_pop($actions), 'increment', array(
            'key' => 'bar',
            'offset' => 1,
        ), false, 'NOT FOUND', $return2);

        $this->checkAction(array_pop($actions), 'increment', array(
            'key' => 'foo',
            'offset' => 42,
        ), true, 'SUCCESS', $return1);
    }

    public function testPrepend()
    {
        $memcached = $this->getMemcachedInterfaceMock(array('SUCCESS', 'NOT STORED'), array('prepend' => array(true, false)));
        $traceable = new BeSimple_Memcached_Debug_Traceable($memcached);

        $return1 = $traceable->prepend('foo', 'bar');
        $this->assertCount(1, $traceable->getActions());

        $return2 = $traceable->prepend('bar', 'foo');
        $actions = $traceable->getActions();
        $this->assertCount(2, $actions);

        $this->checkAction(array_pop($actions), 'prepend', array(
            'key' => 'bar',
            'value' => 'foo',
        ), false, 'NOT STORED', $return2);

        $this->checkAction(array_pop($actions), 'prepend', array(
            'key' => 'foo',
            'value' => 'bar',
        ), true, 'SUCCESS', $return1);
    }

    public function testPrependByKey()
    {
        $memcached = $this->getMemcachedInterfaceMock(array('SUCCESS', 'NOT STORED'), array('prependByKey' => array(true, false)));
        $traceable = new BeSimple_Memcached_Debug_Traceable($memcached);

        $return1 = $traceable->prependByKey('foobar', 'foo', 'bar');
        $this->assertCount(1, $traceable->getActions());

        $return2 = $traceable->prependByKey('barfoo', 'bar', 'foo');
        $actions = $traceable->getActions();
        $this->assertCount(2, $actions);

        $this->checkAction(array_pop($actions), 'prependByKey', array(
            'server_key' => 'barfoo',
            'key' => 'bar',
            'value' => 'foo',
        ), false, 'NOT STORED', $return2);

        $this->checkAction(array_pop($actions), 'prependByKey', array(
            'server_key' => 'foobar',
            'key' => 'foo',
            'value' => 'bar',
        ), true, 'SUCCESS', $return1);
    }

    public function testReplace()
    {
        $memcached = $this->getMemcachedInterfaceMock(array('SUCCESS', 'NOT FOUND'), array('replace' => array(true, false)));
        $traceable = new BeSimple_Memcached_Debug_Traceable($memcached);

        $return1 = $traceable->replace('foo', 'bar', 42);
        $this->assertCount(1, $actions = $traceable->getActions());

        $return2 = $traceable->replace('bar', 'foo');
        $actions = $traceable->getActions();
        $this->assertCount(2, $actions);

        $this->checkAction(array_pop($actions), 'replace', array(
            'key' => 'bar',
            'value' => 'foo',
            'expiration' => null,
        ), false, 'NOT FOUND', $return2);

        $this->checkAction(array_pop($actions), 'replace', array(
            'key' => 'foo',
            'value' => 'bar',
            'expiration' => 42,
        ), true, 'SUCCESS', $return1);
    }

    public function testReplaceByKey()
    {
        $memcached = $this->getMemcachedInterfaceMock(array('SUCCESS', 'NOT FOUND'), array('replaceByKey' => array(true, false)));
        $traceable = new BeSimple_Memcached_Debug_Traceable($memcached);

        $return1 = $traceable->replaceByKey('foobar', 'foo', 'bar', 42);
        $this->assertCount(1, $actions = $traceable->getActions());

        $return2 = $traceable->replaceByKey('barfoo', 'bar', 'foo');
        $actions = $traceable->getActions();
        $this->assertCount(2, $actions);

        $this->checkAction(array_pop($actions), 'replaceByKey', array(
            'server_key' => 'barfoo',
            'key' => 'bar',
            'value' => 'foo',
            'expiration' => null,
        ), false, 'NOT FOUND', $return2);

        $this->checkAction(array_pop($actions), 'replaceByKey', array(
            'server_key' => 'foobar',
            'key' => 'foo',
            'value' => 'bar',
            'expiration' => 42,
        ), true, 'SUCCESS', $return1);
    }

    public function testSet()
    {
        $memcached = $this->getMemcachedInterfaceMock(array('SUCCESS', 'SOME ERRORS WERE REPORTED'), array('set' => array(true, false)));
        $traceable = new BeSimple_Memcached_Debug_Traceable($memcached);

        $return1 = $traceable->set('foo', 'bar', 42);
        $this->assertCount(1, $actions = $traceable->getActions());

        $return2 = $traceable->set('bar', 'foo');
        $actions = $traceable->getActions();
        $this->assertCount(2, $actions);

        $this->checkAction(array_pop($actions), 'set', array(
            'key' => 'bar',
            'value' => 'foo',
            'expiration' => null,
        ), false, 'SOME ERRORS WERE REPORTED', $return2);

        $this->checkAction(array_pop($actions), 'set', array(
            'key' => 'foo',
            'value' => 'bar',
            'expiration' => 42,
        ), true, 'SUCCESS', $return1);
    }

    public function testSetByKey()
    {
        $memcached = $this->getMemcachedInterfaceMock(array('SUCCESS', 'SOME ERRORS WERE REPORTED'), array('setByKey' => array(true, false)));
        $traceable = new BeSimple_Memcached_Debug_Traceable($memcached);

        $return1 = $traceable->setByKey('foobar', 'foo', 'bar', 42);
        $this->assertCount(1, $actions = $traceable->getActions());

        $return2 = $traceable->setByKey('barfoo', 'bar', 'foo');
        $actions = $traceable->getActions();
        $this->assertCount(2, $actions);

        $this->checkAction(array_pop($actions), 'setByKey', array(
            'server_key' => 'barfoo',
            'key' => 'bar',
            'value' => 'foo',
            'expiration' => null,
        ), false, 'SOME ERRORS WERE REPORTED', $return2);

        $this->checkAction(array_pop($actions), 'setByKey', array(
            'server_key' => 'foobar',
            'key' => 'foo',
            'value' => 'bar',
            'expiration' => 42,
        ), true, 'SUCCESS', $return1);
    }

    public function testSetMulti()
    {
        $memcached = $this->getMemcachedInterfaceMock(array('SUCCESS', 'SOME ERRORS WERE REPORTED'), array('setMulti' => array(true, false)));
        $traceable = new BeSimple_Memcached_Debug_Traceable($memcached);

        $return1 = $traceable->setMulti($items1 = array(
            'foo' => 'bar',
            'bar' => 'foo',
        ), 42);
        $this->assertCount(1, $actions = $traceable->getActions());

        $return2 = $traceable->setMulti($items2 = array(
            'foo' => 'foo',
            'bar' => 'bar',
        ));
        $actions = $traceable->getActions();
        $this->assertCount(2, $actions);

        $this->checkAction(array_pop($actions), 'setMulti', array(
            'items' => $items2,
            'expiration' => null,
        ), false, 'SOME ERRORS WERE REPORTED', $return2);

        $this->checkAction(array_pop($actions), 'setMulti', array(
            'items' => $items1,
            'expiration' => 42,
        ), true, 'SUCCESS', $return1);
    }

    public function testSetMultiByKey()
    {
        $memcached = $this->getMemcachedInterfaceMock(array('SUCCESS', 'SOME ERRORS WERE REPORTED'), array('setMultiByKey' => array(true, false)));
        $traceable = new BeSimple_Memcached_Debug_Traceable($memcached);

        $return1 = $traceable->setMultiByKey('foobar', $items1 = array(
            'foo' => 'bar',
            'bar' => 'foo',
        ), 42);
        $this->assertCount(1, $actions = $traceable->getActions());

        $return2 = $traceable->setMultiByKey('barfoo', $items2 = array(
            'foo' => 'foo',
            'bar' => 'bar',
        ));
        $actions = $traceable->getActions();
        $this->assertCount(2, $actions);

        $this->checkAction(array_pop($actions), 'setMultiByKey', array(
            'server_key' => 'barfoo',
            'items' => $items2,
            'expiration' => null,
        ), false, 'SOME ERRORS WERE REPORTED', $return2);

        $this->checkAction(array_pop($actions), 'setMultiByKey', array(
            'server_key' => 'foobar',
            'items' => $items1,
            'expiration' => 42,
        ), true, 'SUCCESS', $return1);
    }

    public function testSetOption()
    {
        $memcached = $this->getMemcachedInterfaceMock(array('SUCCESS', 'SOME ERRORS WERE REPORTED'), array('setOption' => array(true, false)));
        $traceable = new BeSimple_Memcached_Debug_Traceable($memcached);

        $return1 = $traceable->setOption(-1001, false); // Memcached::OPT_COMPRESSION
        $this->assertCount(1, $traceable->getActions());

        $return2 = $traceable->setOption(9, 1); // Memcached::OPT_DISTRIBUTION, Memcached::DISTRIBUTION_CONSISTENT
        $actions = $traceable->getActions();
        $this->assertCount(2, $actions);

        $this->checkAction(array_pop($actions), 'setOption', array(
            'option' => 9,
            'value' => 1,
        ), false, 'SOME ERRORS WERE REPORTED', $return2);

        $this->checkAction(array_pop($actions), 'setOption', array(
            'option' => -1001,
            'value' => false,
        ), true, 'SUCCESS', $return1);
    }

    private function checkAction($action, $methodCalled, array $params, $returnedValue, $resultMessage, $returned)
    {
        $this->assertTrue(0 < $action['time']);
        unset($action['time']);

        $this->assertSame(array(
            'name' => $methodCalled,
            'params' => $params,
            'returned_value' => $returnedValue,
            'result_message' => $resultMessage,
        ), $action);

        $this->assertSame($returned, $action['returned_value']);
    }

    private function getMemcachedInterfaceMock(array $resultMessages = null, array $methods = null)
    {
        $memcached = $this->getMock('BeSimple_Memcached_MemcachedInterface');

        if ($resultMessages) {
            $memcached
                ->expects($this->exactly(count($resultMessages)))
                ->method('getResultMessage')
                ->will(call_user_func_array(array($this, 'onConsecutiveCalls'), $resultMessages))
            ;
        }

        if ($methods) {
            foreach ($methods as $name => $values) {
                $memcached
                    ->expects($this->exactly(count($values)))
                    ->method($name)
                    ->will(call_user_func_array(array($this, 'onConsecutiveCalls'), $values))
                ;
            }
        }

        return $memcached;
    }
}
