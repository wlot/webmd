<?php

class Markdown()
{
  public function __construct()
  {
  }

  public function is_md_file()
  {
    if (!empty($_FILES['md_file']['name']) && $_FILES['md_file']['type'] == "text/markdown") {
      return true;
    } else {
      return false;
    }
  }
}

?>
