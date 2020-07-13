<?php

class Request extends Aes
{
  public $post = [];
  public $get = [];
  public $files = [];
  public function __construct()
  {
    if($_GET) {
      $this->get = $_GET;
    }

    if ($_POST) {
      $this->post = json_decode($this->decrypt($_POST, getenv('APP_KEY')), true);
    }

    if ($_FILES) {
      $this->files = json_decode($this->decrypt($_FILES, getenv('APP_KEY')), true);
    }

  }
}
