<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require '../../symfony_process/vendor/autoload.php';
require 'menu.php';

$git_repo = 'linuxos-docs';
$git_repo_path = '/var/www/markdown/'.$git_repo;

spl_autoload_register(function ($class) {
  $class_file = str_replace("\\", "/", $class);
  include getcwd().'/'.$class_file.'.php';
});

use Gitonomy\Git\Repository;

$repo = new Repository($git_repo_path);

$repo->run('pull', array('--all'));

$markdown = file_get_contents($_SERVER['DOCUMENT_ROOT'] . $_SERVER['SCRIPT_NAME']);

require getcwd().'/parsedown/Parsedown.php';
require getcwd().'/parsedown/ParsedownExtra.php';

$parser = new ParsedownExtra();

$output = $parser->text($markdown);

$html_header = '<head>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="/css/markdown.css">
  <link rel="stylesheet" href="/css/menu.css">
</head>';

$html_menu = '';
get_html_menu($git_repo_path, "", $html_menu);

$html = "<html>";
$html .= $html_header;
$html .= "<body class=\"dotted\">".$html_menu."<div id=\"page\">".$output."</div></body>";
$html .= "</html>";

echo $html;

?>
