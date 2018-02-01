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
}

?>
