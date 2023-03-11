<?php

class StokKeluarKategoriController extends Controller
{
  public function index()
  {
    $stokKeluarKategori = $this->model('StokKeluarKategori');
    $data = $stokKeluarKategori->read(['id_stok_keluar_kategori', 'nama_stok_keluar_kategori']);
    echo json_encode($data['data']);
  }
}
