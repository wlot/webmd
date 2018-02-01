<?php

class Markdown
{
  public function __construct()
  {
  }

  public function get_output($md_file)
  {
    $content = file_get_contents($md_file);
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
}

?>
