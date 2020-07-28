<?php

class ProfilController extends Controller
{
  private $_model;
  public function __construct()
  {
    parent::__construct();
    $this->role(['admin']);
    $this->_model = $this->model('Admin');
  }
  public function edit()
  {

    $username = Auth::user('username');
    $data = $this->_model->read(
      ['id_admin', 'username', 'name', 'email'],
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

    $this->_web->title('Ubah Profil');
    $this->_web->breadcrumb([
      ['admin.profil.edit', 'Ubah profil']
    ]);
    $this->_web->layout('dashboard');
    $this->_web->view('admin.profil.edit', $data);
  }

  public function update($id)
  {
    if ($_POST['attr']['password'] === '') {
      unset($_POST['attr']['password']);
    } else {
      $_POST['attr']['password'] = Mod::hash($_POST['attr']['password']);
    }
    extract($_POST);
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

    $this->redirect('admin.profil.edit');
  }
}
