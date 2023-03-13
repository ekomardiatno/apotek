<?php

class PasienController extends Controller
{
  public function __construct()
  {
    parent::__construct();
  }
  
  public function index()
  {
    $this->role(['konsul', 'farma']);
    $this->_web->title('Pasien');
    $this->_web->breadcrumb([
      [
        'pasien', 'Data Pasien'
      ]
    ]);
    $this->_web->view('pasien');
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
      $searchQuery = " AND (nik LIKE :nik OR norm LIKE :norm OR nama LIKE :nama) ";
      $searchArray = array(
        'nik' => "%$searchValue%",
        'norm' => "%$searchValue%",
        'nama' => "%$searchValue%"
      );
    }

    // Total number of records without filtering
    $stmt = $pdo->prepare("SELECT COUNT(*) AS allcount FROM pasien");
    $stmt->execute();
    $records = $stmt->fetch();
    $totalRecords = $records['allcount'];

    // Total number of records with filtering
    $stmt = $pdo->prepare("SELECT COUNT(*) AS allcount FROM pasien WHERE 1" . $searchQuery);
    $stmt->execute($searchArray);
    $records = $stmt->fetch();
    $totalRecordwithFilter = $records['allcount'];

    // Fetch records
    $stmt = $pdo->prepare("SELECT nama, nik, norm, tanggal_lahir, jenis_kelamin, tanggal_dibuat FROM pasien WHERE 1" . $searchQuery . " ORDER BY " . $columnName . " " . $columnSortOrder . " LIMIT :limit,:offset");

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
        "nama" => "<a href='" . Web::url('pasien.detail.' . md5($row['nik'])) . "'>" . ($row['nama'] !== NULL ? $row['nama'] : '-') . "</a>",
        "nik" => $row['nik'],
        "norm" => $row['norm'],
        "jenis_kelamin" => $row['jenis_kelamin'] !== NULL ? strtoupper($row['jenis_kelamin']) : '-',
        "tanggal_lahir" => $row['tanggal_lahir'] ? Mod::timepiece($row['tanggal_lahir']) : '-',
        "tanggal_dibuat" => Mod::timepiece($row['tanggal_dibuat']),
        "pengaturan" => "<a href='" . Web::url('pasien.edit.' . md5($row['nik'])) . "' class='btn btn-outline-warning btn-sm'><span class='fas fa-edit'></span> Edit</a>"
          . "<button type='button' class='btn btn-outline-danger btn-sm hapus-data' data-keyid='nik' data-action='" . Web::url('pasien.hapus') . "' data-key='" . getenv('APP_KEY') . "' data-id='" . md5($row['nik']) . "'><span class='fas fa-trash'></span> Hapus</button>"
          . "<a href='" . Web::url('konsul.daftar.' . md5($row['nik'])) . "' class='btn btn-outline-primary btn-sm'><span class='fas fa-plus'></span> Konsultasi</a>"
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
    $this->role(['konsul']);
    $post = $this->request()->post;
    echo json_encode($post); die;
    $pasien = $this->model('Pasien');
    $delete = $pasien->delete(
      [
        'params' => [
          [
            'column' => 'md5(nik)',
            'value' => $post['nik']
          ]
        ]
      ]
    );

    if ($delete['success']) {
      Flasher::setFlash('Data telah terhapus.', 'success', 'ni ni-check-bold');
    } else {
      Flasher::setFlash('Ada kesalahan yang tidak diketahui, silakan coba lagi.', 'danger', 'ni ni-fat-remove');
    }

