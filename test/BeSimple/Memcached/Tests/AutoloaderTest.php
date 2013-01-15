<?php

class BeSimple_Memcached_Tests_AutoloaderTest extends PHPUnit_Framework_TestCase
{
    public function testAutoload()
    {
        $this->assertFalse(class_exists('FooBarFoo'), '->autoload() does not try to load classes that does not begin with BeSimple_Memcached_');

        $autoloader = new BeSimple_Memcached_Autoloader();
        $this->assertFalse($autoloader->autoload('FooBarFoo'), '->autoload() returns false if it is not able to load a class');
    }
}
