<?php

class HomeController extends Controller
{

    public function index()
    {
        $db = Database::getInstance();
        $sql = "SELECT a.id_konsul, a.tanggal, b.nik, b.nama, b.alamat, b.jenis_kelamin, b.norm, a.tanggal_kembali FROM konsul a LEFT JOIN pasien b ON b.nik=a.nik";
        $data = $db->query($sql);
        $this->_web->view('home', $data);
    }

}
