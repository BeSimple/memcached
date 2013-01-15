<?php

class BeSimple_Memcached_Autoloader
{
    /**
     * Registers BeSimple_Memcached_Autoloader as an SPL autoloader.
     */
    public static function register()
    {
        ini_set('unserialize_callback_func', 'spl_autoload_call');
        spl_autoload_register(array(new self, 'autoload'));
    }

    /**
     * Handles autoloading of classes.
     *
     * @param string $class A class name.
     */
    public function autoload($class)
    {
        if (0 !== strpos($class, 'BeSimple_Memcached_')) {
            return false;
        }

        if (is_file($file = dirname(__FILE__).'/../../'.str_replace(array('_', "\0"), array('/', ''), $class).'.php')) {
            require $file;

            return true;
        }
    }
}
