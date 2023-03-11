<?php

class HomeController extends Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $this->_web->title('Konsultasi');
        $this->_web->breadcrumb([
            [
                'home', 'Konsultasi'
            ]
        ]);
        $this->_web->view('home');
    }

    public function fetch()
    {

        if (empty($_SERVER['HTTP_X_REQUESTED_WITH']) || (empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) !== 'xmlhttprequest')) {
            echo 'This is only for AJAX request';
            exit;
        }

        $isDokter = Auth::user('role') === 'dokter' ? true : false;

        $dokter = $isDokter ? $this->model('Dokter')->read(['id_dokter'], [
            'params' => [
                [
                    'column' => 'username',
                    'value' => Auth::user('username')
                ]
            ]
        ], 'ARRAY_ONE')['data'] : null;

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
            $searchQuery = " AND (pasien.nik LIKE :nik OR pasien.norm LIKE :norm OR pasien.nama LIKE :nama) ";
            $searchArray = array(
                'nik' => "%$searchValue%",
                'norm' => "%$searchValue%",
                'nama' => "%$searchValue%"
            );
        }

        // Total number of records without filtering
        $stmt = $pdo->prepare("SELECT COUNT(*) AS allcount FROM konsul" . ($isDokter ? " WHERE id_dokter='$dokter[id_dokter]' AND konsul.status_selesai='0'" : ''));
        $stmt->execute();
        $records = $stmt->fetch();
        $totalRecords = $records['allcount'];

        // Total number of records with filtering
        $stmt = $pdo->prepare("SELECT COUNT(*) AS allcount, pasien.nik AS nik, pasien.norm AS norm, pasien.nama AS nama FROM konsul LEFT JOIN pasien ON konsul.nik=pasien.nik WHERE 1" . $searchQuery . ($isDokter ? " AND id_dokter='$dokter[id_dokter]' AND konsul.status_selesai='0'" : '') . ' GROUP BY pasien.nik');
        $stmt->execute($searchArray);
        $records = $stmt->fetch();
        $totalRecordwithFilter = $records['allcount'] ?? 0;

        // Fetch records
        $stmt = $pdo->prepare("SELECT konsul.id_konsul AS id_konsul, pasien.nama AS nama, pasien.nik AS nik, pasien.norm AS norm, pasien.jenis_kelamin AS jenis_kelamin, konsul.tanggal AS tanggal, konsul.tanggal_kembali AS tanggal_kembali, konsul.tanggal_dibuat, pasien.tanggal_lahir FROM konsul LEFT JOIN pasien ON konsul.nik=pasien.nik WHERE 1" . $searchQuery . ($isDokter ? " AND id_dokter='$dokter[id_dokter]' AND konsul.status_selesai='0'" : '') . " ORDER BY " . $columnName . " " . $columnSortOrder . ", konsul.tanggal_dibuat " . $columnSortOrder . " LIMIT :limit,:offset");

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
        $dateNow = new DateTime();
        foreach ($empRecords as $row) {
            $bornDate = new DateTime($row['tanggal_lahir']);
            $data[] = array(
                "no" => $i,
                "tanggal" => Mod::timepiece($row['tanggal']),
                "nama" => "<a href='" . Web::url('pasien.detail.' . md5($row['nik'])) . "'>" . ($row['nama'] !== NULL ? $row['nama'] : '-') . "</a>",
                "nik" => $row['nik'],
                "norm" => $row['norm'],
                "jenis_kelamin" => $row['jenis_kelamin'] !== NULL ? strtoupper($row['jenis_kelamin']) : '-',
                "tanggal_kembali" => Mod::timepiece($row['tanggal_kembali']),
                "tanggal_dibuat" => Mod::timepiece($row['tanggal_dibuat']),
                'umur' => $dateNow->diff($bornDate)->y . ' Tahun',
                "pengaturan" => $isDokter ? "<a href='" . Web::url('resep.tambah.' . md5($row['id_konsul'])) . "' class='btn btn-primary btn-sm'><span class='fas fa-edit'></span> Buat Resep</a>" : ("<a href='" . Web::url('konsul.edit.' . md5($row['id_konsul'])) . "' class='btn btn-outline-warning btn-sm'><span class='fas fa-edit'></span> Edit</a>"
                    . "<button type='button' class='btn btn-outline-danger btn-sm hapus-data' data-action='" . Web::url('konsul.hapus') . "' data-key='" . getenv('APP_KEY') . "' data-id='" . md5($row['id_konsul']) . "'><span class='fas fa-trash'></span> Hapus</button>")
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
}
