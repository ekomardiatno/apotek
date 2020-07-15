<?php

class PasienController extends Controller {
  public function index() {
    $this->_web->title('Pasien');
    $this->_web->breadcrumb([
        [
            'pasien', 'Pasien'
        ]
    ]);
    $pasien = $this->model('Pasien');
    $data = $pasien->read(
      ['nik', 'nama', 'jenis_kelamin', 'alamat', 'norm', 'tanggal_dibuat']
    );
    $this->_web->view('pasien', $data);
  }
}