<?php

class StokMasukKategoriController extends Controller
{
  public function index()
  {
    $stokMasukKategori = $this->model('StokMasukKategori');
    $data = $stokMasukKategori->read(['id_stok_masuk_kategori', 'nama_stok_masuk_kategori']);
    echo json_encode($data['data']);
  }
}
