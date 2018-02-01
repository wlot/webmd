<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$www_path = '/var/www/wiki';

spl_autoload_register(function ($class) {
  $class_file = str_replace("\\", "/", $class);
  include_once $www_path.'/scripts/'.$class_file.'.php';
});

?>
