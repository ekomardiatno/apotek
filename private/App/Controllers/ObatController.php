<?php

use Dompdf\Dompdf;

class ObatController extends Controller
{
  public function __construct()
  {
    parent::__construct();
  }
  public function index()
  {
    $this->role(['farma']);
    $this->_web->title('Obat');
    $this->_web->breadcrumb([
      [
        'obat', 'Data Obat'
      ]
    ]);
    $this->_web->view('Obat');
  }

  public function fetch()
  {
    if (empty($_SERVER['HTTP_X_REQUESTED_WITH']) || (empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) !== 'xmlhttprequest')) {
      echo 'This is only for AJAX request';
      exit;
    }

    $pdo = Database::getPDOInstance();

    $get = $this->request()->get;

    $draw = $get['draw'];
    $row = $get['start'];
    $rowperpage = $get['length']; // Rows display per page
    $columnIndex = $get['order'][0]['column']; // Column index
    $columnName = $get['columns'][$columnIndex]['data']; // Column name
    $columnSortOrder = $get['order'][0]['dir']; // asc or desc
    $searchValue = $get['search']['value']; // Search value

    $searchArray = array();

    // Search
    $searchQuery = " ";
    if ($searchValue !== '') {
      $searchQuery = " AND (nama_obat LIKE :nama_obat) ";
      $searchArray = array(
        'nama_obat' => "%$searchValue%",
      );
    }

    // Total number of records without filtering
    $stmt = $pdo->prepare("SELECT COUNT(*) AS allcount FROM obat");
    $stmt->execute();
    $records = $stmt->fetch();
    $totalRecords = $records['allcount'];

    // Total number of records with filtering
    $stmt = $pdo->prepare("SELECT COUNT(*) AS allcount FROM obat WHERE 1" . $searchQuery);
    $stmt->execute($searchArray);
    $records = $stmt->fetch();
    $totalRecordwithFilter = $records['allcount'];

