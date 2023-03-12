<?php

class ResepController extends Controller
{

  public function index($type = null)
  {
    $this->_web->title('Resep');
    $this->_web->breadcrumb([
      [
        'resep', 'Data Resep'
      ]
    ]);
    $this->_web->view('resep', $type);
  }

  public function fetch($type = null)
  {

    if (empty($_SERVER['HTTP_X_REQUESTED_WITH']) || (empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) !== 'xmlhttprequest')) {
      echo 'This is only for AJAX request';
      exit;
    }

    $pdo = Database::getPDOInstance();

    $get = $this->request()->get;

    $draw = $get['draw'];
    $row = $get['start'];
    $rowperpage = $get['length']; // Rows display per page
    $columnIndex = $get['order'][0]['column']; // Column index
    $columnName = $get['columns'][$columnIndex]['data']; // Column name
    $columnSortOrder = $get['order'][0]['dir']; // asc or desc
    $searchValue = $get['search']['value']; // Search value

    $searchArray = array();

    // Search
    $searchQuery = " ";
    if ($searchValue !== '') {
      $searchQuery = " AND (nama LIKE :nama) ";
      $searchArray = array(
        'nama' => "%$searchValue%",
      );
    }

    $dokter = Auth::user('role') !== 'dokter' ? null : $this->model('Dokter')->read(
      ['id_dokter'],
      [
        'params' => [
          [
            'column' => 'username',
            'value' => Auth::user('username')
          ]
        ]
      ],
      'ARRAY_ONE'
    )['data']['id_dokter'];

    // Total number of records without filtering
    $stmt = $pdo->prepare("SELECT COUNT(*) AS allcount FROM resep LEFT JOIN konsul ON konsul.id_konsul=resep.id_konsul" . (Auth::user('role') === 'dokter' ? ' WHERE konsul.id_dokter="' . $dokter . '"' : ($type === 'all' ? '' : ' WHERE resep.status_dicetak="0"')));

    $stmt->execute();
    $records = $stmt->fetch();
    $totalRecords = $records['allcount'];

    // Total number of records with filtering
    $stmt = $pdo->prepare("SELECT COUNT(*) AS allcount FROM resep LEFT JOIN konsul ON konsul.id_konsul=resep.id_konsul WHERE 1" . $searchQuery . (Auth::user('role') === 'dokter' ? ' AND konsul.id_dokter="' . $dokter . '"' : ($type === 'all' ? '' : ' AND resep.status_dicetak="0"')));
    $stmt->execute($searchArray);
    $records = $stmt->fetch();
    $totalRecordwithFilter = $records['allcount'];

    // Fetch records
    $stmt = $pdo->prepare("SELECT resep.id_resep,resep.data_resep,resep.tanggal_diubah,pasien.nama,pasien.jenis_kelamin,pasien.tanggal_lahir,resep.status_dicetak, user.name FROM resep LEFT JOIN konsul ON resep.id_konsul=konsul.id_konsul LEFT JOIN pasien ON pasien.nik=konsul.nik LEFT JOIN dokter ON dokter.id_dokter=konsul.id_dokter LEFT JOIN user ON user.username=dokter.username WHERE 1" . $searchQuery . (Auth::user('role') === 'dokter' ? ' AND konsul.id_dokter="' . $dokter . '"' : ($type === 'all' ? '' : ' AND resep.status_dicetak="0"')) . " ORDER BY " . $columnName . " " . $columnSortOrder . " LIMIT :limit,:offset");

    // Bind values
    foreach ($searchArray as $key => $search) {
      $stmt->bindValue(':' . $key, $search, PDO::PARAM_STR);
    }

    $stmt->bindValue(':limit', (int)$row, PDO::PARAM_INT);
    $stmt->bindValue(':offset', (int)$rowperpage, PDO::PARAM_INT);
    $stmt->execute();
    $empRecords = $stmt->fetchAll();

    $data = array();

    $i = $row + 1;
    foreach ($empRecords as $row) {
      $tanggal_lahir = new DateTime($row['tanggal_lahir']);
      $tanggal_now = new DateTime();
      $diff_tanggal = $tanggal_now->diff($tanggal_lahir);
      $data[] = [
        'no' => $i,
        'tanggal_diubah' => $row['tanggal_diubah'],
        'tanggal' => Mod::timepiece($row['tanggal_diubah']),
        'nama' => $row['nama'],
        'jenis_kelamin' => $row['jenis_kelamin'] === 'l' ? 'Laki-laki' : 'Perempuan',
        'umur' => $diff_tanggal->y,
        'data_resep' => '-',
        'pengaturan' => Auth::user('role') === 'dokter' ? "<button type='button' class='btn btn-outline-warning btn-sm'><span class='fas fa-edit'></span><span class='d-none d-md-inline-block ml-1'>Edit</span></button><button type='button' class='btn btn-outline-danger btn-sm hapus-data' data-action='" . Web::url('resep.hapus') . "' data-key='" . getenv('APP_KEY') . "' data-id='" . md5($row['id_resep']) . "'><span class='fas fa-trash'></span><span class='d-none d-md-inline-block ml-1'>Hapus</span></button>" : "<button type='button' class='btn btn-outline-primary btn-sm'><span class='fas fa-print'></span><span class='d-none d-md-inline-block ml-1'>Cetak</span></button>",
        'nama_dokter' => $row['name']
      ];
      $i++;
    }

    // Response
    $response = array(
      "draw" => intval($draw),
      "iTotalRecords" => $totalRecords,
      "iTotalDisplayRecords" => $totalRecordwithFilter,
      "aaData" => $data
    );

