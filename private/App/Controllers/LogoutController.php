<?php

class LogoutController extends Controller
{

  public function index()
  {

    if(isset($_SESSION['auth'])) {

      unset($_SESSION['auth']);

      Flasher::setFlash('Anda sudah keluar.', 'success', 'ni ni-check-bold');

    }

    $this->redirect('admin.login');

  }

}