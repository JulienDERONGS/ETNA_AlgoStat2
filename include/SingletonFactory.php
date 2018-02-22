<?php

/**
 * Inheritable singleton factory
 */
abstract class              SingletonFactory
{
    private static          $instances = array();

    // Prevent any other singleton object to be created
    protected function      __construct() {}
    protected function      __clone() {}
    public function         __wakeup()
    {
        throw new Exception("Unserialization of the singleton forbidden.");
    }

    // Instanciate only one of each class
    public static function  getInstance()
    {
        $class = get_called_class();
        if (!isset(self::$instances[$class]))
        {
          self::$instances[$class] = new $class();
        }
        return self::$instances[$class];
    }
}
?>
