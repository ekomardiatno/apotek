<?php

class ObatController extends Controller
{
  public function __construct()
  {
    parent::__construct();
    $this->role(['farma']);
  }
  public function index()
  {
    $this->_web->title('Obat');
    $this->_web->breadcrumb([
      [
        'obat', 'Data Obat'
      ]
    ]);
    $this->_web->view('Obat');
  }

  public function fetch()
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
      $searchQuery = " AND (nama_obat LIKE :nama_obat) ";
      $searchArray = array(
        'nama_obat' => "%$searchValue%",
      );
    }

    // Total number of records without filtering
    $stmt = $pdo->prepare("SELECT COUNT(*) AS allcount FROM obat");
    $stmt->execute();
    $records = $stmt->fetch();
    $totalRecords = $records['allcount'];

    // Total number of records with filtering
    $stmt = $pdo->prepare("SELECT COUNT(*) AS allcount FROM obat WHERE 1" . $searchQuery);
    $stmt->execute($searchArray);
    $records = $stmt->fetch();
    $totalRecordwithFilter = $records['allcount'];

    // Fetch records
    $stmt = $pdo->prepare("SELECT id_obat, nama_obat, satuan_obat, stok_obat, deskripsi_obat, tanggal_dibuat, tanggal_diubah FROM obat WHERE 1" . $searchQuery . " ORDER BY " . $columnName . " " . $columnSortOrder . " LIMIT :limit,:offset");

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
      $row['id_obat'] = md5($row['id_obat']);
      $data[] = array(
        "no" => $i,
        "nama_obat" => $row['nama_obat'],
        "satuan_obat" => $row['satuan_obat'],
        "stok_obat" => "<button type='button' class='btn btn-secondary btn-sm stock-btn mr-0' data-type='remove' data-id='" . $row['id_obat'] . "' data-name='" . $row['nama_obat'] . "' data-qty='" . $row['stok_obat'] . "'><span class='fas fa-minus'></span></button>"
          . "<span class='mx-2 font-weight-bold h4 mt-1' style='vertical-align:middle'>" . $row['stok_obat'] . "</span>"
          . "<button type='button' class='btn btn-secondary btn-sm stock-btn' data-type='add' data-id='" . $row['id_obat'] . "' data-name='" . $row['nama_obat'] . "' data-qty='" . $row['stok_obat'] . "'><span class='fas fa-plus'></span></button>",
        "deskripsi_obat" => $row['deskripsi_obat'] !== '' ? $row['deskripsi_obat'] : '-',
        "pengaturan" => "<a href='" . Web::url('obat.edit.' . $row['id_obat']) . "' class='btn btn-outline-warning btn-sm'><span class='fas fa-edit'></span><span class='ml-1 d-none d-md-inline-block'>Edit</span></a>"
          . "<button type='button' class='btn btn-outline-danger btn-sm hapus-data' data-keyid='id_obat' data-action='" . Web::url('obat.hapus') . "' data-key='" . getenv('APP_KEY') . "' data-id='" . $row['id_obat'] . "'><span class='fas fa-trash'></span><span class='ml-1 d-none d-md-inline-block'>Hapus</span></button>"
      );
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

  public function tambah()
  {
    $this->_web->title('Obat');
    $this->_web->breadcrumb([
      [
        'obat', 'Data Obat'
      ],
      [
        'obat.tambah', 'Tambah Data'
      ]
    ]);
    $this->_web->view('obat_form');
  }

  public function edit($id)
  {
    $this->_web->title('Obat');
    $this->_web->breadcrumb([
      [
        'obat', 'Data Obat'
      ],
      [
        'obat.edit', 'Edit Data'
      ]
    ]);

    $obat = $this->model('Obat');
    $data = $obat->read(
      ['nama_obat', 'satuan_obat', 'deskripsi_obat'],
      [
        'params' => [
          [
            'column' => 'md5(id_obat)',
            'value' => $id
          ]
        ]
      ],
      'ARRAY_ONE'
    )['data'];

    if (!$data) return printf('Data tidak ditemukan');

    $data['id_obat'] = $id;

    $this->_web->view('obat_form', $data);
  }

  public function pos()
  {
    $this->role(['farma']);
    $post = $this->request()->post;
    $obat = $this->model('Obat');

    if (!$obat->insert($post)['success']) {
      Flasher::setData($post);
      Flasher::setFlash('Ada kesalahan yang tidak diketahui, silakan coba lagi.', 'danger', 'ni ni-fat-remove');
      return $this->redirect('obat.tambah');
    }

    Flasher::setFlash('Tambah data obat berhasil', 'success', 'ni ni-check-bold');
    $this->redirect('obat');
  }

  public function ubah()
  {
    $this->role(['farma']);
    $post = $this->request()->post;
    $obat = $this->model('Obat');
    $id = $post['id_obat'];
    array_splice($post, 0, 1);

    if (!$obat->update($post, [
      'params' => [
        [
          'column' => 'md5(id_obat)',
          'value' => $id
        ]
      ]
    ])['success']) {
      Flasher::setFlash('Ada kesalahan yang tidak diketahui, silakan coba lagi.', 'danger', 'ni ni-fat-remove');
      Flasher::setData($post);
    } else {
      Flasher::setFlash('Data berhasil diperbarui.', 'success', 'ni ni-check-bold');
    }
    $this->redirect('obat.edit.' . $id);
  }

  public function hapus()
  {
    $this->role(['farma']);
    $post = $this->request()->post;
    echo json_encode($post);
    die;
    $obat = $this->model('Obat');
    $delete = $obat->delete(
      [
        'params' => [
          [
            'column' => 'md5(id_obat)',
            'value' => $post['id_obat']
          ]
        ]
      ]
    );

    if ($delete['success']) {
      Flasher::setFlash('Data telah terhapus.', 'success', 'ni ni-check-bold');
    } else {
      Flasher::setFlash('Ada kesalahan yang tidak diketahui, silakan coba lagi.', 'danger', 'ni ni-fat-remove');
    }

    $this->redirect('obat');
  }

  public function stok()
  {
    $post = $this->request()->post;
    $post['stok_obat'] = intval($post['stok_obat']);
    $post['kuantitas'] = intval($post['kuantitas']);
    $db = new Database;
    $obat = $db->query('SELECT id_obat FROM obat WHERE md5(id_obat)="' . $post['id_obat'] . '"', 'ARRAY_ONE')['data'];
    if (!$obat) {
      Flasher::setFlash('Data obat tidak ditemukan', 'danger', 'ni ni-fat-remove');
      return $this->redirect('obat');
    }
    $id_obat = $obat['id_obat'];
    $stok_baru = $post['type'] === 'add' ? $post['stok_obat'] + $post['kuantitas'] : $post['stok_obat'] - $post['kuantitas'];
    $stokKategori = $post['type'] === 'add' ? $this->model('StokMasukKategori') : $this->model('StokKeluarKategori');
    if ($post['id_kategori'] === "" || !isset($post['id_kategori'])) {
      $nameKeyName = $post['type'] === 'add' ? 'nama_stok_masuk_kategori' : 'nama_stok_keluar_kategori';
      $stokKategoriInsert = $stokKategori->insert([
        $nameKeyName => $post['nama_kategori']
      ]);
      if (!$stokKategoriInsert['success']) {
        Flasher::setFlash('Gagal menyimpan data.', 'danger', 'ni ni-fat-remove');
        return $this->redirect('obat');
      }
      $post['id_kategori'] = $stokKategoriInsert['last_inserted_id'];
    }

    $stokHistori = $post['type'] === 'add' ? $this->model('StokMasuk') : $this->model('StokKeluar');
    $idKeyName = $post['type'] === 'add' ? 'id_stok_masuk_kategori' : 'id_stok_keluar_kategori';
    $qtyKeyName = $post['type'] === 'add' ? 'kuantitas_stok_masuk' : 'kuantitas_stok_keluar';
    $stokHistoriInsert = $stokHistori->insert([
      $idKeyName => $post['id_kategori'],
      $qtyKeyName => $post['kuantitas'],
      'id_obat' => $id_obat
    ]);

    if (!$stokHistoriInsert['success']) {
      Flasher::setFlash('Gagal menyimpan data.', 'danger', 'ni ni-fat-remove');
      return $this->redirect('obat');
    }

    $obat = $this->model('Obat');
    $obatUpdate = $obat->update(
      [
        'stok_obat' => $stok_baru
      ],
      [
        'params' => [
          [
            'column' => 'md5(id_obat)',
            'value' => $post['id_obat']
          ]
        ]
      ]
    );

    if (!$obatUpdate['success']) {
      $stokHistori->delete(
        [
          'params' => [
            [
              'column' => $idKeyName,
              'value' => $stokHistoriInsert['last_inserted_id']
            ]
          ]
        ]
      );
      Flasher::setFlash('Gagal menyimpan data.', 'danger', 'ni ni-fat-remove');
      return $this->redirect('obat');
    }

    Flasher::setFlash('Stok telah diperbarui', 'success', 'ni ni-check-bold');
    return $this->redirect('obat');
  }
}
