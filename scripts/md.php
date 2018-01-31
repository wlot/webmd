<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

$http_path = "/wiki";
$http_git_path = "/wiki/docs";
$git_repo_path = getcwd()."/../docs";

require '../../symfony_process/vendor/autoload.php';

// Update git repo if necessary.
spl_autoload_register(function ($class) {
  $class_file = str_replace("\\", "/", $class);
  include getcwd().'/' . $class_file . '.php';
});
use Gitonomy\Git\Repository;

$repo = new Repository($git_repo_path);
$repo->run('pull', array('--all'));

$markdown = file_get_contents($_SERVER['DOCUMENT_ROOT'] . $_SERVER['SCRIPT_NAME']);

require getcwd().'/Parsedown.php';

$parser = new Parsedown();

$output = $parser->text($markdown);

function get_md_file_title($md_file)
{
  $title = "Unbekannt";
  if ($file = fopen($md_file, "r")) {
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

function get_html_menu()
{
  $md_files = array();
  $www_path = '/var/www/wiki/docs';

  if ($dir_handle = opendir($www_path)) {
    while (false !== ($entry = readdir($dir_handle))) {
      if (preg_match('/^.*\.md$/', $entry)) {
        if ($entry != "index.md" && $entry != "template.md") {
          array_push($md_files, $entry);
        }
      }
    }
    closedir($dir_handle);
  }

  asort($md_files);

  $menu = '<ul id="menu">';

  foreach ($md_files as &$md_file) {
    $title = get_md_file_title($www_path."/".$md_file);
    $menu .= '<li><a href="http://homeserver/wiki/docs/'.$md_file.'">'.$title.'</a></li>';
  }

  $menu .= '</ul>';

  return $menu;
}

$html_header = '<head>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="/wiki/css/markdown.css">
</head>';

$html_menu = get_html_menu();

$html = "<html>";
$html .= $html_header;
$html .= "<body class=\"dotted\">".$html_menu."<div id=\"page\">".$output."</div></body>";
$html .= "</html>";

echo $html;

?>
