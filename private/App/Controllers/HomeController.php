<?php

class HomeController extends Controller
{

    public function index()
    {
        $this->role(['konsul', 'farma']);
        $db = Database::getInstance();
        $sql = "SELECT a.id_konsul, a.tanggal, a.nik, b.nama, b.alamat, b.jenis_kelamin, b.norm, a.tanggal_kembali FROM konsul a LEFT JOIN pasien b ON b.nik=a.nik ORDER BY a.tanggal DESC";
        $data = $db->query($sql);
        $this->_web->view('home', $data);
    }

}
