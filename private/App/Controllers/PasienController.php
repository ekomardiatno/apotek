<?php

class PasienController extends Controller {
  public function index() {
    $this->role(['konsul', 'farma']);
    $this->_web->title('Pasien');
    $this->_web->breadcrumb([
        [
            'pasien', 'Pasien'
        ]
    ]);
    $pasien = $this->model('Pasien');
    $data = $pasien->read(
      ['nik', 'nama', 'jenis_kelamin', 'alamat', 'norm', 'tanggal_dibuat'],
      [
        'order_by' => ['tanggal_dibuat', 'DESC'],
      ]
    );
    $this->_web->view('pasien', $data);
  }

  public function hapus() {
    $this->role(['konsul']);
    $post = $this->request()->post;
    $pasien = $this->model('Pasien');
    $delete = $pasien->delete(
      [
        'params' => [
          [
            'column' => 'nik',
            'value' => $post['nik']
          ]
        ]
      ]
    );

    if($delete) {
      Flasher::setFlash('Data telah terhapus.', 'success', 'ni ni-check-bold');
    } else {
      Flasher::setFlash('Ada kesalahan yang tidak diketahui, silakan coba lagi.', 'danger', 'ni ni-fat-remove');
    }

    $this->redirect('pasien');
  }

  public function edit($id) {
    $this->role(['konsul']);
    $pasien = $this->model('Pasien');
    $data = $pasien->read(
      ['nik', 'nama', 'alamat', 'jenis_kelamin', 'norm'],
      [
        'params' => [
          [
            'column' => 'nik',
            'value' => $id
          ]
        ]
      ],
      'ARRAY_ONE'
    );
    
    $this->_web->title('Edit Pasien');
    $this->_web->breadcrumb([
      [
        'pasien.edit', 'Edit Pasien'
      ]
    ]);
    $this->_web->view('pasien_edit', $data);
  }

  public function perbarui($id) {
    $this->role(['konsul']);
    $post = $this->request()->post;
    $pasien = $this->model('Pasien');
    $konsul = $this->model('Konsul');
    if($id !== $post['nik']) {
      if(
        !$konsul->update(
          [
            'nik' => $post['nik']
          ],
          [
            'params' => [
              [
                'column' => 'nik',
                'value' => $id
              ]
            ]
          ]
        )
      ) {
        Flasher::setFlash('Ada kesalahan yang tidak diketahui, silakan coba lagi.', 'danger', 'ni ni-fat-remove');
        $this->redirect('pasien.edit.' . $id);
        die;
      }
    }

    if(
      $pasien->update(
        [
          'nik' => $post['nik'],
          'nama' => $post['nama'],
          'alamat' => $post['alamat'],
          'jenis_kelamin' => $post['jenis_kelamin'],
          'norm' => $post['norm']
        ],
        [
          'params' => [
            [
              'column' => 'nik',
              'value' => $id
            ]
          ]
        ]
      )
    ) {
      Flasher::setFlash('Data telah diperbarui.', 'success', 'ni ni-check-bold');
      if($id !== $post['nik']) {
        $this->redirect('pasien.edit.' . $post['nik']);
        die;
      }
    } else {
      Flasher::setFlash('Ada kesalahan yang tidak diketahui, silakan coba lagi.', 'danger', 'ni ni-fat-remove');
    }
    $this->redirect('pasien.edit.' . $id);
  }
}