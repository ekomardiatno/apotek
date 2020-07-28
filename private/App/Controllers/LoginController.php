<?php

/**
 * LoginController - Created by Eko Mardiatno.
 * Copyright 2018 KOMA MVC. All Right Reserved.
 * Instagram @komafx
 * Licensed under MIT (https://github.com/ekomardiatno/koma-mvc/blob/master/LICENSE)
 */

class LoginController extends Controller
{

  public function __construct()
  {

    parent::__construct();

    if(isset($_SESSION['auth'])) {

      if($_SESSION['auth']['hasLogin']) {

        $this->redirect();

      }

    }

  }

  public function index()
  {

    $this->_web->layout('login');
    $this->_web->view('login');

  }

  public function action()
  {

    $data = $this->request()->post;
    if ( isset($data['username']) ) {

      $attr = [
        'password',
        'name',
        'email',
        'role'
      ];

      $where = [
        'params' => [
          [
            'column' => 'username',
            'value' => $data['username']
          ]
        ],
        'limit' => [0,1]
      ];

      if($this->model('User')->read($attr, $where, 'NUM_ROWS') > 0) {

        $user = $this->model('User')->read($attr, $where, 'ARRAY_ONE');

        extract($user);

        if(password_verify($data['password'], $password)) {

          $_SESSION['auth']['hasLogin'] = true;
          $_SESSION['auth']['username'] = $data['username'];
          $_SESSION['auth']['name'] = $name;
          $_SESSION['auth']['email'] = $email;
          $_SESSION['auth']['role'] = $role;
          $_SESSION['auth']['last_login'] = date('Y-m-d H:i:s');

          $where = [
            'params' => [
              [
                'column' => 'username',
                'value' => $data['username']
              ]
            ]
          ];

          $this->model('User')->update([
            'last_login' => date('Y-m-d H:i:s')
          ], $where);

          Flasher::setFlash('<b>Login berhasil</b>. Selamat datang di Halaman Admin.', 'success', 'ni ni-check-bold');

          $url = Auth::getUrl();
          if($url != null) {
            $this->redirect($url);
          } else {
            $this->redirect();
          }

        } else {

          Flasher::setFlash('Kombinasi username dan password tidak tepat.', 'danger', 'ni ni-fat-remove');

          $this->redirect('login');

        }

      } else {

        Flasher::setFlash('Kombinasi username dan password tidak tepat.', 'danger', 'ni ni-fat-remove');

        $this->redirect('login');

      }

    } else {

      $this->_web->view('error.404');

    }

  }

}