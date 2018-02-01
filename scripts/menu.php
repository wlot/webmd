<?php

function get_md_file_title($md_file)
{
  $title = "Unbekannt";
  if ($file = fopen($md_file, "r")) {
    $done = false;
    while (!$done && !feof($file)) {
      $line = fgets($file);
      if (preg_match('/^#.*$/', $line)) {
        $title = substr($line, 2);
        $done = true;
      }
    }
    fclose($file);
  }

  return $title;
}

function get_html_menu($path, $subdir, &$menu)
{
  $md_files = array();

  if ($dir_handle = opendir($path."/".$subdir)) {
    while (false !== ($entry = readdir($dir_handle))) {
      if (preg_match('/^.*\.md$/', $entry)) {
        if ($entry !== "index.md") {
          if (!empty($subdir)) {
            array_push($md_files, $subdir."/".$entry);
	  } else {
            array_push($md_files, $entry);
	  }
        }
      } else if (is_dir($path.'/'.$subdir.'/'.$entry)) {
        if (!preg_match('/^\..*/', $entry) && $entry !== "imgs") {
          get_html_menu($path, $entry, $menu);
        }
      }
    }
    closedir($dir_handle);
  }

  asort($md_files);

  $menu .= '<ul class="menu">';
  $menu .= '<h3>'.$subdir.'</h3>';
  $menu .= '<li><a href="/">Home</a></li>';

  foreach ($md_files as &$md_file) {
    $title = get_md_file_title($path.'/'.$md_file);
    $menu .= '<li><a href="/linuxos-docs/'.$md_file.'">'.$title.'</a></li>';
  }

  $menu .= '</ul>';
}

?>
