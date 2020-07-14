<?php

class HomeController extends Controller
{

    public function index()
    {
        $db = Database::getInstance();
        $sql = "SELECT a.tanggal, b.nik, b.nama, b.alamat, b.jenis_kelamin, b.norm, a.tanggal_kembali FROM konsul a RIGHT JOIN pasien b ON b.nik=a.nik";
        $data = $db->query($sql);
        $this->_web->view('home', $data);
    }

}
