<?php

class Html
{
  private $http_path = "/wiki";
  private $http_git_path = "/wiki/docs";
  private $git_repo_path = "/var/www/wiki/docs";

  public function __construct() {}

  public function get_html($links, $body, $body_attr = "")
  {
    $html = '<html><head>
<meta name="viewport" content="width=device-width, initial-scale=1" />
<link rel="stylesheet" href="'.$this->http_path.'/css/common.css">';

    foreach ($links as &$link) {
      $html .= '<link rel="stylesheet" href="'.$this->http_path.'/css/'.$link.'.css">';
    }

    $menu = "";
    $this->get_menu("", $menu);

    $html .= '</head><body '.$body_attr.'>'.$menu.$body.'</body></html>';
    return $html;
  }

  private function get_menu($subdir, &$menu)
  {
    $md = new Markdown();
    $md_files = array();
    $is_meta_file = false;

    if (empty($subdir)) {
      $menu = '<ul class="menu">
<li class="dropdown"><a class="dropbtn" href="'.$this->http_path.'">Home</a></li>';
    }

    if ($dir_handle = opendir($this->git_repo_path.'/'.$subdir)) {
      while (false !== ($entry = readdir($dir_handle))) {
        if (preg_match('/^.*\.md$/', $entry)) {
          if (!empty($subdir)) {
            array_push($md_files, $subdir.'/'.$entry);
          } else {
            array_push($md_files, $entry);
          }
        } else if ($entry == "dir.meta") {
          $is_meta_file = true;
        } else if (is_dir($this->git_repo_path.'/'.$subdir.'/'.$entry)) {
          if (!preg_match('/^\..*/', $entry) && $entry !== "imgs" && $entry !== "data") {
            $this->get_menu($entry, $menu);
          }
        }
      }
      closedir($dir_handle);
    }

    asort($md_files);

    if ($is_meta_file && count($md_files) > 0) {
      $meta = parse_ini_file($this->git_repo_path.'/'.$subdir.'/dir.meta');

      $menu .= '<li class="dropdown"><div class="dropbtn">'.$meta["name"].'</div><div class="submenu">';

      foreach($md_files as &$md_file) {
        $title = $md->get_file_title($this->git_repo_path.'/'.$md_file);
        $menu .= '<a href="'.$this->http_git_path.'/'.$md_file.'">'.$title.'</a>';
      }

      $menu .= '</div></li>';
    }

    if (empty($subdir)) {
      $menu .= '</ul>';
    }
  }

  public function get_grep_html($grep_results) 
  {
    $ret = "";
    $results = array();
    $md = new Markdown();

    if (count($grep_results) > 0) {
      foreach($grep_results as &$grep_result) {
        $pos = strpos($grep_result, ':');

        if ($pos > 0) {
          $file_name = substr($grep_result, 0, $pos);
          $result = substr($grep_result, $pos + 1);
        } else {
          continue;
        }

        if (!isset($results[$file_name]) || !array_key_exists($file_name, $results)) {
          $results[$file_name] = array();
        }

        $array = &$results[$file_name];
        array_push($array, $result);
      }

      foreach(array_keys($results) as $key) {
        $li_data = '';
        foreach($results[$key] as $data) {
          $li_data .= "<li>".$data."</li>\n";
        }
        $ret .= '<div id="result_block">
  <div><a href="'.$this->http_git_path.'/'.$key.'">'.$md->get_file_title($this->git_repo_path.'/'.$key).'</a></div>
  <ul>'.$li_data.'</ul>
</div>';
      }
    } else {
      $ret = "Kein Treffer";
    }

    return $ret;
  }
}
// vim: tabstop=2 shiftwidth=2 expandtab number
?>