    $this->redirect('pasien');
  }

  public function edit($id)
  {
    $this->role(['konsul']);
    $pasien = $this->model('Pasien');
    $data = $pasien->read(
      ['nik', 'nama', 'alamat', 'tanggal_lahir', 'jenis_kelamin', 'norm'],
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

    if (!$data) return printf('NIK tidak valid');

    $this->_web->title('Edit Pasien');
    $this->_web->breadcrumb([
      [
        'pasien', 'Pasien'
      ],
      [
        'pasien.edit', 'Edit Pasien'
      ]
    ]);
    $this->_web->view('pasien_edit', $data);
  }

  public function perbarui($id)
  {
    $this->role(['konsul']);
    $post = $this->request()->post;
    $pasien = $this->model('Pasien');
    $konsul = $this->model('Konsul');
    if ($id !== md5($post['nik'])) {
      if (
        !$konsul->update(
          [
            'nik' => $post['nik']
          ],
          [
            'params' => [
              [
                'column' => 'md5(nik)',
                'value' => $id
              ]
            ]
          ]
        )['success']
      ) {
        Flasher::setFlash('Ada kesalahan yang tidak diketahui, silakan coba lagi.', 'danger', 'ni ni-fat-remove');
        $this->redirect('pasien.edit.' . $id);
        die;
      }
    }

    if (
      $pasien->update(
        [
          'nik' => $post['nik'],
          'nama' => $post['nama'],
          'alamat' => $post['alamat'],
          'jenis_kelamin' => $post['jenis_kelamin'],
          'tanggal_lahir' => $post['tanggal_lahir'],
          'norm' => $post['norm']
        ],
        [
          'params' => [
            [
              'column' => 'md5(nik)',
              'value' => $id
            ]
          ]
        ]
      )['success']
    ) {
      Flasher::setFlash('Data telah diperbarui.', 'success', 'ni ni-check-bold');
      if ($id !== md5($post['nik'])) {
        $this->redirect('pasien.edit.' . md5($post['nik']));
        die;
      }
    } else {
      Flasher::setFlash('Ada kesalahan yang tidak diketahui, silakan coba lagi.', 'danger', 'ni ni-fat-remove');
    }
    $this->redirect('pasien.edit.' . $id);
  }

  public function detail($nik = '')
  {
    if (!$nik) Flasher::setFlash('NIK tidak ditemukan', 'danger', 'ni ni-fat-remove');
    if (!$nik) return $this->redirect('pasien');
    $pasien_m = $this->model('Pasien');
    $konsul_m = $this->model('Konsul');
    $pasien = $pasien_m->read(
      ['nik', 'nama', 'alamat', 'jenis_kelamin', 'timestampdiff(year, tanggal_lahir, curdate()) as umur', 'tanggal_lahir', 'norm', 'tanggal_dibuat'],
      [
        'params' => [
          [
            'column' => 'md5(nik)',
            'value' => $nik
          ]
        ]
      ],
      'ARRAY_ONE'
    )['data'];
    if (!$pasien) return printf('NIK tidak valid');

    $pasien['tanggal_dibuat'] = Mod::timepiece($pasien['tanggal_dibuat']);
    $pasien['tanggal_lahir'] = Mod::timepiece($pasien['tanggal_lahir']);
    $pasien['umur'] .= ' Tahun';

    switch ($pasien['jenis_kelamin']) {
      case 'l':
        $pasien['jenis_kelamin'] = 'Laki-laki';
        break;
      case 'p':
        $pasien['jenis_kelamin'] = 'Perempuan';
        break;
      default:
        $pasien['jenis_kelamin'] = '-';
    }

    $konsul = $konsul_m->read(
      ['tanggal', 'tanggal_kembali'],
      [
        'params' => [
          [
            'column' => 'md5(nik)',
            'value' => $nik
          ]
        ],
        'order_by' => ['tanggal', 'DESC']
      ]
    )['data'];

    $this->_web->title('Detail Pasien');
    $this->_web->breadcrumb([
      [
        'pasien', 'Pasien'
      ],
      [
        'pasien.detail', 'Detail Pasien'
      ]
    ]);
    $this->_web->view('pasien_detail', [
      'pasien' => $pasien,
      'konsul' => $konsul
    ]);
  }
}
