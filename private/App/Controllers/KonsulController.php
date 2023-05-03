<?php

class KonsulController extends Controller
{


  private $db;
  private $monthShort;
  public function __construct()
  {
    parent::__construct();
    $this->role();
    $this->db = Database::getInstance();
    $this->monthShort = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
  }

  private function __dokter()
  {
    $dokter = $this->model('Dokter');
    $data_dokter = $dokter->join(
      'LEFT JOIN',
      [
        'dokter' => ['id_dokter'],
        'user' => ['name']
      ],
      [
        'user' => ['index_table' => 'dokter', 'index_id' => 'username']
      ]
    );

    return $data_dokter['data'];
  }

  public function index()
  {
    $this->redirect('');
  }

  public function daftar($id = '')
  {
    $this->role(['konsul']);
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
        ['nik', 'nama', 'jenis_kelamin', 'tanggal_lahir', 'alamat', 'norm', 'no_hp'],
        [
          'params' => [
            [
              'column' => 'md5(nik)',
              'value' => $id
            ]
          ]
        ],
        'ARRAY_ONE'
      )['data'];
    }

    $data_dokter = $this->__dokter();

    $this->_web->view('konsul', ['pasien' => $data_pasien, 'dokter' => $data_dokter]);
  }

  public function pos()
  {
    $this->role(['konsul']);
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
    )['data'];

    $post['tanggal'] = substr($post['tanggal'], 7, 4) . '-' . sprintf("%02d", (array_search(substr($post['tanggal'], 3, 3), $this->monthShort) + 1)) . '-' . substr($post['tanggal'], 0, 2);
    $post['tanggal_kembali'] = substr($post['tanggal_kembali'], 7, 4) . '-' . sprintf("%02d", (array_search(substr($post['tanggal_kembali'], 3, 3), $this->monthShort) + 1)) . '-' . substr($post['tanggal_kembali'], 0, 2);

    $start_date = new DateTime($post['tanggal']);
    $end_date = new DateTime($post['tanggal_kembali']);
    $interval = $start_date->diff($end_date);

    if (strtotime($post['tanggal']) > strtotime($post['tanggal_kembali']) || $interval->days < 10) {
      Flasher::setData($post);
      Flasher::setFlash('Selisih tanggal kurang dari 10 hari.', 'danger', 'ni ni-fat-remove');
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
            'tanggal_lahir' => $post['tanggal_lahir'],
            'norm' => $post['norm'],
            'no_hp' => $post['no_hp']
          ]
        )['success']
      ) {
        Flasher::setData($post);
        Flasher::setFlash('Ada kesalahan yang tidak diketahui, silakan coba lagi.', 'danger', 'ni ni-fat-remove');
        return $this->redirect('konsul.daftar');
      }
    } else {
      $sql_existed_date = "SELECT * FROM `konsul` WHERE nik = '" . $post['nik'] . "' AND ('" . $post['tanggal'] . "' BETWEEN tanggal AND tanggal_kembali OR '" . $post['tanggal_kembali'] . "' BETWEEN tanggal AND tanggal_kembali) ORDER BY tanggal ASC";
      $data_existed_date = $this->db->query($sql_existed_date)['data'];
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
          'tanggal_kembali' => $post['tanggal_kembali'],
          'id_dokter' => $post['id_dokter']
        ]
      )['success']
    ) {
      Flasher::setFlash('Pendaftaran konsultasi berhasil.', 'success', 'ni ni-check-bold');
    } else {
      Flasher::setFlash('Ada kesalahan yang tidak diketahui, silakan coba lagi.', 'danger', 'ni ni-fat-remove');
    }

    $this->redirect();
  }

  public function hapus()
  {
    $this->role(['konsul']);
    $post = $this->request()->post;
    // echo json_encode($post);
    // die;
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

    if ($delete['success']) {
      Flasher::setFlash('Data telah terhapus.', 'success', 'ni ni-check-bold');
    } else {
      Flasher::setFlash('Ada kesalahan yang tidak diketahui, silakan coba lagi.', 'danger', 'ni ni-fat-remove');
    }

    $this->redirect();
  }

  public function edit($id)
  {
    $this->role(['konsul']);
    $db = Database::getInstance();
    $sql = "SELECT id_konsul, a.id_dokter, a.tanggal, a.nik, b.nama, b.alamat, b.tanggal_lahir, b.jenis_kelamin, b.norm, b.no_hp, a.tanggal_kembali FROM konsul a LEFT JOIN pasien b ON b.nik=a.nik WHERE md5(id_konsul)='" . $id . "'";
    $data = $db->query($sql, 'ARRAY_ONE')['data'];

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

    $data_dokter = $this->__dokter();

    $this->_web->view('konsul_edit', ['konsultasi' => $data, 'dokter' => $data_dokter]);
  }

  public function perbarui($id)
  {
    $this->role(['konsul']);
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
    )['data'];

    $post['tanggal'] = substr($post['tanggal'], 7, 4) . '-' . sprintf("%02d", (array_search(substr($post['tanggal'], 3, 3), $this->monthShort) + 1)) . '-' . substr($post['tanggal'], 0, 2);
    $post['tanggal_kembali'] = substr($post['tanggal_kembali'], 7, 4) . '-' . sprintf("%02d", (array_search(substr($post['tanggal_kembali'], 3, 3), $this->monthShort) + 1)) . '-' . substr($post['tanggal_kembali'], 0, 2);

    $start_date = new DateTime($post['tanggal']);
    $end_date = new DateTime($post['tanggal_kembali']);
    $interval = $start_date->diff($end_date);

    if (strtotime($post['tanggal']) > strtotime($post['tanggal_kembali']) || $interval->days < 10) {
      Flasher::setData($post);
      Flasher::setFlash('Selisih tanggal kurang dari 10 hari.', 'danger', 'ni ni-fat-remove');
      return $this->redirect('konsul.edit.' . $id);
    }

    if (!$check_pasien) {
      if ($post['nama'] !== '' && $post['jenis_kelamin'] !== '' && $post['norm'] !== '') {
        if (
          !$pasien->insert(
            [
              'nik' => $post['nik'],
              'nama' => $post['nama'],
              'jenis_kelamin' => $post['jenis_kelamin'],
              'tanggal_lahir' => $post['tanggal_lahir'],
              'alamat' => $post['alamat'],
              'norm' => $post['norm'],
              'no_hp' => $post['no_hp']
            ]
          )['success']
        ) {
          Flasher::setData($post);
          Flasher::setFlash('Ada kesalahan yang tidak diketahui, silakan coba lagi.', 'danger', 'ni ni-fat-remove');
          return $this->redirect('konsul.edit.' . $id);
        }
      }
    } else {
      if ($post['nama'] !== '' && $post['jenis_kelamin'] !== '' && $post['norm'] !== '') {
        if (
          !$pasien->update(
            [
              'nama' => $post['nama'],
              'jenis_kelamin' => $post['jenis_kelamin'],
              'tanggal_lahir' => $post['tanggal_lahir'],
              'alamat' => $post['alamat'],
              'norm' => $post['norm'],
              'no_hp' => $post['no_hp'],
            ],
            [
              'params' => [
                [
                  'column' => 'nik',
                  'value' => $post['nik']
                ]
              ]
            ]
          )['success']
        ) {
          Flasher::setData($post);
          Flasher::setFlash('Ada kesalahan yang tidak diketahui, silakan coba lagi.', 'danger', 'ni ni-fat-remove');
          return $this->redirect('konsul.edit.' . $id);
        }
      }
    }

    if (
      $konsul->update(
        [
          'tanggal' => $post['tanggal'],
          'nik' => $post['nik'],
          'tanggal_kembali' => $post['tanggal_kembali'],
          'id_dokter' => $post['id_dokter']
        ],
        [
          'params' => [
            [
              'column' => 'md5(id_konsul)',
              'value' => $id
            ]
          ]
        ]
      )['success']
    ) {
      Flasher::setFlash('Perbarui data konsultasi berhasil.', 'success', 'ni ni-check-bold');
    } else {
      Flasher::setFlash('Ada kesalahan yang tidak diketahui, silakan coba lagi.', 'danger', 'ni ni-fat-remove');
    }

    $this->redirect('konsul.edit.' . $id);
  }
}
