<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require '../symfony_process/vendor/autoload.php';
require 'scripts/menu.php';

$git_repo = "linuxos-docs";
$git_repo_path = "/var/www/markdown/".$git_repo;

// Update git repo if necessary.
spl_autoload_register(function ($class) {
  $class_file = str_replace("\\", "/", $class);
  include getcwd().'/scripts/' . $class_file . '.php';
});
use Gitonomy\Git\Repository;

$grep = "";

if (isset($_POST['search_text']) && array_key_exists('search_text', $_POST)) {
  $search = $_POST['search_text'];
} else {
  $search = "";
}

$repo = new Repository(getcwd().'/'.$git_repo);
$repo->run('pull', array('--all'));

$grep_result = false;
if (!empty($search)) {
  try {
    $grep = $repo->run('grep', array('-i',$search, '.'));
    $grep_result = true;
  } catch(Exception $e) {
    $grep = "Keine Treffer";
  }
}

$html_grep = $grep; 

if ($grep_result) {
  $html_grep = '';
  $grep_result = array();

  $line = strtok($grep, PHP_EOL);
  while($line !== FALSE) {
    $pos = strpos($line, ':');
    if ($pos > 0) {
      $file_name = substr($line, 0, $pos);
      $result = substr($line, $pos + 1);
    } else {
      continue;
    }
  
    if (!isset($grep_result[$file_name]) || !array_key_exists($file_name, $grep_result)) {
      $grep_result[$file_name] = array();
    }

    $array = &$grep_result[$file_name]; 
    array_push($array, $result);

    $line = strtok(PHP_EOL);
  }

  strtok('','');

  foreach(array_keys($grep_result) as $key) {
    $li_data = '';
    foreach($grep_result[$key] as $line) {
      $li_data .= '<li>'.$line.'</li>';
    }
    $html_grep .= '<div id="result_block">
  <div><a href="/'.$git_repo.'/'.$key.'">'.get_md_file_title($git_repo_path.'/'.$key).'</a></div>
  <ul>'.$li_data.'</ul>
</div>';
  }
}
$html_menu = '';
get_html_menu($git_repo_path, "", $html_menu);

$html='<html>
  <head>
    <link rel="stylesheet" href="/css/index.css">
    <link rel="stylesheet" href="/css/menu.css">
  </head>
  <body class="dotted">'.$html_menu.'
    <div id="page">
      <h1>WAGO LinuxOs White Papers und Beschreibungen</h1>
      <img id="logo" src="imgs/logo.png" />
      <div id="action">
        <form id="search_form" action="" method="POST" enctype="multipart/form-data">
          <input id="search_text" type="text" name="search_text" />
          <input id="search_submit" type="submit" value="Suchen" />
        </form>
	<form id="test_form" action="/test.php" >
          <input id="test_submit" type="submit" value="Test" />
        </form>
      </div>';
if(!empty($html_grep)) {
  $html .= '<div id="search_result">'.$html_grep.'</div>';
}
$html .= '</div>
  </body>
</html>';

echo $html
?>