    // Fetch records
    $stmt = $pdo->prepare("SELECT id_obat, nama_obat, satuan_obat, stok_obat, deskripsi_obat, tanggal_dibuat, tanggal_diubah FROM obat WHERE 1" . $searchQuery . " ORDER BY " . $columnName . " " . $columnSortOrder . " LIMIT :limit,:offset");

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
    foreach ($empRecords as $row) {
      $row['id_obat'] = md5($row['id_obat']);
      $data[] = array(
        "no" => $i,
        "nama_obat" => $row['nama_obat'],
        "satuan_obat" => $row['satuan_obat'],
        "stok_obat" => "<button type='button' class='btn btn-secondary btn-sm stock-btn mr-0' data-type='remove' data-id='" . $row['id_obat'] . "' data-name='" . $row['nama_obat'] . "' data-qty='" . $row['stok_obat'] . "'><span class='fas fa-minus'></span></button>"
          . "<span class='mx-2 font-weight-bold h4 mt-1' style='vertical-align:middle'>" . Mod::numeral($row['stok_obat']) . "</span>"
          . "<button type='button' class='btn btn-secondary btn-sm stock-btn' data-type='add' data-id='" . $row['id_obat'] . "' data-name='" . $row['nama_obat'] . "' data-qty='" . $row['stok_obat'] . "'><span class='fas fa-plus'></span></button>",
        "deskripsi_obat" => $row['deskripsi_obat'] !== '' ? $row['deskripsi_obat'] : '-',
        "pengaturan" => "<a href='" . Web::url('obat.edit.' . $row['id_obat']) . "' class='btn btn-outline-warning btn-sm'><span class='fas fa-edit'></span><span class='ml-1 d-none d-md-inline-block'>Edit</span></a>"
          . "<button type='button' class='btn btn-outline-danger btn-sm hapus-data' data-keyid='id_obat' data-action='" . Web::url('obat.hapus') . "' data-key='" . getenv('APP_KEY') . "' data-id='" . $row['id_obat'] . "'><span class='fas fa-trash'></span><span class='ml-1 d-none d-md-inline-block'>Hapus</span></button>"
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

  public function tambah()
  {
    $this->role(['farma']);
    $this->_web->title('Obat');
    $this->_web->breadcrumb([
      [
        'obat', 'Data Obat'
      ],
      [
        'obat.tambah', 'Tambah Data'
      ]
    ]);
    $this->_web->view('obat_form');
  }

  public function edit($id)
  {
    $this->role(['farma']);
    $this->_web->title('Obat');
    $this->_web->breadcrumb([
      [
        'obat', 'Data Obat'
      ],
      [
        'obat.edit', 'Edit Data'
      ]
    ]);

    $obat = $this->model('Obat');
    $data = $obat->read(
      ['nama_obat', 'satuan_obat', 'deskripsi_obat'],
      [
        'params' => [
          [
            'column' => 'md5(id_obat)',
            'value' => $id
          ]
        ]
      ],
      'ARRAY_ONE'
    )['data'];

    if (!$data) return printf('Data tidak ditemukan');

    $data['id_obat'] = $id;

    $this->_web->view('obat_form', $data);
  }

  public function pos()
  {
    $this->role(['farma']);
    $post = $this->request()->post;
    $obat = $this->model('Obat');
    $posObat = $obat->insert($post);
    if (!$posObat['success']) {
      Flasher::setData($post);
      Flasher::setFlash('Ada kesalahan yang tidak diketahui, silakan coba lagi.', 'danger', 'ni ni-fat-remove');
      return $this->redirect('obat.tambah');
    }

    $this->updatestok($posObat['last_inserted_id']);

    Flasher::setFlash('Tambah data obat berhasil', 'success', 'ni ni-check-bold');
    $this->redirect('obat');
  }

  public function ubah()
  {
    $this->role(['farma']);
    $post = $this->request()->post;
    $obat = $this->model('Obat');
    $id = $post['id_obat'];
    array_splice($post, 0, 1);

    if (!$obat->update($post, [
      'params' => [
        [
          'column' => 'md5(id_obat)',
          'value' => $id
        ]
      ]
    ])['success']) {
      Flasher::setFlash('Ada kesalahan yang tidak diketahui, silakan coba lagi.', 'danger', 'ni ni-fat-remove');
      Flasher::setData($post);
    } else {
      Flasher::setFlash('Data berhasil diperbarui.', 'success', 'ni ni-check-bold');
    }
    $this->redirect('obat.edit.' . $id);
  }

  public function hapus()
  {
    $this->role(['farma']);
    $post = $this->request()->post;
    echo json_encode($post);
    die;
    $obat = $this->model('Obat');
    $delete = $obat->delete(
      [
        'params' => [
          [
            'column' => 'md5(id_obat)',
            'value' => $post['id_obat']
          ]
        ]
      ]
    );

    if ($delete['success']) {
      Flasher::setFlash('Data telah terhapus.', 'success', 'ni ni-check-bold');
    } else {
      Flasher::setFlash('Ada kesalahan yang tidak diketahui, silakan coba lagi.', 'danger', 'ni ni-fat-remove');
    }

    $this->redirect('obat');
  }

  public function stok()
  {
    $this->role(['farma']);
    $post = $this->request()->post;
    $post['kuantitas'] = intval($post['kuantitas']);
    $db = new Database;
    $obat = $db->query('SELECT id_obat, stok_obat FROM obat WHERE md5(id_obat)="' . $post['id_obat'] . '"', 'ARRAY_ONE')['data'];
    if (!$obat) {
      Flasher::setFlash('Data obat tidak ditemukan', 'danger', 'ni ni-fat-remove');
      return $this->redirect('obat');
    }

    $id_obat = $obat['id_obat'];
    $stok_obat = intval($obat['stok_obat']);

    if ($post['type'] !== 'add' && $post['kuantitas'] > $stok_obat) {
      Flasher::setFlash('Maksimal stok keluar: ' . $stok_obat, 'danger', 'ni ni-fat-remove');
      return $this->redirect('obat');
    }

    $stok_baru = $post['type'] === 'add' ? $stok_obat + $post['kuantitas'] : $stok_obat - $post['kuantitas'];
    $stokKategori = $post['type'] === 'add' ? $this->model('StokMasukKategori') : $this->model('StokKeluarKategori');
    if ($post['id_kategori'] === "" || !isset($post['id_kategori'])) {
      $nameKeyName = $post['type'] === 'add' ? 'nama_stok_masuk_kategori' : 'nama_stok_keluar_kategori';
      $stokKategoriInsert = $stokKategori->insert([
        $nameKeyName => $post['nama_kategori']
      ]);
      if (!$stokKategoriInsert['success']) {
        Flasher::setFlash('Gagal menyimpan data.', 'danger', 'ni ni-fat-remove');
        return $this->redirect('obat');
      }
      $post['id_kategori'] = $stokKategoriInsert['last_inserted_id'];
    }

    $stokHistori = $post['type'] === 'add' ? $this->model('StokMasuk') : $this->model('StokKeluar');
    $idKeyName = $post['type'] === 'add' ? 'id_stok_masuk_kategori' : 'id_stok_keluar_kategori';
    $qtyKeyName = $post['type'] === 'add' ? 'kuantitas_stok_masuk' : 'kuantitas_stok_keluar';
    $stokHistoriInsert = $stokHistori->insert([
      $idKeyName => $post['id_kategori'],
      $qtyKeyName => $post['kuantitas'],
      'id_obat' => $id_obat
    ]);

    if (!$stokHistoriInsert['success']) {
      Flasher::setFlash('Gagal menyimpan data.', 'danger', 'ni ni-fat-remove');
      return $this->redirect('obat');
    }

    $obat = $this->model('Obat');
    $obatUpdate = $obat->update(
      [
        'stok_obat' => $stok_baru
      ],
      [
        'params' => [
          [
            'column' => 'md5(id_obat)',
            'value' => $post['id_obat']
          ]
        ]
      ]
    );

    if (!$obatUpdate['success']) {
      $stokHistori->delete(
        [
          'params' => [
            [
              'column' => $idKeyName,
              'value' => $stokHistoriInsert['last_inserted_id']
            ]
          ]
        ]
      );
      Flasher::setFlash('Gagal menyimpan data.', 'danger', 'ni ni-fat-remove');
      return $this->redirect('obat');
    }

    $this->updatestok($id_obat, $post['type'] === 'add' ? $post['kuantitas'] : $post['kuantitas'] * -1);

    Flasher::setFlash('Stok telah diperbarui', 'success', 'ni ni-check-bold');
    return $this->redirect('obat');
  }

  public function updatestok($id = null, $stok = 0, $encryptedId = false)
  {
    $this->role(['farma', 'dokter']);
    if (!$id) return false;
    $month = date('m');
    $year = date('Y');
    $db = new Database;
    if ($encryptedId) {
      $getPlainId = $db->query('SELECT id_obat FROM obat WHERE md5(id_obat)="' . $id . '"', 'ARRAY_ONE');
      $id = $getPlainId['data']['id_obat'];
    }
    $riwayat = $db->query('SELECT * FROM riwayat_stok WHERE MONTH(tanggal_diperbarui)=' . $month . ' AND YEAR(tanggal_diperbarui)=' . $year . ' AND id_obat=' . $id, 'ARRAY_ONE');
    if (!$riwayat['data']) {
      $stmt = $db->query('INSERT INTO riwayat_stok(id_obat,stok_akhir) VALUES(' . $id . ',' . $stok . ')');
    } else {
      $stmt = $db->query('UPDATE riwayat_stok SET stok_akhir=stok_akhir+' . $stok . ' WHERE id_riwayat_stok=' . $riwayat['data']['id_riwayat_stok']);
    }

    return $stmt;
  }

  public function print()
  {
    $months = ['Jan' => 1, 'Feb' => 2, 'Mar' => 3, 'Apr' => 4, 'Mei' => 5, 'Jun' => 6, 'Jul' => 7, 'Ags' => 8, 'Sep' => 9, 'Okt' => 10, 'Nov' => 11, 'Des' => 12];
    $post = $this->request()->post;
    if (!$post || !isset($post['bulan'])) return $this->redirect('obat');
    $label_bulan_laporan = $post['bulan'];
    $bulan = explode(' ', $post['bulan']);
    $tahun = $bulan[1];
    $bulan = $months[$bulan[0]];
    $tanggal = $tahun . '-' . sprintf("%02d", $bulan) . '-01';
    $tanggal_akhir = date('Y-m-t', strtotime($tanggal));
    $db = new Database;
    $obat = $db->query('SELECT id_obat, nama_obat, satuan_obat FROM obat');
    $obat = $obat['data'];

    $stok_masuk = $db->query("SELECT stok_masuk.id_obat, stok_masuk_kategori.nama_stok_masuk_kategori, stok_masuk_kategori.id_stok_masuk_kategori, CASE WHEN SUM(stok_masuk.kuantitas_stok_masuk) IS NULL THEN 0 ELSE SUM(stok_masuk.kuantitas_stok_masuk) END AS kuantitas_stok_masuk FROM stok_masuk RIGHT JOIN stok_masuk_kategori ON stok_masuk.id_stok_masuk_kategori=stok_masuk_kategori.id_stok_masuk_kategori WHERE id_obat IS NOT NULL AND stok_masuk.tanggal_diubah BETWEEN '" . $tanggal . "' AND '" . $tanggal_akhir . "' GROUP BY stok_masuk_kategori.id_stok_masuk_kategori, stok_masuk.id_obat")['data'];
    $stok_masuk_kategori = [];
    foreach ($stok_masuk as $kategori) {
      $indexedKategori = ArrayHelpers::indexOf(function ($obj, $i) use ($kategori) {
        return $obj['id_stok_masuk_kategori'] === $kategori['id_stok_masuk_kategori'];
      }, $stok_masuk_kategori);
      if ($indexedKategori < 0) {
        $stok_masuk_kategori[] = [
          'id_stok_masuk_kategori' => $kategori['id_stok_masuk_kategori'],
          'nama_stok_masuk_kategori' => $kategori['nama_stok_masuk_kategori']
        ];
      }
    }

    $stok_keluar = $db->query("SELECT stok_keluar.id_obat, stok_keluar_kategori.nama_stok_keluar_kategori, stok_keluar_kategori.id_stok_keluar_kategori, CASE WHEN SUM(stok_keluar.kuantitas_stok_keluar) IS NULL THEN 0 ELSE SUM(stok_keluar.kuantitas_stok_keluar) END AS kuantitas_stok_keluar FROM stok_keluar RIGHT JOIN stok_keluar_kategori ON stok_keluar.id_stok_keluar_kategori=stok_keluar_kategori.id_stok_keluar_kategori WHERE id_obat IS NOT NULL AND stok_keluar.tanggal_diubah BETWEEN '" . $tanggal . "' AND '" . $tanggal_akhir . "' GROUP BY stok_keluar_kategori.id_stok_keluar_kategori, stok_keluar.id_obat")['data'];
    $stok_keluar_kategori = [];
    foreach ($stok_keluar as $kategori) {
      $indexedKategori = ArrayHelpers::indexOf(function ($obj, $i) use ($kategori) {
        return $obj['id_stok_keluar_kategori'] === $kategori['id_stok_keluar_kategori'];
      }, $stok_keluar_kategori);
      if ($indexedKategori < 0) {
        $stok_keluar_kategori[] = [
          'id_stok_keluar_kategori' => $kategori['id_stok_keluar_kategori'],
          'nama_stok_keluar_kategori' => $kategori['nama_stok_keluar_kategori']
        ];
      }
    }

    $resep = $db->query('SELECT data_resep FROM resep WHERE tanggal_diubah BETWEEN "' . $tanggal . '" AND "' . $tanggal_akhir . '"')['data'];
    foreach ($obat as $i => $params) {
      $riwayat_stok = $db->query('SELECT stok_akhir FROM riwayat_stok WHERE id_obat="' . $params['id_obat'] . '" AND tanggal_diperbarui < "' . $tanggal . '" ORDER BY tanggal_diperbarui DESC', 'ARRAY_ONE');
      if ($riwayat_stok['data']) {
        $obat[$i]['stok_awal'] = intval($riwayat_stok['data']['stok_akhir']);
      } else {
        $obat[$i]['stok_awal'] = 0;
      }
    }

    $reseps = [];
    foreach ($resep as $objResep) {
      $data_resep = unserialize($objResep['data_resep']);
      foreach ($data_resep as $objDataResep) {
        $indexedObat = ArrayHelpers::indexOf(function ($obj, $i) use ($objDataResep) {
          return $obj['id_obat'] === $objDataResep['id_obat'];
        }, $reseps);
        if ($indexedObat > -1) {
          $reseps[$i] = [
            'id_obat' => $reseps[$i]['id_obat'],
            'kuantitas' => $reseps[$i]['kuantitas'] + $objDataResep['kuantitas']
          ];
        } else {
          $reseps[] = [
            'id_obat' => $objDataResep['id_obat'],
            'kuantitas' => $objDataResep['kuantitas']
          ];
        }
      }
    }

    $html = '<html>';
    $html .= '<head>';
    $html .= '<title>' . 'Laporan Stok Obat Bulan ' . $label_bulan_laporan . '</title>';
    $html .= '<link href="' . Web::assets('report.css', 'css') . '" rel="stylesheet" />';
    $html .= '</head>';
    $html .= '<body>';
    $html .= '<h1 class="text-center">Laporan Stok Obat</h1>';
    $html .= '<p>Bulan: ' . $label_bulan_laporan . '</p>';
    $html .= '<table>';
    $html .= '<thead>';
    $html .= '<tr>';
    $html .= '<th style="width:1%" rowspan="2">No.</th>';
    $html .= '<th rowspan="2">Nama Obat</th>';
    $html .= '<th rowspan="2">Satuan</th>';
    $html .= '<th rowspan="2">Stok Awal</th>';
    $html .= '<th rowspan="' . (count($stok_masuk_kategori) > 0 ? 1 : 2) . '" colspan="' . count($stok_masuk_kategori) . '">Stok Masuk</th>';
    $html .= '<th rowspan="1" colspan="' . (count($stok_keluar_kategori) + 1) . '">Stok Keluar</th>';
    $html .= '<th rowspan="2">Stok Akhir</th>';
    $html .= '</tr>';
    $html .= '<tr>';
    foreach ($stok_masuk_kategori as $kategori) {
      $html .= '<th>' . $kategori['nama_stok_masuk_kategori'] . '</th>';
    }
    $html .= '<th>Resep</th>';
    foreach ($stok_keluar_kategori as $kategori) {
      $html .= '<th>' . $kategori['nama_stok_keluar_kategori'] . '</th>';
    }
    $html .= '</tr>';
    $html .= '</thead>';
    $html .= '<tbody>';
    $no = 1;
    foreach ($obat as $objObat) {
      $stok_akhir = 0;
      $html .= '<tr>';
      $html .= '<td class="text-center">' . $no . '</td>';
      $html .= '<td>' . $objObat['nama_obat'] . '</td>';
      $html .= '<td>' . $objObat['satuan_obat'] . '</td>';
      $html .= '<td class="text-center">' . Mod::numeral($objObat['stok_awal']) . '</td>';

      if (count($stok_masuk_kategori) < 1) {
        $html .= '<td class="text-center">0</td>';
      } else {
        foreach ($stok_masuk_kategori as $kategori) {
          $indexedStokMasuk = ArrayHelpers::indexOf(function ($obj, $i) use ($objObat, $kategori) {
            return $objObat['id_obat'] == $obj['id_obat'] && $obj['id_stok_masuk_kategori'] == $kategori['id_stok_masuk_kategori'];
          }, $stok_masuk);
          if ($indexedStokMasuk < 0) {
            $html .= '<td class="text-center">0</td>';
          } else {
            $stok_akhir += $stok_masuk[$indexedStokMasuk]['kuantitas_stok_masuk'];
            $html .= '<td class="text-center">' . Mod::numeral($stok_masuk[$indexedStokMasuk]['kuantitas_stok_masuk']) . '</td>';
          }
        }
      }

      $indexedResep = ArrayHelpers::indexOf(function ($obj, $i) use ($objObat) {
        return md5($objObat['id_obat']) == $obj['id_obat'];
      }, $reseps);
      if ($indexedResep < 0) {
        $html .= '<td class="text-center">0</td>';
      } else {
        $stok_akhir -= $reseps[$indexedResep]['kuantitas'];
        $html .= '<td class="text-center">' . Mod::numeral($reseps[$indexedResep]['kuantitas']) . '</td>';
      }

      if (count($stok_keluar_kategori) > 0) {
        foreach ($stok_keluar_kategori as $kategori) {
          $indexedStokKeluar = ArrayHelpers::indexOf(function ($obj, $i) use ($objObat, $kategori) {
            return $objObat['id_obat'] == $obj['id_obat'] && $obj['id_stok_keluar_kategori'] == $kategori['id_stok_keluar_kategori'];
          }, $stok_keluar);
          if ($indexedStokKeluar < 0) {
            $html .= '<td class="text-center">0</td>';
          } else {
            $stok_akhir -= $stok_keluar[$indexedStokKeluar]['kuantitas_stok_keluar'];
            $html .= '<td class="text-center">' . Mod::numeral($stok_keluar[$indexedStokKeluar]['kuantitas_stok_keluar']) . '</td>';
          }
        }
      }
      $html .= '<td class="text-center">' . Mod::numeral($stok_akhir) . '</td>';
      $html .= '</tr>';
      $no++;
    }
    $html .= '</tbody>';
    $html .= '</table>';
    $html .= '</body>';
    $html .= '</html>';

    // echo $html;
    // die;

    $dompdf = new Dompdf();
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'landscape');
    $dompdf->render();
    $dompdf->stream('laporan-stok-obat-' . $bulan . '-' . $tahun . '-' . time() . '.pdf', array("Attachment" => false));
  }
}
