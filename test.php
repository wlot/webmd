<?php

include getcwd().'/scripts/common.php';

$md = new Markdown();
$html = new Html();

function is_md_file()
{
  if (!empty($_FILES['md_file']['name']) && $_FILES['md_file']['type'] == "text/markdown")
    return true;
  else
    return false;
}

$links = array('test', 'menu');
$body = '
<form id="md_file_form" action="" method="POST" enctype="multipart/form-data">
<input id="md_file_input" type="file" name="md_file" accept="text/markdown" />
<input id="md_file_submit" type="submit" value="Testen" />
</form>';

$html_get_file = $html->get_html($links, $body);

if (!is_md_file())
  echo $html_get_file;
else
{
  $output = $md->get_output($_FILES['md_file']['tmp_name']);

  $links = array('markdown', 'test', 'menu');
  $body_attr = 'class="dotted"';
  $body = '
<form id="md_file_form" action="" method="POST" enctype="multipart/form-data">
<input id="md_file_input" type="file" name="md_file" accept="text/markdown" />
<input id="md_file_submit" type="submit" value="Neu Laden" />
</form>
<div id="page">'.$output.'</div>';

  $html_output = $html->get_html($links, $body, $body_attr);

  echo $html_output;
}
// vim: tabstop=2 shiftwidth=2 expandtab number
?>
