<?php

/**
 * Pagination 1.0.0 - Created by Eko Mardiatno.
 * Copyright 2018 KOMA MVC. All Right Reserved.
 * Instagram @komafx
 * Licensed under MIT (https://github.com/ekomardiatno/koma-mvc/blob/master/LICENSE)
 */

class Pagination
{

    private static $pagination = false,
    $page,
    $_total,
    $_limit,
    $paginationCount,
    $url;

    public static function setPagination($var)
    {

        self::$pagination = $var;

    }

    public static function setUrl($var)
    {

        self::$url = $var;

    }

    private static function getUrl($var)
    {

        $url = str_replace('{page}', $var, self::$url);

        return getenv('APP_URL') . $url;

    }

    public static function setPage($var)
    {

        self::$page = $var;

    }

    public static function setTotal($var)
    {

        self::$_total = $var;

    }

    public static function setLimit($var)
    {

        self::$_limit = $var;

    }

    public static function setPaginationCount($var)
    {

        self::$paginationCount = $var;

    }

    public static function show()
    {

        if (self::$pagination && self::$_limit < self::$_total) {

            self::$paginationCount = self::$paginationCount - 2;

            $last = ceil(self::$_total / self::$_limit);

            $start = self::$page > 2 ? self::$page : 1;
            $end = self::$page < $last - self::$paginationCount ? self::$paginationCount + (self::$page - 1) : $last;

            if (self::$paginationCount + 2 > $last) {
                $start = 1;
            } else if (self::$page > $last - self::$paginationCount) {
                $start = $last - self::$paginationCount;
            }

            if (self::$paginationCount + 2 >= $last) {
                $end = $last;
            } else if (self::$page < 2) {
                $end = $start + self::$paginationCount;
            }

            $html = '<ul class=\'pagination\'>';

            if (self::$page == 1) {
                $html .= '<li class=\'disabled\'><a href=\'#\'><span class=\'fas fa-long-arrow-alt-left\'></span></a></li>';
            } else {
                $prev = self::$page - 1;
                $html .= '<li><a href=\'' . self::getUrl($prev) . '\'><span class=\'fas fa-long-arrow-alt-left\'></span></a></li>';
            }

            if (self::$page > 2) {
                $html .= '<li><a href=\'' . self::getUrl(1) . '\'>1</a></li>';
                $html .= '<li class=\'disabled\'><span>...</span></li>';
            }

            for ($i = $start; $i <= $end; $i++) {
                $class = self::$page == $i ? 'active' : '';
                $html .= '<li class=\'' . $class . '\'><a href=\'' . self::getUrl($i) . '\'>' . $i . '</a></li>';
            }

            if (self::$page < $last - self::$paginationCount && $last > self::$paginationCount + 2) {
                $html .= '<li class=\'disabled\'><span>...</span></li>';
                $html .= '<li><a href=\'' . self::getUrl($last) . '\'>' . $last . '</a></li>';
            }

            if (self::$page == $last) {
                $html .= '<li class=\'disabled\'><a href=\'#\'><span class=\'fas fa-long-arrow-alt-right\'></span></a></li>';
            } else {
                $next = self::$page + 1;
                $html .= '<li><a href=\'' . self::getUrl($next) . '\'><span class=\'fas fa-long-arrow-alt-right\'></span></a></li>';
            }

            $html .= '</ul>';

            return $html;

        }

    }

}
