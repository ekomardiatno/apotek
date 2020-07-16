<?php

class KonsulController extends Controller
{

  public function daftar()
  {
      $this->_web->title('Pendaftaran');
      $this->_web->breadcrumb([
          [
              'konsul.daftar', 'Pendaftaran'
          ]
      ]);
      $this->_web->view('konsul');
  }

  public function pos()
  {

      $post = $this->request()->post;
      $pasien = $this->model('pasien');
      $konsul = $this->model('konsul');
      $check_pasien = $pasien->read(
        null,
        [
          'params' => [
            [
              'column' => 'nik',
              'value' => $post['nik']
            ]
          ]
        ],
        'NUM_ROWS'
      );

      $post['tanggal'] = substr($post['tanggal'], 6, 4) . '-' . substr($post['tanggal'], 3, 2) . '-' . substr($post['tanggal'], 0, 2);
      $post['tanggal_kembali'] = substr($post['tanggal_kembali'], 6, 4) . '-' . substr($post['tanggal_kembali'], 3, 2) . '-' . substr($post['tanggal_kembali'], 0, 2);

      if(!$check_pasien) {
          if(
            !$pasien->insert(
              [
                'nik' => $post['nik'],
                'nama' => $post['nama'],
                'jenis_kelamin' => $post['jenis_kelamin'],
                'alamat' => $post['alamat'],
                'norm' => $post['norm'],
                'tanggal_dibuat' => date('Y-m-d')
              ]
            )
          ) {
            Flasher::setFlash('Ada kesalahan yang tidak diketahui, silakan coba lagi.', 'danger', 'ni ni-fat-remove');
            $this->redirect();
            die;
          }
      }

      if (
        $konsul->insert(
          [
            'tanggal' => $post['tanggal'],
            'nik' => $post['nik'],
            'tanggal_kembali' => $post['tanggal_kembali']
          ]
        )
      ) {
        Flasher::setFlash('Pendaftaran konsultasi berhasil.', 'success', 'ni ni-check-bold');
      } else {
        Flasher::setFlash('Ada kesalahan yang tidak diketahui, silakan coba lagi.', 'danger', 'ni ni-fat-remove');
      }

      $this->redirect();
  }

  public function hapus() {
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

  public function edit($id) {
    $db = Database::getInstance();
    $sql = "SELECT a.id_konsul, a.tanggal, a.nik, b.nama, b.alamat, b.jenis_kelamin, b.norm, a.tanggal_kembali FROM konsul a LEFT JOIN pasien b ON b.nik=a.nik WHERE id_konsul='" . $id . "'";
    $data = $db->query($sql, 'ARRAY_ONE');
    
    $this->_web->title('Edit Konsul');
    $this->_web->breadcrumb([
      [
        'konsul.edit', 'Edit Konsul'
      ]
    ]);
    $this->_web->view('konsul_edit', $data);
  }

  public function perbarui($id) {

    $post = $this->request()->post;
    $pasien = $this->model('pasien');
    $konsul = $this->model('konsul');
    $check_pasien = $pasien->read(
      null,
      [
        'params' => [
          [
            'column' => 'nik',
            'value' => $post['nik']
          ]
        ]
      ],
      'NUM_ROWS'
    );

    $post['tanggal'] = substr($post['tanggal'], 6, 4) . '-' . substr($post['tanggal'], 3, 2) . '-' . substr($post['tanggal'], 0, 2);
    $post['tanggal_kembali'] = substr($post['tanggal_kembali'], 6, 4) . '-' . substr($post['tanggal_kembali'], 3, 2) . '-' . substr($post['tanggal_kembali'], 0, 2);

    if(!$check_pasien) {
      if($post['nama'] !== '' && $post['jenis_kelamin'] !== '' && $post['norm'] !== '') {
        if(
          !$pasien->insert(
            [
              'nik' => $post['nik'],
              'nama' => $post['nama'],
              'jenis_kelamin' => $post['jenis_kelamin'],
              'alamat' => $post['alamat'],
              'norm' => $post['norm'],
              'tanggal_dibuat' => date('Y-m-d')
            ]
          )
        ) {
          Flasher::setFlash('Ada kesalahan yang tidak diketahui, silakan coba lagi.', 'danger', 'ni ni-fat-remove');
          $this->redirect('konsul.edit.' . $id);
          die;
        }
      }
    } else {
      if($post['nama'] !== '' && $post['jenis_kelamin'] !== '' && $post['norm'] !== '') {
        if(
          !$pasien->update(
            [
              'nama' => $post['nama'],
              'jenis_kelamin' => $post['jenis_kelamin'],
              'alamat' => $post['alamat'],
              'norm' => $post['norm'],
            ],
            [
              'params' => [
                [
                  'column' => 'nik',
                  'value' => $post['nik']
                ]
              ]
            ]
          )
        ) {
          Flasher::setFlash('Ada kesalahan yang tidak diketahui, silakan coba lagi.', 'danger', 'ni ni-fat-remove');
          $this->redirect('konsul.edit.' . $id);
          die;
        }
      }
    }

    if (
      $konsul->update(
        [
          'tanggal' => $post['tanggal'],
          'nik' => $post['nik'],
          'tanggal_kembali' => $post['tanggal_kembali']
        ],
        [
          'params' => [
            [
              'column' => 'id_konsul',
              'value' => $id
            ]
          ]
        ]
      )
    ) {
      Flasher::setFlash('Perbarui data konsultasi berhasil.', 'success', 'ni ni-check-bold');
    } else {
      Flasher::setFlash('Ada kesalahan yang tidak diketahui, silakan coba lagi.', 'danger', 'ni ni-fat-remove');
    }

    $this->redirect('konsul.edit.' . $id);
  }

}