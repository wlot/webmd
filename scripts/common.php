<?php

$git_repo = 'linuxos-docs';
$www_path = '/var/www/markdown';
$git_repo_path = $www_path.'/'.$git_repo;
$http_path = '';
$http_git_path = $http_path.'/'.$git_repo;

error_reporting(E_ALL);
ini_set('display_errors', 1);

function load_class($class) {
  global $www_path;
  $class_file = str_replace("\\", "/", $class);
  include_once $www_path.'/scripts/'.$class_file.'.php';
}

spl_autoload_register('load_class');

// vim: tabstop=2 shiftwidth=2 expandtab number

?>
