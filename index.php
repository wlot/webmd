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

$grep_result = false;
if (!empty($search)) {
    $grep = $git->grep($search);
    if (!empty($grep)) {
      $grep_result = true;
    } else {
      $grep = "Kein Treffer";
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
  <div><a href="/wiki/docs/'.$key.'">'.$md->get_file_title('/var/www/wiki/docs/'.$key).'</a></div>
  <ul>'.$li_data.'</ul>
</div>';
  }
}

$links = array('index', 'menu');
$body_attr = 'class="dotted"';

$body = '
<div id="page">
<h1>White Papers und Beschreibungen</h1>
<img id="logo" src="imgs/logo.png" />
<div id="action">
  <form id="search_form" action="" method="POST" enctype="multipart/form-data">
    <input id="search_text" type="text" name="search_text" />
    <input id="search_submit" type="submit" value="Suchen" />
  </form>
  <form id="test_form" action="/wiki/test.php" >
    <input id="test_submit" type="submit" value="Test" />
  </form>
</div>';

if (!empty($html_grep)) {
  $body .= '<div id="search_result">'.$html_grep.'</div>';
}

$body .= '</div>';

$html_output = $html->get_html($links, $body, $body_attr);

echo $html_output
?>
