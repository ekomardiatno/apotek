<?php

class KonsulController extends Controller
{


  private $db;
  public function __construct()
  {
    parent::__construct();
    $this->role(['konsul']);
    $this->db = Database::getInstance();
  }

  public function index()
  {
    $this->redirect('konsul.daftar');
  }

  public function daftar($id = '')
  {
    $this->_web->title('Pendaftaran');
    $this->_web->breadcrumb([
      [
        'home', 'Konsultasi'
      ],
      [
        'konsul.daftar', 'Pendaftaran'
      ]
    ]);

    $data_pasien = null;
    if ($id !== '') {
      $pasien_m = $this->model('Pasien');
      $data_pasien = $pasien_m->read(
        ['nik', 'nama', 'jenis_kelamin', 'alamat', 'norm'],
        [
          'params' => [
            [
              'column' => 'md5(nik)',
              'value' => $id
            ]
          ]
        ],
        'ARRAY_ONE'
      );
    }

    $this->_web->view('konsul', $data_pasien);
  }

  public function pos()
  {

    $post = $this->request()->post;
    $pasien = $this->model('Pasien');
    $konsul = $this->model('Konsul');
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

    if (strtotime($post['tanggal']) > strtotime($post['tanggal_kembali'])) {
      Flasher::setData($post);
      return $this->redirect('konsul.daftar');
    }

    $start_date = new DateTime($post['tanggal']);
    $end_date = new DateTime($post['tanggal_kembali']);
    $interval = $start_date->diff($end_date);

    if ($interval->days < 10) {
      Flasher::setData($post);
      return $this->redirect('konsul.daftar');
    }

    if (!$check_pasien) {
      if (
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
        Flasher::setData($post);
        Flasher::setFlash('Ada kesalahan yang tidak diketahui, silakan coba lagi.', 'danger', 'ni ni-fat-remove');
        return $this->redirect('konsul.daftar');
      }
    } else {
      $sql_existed_date = "SELECT * FROM `konsul` WHERE nik = '" . $post['nik'] . "' AND ('" . $post['tanggal'] . "' BETWEEN tanggal AND tanggal_kembali OR '" . $post['tanggal_kembali'] . "' BETWEEN tanggal AND tanggal_kembali) ORDER BY tanggal ASC";
      $data_existed_date = $this->db->query($sql_existed_date);
      $available_date = false;
      if (count($data_existed_date) >= 2) {
        if (strtotime($post['tanggal']) >= strtotime($data_existed_date[0]['tanggal_kembali']) && strtotime($post['tanggal_kembali']) <= strtotime($data_existed_date[1]['tanggal'])) $available_date = true;
      } else if (count($data_existed_date) > 0) {
        if (strtotime($post['tanggal_kembali']) <= strtotime($data_existed_date[0]['tanggal']) || strtotime($post['tanggal']) >= strtotime($data_existed_date[0]['tanggal_kembali'])) $available_date = true;
      } else {
        $available_date = true;
      }

      $msg = "Tidak dapat memproses pendaftaran untuk tanggal yang dipilih";
      if (!$available_date) {
        Flasher::setData($post);
        Flasher::setFlash($msg, 'danger', 'ni ni-fat-remove');
        return $this->redirect('konsul.daftar');
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

  public function hapus()
  {
    $post = $this->request()->post;
    $konsul = $this->model('Konsul');
    $delete = $konsul->delete(
      [
        'params' => [
          [
            'column' => 'md5(id_konsul)',
            'value' => $post['id_konsul']
          ]
        ]
      ]
    );

    if ($delete) {
      Flasher::setFlash('Data telah terhapus.', 'success', 'ni ni-check-bold');
    } else {
      Flasher::setFlash('Ada kesalahan yang tidak diketahui, silakan coba lagi.', 'danger', 'ni ni-fat-remove');
    }

    $this->redirect();
  }

  public function edit($id)
  {
    $db = Database::getInstance();
    $sql = "SELECT id_konsul, a.tanggal, a.nik, b.nama, b.alamat, b.jenis_kelamin, b.norm, a.tanggal_kembali FROM konsul a LEFT JOIN pasien b ON b.nik=a.nik WHERE md5(id_konsul)='" . $id . "'";
    $data = $db->query($sql, 'ARRAY_ONE');

    if (!$data) return printf('ID Konsultasi tidak valid');

    $this->_web->title('Edit Konsul');
    $this->_web->breadcrumb([
      [
        'home', 'Konsultasi'
      ],
      [
        'konsul.edit', 'Edit Konsul'
      ]
    ]);
    $this->_web->view('konsul_edit', $data);
  }

  public function perbarui($id)
  {
    $post = $this->request()->post;
    $pasien = $this->model('Pasien');
    $konsul = $this->model('Konsul');
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

    if (strtotime($post['tanggal']) > strtotime($post['tanggal_kembali'])) return $this->redirect('konsul.edit.' . $id);

    $start_date = new DateTime($post['tanggal']);
    $end_date = new DateTime($post['tanggal_kembali']);
    $interval = $start_date->diff($end_date);

    if ($interval->days < 10) return $this->redirect('konsul.edit.' . $id);

    if (!$check_pasien) {
      if ($post['nama'] !== '' && $post['jenis_kelamin'] !== '' && $post['norm'] !== '') {
        if (
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
      if ($post['nama'] !== '' && $post['jenis_kelamin'] !== '' && $post['norm'] !== '') {
        if (
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
              'column' => 'md5(id_konsul)',
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