    echo json_encode($response);
  }

  private function __dataObat($plainId = false, $where = null, $fetch = 'ARRAY')
  {
    $obat = $this->model('Obat');
    $data_obat = $obat->read(['nama_obat', 'satuan_obat', ($plainId ? 'id_obat' : 'md5(id_obat) as id_obat')], $where, $fetch);
    return $data_obat['data'];
  }

  private function __plainIdKonsul($id)
  {
    $konsul = $this->model('Konsul');
    $idKonsul = $konsul->read(['id_konsul'], [
      'params' => [
        [
          'column' => 'md5(id_konsul)',
          'value' => $id
        ]
      ]
    ], 'ARRAY_ONE');

    return $idKonsul['data']['id_konsul'];
  }

  public function tambah($id = '')
  {
    $this->_web->title('Resep');
    $this->_web->breadcrumb([
      [
        'resep', 'Data Resep'
      ],
      [
        'resep.tambah', 'Tambah Resep'
      ]
    ]);
    if ($id === '') return $this->redirect('konsul');
    $db = Database::getInstance();
    $pasien = $db->query("SELECT pasien.nama, pasien.jenis_kelamin, pasien.tanggal_lahir, pasien.alamat FROM `konsul` LEFT JOIN pasien ON pasien.nik=konsul.nik WHERE md5(id_konsul)='" . $id . "'", 'ARRAY_ONE');
    $pasien = $pasien['success'] ? $pasien['data'] : null;
    $this->_web->view('resep_form', [
      'id_konsul' => $id,
      'obat' => $this->__dataObat(),
      'pasien' => $pasien
    ]);
  }
  public function pos()
  {
    $post = $this->request()->post;
    if (!isset($post['data'])) return $this->redirect('konsul');
    $data = [];
    $unableToSaved = [];
    $obat = $this->model('Obat');
    $stok_baru = [];
    $stok_lama = [];
    foreach ($post['data']['id_obat'] as $index => $id_obat) {
      $kuantitas = intval($post['data']['kuantitas'][$index]);
      $dosis = $post['data']['dosis'][$index];
      $nama_obat = $post['data']['nama_obat'][$index];
      $stok = $obat->read(['stok_obat'], [
        'params' => [
          [
            'column' => 'md5(id_obat)',
            'value' => $id_obat
          ]
        ]
      ], 'ARRAY_ONE')['data']['stok_obat'];
      $stok = intval($stok);
      if ($stok < $kuantitas) {
        $unableToSaved[] = [
          'id_obat' => $id_obat,
          'nama_obat' => $nama_obat,
          'kuantitas' => $kuantitas,
          'dosis' => $dosis,
        ];
      } else {
        $data[] = [
          'id_obat' => $id_obat,
          'nama_obat' => $nama_obat,
          'kuantitas' => $kuantitas,
          'dosis' => $dosis,
        ];
        $stok_baru[] = [
          'id_obat' => $id_obat,
          'stok_obat' => $stok - $kuantitas
        ];
        $stok_lama[] = [
          'id_obat' => $id_obat,
          'stok_obat' => $stok
        ];
      }
    }

    if (count($unableToSaved) > 0) {
      Flasher::setFlash('Beberapa obat memiliki stok yg tidak cukup.', 'error', 'danger', 'ni ni-fat-remove');
      foreach ($data as $key => $val) {
        if (in_array($val, $unableToSaved)) array_splice($data, $key, 1);
      }
      Flasher::setData([
        'resep' => $data
      ]);
      return $this->redirect('resep.tambah.' . $post['id_konsul']);
    }

    $resep = $this->model('Resep');
    $resepInsert = $resep->insert([
      'id_konsul' => $this->__plainIdKonsul($post['id_konsul']),
      'data_resep' => serialize($data)
    ]);

    if (!$resepInsert['success']) {
      Flasher::setFlash('Tidak dapat menyimpan data.', 'error', 'danger', 'ni ni-fat-remove');
      Flasher::setData([
        'resep' => $data
      ]);
      return $this->redirect('resep.tambah.' . $post['id_konsul']);
    }

    $success = true;
    foreach ($stok_baru as $i => $stok) {
      $stokUpdated = $obat->update(['stok_obat' => $stok['stok_obat']], [
        'params' => [
          [
            'column' => 'md5(id_obat)',
            'value' => $stok['id_obat']
          ]
        ]
      ]);
      if (!$stokUpdated['success']) {
        $success = false;
        break;
      }
    }

    if (!$success) {
      foreach ($stok_lama as $i => $stok) {
        $obat->update(['stok_obat' => $obat['stok_obat']], [
          'params' => [
            [
              'column' => 'md5(id_obat)',
              'value' => $stok['id_obat']
            ]
          ]
        ]);
      }
      $resep->delete([
        'params' => [
          [
            'column' => 'id_resep',
            'value' => $resepInsert['last_inserted_id']
          ]
        ]
      ]);
      Flasher::setFlash('Tidak dapat menyimpan data.', 'error', 'danger', 'ni ni-fat-remove');
      Flasher::setData([
        'resep' => $data
      ]);
      return $this->redirect('resep.tambah.' . $post['id_konsul']);
    }

    $konsul = $this->model('Konsul');
    $konsul->update(['status_selesai' => 1], [
      'params' => [
        [
          'column' => 'md5(id_konsul)',
          'value' => $post['id_konsul']
        ]
      ]
    ]);

    Flasher::setFlash('Resep telah dibuat.', 'success', 'ni ni-check-bold');
    return $this->redirect('konsul');
  }
}
