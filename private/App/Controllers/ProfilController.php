<?php

class ProfilController extends Controller
{
  private $_model;
  public function __construct()
  {
    parent::__construct();
    $this->role();
    $this->_model = $this->model('User');
  }
  public function index()
  {

    $username = Auth::user('username');
    $data = $this->_model->read(
      ['md5(id_user) as id_user', 'username', 'name', 'email'],
      [
        'params' => [
          [
            'column' => 'username',
            'value' => $username
          ]
        ]
      ],
      'ARRAY_ONE'
    )['data'];

    $this->_web->title(Auth::user('name'));
    $this->_web->breadcrumb([
      ['profil', Auth::user('name')]
    ]);
    $this->_web->view('profil', $data);
  }

  public function update()
  {
    $post = $this->request()->post;
    $id = $post['id_user'];
    if ($post['attr']['password'] === '') {
      unset($post['attr']['password']);
    } else {
      $post['attr']['password'] = Mod::hash($post['attr']['password']);
    }
    extract($post);
    $user = $this->_model->read(
      ['password'],
      [
        'params' => [
          [
            'column' => 'md5(id_user)',
            'value' => $id
          ]
        ]
      ],
      'ARRAY_ONE'
    )['data'];

    if (password_verify($password, $user['password'])) {
      $update = $this->_model->update($attr, [
        'params' => [
          [
            'column' => 'md5(id_user)',
            'value' => $id
          ]
        ]
      ]);
      if ($update['success']) {
        $_SESSION['auth']['username'] = $attr['username'];
        $_SESSION['auth']['name'] = $attr['name'];
        $_SESSION['auth']['email'] = $attr['email'];
        Flasher::setFlash('Profil berhasil diperbarui', 'success', 'ni ni-check-bold', 'top', 'center');
      } else {
        Flasher::setFlash('Username atau Email telah digunakan akun lain', 'danger', 'ni ni-fat-remove', 'top', 'center');
      }
    } else {
      Flasher::setFlash('Password tidak tepat', 'danger', 'ni ni-fat-remove', 'top', 'center');
    }

    $this->redirect('profil.edit');
  }
}
