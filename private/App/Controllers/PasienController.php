<?php

class PasienController extends Controller
{
  public function index()
  {
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

  public function fetch()
  {

    if (empty($_SERVER['HTTP_X_REQUESTED_WITH']) || (empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) !== 'xmlhttprequest')) {
      echo 'This is only for AJAX request';
      exit;
    }

    $pdo = Database::getPDOInstance();

    $post = $this->request()->post;

    $draw = $post['draw'];
    $row = $post['start'];
    $rowperpage = $post['length']; // Rows display per page
    $columnIndex = $post['order'][0]['column']; // Column index
    $columnName = $post['columns'][$columnIndex]['data']; // Column name
    $columnSortOrder = $post['order'][0]['dir']; // asc or desc
    $searchValue = $post['search']['value']; // Search value

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
    $stmt = $pdo->prepare("SELECT nama, nik, norm, jenis_kelamin, tanggal_dibuat FROM pasien WHERE 1" . $searchQuery . " ORDER BY " . $columnName . " " . $columnSortOrder . " LIMIT :limit,:offset");

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
        "nama" => "<a href='" . Web::url('pasien.detail.' . $row['nik']) . "'>" . ($row['nama'] !== NULL ? $row['nama'] : '-') . "</a>",
        "nik" => $row['nik'],
        "norm" => $row['norm'],
        "jenis_kelamin" => $row['jenis_kelamin'] !== NULL ? strtoupper($row['jenis_kelamin']) : '-',
        "tanggal_dibuat" => Mod::timepiece($row['tanggal_dibuat']),
        "pengaturan" => "<a href='" . Web::url('pasien.edit.' . $row['nik']) . "' class='btn btn-outline-warning btn-sm'><span class='fas fa-edit'></span> Edit</a>"
          . "<button type='button' class='btn btn-outline-danger btn-sm hapus-data' data-action='" . Web::url('pasien.hapus') . "' data-key='" . getenv('APP_KEY') . "' data-id='" . $row['nik'] . "'><span class='fas fa-trash'></span> Hapus</button>"
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

    if ($delete) {
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

  public function perbarui($id)
  {
    $this->role(['konsul']);
    $post = $this->request()->post;
    $pasien = $this->model('Pasien');
    $konsul = $this->model('Konsul');
    if ($id !== $post['nik']) {
      if (
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

    if (
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
      if ($id !== $post['nik']) {
        $this->redirect('pasien.edit.' . $post['nik']);
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
      ['nik', 'nama', 'alamat', 'jenis_kelamin', 'norm', 'tanggal_dibuat'],
      [
        'params' => [
          [
            'column' => 'nik',
            'value' => $nik
          ]
        ]
      ],
      'ARRAY_ONE'
    );

    $pasien['tanggal_dibuat'] = Mod::timepiece($pasien['tanggal_dibuat']);

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
            'column' => 'nik',
            'value' => $nik
          ]
        ],
        'order_by' => ['tanggal', 'DESC']
      ]
    );

    $this->_web->title('Detail Pasien');
    $this->_web->breadcrumb([
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
