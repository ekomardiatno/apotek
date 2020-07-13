<?php

/**
 * Flasher 1.0.0 - Created by Eko Mardiatno.
 * Copyright 2018 KOMA MVC. All Right Reserved.
 * Instagram @komafx
 * Licensed under MIT (https://github.com/ekomardiatno/koma-mvc/blob/master/LICENSE)
 */

class Flasher
{

    private static $msg = null,
    $data = null;

    public static function setFlash($msg, $type, $icon = null, $y = 'top', $x = 'center')
    {

        $_SESSION['flash'] = [
            'msg' => $msg,
            'type' => $type,
            'icon' => $icon,
            'x' => $x,
            'y' => $y
        ];

    }

    public static function flash()
    {

        if (isset($_SESSION['flash'])) {
            self::$msg = $_SESSION['flash'];
        }

        unset($_SESSION['flash']);
        return self::$msg;

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
