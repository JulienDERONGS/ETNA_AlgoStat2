<?php

/**
 * Custom autoloader
 * Use :
 * $autoloader = new Autoloader();
 * $obj = new Class1();
 * $obj = new Class2();
*/
class                 Autoloader
{
    public function   __construct()
    {
      spl_autoload_register(array($this, 'loader'));
    }

    private function  loader($classToLoad)
    {
      if ($classToLoad != "Autoloader")
        {
          include $classToLoad . '.php';
        }
    }
}

?>
