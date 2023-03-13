<?php

class ResepController extends Controller
{

  private $_db;

  public function __construct()
  {
    parent::__construct();
    $this->_db = new Database;
    $this->role(['konsul', 'dokter']);
  }

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
      $data_resep = '';
      foreach (unserialize($row['data_resep']) as $id => $resep) {
        $data_resep .= '(' . $resep['kuantitas'] . ') ' . $resep['nama_obat'] . ' - ' . $resep['dosis'] . ($id < count(unserialize($row['data_resep'])) - 1 ? ', ' : '');
      }
      $data[] = [
        'no' => $i,
        'tanggal_diubah' => $row['tanggal_diubah'],
        'tanggal' => Mod::timepiece($row['tanggal_diubah']),
        'nama' => $row['nama'],
        'jenis_kelamin' => $row['jenis_kelamin'] === 'l' ? 'Laki-laki' : 'Perempuan',
        'umur' => $diff_tanggal->y . ' Tahun',
        'data_resep' => $data_resep,
        'pengaturan' => Auth::user('role') === 'dokter' ? "<a href='" . Web::url('resep.edit.' . md5($row['id_resep'])) . "' class='btn btn-outline-warning btn-sm'><span class='fas fa-edit'></span><span class='d-none d-md-inline-block ml-1'>Edit</span></a><button type='button' class='btn btn-outline-danger btn-sm hapus-data' data-keyid='id_resep' data-action='" . Web::url('resep.hapus') . "' data-key='" . getenv('APP_KEY') . "' data-id='" . md5($row['id_resep']) . "'><span class='fas fa-trash'></span><span class='d-none d-md-inline-block ml-1'>Hapus</span></button>" : "<button type='button' class='btn btn-outline-primary btn-sm'><span class='fas fa-print'></span><span class='d-none d-md-inline-block ml-1'>Cetak</span></button>",
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

  private function __dataObat()
  {
    $obat = $this->model('Obat');
    $data_obat = $obat->read(['nama_obat', 'satuan_obat', 'md5(id_obat) as id_obat']);
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
    $this->role(['dokter']);
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
    $pasien = $db->query("SELECT pasien.nama, pasien.jenis_kelamin, timestampdiff(year, pasien.tanggal_lahir, curdate()) as umur, pasien.alamat FROM `konsul` LEFT JOIN pasien ON pasien.nik=konsul.nik WHERE md5(konsul.id_konsul)='" . $id . "' AND konsul.status_selesai='0'", 'ARRAY_ONE');
    $pasien = $pasien['success'] ? $pasien['data'] : null;
    if (!$pasien) return $this->redirect('konsul');
    $this->_web->view('resep_form', [
      'id_konsul' => $id,
      'obat' => $this->__dataObat(),
      'pasien' => $pasien
    ]);
  }
  public function pos()
  {
    $this->role(['dokter']);
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
      Flasher::setFlash('Stok ' . $unableToSaved[0]['nama_obat'] . ' tidak cukup.', 'danger', 'ni ni-fat-remove');
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
      Flasher::setFlash('Tidak dapat menyimpan data.', 'danger', 'ni ni-fat-remove');
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
      Flasher::setFlash('Tidak dapat menyimpan data.', 'danger', 'ni ni-fat-remove');
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
    $this->redirect('konsul');
  }

  public function edit($id = null)
  {
    $this->role(['dokter']);
    $this->_web->title('Resep');
    $this->_web->breadcrumb([
      [
        'resep', 'Data Resep'
      ],
      [
        'resep.edit', 'Edit Resep'
      ]
    ]);
    if (!$id) return $this->redirect('resep');
    $pasien = $this->_db->query('SELECT resep.data_resep, pasien.nama, pasien.jenis_kelamin, timestampdiff(year, pasien.tanggal_lahir, curdate()) as umur, pasien.alamat FROM resep LEFT JOIN konsul ON konsul.id_konsul=resep.id_konsul LEFT JOIN pasien ON pasien.nik=konsul.nik WHERE md5(resep.id_resep)="' . $id . '"', 'ARRAY_ONE')['data'];
    $data_resep = $pasien['data_resep'];
    unset($pasien['data_resep']);
    $this->_web->view('resep_form', [
      'id_resep' => $id,
      'pasien' => $pasien,
      'obat' => $this->__dataObat(),
      'resep' => unserialize($data_resep)
    ]);
  }

  private function searchForId($val, $array, $prop)
  {
    foreach ($array as $key => $obj) {
      if ($obj[$prop] === $val) {
        return $key;
      }
    }
    return -1;
  }

  public function update()
  {
    $this->role(['dokter']);
    $post = $this->request()->post;
    $curResep = $this->model('Resep')->read(['data_resep'], [
      'params' => [
        ['column' => 'md5(id_resep)', 'value' => $post['id_resep']]
      ]
    ], 'ARRAY_ONE');
    $curResep = $curResep ? $curResep['data']['data_resep'] : null;
    $nextResep = [];
    foreach ($post['data']['id_obat'] as $index => $id_obat) {
      $kuantitas = intval($post['data']['kuantitas'][$index]);
      $dosis = $post['data']['dosis'][$index];
      $nama_obat = $post['data']['nama_obat'][$index];
      $nextResep[] = [
        'id_obat' => $id_obat,
        'nama_obat' => $nama_obat,
        'kuantitas' => $kuantitas,
        'dosis' => $dosis,
      ];
    }
    if (!$curResep['success']) {
      Flasher::setFlash('Tidak dapat menyimpan data.', 'danger', 'ni ni-fat-remove');
      Flasher::setData([
        'resep' => $nextResep
      ]);
      return $this->redirect('resep.edit.' . $post['id_resep']);
    }
    $curResep = unserialize($curResep);

    foreach ($nextResep as $obj) {
      $indexedCurResep = $this->searchForId($obj['id_obat'], $curResep, 'id_obat');
      $checkAny = $this->_db->query('SELECT stok_obat FROM obat WHERE stok_obat>=' . ($indexedCurResep < 0 ? $obj['kuantitas'] : $obj['kuantitas'] - $curResep[$indexedCurResep]['kuantitas']) . ' AND md5(id_obat)="' . $obj['id_obat'] . '"', 'ARRAY_ONE')['data'];
      if (!$checkAny) {
        Flasher::setFlash('Stok ' . $obj['nama_obat'] . ' tidak cukup.', 'danger', 'ni ni-fat-remove');
        Flasher::setData([
          'resep' => $nextResep
        ]);
        return $this->redirect('resep.edit.' . $post['id_resep']);
      }
    }

    $updateResep = $this->_db->query("UPDATE resep SET data_resep='" . serialize($nextResep) . "' WHERE md5(id_resep)='" . $post['id_resep'] . "'");

    if (!$updateResep['success']) {
      Flasher::setFlash('Tidak dapat menyimpan data.', 'danger', 'ni ni-fat-remove');
      Flasher::setData([
        'resep' => $nextResep
      ]);
      return $this->redirect('resep.edit.' . $post['id_resep']);
    }

    foreach ($nextResep as $obj) {
      $indexedCurResep = $this->searchForId($obj['id_obat'], $curResep, 'id_obat');
      if ($indexedCurResep < 0) {
        $this->_db->query('UPDATE obat SET stok_obat=stok_obat-' . $obj['kuantitas'] . ' WHERE md5(id_obat)="' . $obj['id_obat'] . '"');
      } else {
        $stok_baru = $obj['kuantitas'] - $curResep[$indexedCurResep]['kuantitas'];
        $this->_db->query('UPDATE obat SET stok_obat=stok_obat-' . $stok_baru . ' WHERE md5(id_obat)="' . $obj['id_obat'] . '"');
        array_splice($curResep, $indexedCurResep, 1);
      }
    }

    if (count($curResep) > 0) {
      foreach ($curResep as $obj) {
        $this->_db->query('UPDATE obat SET stok_obat=stok_obat+' . $obj['kuantitas'] . ' WHERE md5(id_obat)="' . $obj['id_obat'] . '"');
      }
    }

    Flasher::setFlash('Resep telah diubah.', 'success', 'ni ni-check-bold');
    $this->redirect('resep.edit.' . $post['id_resep']);
  }

  public function hapus()
  {
    $this->role(['dokter']);
    $post = $this->request()->post;
    $curResep = $this->model('Resep')->read(['id_konsul', 'data_resep'], [
      'params' => [
        ['column' => 'md5(id_resep)', 'value' => $post['id_resep']]
      ]
    ], 'ARRAY_ONE');
    if (!$curResep['success'] || !$curResep['data']) {
      Flasher::setFlash('Tidak dapat menghapus data.', 'danger', 'ni ni-fat-remove');
      return $this->redirect('resep');
    }

    $updateKonsul = $this->_db->query('UPDATE konsul SET status_selesai="0" WHERE id_konsul="' . $curResep['data']['id_konsul'] . '"');
    if (!$updateKonsul['success']) {
      Flasher::setFlash('Tidak dapat menghapus data.', 'danger', 'ni ni-fat-remove');
      return $this->redirect('resep');
    }

    $error = [];
    foreach (unserialize($curResep['data']['data_resep']) as $obj) {
      $updateStok = $this->_db->query('UPDATE obat SET stok_obat=stok_obat+' . $obj['kuantitas'] . ' WHERE md5(id_obat)="' . $obj['id_obat'] . '"');
      if (!$updateStok['success']) {
        $error[] = $obj;
      }
    }

    $deleteResep = $this->_db->query('DELETE FROM resep WHERE md5(id_resep)="' . $post['id_resep'] . '"');
    if (!$deleteResep['success']) {
      foreach (unserialize($curResep['data']['data_resep']) as $obj) {
        if ($this->searchForId($obj['id_obat'], $error, 'id_obat') < 0) {
          $this->_db->query('UPDATE obat SET stok_obat=stok_obat+' . $obj['kuantitas'] . ' WHERE md5(id_obat)="' . $obj['id_obat'] . '"');
        }
      }
      Flasher::setFlash('Tidak dapat menghapus data.', 'danger', 'ni ni-fat-remove');
      return $this->redirect('resep');
    }

    Flasher::setFlash('Data telah terhapus.', 'success', 'ni ni-check-bold');
    $this->redirect('resep');
  }
}
