<?php

class ProfilController extends Controller
{
  private $_model;
  public function __construct()
  {
    parent::__construct();
    $this->role(['konsul', 'farma']);
    $this->_model = $this->model('User');
  }
  public function index()
  {

    $username = Auth::user('username');
    $data = $this->_model->read(
      ['id_user', 'username', 'name', 'email'],
      [
        'cond' => [
          [
            'column' => 'username',
            'value' => $username
          ]
        ]
      ],
      'ARRAY_ONE'
    );

    $this->_web->title(Auth::user('name'));
    $this->_web->breadcrumb([
      ['profil', Auth::user('name')]
    ]);
    $this->_web->view('profil', $data);
  }

  public function update($id)
  {
    $post = $this->request()->post;
    if ($post['attr']['password'] === '') {
      unset($post['attr']['password']);
    } else {
      $post['attr']['password'] = Mod::hash($post['attr']['password']);
    }
    extract($post);
    $user = $this->_model->read(
      ['password'],
      [
        'cond' => [
          [
            'column' => 'id_users',
            'value' => $id
          ]
        ]
      ],
      'ARRAY_ONE'
    );

    if (password_verify($password, $user['password'])) {
      $update = $this->_model->update($attr, ['data_id' => $id]);
      if ($update) {
        $_SESSION['auth']['username'] = $attr['username'];
        $_SESSION['auth']['name'] = $attr['name'];
        $_SESSION['auth']['email'] = $attr['email'];
        Flasher::setFlash('<b>Berhasil!</b> profil diperbarui', 'success', 'ni ni-check-bold', 'top', 'center');
      } else {
        Flasher::setFlash('<b>Gagal!</b> Ada kesalahan', 'danger', 'ni ni-fat-remove', 'top', 'center');
      }
    } else {
      Flasher::setFlash('<b>Gagal!</b> Ada kesalahan', 'danger', 'ni ni-fat-remove', 'top', 'center');
    }

    $this->redirect('profil.edit');
  }
}
