<?php

include_once getcwd().'/common.php';

$md = new Markdown();
$html = new Html();

require '../../symfony_process/vendor/autoload.php';

$git_repo = 'docs';
$git_repo_path = '/var/www/wiki/'.$git_repo;

use Gitonomy\Git\Repository;

$repo = new Repository($git_repo_path);
$repo->run('pull', array('--all'));

$output = $md->get_output($_SERVER['DOCUMENT_ROOT'].$_SERVER['SCRIPT_NAME']);

$links = array('markdown', 'menu');
$body_attr = 'class="dotted"';
$body = '<div id="page">'.$output.'</div></body>';

$html_output = $html->get_html($links, $body, $body_attr);

echo $html_output;
?>
