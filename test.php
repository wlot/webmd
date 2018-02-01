<?php

include getcwd().'/scripts/common.php';

$md = new Markdown();
$html = new Html();

$links = array('test');
$body = '
<form id="md_file_form" action="" method="POST" enctype="multipart/form-data">
<input id="md_file_input" type="file" name="md_file" accept="text/markdown" />
<input id="md_file_submit" type="submit" value="Testen" />
</form>';

$html_get_file = $html->get_html($links, $body);

if (!$md->is_md_file())
  echo $html_get_file;
else
{
  $markdown = file_get_contents($_FILES['md_file']['tmp_name']);

  $parser = new ParsedownExtra();
  $output = $parser->text($markdown);

  $output = $md->get_output($_FILES['md_files']['tmp_name']);

  $links = array('markdown', 'test');
  $body_attr = 'class="dotted"';
  $body = '
<form id="md_file_form" action="" method="POST" enctype="multipart/form-data">
<input id="md_file_input" type="file" name="md_file" accept="text/markdown" />
<input id="md_file_submit" type="submit" value="Neu Laden" />
</form>
<div id="page">'.$output.'</div>';

  $html_output = $html->get_html($links, $body, $body_attr);

  echo $html;
}
?>
