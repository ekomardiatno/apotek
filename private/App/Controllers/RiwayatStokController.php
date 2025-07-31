<?php

class RiwayatStokController extends Controller
{
  private $maxRemovable = 30;
  public function index($id = null, $type = '')
  {
    if (!$id || $type === '') return $this->redirect('');
    if ($type !== 'masuk' && $type !== 'keluar') return $this->redirect('');
    $db = new Database;
    $query = $db->query('SELECT nama_obat, satuan_obat FROM obat WHERE obat.is_deleted IS FALSE AND md5(id_obat)="' . $id . '"', 'ARRAY_ONE');
    if (!$query['data']) return $this->redirect('');
    $this->_web->title('Riwayat Stok');
    $this->_web->breadcrumb([
      [
        'obat', 'Data Obat'
      ],
      [
        'riwayatstok', 'Data Riwayat Stok'
      ]
    ]);
    $this->_web->view('riwayat_stok', [
      'id' => $id,
      'type' => $type,
      'data_obat' => $query['data']
    ]);
  }

  public function fetch($id, $type = 'masuk')
  {
    if (empty($_SERVER['HTTP_X_REQUESTED_WITH']) || (empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) !== 'xmlhttprequest')) {
      echo 'This is only for AJAX request';
      exit;
    }

    $pdo = Database::getPDOInstance();

    $get = $this->request()->post;

    $draw = $get['draw'];
    $row = $get['start'];
    $rowperpage = $get['length']; // Rows display per page
    $columnIndex = $get['order'][0]['column']; // Column index
    $columnName = $get['columns'][$columnIndex]['data']; // Column name
    $columnSortOrder = $get['order'][0]['dir']; // asc or desc
    $searchValue = $get['search']['value']; // Search value

    $searchArray = array();

    $categoryNameKeyName = $type === 'masuk' ? 'nama_stok_masuk_kategori' : 'nama_stok_keluar_kategori';

    // Search
    $searchQuery = " ";
    if ($searchValue !== '') {
      $searchQuery = " AND (" . $categoryNameKeyName . " LIKE :" . $categoryNameKeyName . ") ";
      $searchArray = array(
        $categoryNameKeyName => "%$searchValue%",
      );
    }

    $tableName = $type === 'masuk' ? 'stok_masuk' : 'stok_keluar';

    // Total number of records without filtering
    $stmt = $pdo->prepare("SELECT COUNT(*) AS allcount FROM " . $tableName . " WHERE is_deleted IS FALSE AND md5(id_obat)='" . $id . "'");
    $stmt->execute();
    $records = $stmt->fetch();
    $totalRecords = $records['allcount'];

    // Total number of records with filtering
    $stmt = $pdo->prepare("SELECT COUNT(*) AS allcount FROM " . $tableName . " WHERE is_deleted IS FALSE AND md5(id_obat)='" . $id . "'" . $searchQuery);
    $stmt->execute($searchArray);
    $records = $stmt->fetch();
    $totalRecordwithFilter = $records['allcount'];

