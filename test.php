<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

$http_path = "/wiki";

require getcwd().'/scripts/Parsedown.php';

function is_md_file()
{
  if(!empty($_FILES['md_file']['name']) && $_FILES['md_file']['type'] == "text/markdown") {
    return true;
  } else {
    return false;
  }
}

$get_file_html='
<html>
  <head>
    <link rel="stylesheet" href="'.$http_path.'/css/test.css">
  </head>
  <body>
    <form id="md_file_form" action="" method="POST" enctype="multipart/form-data">
      <input id="md_file_input" type="file" name="md_file" accept="text/markdown" />
      <input id="md_file_submit" type="submit" value="Testen" />
    </form>
  </body>
</html>';

if (!is_md_file())
  echo $get_file_html;
else
{
  $markdown = file_get_contents($_FILES['md_file']['tmp_name']);

  $parser = new Parsedown();
  $output = $parser->text($markdown);

  $html_header = '<head>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="'.$http_path.'/css/markdown.css">
  <link rel="stylesheet" href="'.$http_path.'/css/test.css">
</head>';

  $html = "<html>";
  $html .= $html_header;
  $html .= '<body class="dotted">
<form id="md_file_form" action="" method="POST" enctype="multipart/form-data">
<input id="md_file_input" type="file" name="md_file" accept="text/markdown" />
<input id="md_file_submit" type="submit" value="Neu Laden" />
</form>
<div id="page">'.$output.'</div></body>';
  $html .= "</html>";

  echo $html;
}
?>
