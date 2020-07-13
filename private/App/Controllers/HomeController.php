<?php

class HomeController extends Controller
{

    public function index()
    {
        $this->_web->view('home');
    }

}
