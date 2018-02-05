<?php

include getcwd().'/scripts/common.php';

$html = new Html();
$md = new Markdown();
$git = new Git();

$grep = "";

if (isset($_POST['search_text']) && array_key_exists('search_text', $_POST)) {
  $search = $_POST['search_text'];
} else {
  $search = "";
}

$git->pull();
$html_grep  = "";

if (!empty($search)) {
  $grep_results = $git->grep($search);
  $html_grep = $html->get_grep_html($grep_results);
}

$links = array('index', 'menu');
$body_attr = 'class="dotted"';

$body = '
<div id="page">
<h1>White Papers und Beschreibungen</h1>
<img id="logo" src="'.$http_path.'/imgs/logo.png" />
<div id="action">
  <form id="search_form" action="" method="POST" enctype="multipart/form-data">
    <input id="search_text" type="text" name="search_text" />
    <input id="search_submit" type="submit" value="Suchen" />
  </form>
  <form id="test_form" action="'.$http_path.'/test.php" >
    <input id="test_submit" type="submit" value="Test" />
  </form>
</div>';

if (!empty($html_grep)) {
  $body .= '<div id="search_result">'.$html_grep.'</div>';
}

$body .= '</div>';

$html_output = $html->get_html($links, $body, $body_attr);

echo $html_output

// vim: tabstop=2 shiftwidth=2 expandtab number
?>
