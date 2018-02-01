<?php

require '/var/www/symfony_process/vendor/autoload.php';
use Gitonomy\Git\Repository;

class Git
{
  private $git_repo_path = "/var/www/wiki/docs";

  public function __construct()
  {
    $this->repo = new Repository($this->git_repo_path);
  }

  public function pull()
  {
    $this->repo->run('pull', array('--all'));
  }

  public function grep($search)
  {
    try {
      $result = $this->repo->run('grep', array('-i', $search));
    } catch(Exception $e) {
      $result = "";
    }

    return $result;
  }
}
?>
