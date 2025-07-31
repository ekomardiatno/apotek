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
    $stmt = $pdo->prepare("SELECT COUNT(*) AS allcount FROM dokter WHERE is_deleted IS FALSE");
    $stmt->execute();
    $records = $stmt->fetch();
    $totalRecords = $records['allcount'];

    // Total number of records with filtering
    $stmt = $pdo->prepare("SELECT COUNT(*) AS allcount FROM dokter WHERE is_deleted IS FALSE" . $searchQuery);
    $stmt->execute($searchArray);
    $records = $stmt->fetch();
    $totalRecordwithFilter = $records['allcount'];

    // Fetch records
    $stmt = $pdo->prepare("SELECT dokter.id_dokter, dokter.sip_dokter, dokter.kategori_dokter, user.name, user.username, user.email, dokter.no_hp FROM dokter LEFT JOIN user ON user.username=dokter.username WHERE is_deleted IS FALSE" . $searchQuery . " ORDER BY " . $columnName . " " . $columnSortOrder . " LIMIT :limit,:offset");

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
        "no_hp" => $row['no_hp'] === '' ? '-' : $row['no_hp'],
        "kategori_dokter" => $row['kategori_dokter'],
        "sip_dokter" => $row['sip_dokter'],
        "pengaturan" => "<a href='" . Web::url('dokter.edit.' . md5($row['id_dokter'])) . "' class='btn btn-outline-warning btn-sm'><span class='fas fa-edit'></span> Edit</a>"
          . "<button type='button' class='btn btn-outline-danger btn-sm hapus-data' data-action='" . Web::url('dokter.hapus') . "' data-key='" . getenv('APP_KEY') . "' data-keyid='username' data-id='" . md5($row['username']) . "'><span class='fas fa-trash'></span> Hapus</button>"
          . "<form action='" . Web::url('dokter.reset') . "' method='post' class='d-inline-block'>" . Web::key_field() . "<input type='hidden' name='username' value='" . $row['username'] . "' /><button type='submit' class='btn btn-outline-primary btn-sm'><span class='fas fa-key'></span> Reset</button></form>"
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

  public function reset()
  {
    $this->role(['farma']);
    $post = $this->request()->post;
    $user = $this->model('User');
    $updateUser = $user->update(['password' => Mod::hash($post['username'])], [
      'params' => [
        ['column' => 'username', 'value' => $post['username']]
      ]
    ]);
    if (!$updateUser['success']) {
      Flasher::setFlash('Gagal mengatur ulang password', 'danger', 'ni ni-fat-remove');
    } else {
      Flasher::setFlash('Gagal mengatur ulang password', 'success', 'ni ni-check-bold');
    }
    $this->redirect('dokter');
  }

  public function tambah()
  {
    $this->role(['farma']);
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
    $this->role(['farma']);
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
      'kategori_dokter' => $post['kategori_dokter'],
      'jadwal_praktek' => serialize($post['jadwal_praktek']),
      'no_hp' => $post['no_hp']
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

  public function edit($id = null)
  {
    if (!$id) return $this->redirect('dokter');
    $this->role(['farma']);
    $this->_web->title('Dokter');
    $this->_web->breadcrumb([
      [
        'dokter', 'Data Dokter'
      ],
      [
        'dokter.edit', 'Edit Dokter'
      ]
    ]);
    $db = new Database;
    $dokter = $db->query('SELECT md5(dokter.id_dokter) AS id_dokter, md5(user.id_user) as id_user, user.name, dokter.kategori_dokter, dokter.sip_dokter, user.username, user.email, dokter.jadwal_praktek, dokter.no_hp FROM dokter LEFT JOIN user ON user.username=dokter.username WHERE is_deleted IS FALSE AND md5(dokter.id_dokter)="' . $id . '"', 'ARRAY_ONE');

    if (!$dokter['success'] || !$dokter['data']) {
      Flasher::setFlash('Data tidak ditemukan', 'danger', 'ni ni-fat-remove');
      return $this->redirect('dokter');
    }
    if ($dokter['data']['jadwal_praktek'] !== '') {
      $dokter['data']['jadwal_praktek'] = unserialize($dokter['data']['jadwal_praktek']);
    } else {
      unset($dokter['data']['jadwal_praktek']);
    }

    $this->_web->view('dokter_form', $dokter['data']);
  }

  public function ubah()
  {
    $post = $this->request()->post;

    if ($this->scheduleValidating($post['jadwal_praktek'])) {
      Flasher::setFlash($this->scheduleValidating($post['jadwal_praktek']), 'danger', 'ni ni-fat-remove');
      Flasher::setData($post);
      return $this->redirect('dokter.edit.' . $post['id_dokter']);
    }

    $db = new Database;
    $updateDokter = $db->query('UPDATE dokter SET kategori_dokter="' . $post['kategori_dokter'] . '", sip_dokter="' . $post['sip_dokter'] . '", username="' . $post['username'] . '", jadwal_praktek=\'' . serialize($post['jadwal_praktek']) . '\', no_hp="' . $post['no_hp'] . '" WHERE md5(id_dokter)="' . $post['id_dokter'] . '"');
    $updateUser = $db->query('UPDATE user SET name="' . $post['name'] . '", username="' . $post['username'] . '", email="' . $post['email'] . '" WHERE md5(id_user)="' . $post['id_user'] . '"');

    if (!$updateDokter['success'] || !$updateUser['success']) {
      Flasher::setFlash('Beberapa data tidak dapat diubah.', 'danger', 'ni ni-fat-remove');
      Flasher::setData($post);
    } else {
      Flasher::setFlash('Berhasil mengubah data dokter', 'success', 'ni ni-check-bold');
    }

    $this->redirect('dokter.edit.' . $post['id_dokter']);
  }

  private function scheduleValidating($sch)
  {
    $invalidMsg = null;
    foreach ($sch as $i => $obj) {
      if ($obj['jam_mulai'] === '' && $obj['jam_selesai'] === '') continue;
      if (($obj['jam_mulai'] === '' && $obj['jam_selesai'] !== '') || ($obj['jam_mulai'] !== '' && $obj['jam_selesai'] === '')) {
        $invalidMsg = 'Masukan jam mulai dan jam selesai atau tidak keduanya';
        break;
      }
      $d = date('d');
      $m = date('m');
      $y = date('Y');
      $h_mulai = substr($obj['jam_mulai'], 0, 2);
      $m_mulai = substr($obj['jam_mulai'], 3, 2);
      $h_selesai = substr($obj['jam_selesai'], 0, 2);
      $m_selesai = substr($obj['jam_selesai'], 3, 2);
      $date_mulai = date('Y-m-d ' . $h_mulai . ':' . $m_mulai . ':00');
      $date_selesai = date('Y-m-d ' . $h_selesai . ':' . $m_selesai . ':00');
      if (strtotime($date_mulai) > strtotime($date_selesai)) {
        $invalidMsg = 'Jam selesai harus lebih besar dari jam mulai';
        break;
      }
    }

    return $invalidMsg;
  }

  public function formattedSchedule($sch)
  {
    $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
    $hari = [];
    $jam = [];
    $index = 0;
    foreach ($sch as $i => $obj) {
      if ($obj['jam_mulai'] === '' && $obj['jam_selesai'] === '') continue;
      $day = $days[$i];
      $startAndEnd = $obj['jam_mulai'] . ' - ' . $obj['jam_selesai'];
      if (count($jam) < 1 || (count($jam) > 0 && $startAndEnd !== $jam[$index])) {
        $hari[] = $day;
        $jam[] = $startAndEnd;
        if ($i > 0) {
          $index++;
        }
        continue;
      }
      if ($startAndEnd === $jam[$index]) {
        $explodedHari = explode(' - ', $hari[$index]);
        $hari[$index] = $explodedHari[0] . ' - ' . $days[$i];
        continue;
      }
    }
    $str = '';
    foreach ($hari as $i => $val) {
      if ($i > 0) $str .= '<br>';
      $str .= $val . ', ' . $jam[$i];
    }

    return $str;
  }

  public function hapus()
  {
    $post = $this->request()->post;
    $db = new Database;
    $db->query('UPDATE dokter SET is_deleted=true WHERE md5(username)="' . $post['username'] . '"');
    $db->query('UPDATE user SET is_deleted=true WHERE md5(username)="' . $post['username'] . '"');

    Flasher::setFlash('Berhasil menghapus data dokter', 'success', 'ni ni-check-bold');
    $this->redirect('dokter');
  }
}
