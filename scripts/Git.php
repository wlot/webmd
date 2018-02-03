<?php

class Git
{
  private $git_repo_path = "/var/www/wiki/docs";

  public function __construct() {}

  public function pull()
  {
	  exec("git -C ".$this->git_repo_path." pull --all", $output, $ret);
    if ($ret) {
      throw new Exception("Git pull faled. Error: ".$ret);
    }
  }

  public function grep($search)
  {
    exec("git -C ".$this->git_repo_path." grep -i \"".$search."\" ".$this->git_repo_path."/*.md", $output, $ret);
    if ($ret) {
      $output = array();
    }

    return $output;
  }
}

// vim: tabstop=2 shiftwidth=2 expandtab number
?>
