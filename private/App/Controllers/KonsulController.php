<?php

class KonsulController extends Controller
{

  public function delete() {
    $post = $this->request()->post;
    $konsul = $this->model('Konsul');
    $delete = $konsul->delete(
      [
        'params' => [
          [
            'column' => 'id_konsul',
            'value' => $post['id_konsul']
          ]
        ]
      ]
    );

    if($delete) {
      Flasher::setFlash('Data telah terhapus.', 'success', 'ni ni-check-bold');
    } else {
      Flasher::setFlash('Ada kesalahan yang tidak diketahui, silakan coba lagi.', 'danger', 'ni ni-fat-remove');
    }

    $this->redirect();
  }

}