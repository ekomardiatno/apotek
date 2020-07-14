<?php

class HomeController extends Controller
{
    private $db;
    public function __construct()
    {
        $this->db = Database::getInstance();
    }
    public function pasien() {
        $post = $this->request()->post;
        $sql = "SELECT nik AS value, CONCAT(nik, ' - ', nama) AS text FROM pasien WHERE nik LIKE '%" . $post['queries'] . "%' ORDER BY nama ASC LIMIT 10";
        $data = $this->db->query($sql);
        echo json_encode($data);
    }

    public function konsul() {
        $post = $this->request()->post;
        $sql = "SELECT a.tanggal, b.nik, b.nama, b.alamat, b.jenis_kelamin, b.norm, a.tanggal_kembali FROM konsul a RIGHT JOIN pasien b ON b.nik=a.nik WHERE a.nik='" . $post['nik'] . "' ORDER BY a.tanggal DESC LIMIT 1";
        $data = $this->db->query($sql, 'ARRAY_ONE');
        echo json_encode($data);
    }
}