<?php

include_once getcwd().'/common.php';

$md = new Markdown();
$html = new Html();
$git = new Git();

$git->pull();

$output = $md->get_output($_SERVER['DOCUMENT_ROOT'].$_SERVER['SCRIPT_NAME']);

$links = array('markdown', 'menu');
$body_attr = 'class="dotted"';
$body = '<div id="page">'.$output.'</div></body>';

$html_output = $html->get_html($links, $body, $body_attr);

echo $html_output;
?>
