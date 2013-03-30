<?php

class BeSimple_Memcached_Tests_MemcachedTest extends PHPUnit_Framework_TestCase
{
    public function testMemcachedInstance()
    {
        $memcached = new BeSimple_Memcached_Memcached();

        $this->assertInstanceOf('Memcached', $memcached);
        $this->assertInstanceOf('BeSimple_Memcached_MemcachedInterface', $memcached);
    }
}
