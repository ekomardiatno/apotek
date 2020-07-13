<?php

/**
 * Web 1.1.0 - Created by Eko Mardiatno.
 * Copyright 2018 KOMA MVC. All Right Reserved.
 * Instagram @komafx
 * Licensed under MIT (https://github.com/ekomardiatno/koma-mvc/blob/master/LICENSE)
 */

class Web
{
    private static $_instance = null;
    private $title = '';
    private $desc = '';
    private $breadcrumb = null;
    private $layout = 'default';
    private $dir_view = __DIR__.'/../Views/';
    private $dir_layout = __DIR__.'/../Views/layout/';

    public static function assets($name, $type)
    {

        return getenv('APP_URL') . 'assets/' . $type . '/' . $name;
    }

    public static function url($url = '')
    {

        $url = str_replace('.', '/', $url);
        return getenv('APP_URL') . $url;
    }
    public static function getInstance()
    {

        if (!isset(self::$_instance)) {

            self::$_instance = new Web;
        }

        return self::$_instance;
    }

    public function layout($file)
    {
        $this->layout = $file;
    }

    public function title($title)
    {
        $this->title = $title;
    }

    private function getTitle()
    {
        return $this->title;
    }

    public function desc($desc)
    {
        $this->desc = $desc;
    }

    private function getDesc()
    {
        if ($this->desc == '') {
            $this->desc = getenv('APP_DESC');
        }

        return $this->desc;
    }

    public function breadcrumb($breadcrumb = null)
    {
        $this->breadcrumb = $breadcrumb;
    }

    private function getBreadcrumb()
    {
        return $this->breadcrumb;
    }

    public function view($file, $data = [])
    {

        $title = $this->getTitle();
        $desc = $this->getDesc();
        $content = $this->dir_view . str_replace('.', '/', $file) . '.php';
        $breadcrumb = $this->getBreadcrumb();
        require_once($this->dir_layout . $this->layout . '.php');
    }

    public static function key_field()
    {
        return '<input type="hidden" name="_key" value="'. getenv('APP_KEY') .'">';
    }
}
