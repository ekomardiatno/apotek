<?php

class PrintController extends Controller {
    public function index() {
        $db = Database::getInstance();
        $sql = "SELECT a.tanggal, a.nik, b.nama, b.alamat, b.jenis_kelamin, b.norm, a.tanggal_kembali FROM konsul a LEFT JOIN pasien b ON b.nik=a.nik WHERE a.tanggal >= '2020-07-16' && a.tanggal <= '2020-08-18' ORDER BY a.tanggal OR b.nama ASC";
        $data = $db->query($sql);
        $this->_web->layout('print');
        $data = [
            'start' => '2020-07-16',
            'end' => '2020-08-18',
            'response' => $data
        ];
        $this->_web->view('print', $data);
    }
}