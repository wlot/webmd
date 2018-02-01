<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

function load_class($class) {
  $www_path = '/var/www/wiki';
  $class_file = str_replace("\\", "/", $class);
  include_once $www_path.'/scripts/'.$class_file.'.php';
}

spl_autoload_register('load_class');
?>
