<?php

class Markdown
{
  public function __construct()
  {
  }

  public function get_output($md_file)
  {
    $content = file_get_contents($md_file);
    $content = $this->toc($content);
    $parser = new ParsedownExtra();
    $output = $parser->text($content);

    return $output;
  }

  public function get_file_title($md_file)
  {
    $title = "Unbekannt";

    if ($file = fopen($md_file, "r")) {
      $done = false;
      while (!$done && !feof($file)) {
        $line = fgets($file);
        if (preg_match('/^# .*$/', $line)) {
          $title = substr($line, 2);
          $done = true;
        }
     }
     fclose($file);
   }

   return $title;
  }

  private function toc($content)
  {
    $ret = "";
    $toc_str = "";
    $toc = array();
    $toc_nums = array();
    $code = false;

    for($i = 1; $i < 7; $i++) {
      $toc_nums[$i] = 0;
    }

    // create toc
    foreach (preg_split('/[\n]{1}/', $content) as &$line) {
      if (preg_match('/^[`]{3}/', $line) || preg_match('/^[~]{3}/', $line)) {
        $code = !$code;
      }

      if (preg_match('/^#/', $line) && !$code) {
        $anchor = $this->get_anchor($line);
        $toc[$anchor]["title"] = trim($line, "# ");
        $toc[$anchor]["num"] = $this->get_toc_num($toc_nums, $line);
        $ret .= preg_replace('/^(#+) /', '\1 '.$toc[$anchor]["num"].' ', $line);
        $ret .= "{#".$anchor."}\n";
      } else {
        $ret .= $line."\n";
      }
    }

    // replace toc tag with toc
    $ret = preg_replace('/\[TOC\]/', $this->get_html_toc($toc), $ret);

    return $ret;
  }

  private function get_anchor($line)
  {
    $anchor = preg_replace('/ /', '_', $line);
    $anchor = preg_replace('/[^A-Za-z0-9_]/', '', $anchor);
    $anchor = trim(strtolower($anchor), "_");

    return $anchor;
  }

  private function get_toc_num(&$toc_nums, $line)
  {
    $hash_count = 0;
    for($i = 0; $i < strlen($line); $i++) {
      if ($line[$i] == '#') {
        $hash_count++;
      } else {
        break;
      }
    }

    $toc_nums[$hash_count]++;

    if ($hash_count < 6) {
      for ($i = $hash_count + 1; $i < 7; $i++) {
        $toc_nums[$i] = 0;
      }
    }

    $ret = "";
    for ($i = 1; $i < 7; $i++) {
      if ($toc_nums[$i] > 0) {
        $ret .= "".$toc_nums[$i].".";
      } else {
        break;
      }
    }

    return trim($ret, ".");
  }

  private function get_html_toc($toc) {
    $ret = "<ul id=\"toc\">";
    $last_level = 0;

    foreach($toc as $anchor => $entry) {
      $level = substr_count($entry["num"], '.');
      if ($level > $last_level) {
        $ret .= '<ul id="toc">';
      } else if ($level < $last_level) {
        $ret .= "</ul>\n";
      }
      $last_level = $level;
      $ret .= "<li><a href=\"#".$anchor."\">".$entry["num"]." ".$entry["title"]."</a></li>\n";
    }

    for ($i = 0; $i <= $last_level; $i++) {
      $ret .= "</ul>\n";
    }

    return $ret;
  }
}
// vim: tabstop=2 shiftwidth=2 expandtab number
?>
