<?php

/**
 * Flasher 1.0.0 - Created by Eko Mardiatno.
 * Copyright 2018 KOMA MVC. All Right Reserved.
 * Instagram @komafx
 * Licensed under MIT (https://github.com/ekomardiatno/koma-mvc/blob/master/LICENSE)
 */

class Flasher
{

    private static $data = null;

    public static function setFlash($msg, $type, $icon = null, $y = 'top', $x = 'center')
    {

        $_SESSION['flash'] = [
            'msg' => $msg,
            'type' => $type,
            'icon' => $icon,
            'x' => $x,
            'y' => $y
        ];

        return isset($_SESSION['flash']);
    }

    public static function flash()
    {
        $msg = null;

        if (isset($_SESSION['flash'])) {
            $msg = $_SESSION['flash'];
            unset($_SESSION['flash']);
        }

        return $msg;
    }

    public static function setData($data)
    {

        $_SESSION['data_flasher'] = $data;
    }

    public static function data()
    {

        if (isset($_SESSION['data_flasher'])) {
            self::$data = $_SESSION['data_flasher'];
        }

        unset($_SESSION['data_flasher']);
        return self::$data;
    }
}
