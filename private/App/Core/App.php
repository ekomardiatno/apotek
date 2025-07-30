
<?php

/**
 * App 0.1.0 - Created by Eko Mardiatno.
 * Copyright 2018 KOMA MVC. All Right Reserved.
 * Instagram @komafx
 * Licensed under MIT (https://github.com/ekomardiatno/koma-mvc/blob/master/LICENSE)
 */

class App extends Aes
{
  public $license_status = 'invalid';
  public static $license_info = [];
  private $controller = 'HomeController';
  private $method = 'index';
  private $params = [];
  private $controller_dir = __DIR__ . '/../Controllers/';
  private $error_404 = __DIR__ . '/../Views/error/404.php';
  private $error_invalid = __DIR__ . '/../Views/error/invalid.php';
  private $error_expired = __DIR__ . '/../Views/error/expired.php';
  private $chiper_method = 'aes-256-cbc';
  public function __construct()
  {
    $iv = $this->iv();
    $chipertext = $this->chipertext();
    $hash = $this->hash();
    $plaintext = openssl_decrypt(base64_decode($chipertext), $this->chiper_method, $hash, 0, $iv);
    $plaintext = hex2bin($plaintext);
    $app = explode('//', $plaintext);
    self::$license_info = $app;
    if (count($app) > 1) {
      $now = date_create(date('Y-m-d'));
      $expired = substr(end($app), 0, 4) . '-' . substr(end($app), 4, 2) . '-' . substr(end($app), 6, 2);
      $expired = date_create(date($expired));
      $diff = date_diff($now, $expired);
      $diff = intval($diff->format("%R%a"));
      if ($diff < 0) {
        return 'expired';
      } else {
        array_pop($app);
        foreach ($app as $i => $a) {
          switch ($i) {
            case 0:
              $i = $_SERVER['HTTP_HOST'];
              break;
            case 1:
              $i = getenv('APP_NAME');
              break;
            case 2:
              $i = getenv('APP_AUTHOR');
              break;
          }
          if ($a !== $i) {
            $this->license_status = 'invalid';
            break;
          }
        }
      }
      $this->license_status = 'valid';
    } else {
      $this->license_status = 'invalid';
    }
  }

  public function route()
  {
    $request_key = '';
    if (isset($_POST['_key'])) {
      $request_key = $_POST['_key'];
      unset($_POST['_key']);
    }

    if ($_POST) {
      $_POST = $this->encrypt(json_encode($_POST), $request_key);
    }

    if ($_FILES) {
      $_FILES = $this->encrypt(json_encode($_FILES), $request_key);
    }

    $get = $this->parseURL();


    if (isset($get['sub_dir']) && $get['sub_dir'] != '') {

      $this->controller_dir = $this->controller_dir . $get['sub_dir'] . '/';
    }

    if (isset($get['url'])) {

      $url = $get['url'];
    }

    if (isset($url[0])) {

      $url[0] = ucfirst($url[0]);
      if (strpos($url[0], '-')) {
        $str_explode = explode('-', $url[0]);
        $url[0] = '';
        foreach ($str_explode as $s) {
          $url[0] .= ucfirst($s) . '_';
        }
        $url[0] = substr($url[0], 0, -1);
      }
      $files = scandir($this->controller_dir);
      $target_controller = strtolower($url[0]) . 'controller.php';
      $found_controller = '';
      foreach ($files as $file) {
        if (strtolower($file) === $target_controller) {
          $found_controller = explode('.php', $file)[0];
          break;
        }
      }
      if ($found_controller !== '' && file_exists($this->controller_dir . $found_controller . '.php')) {
        $this->controller = $found_controller;
        unset($url[0]);
      } else {

        return require_once $this->error_404;
      }
    }

    switch($this->license_status) {
      // case 'invalid':
      //   return require_once $this->error_invalid;
      // case 'expired':
      //   return require_once $this->error_expired;
      default:
        require_once $this->controller_dir . $this->controller . '.php';
    }

    $this->controller = new $this->controller;

    if (isset($url[1])) {

      if (method_exists($this->controller, $url[1])) {

        $this->method = $url[1];
        unset($url[1]);
      }
    }

    if (!empty($url)) {

      $this->params = array_values($url);
    }

    call_user_func_array([$this->controller, $this->method], $this->params);
  }

  private function parseURL()
  {

      $santize_url = $_GET['url'] ?? '';
      $santize_url = preg_replace('#^/apotek/#', '', $santize_url);

      if (isset($santize_url)) {

          $url = explode('/', filter_var(trim($santize_url), FILTER_SANITIZE_URL));
          $sub_dir = '';

      if (is_dir($this->controller_dir . $url[0])) {

        $sub_dir = $url[0];
        unset($url[0]);
      }

      if (!empty($url)) {

        $url = array_values($url);
      }

      return ['sub_dir' => $sub_dir, 'url' => $url];
    }
  }

  private function hash()
  {
    return substr(hash('sha256', getenv('APP_PASSWORD'), true), 0, 32);
  }

  private function iv()
  {
    $ivlen = openssl_cipher_iv_length($this->chiper_method);
    $hex = substr(getenv('APP_KEY'), 0, $ivlen * 2);
    if (ctype_xdigit($hex)) {
      $iv = hex2bin($hex);
    } else {
      $iv = '';
    }
    return $iv;
  }

  private function chipertext()
  {
    $ivlen = openssl_cipher_iv_length($this->chiper_method);
    return substr(getenv('APP_KEY'), $ivlen * 2);
  }
}
