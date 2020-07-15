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
        $sql_konsul = "SELECT tanggal, tanggal_kembali FROM konsul WHERE nik='" . $post['nik'] . "' ORDER BY tanggal DESC LIMIT 1";
        $sql_pasien = "SELECT nik, nama, alamat, jenis_kelamin, norm FROM pasien WHERE nik='" . $post['nik'] . "'";
        $data_konsul = $this->db->query($sql_konsul, 'ARRAY_ONE');
        $data_pasien = $this->db->query($sql_pasien, 'ARRAY_ONE');
        $data = [
            'konsul' => $data_konsul,
            'pasien' => $data_pasien
        ];
        echo json_encode($data);
    }
}