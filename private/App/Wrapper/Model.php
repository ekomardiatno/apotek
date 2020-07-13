<?php

/**
 * Model 1.0.0 - Created by Eko Mardiatno.
 * Copyright 2018 KOMA MVC. All Right Reserved.
 * Instagram @komafx
 * Licensed under MIT (https://github.com/ekomardiatno/koma-mvc/blob/master/LICENSE)
 */

class Model
{

    protected $_db;

    public function __construct()
    {

        $this->_db = Database::getInstance();

    }

}
