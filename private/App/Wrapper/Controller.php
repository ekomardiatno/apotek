<?php

/**
 * Controller 1.1.3 - Created by Eko Mardiatno.
 * Copyright 2018 KOMA MVC. All Right Reserved.
 * Instagram @komafx
 * Licensed under MIT (https://github.com/ekomardiatno/koma-mvc/blob/master/LICENSE)
 */

class Controller
{

    private $model_dir = __DIR__ . '/../Models/';
    private $has_login = false;
    protected $_web;

    public function __construct()
    {
        $this->_web = Web::getInstance();
    }

    public function model($file)
    {
        require_once $this->model_dir . $file . '.php';
        return new $file;
    }

    public function redirect($url = '')
    {
        $url = $url !== '' ? str_replace('.', '/', $url) : $url;
        header('location: ' . getenv('APP_URL') . $url);
    }

    public function role($role = [])
    {
        // $url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https://" : "http://") . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $url = str_replace(getenv('APP_URL'), '', '');
        $url = str_replace('/', '.', $url);

        if ($_SESSION) {

            if (isset($_SESSION['auth'])) {

                $this->has_login = $_SESSION['auth']['hasLogin'];
            }
        }

        if ($this->has_login) {
            if (count($role) > 0 && !in_array($_SESSION['auth']['role'], $role)) {
                $this->redirect('');
            }
        } else {
            Auth::setUrl($url);
            $this->redirect('login');
        }
    }

    public function request()
    {
        return new Request;
    }
}