    $categoryIdKeyName = $type === 'masuk' ? 'id_stok_masuk_kategori' : 'id_stok_keluar_kategori';
    $idKeyName = $type === 'masuk' ? 'id_stok_masuk' : 'id_stok_keluar';
    $qtyKeyName = $type === 'masuk' ? 'kuantitas_stok_masuk' : 'kuantitas_stok_keluar';
    $categoryTableName = $type === 'masuk' ? 'stok_masuk_kategori' : 'stok_keluar_kategori';
    // Fetch records
    $stmt = $pdo->prepare("SELECT a." . $idKeyName . ",b." . $categoryNameKeyName . ",a." . $qtyKeyName . ",a.tanggal_dibuat,a.tanggal_diubah" . " FROM " . $tableName . " a LEFT JOIN " . $categoryTableName . " b ON a." . $categoryIdKeyName . "=b." . $categoryIdKeyName . " WHERE a.is_deleted IS FALSE" . " AND md5(id_obat)='" . $id . "'" . $searchQuery . " ORDER BY " . $columnName . " " . $columnSortOrder . " LIMIT :limit,:offset");

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
      $row[$idKeyName] = md5($row[$idKeyName]);
      $tanggal_dibuat = new DateTime($row['tanggal_dibuat']);
      $tanggal_now = new DateTime();
      $tanggal_diff = $tanggal_now->diff($tanggal_dibuat);
      $menit = $tanggal_diff->h * 60 + $tanggal_diff->i + $tanggal_diff->s / 60;
      $ableSettings = ($menit > $this->maxRemovable) ? false : true;
      $data[] = array(
        "no" => $i,
        $categoryNameKeyName => $row[$categoryNameKeyName],
        $qtyKeyName => Mod::numeral($row[$qtyKeyName]),
        "tanggal_dibuat" => Mod::timepiece($row['tanggal_dibuat']),
        "pengaturan" => $ableSettings ? "<button type='button' class='btn btn-outline-danger btn-sm hapus-data' data-keyid='" . $idKeyName . "' data-action='" . Web::url('riwayatstok.hapus') . "' data-key='" . getenv('APP_KEY') . "' data-id='" . $row[$idKeyName] . "'><span class='fas fa-trash'></span><span class='ml-1 d-none d-md-inline-block'>Hapus</span></button>" : '-'
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

  public function hapus()
  {
    $post = $this->request()->post;
    $type = isset($post['id_stok_masuk']) ? 'masuk' : 'keluar';
    $id = $type === 'masuk' ? $post['id_stok_masuk'] : $post['id_stok_keluar'];
    if (!isset($post['id_stok_masuk']) && !isset($post['id_stok_masuk'])) return $this->redirect('');
    $db = new Database;
    $riwayat = $db->query('SELECT md5(id_obat) AS id_obat,' . ($type === 'masuk' ? 'kuantitas_stok_masuk' : 'kuantitas_stok_keluar') . ' AS kuantitas, tanggal_dibuat FROM ' . ($type === 'masuk' ? 'stok_masuk' : 'stok_keluar') . ' WHERE is_deleted IS FALSE AND ' . ($type === 'masuk' ? 'md5(id_stok_masuk)' : 'md5(id_stok_keluar)') . '="' . $id . '"', 'ARRAY_ONE')['data'];
    $tanggal_dibuat = new DateTime($riwayat['tanggal_dibuat']);
    $tanggal_now = new DateTime();
    $tanggal_diff = $tanggal_now->diff($tanggal_dibuat);
    $menit = $tanggal_diff->h * 60 + $tanggal_diff->i + $tanggal_diff->s / 60;
    if ($menit > $this->maxRemovable) {
      Flasher::setFlash('Data yang dibuat ' . $this->maxRemovable . ' menit lalu tidak dapat dihapus', 'danger', 'ni ni-fat-remove');
      return $this->redirect('riwayatstok.' . $riwayat['id_obat'] . '.' . $type);
    }
    $query = $db->query('UPDATE ' . ($type === 'masuk' ? 'stok_masuk' : 'stok_keluar') . ' SET is_deleted=true WHERE ' . ($type === 'masuk' ? 'md5(id_stok_masuk)' : 'md5(id_stok_keluar)') . '="' . $id . '"');
    $obatController = new ObatController;
    $obatController->updatestok($riwayat['id_obat'], $riwayat['kuantitas'] * -1, true);
    $db->query('UPDATE obat SET stok_obat=stok_obat-' . $riwayat['kuantitas'] . ' WHERE md5(id_obat)="' . $riwayat['id_obat'] . '"');
    if (!$query['success']) {
      Flasher::setFlash('Gagal menghapus data, silakan coba lagi.', 'danger', 'ni ni-fat-remove');
      return $this->redirect('riwayatstok.' . $riwayat['id_obat'] . '.' . $type);
    }
    Flasher::setFlash('Hapus data riwayat stok berhasil', 'success', 'ni ni-check-bold');
    $this->redirect('riwayatstok.' . $riwayat['id_obat'] . '.' . $type);
  }
}
