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
<meta name="viewport" content="width=device-width, initial-scale=1" />';

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
      $menu = '<ul class="menu">';
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
}
// vim: tabstop=2 shiftwidth=2 expandtab number
?>

