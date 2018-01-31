<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

$http_path = "/wiki";
$http_git_path = "/wiki/docs/";
$git_repo_path = getcwd()."/docs";

require '../symfony_process/vendor/autoload.php';

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

$repo = new Repository($git_repo_path);
if (!empty($search)) {
  try {
    $grep = $repo->run('grep', array($search, '.'));
  } catch(Exception $e) {
    $grep = "Keine Treffer";
  }
}

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

function get_md_file_title($md_file)
{
  $title = "Unbekannt";
  if ($file = fopen('docs/'.$md_file, "r")) {
    while (!feof($file)) {
      $line = fgets($file);
      if (preg_match('/^# .*$/', $line)) {
        $title = substr($line, 2, strpos($line, 'White') - 1);
      }
    }
    fclose($file);
  }

  return $title;
}

$html_grep = ''; 

foreach(array_keys($grep_result) as $key) {
  $li_data = '';
  foreach($grep_result[$key] as $line) {
    $li_data .= '<li>'.$line.'</li>';
  }
  $html_grep .= '<div id="result_block">
  <div><a href="'$http_git_path.$key.'">'.get_md_file_title($key).'</a></div>
  <ul>'.$li_data.'</ul>
</div>';
}

$html='<html>
  <head>
    <link rel="stylesheet" href="'.$http_path.'/css/index.css">
  </head>
  <body class="dotted">
    <div id="page">
      <h1>Mein Wiki</h1>
      <form action="" method="POST" enctype="multipart/form-data">
        <input id="search_text" type="text" name="search_text" />
        <input id="search_submit" type="submit" value="Suchen" />
      </form>
      <div id="search_result">'.$html_grep.'</div>
    </div>
  </body>
</html>';

echo $html
?>
