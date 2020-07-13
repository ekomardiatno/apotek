<?php

/**
 * Auth 1.2.0 - Created by Eko Mardiatno.
 * Copyright 2018 KOMA MVC. All Right Reserved.
 * Instagram @komafx
 * Licensed under MIT (https://github.com/ekomardiatno/koma-mvc/blob/master/LICENSE)
 */

class Auth
{

    private static $url = null;

    public static function user($param = null)
    {

        if ($_SESSION) {
            if (isset($_SESSION['auth'])) {
                if($param === null) {
                    return $_SESSION['auth'];
                } else {
                    return $_SESSION['auth'][$param];
                }
            }
        }
    }

    public static function getUrl()
    {

        if (isset($_SESSION['url'])) {
            self::$url = $_SESSION['url'];
        }

        unset($_SESSION['url']);
        return self::$url;
    }

    public static function setUrl($url)
    {

        $_SESSION['url'] = $url;
    }
}
