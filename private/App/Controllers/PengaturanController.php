<?php

class PengaturanController extends Controller
{
  public function index()
  {
    $this->role(['farma']);
    $db = new Database;
    $sql = "SELECT * FROM pengaturan ORDER BY priority_pengaturan ASC";
    $query = $db->query($sql);
    $this->_web->view('pengaturan', $query['data']);
  }
  public function simpan()
  {
    $this->role(['farma']);
    $post = $this->request()->post;
    $db = new Database;
    $failed = 0;
    foreach ($post as $key => $val) {
      $sql = "UPDATE pengaturan SET value_pengaturan='" . $val . "' WHERE key_pengaturan='" . $key . "'";
      $query = $db->query($sql);
      if (!$query['success']) $failed++;
    }
    if ($failed > 0) {
      Flasher::setFlash('Beberapa data tidak dapat diubah.', 'danger', 'ni ni-fat-remove');
    } else {
      Flasher::setFlash('Berhasil mengubah data', 'success', 'ni ni-check-bold');
    }
    $this->redirect('pengaturan');
  }
}
