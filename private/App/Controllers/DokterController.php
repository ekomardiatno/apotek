<?php

class DokterController extends Controller
{
  public function index()
  {
    $this->_web->title('Dokter');
    $this->_web->breadcrumb([
      [
        'dokter', 'Data Dokter'
      ]
    ]);
    $this->_web->view('dokter');
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
      $searchQuery = " AND (name LIKE :name) ";
      $searchArray = array(
        'name' => "%$searchValue%",
      );
    }

    // Total number of records without filtering
    $stmt = $pdo->prepare("SELECT COUNT(*) AS allcount FROM dokter");
    $stmt->execute();
    $records = $stmt->fetch();
    $totalRecords = $records['allcount'];

    // Total number of records with filtering
    $stmt = $pdo->prepare("SELECT COUNT(*) AS allcount FROM dokter WHERE 1" . $searchQuery);
    $stmt->execute($searchArray);
    $records = $stmt->fetch();
    $totalRecordwithFilter = $records['allcount'];

    // Fetch records
    $stmt = $pdo->prepare("SELECT dokter.id_dokter, dokter.sip_dokter, dokter.kategori_dokter, user.name, user.username, user.email FROM dokter LEFT JOIN user ON user.username=dokter.username WHERE 1" . $searchQuery . " ORDER BY " . $columnName . " " . $columnSortOrder . " LIMIT :limit,:offset");

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
      $data[] = array(
        "no" => $i,
        "name" => $row['name'],
        "username" => $row['username'],
        "email" => $row['email'],
        "kategori_dokter" => $row['kategori_dokter'],
        "sip_dokter" => $row['sip_dokter'],
        "pengaturan" => "<a href='" . Web::url('obat.edit.' . md5($row['id_dokter'])) . "' class='btn btn-outline-warning btn-sm'><span class='fas fa-edit'></span> Edit</a>"
          . "<button type='button' class='btn btn-outline-danger btn-sm hapus-data' data-action='" . Web::url('obat.hapus') . "' data-key='" . getenv('APP_KEY') . "' data-keyid='id_dokter' data-id='" . md5($row['id_dokter']) . "'><span class='fas fa-trash'></span> Hapus</button>"
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
    $this->_web->title('Dokter');
    $this->_web->breadcrumb([
      [
        'dokter', 'Data Dokter'
      ],
      [
        'dokter.tambah', 'Tambah Dokter'
      ]
    ]);
    $this->_web->view('dokter_form');
  }

  public function pos()
  {
    $post = $this->request()->post;
    $post_user = [
      'name' => $post['name'],
      'username' => $post['username'],
      'email' => $post['email'],
      'role' => 'dokter',
      'password' => Mod::hash($post['username'])
    ];
    $post_dokter = [
      'sip_dokter' => $post['sip_dokter'],
      'username' => $post['username'],
      'kategori_dokter' => $post['kategori_dokter']
    ];
    $user = $this->model('User');
    $dokter = $this->model('Dokter');

    if (!$user->insert($post_user)['success']) {
      Flasher::setData($post);
      Flasher::setFlash('Ada kesalahan yang tidak diketahui, silakan coba lagi.', 'danger', 'ni ni-fat-remove');
      return $this->redirect('dokter.tambah');
    }

    if (!$dokter->insert($post_dokter)['success']) {
      Flasher::setData($post);
      Flasher::setFlash('Ada kesalahan yang tidak diketahui, silakan coba lagi.', 'danger', 'ni ni-fat-remove');
      $user->delete(
        [
          'params' => [
            [
              'column' => 'username',
              'value' => $post['username']
            ]
          ]
        ]
      );
      return $this->redirect('dokter.tambah');
    }

    Flasher::setFlash('Berhasil menambahkan data dokter', 'success', 'ni ni-check-bold');
    return $this->redirect('dokter');
  }
}
