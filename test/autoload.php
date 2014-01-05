<?php

spl_autoload_register(function ($class) {
   require_once  dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR .
      str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';
});
