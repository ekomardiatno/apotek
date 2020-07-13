<?php

class FoodController extends Controller
{
  public function index($merchantId)
  {
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Headers: access");
    header("Access-Control-Allow-Methods: GET");
    header("Access-Control-Allow-Credentials: true");
    header('Content-Type: application/json');
    $where = [
      'params' => [
        [
          'column' => 'merchantId',
          'value' => $merchantId
        ]
      ]
    ];
    $data = $this->model('Food')->read(null, $where);
    echo json_encode($data);
  }
  public function home()
  {
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Headers: access");
    header("Access-Control-Allow-Methods: GET");
    header("Access-Control-Allow-Credentials: true");
    header('Content-Type: application/json');

    $kota = '';
    if(isset($_GET['kota'])) {
      $remove_words = ['kabupaten ', 'kabupaten', 'kab ', 'kab', 'kab. ', 'kab.', 'kota ', 'kota'];
      $kota = $_GET['kota'];
      $kota = strtolower($kota);
      foreach($remove_words as $w) {
        if(strpos($kota, $w) || strpos($kota, $w) > -1) {
          $kota = str_replace($w, '', $kota);
          break;
        }
      }

      $kota = ' AND merchant.merchantCity LIKE "%' . $kota . '%"';
    }


    $day = date('l');
    $day = $this->get_index_day($day);
    $db = Database::getInstance();
    $current_time = date('H:i:s');

    $attr = $this->recommend();

    $rekomendasi = 'SELECT food.foodId, food.foodName, food.foodPrice, food.foodDiscount, food.foodPicture, merchant.merchantId, merchant.merchantName FROM food LEFT JOIN merchant ON food.merchantId=merchant.merchantId LEFT JOIN ocs ON merchant.merchantId=ocs.merchantId WHERE ocs.ocsDay="' . $day . '" AND (CASE WHEN ocs.ocsClose < ocs.ocsOpen THEN (ocs.ocsOpen<="' . $current_time . '" OR ocs.ocsClose>="' . $current_time . '") ELSE (ocs.ocsOpen<="' . $current_time . '" AND ocs.ocsClose>="' . $current_time . '") END)' . $kota . $attr['cond'] . ' ORDER BY RAND() LIMIT 10';
    $acak = 'SELECT food.foodId, food.foodName, food.foodPrice, food.foodDiscount, food.foodPicture, merchant.merchantId, merchant.merchantName FROM food LEFT JOIN merchant ON food.merchantId=merchant.merchantId LEFT JOIN ocs ON merchant.merchantId=ocs.merchantId WHERE ocs.ocsDay="' . $day . '" AND (CASE WHEN ocs.ocsClose < ocs.ocsOpen THEN (ocs.ocsOpen<="' . $current_time . '" OR ocs.ocsClose>="' . $current_time . '") ELSE (ocs.ocsOpen<="' . $current_time . '" AND ocs.ocsClose>="' . $current_time . '") END)' . $kota . ' ORDER BY RAND() LIMIT 10';

    $data_rekom = $db->query($rekomendasi);
    $data_acak = $db->query($acak);
    $data = [
      'timeBased' => [
        'title' => $attr['title'],
        'data' => $data_rekom
      ],
      'random' => $data_acak,
      'titleRandom' => ['Banyak pilihan enak-enak nih!', 'Kuy pesan jangan cuma dibayangin.']
    ];

    echo json_encode($data);
  }

  private function get_index_day($day)
  {
    switch ($day) {
      case 'Sunday':
        return '0';
        break;
      case 'Monday':
        return '1';
        break;
      case 'Tuesday':
        return '2';
        break;
      case 'Wednesday':
        return '3';
        break;
      case 'Thursday':
        return '4';
        break;
      case 'Friday':
        return '5';
        break;
      case 'Saturday':
        return '6';
        break;
    }
  }

  private function recommend()
  {
    $awal = strtotime(date('Y-m-d 00:00:00'));
    $akhir = strtotime(date('Y-m-d H:i:s'));
    $diff = $akhir - $awal;
    $jam = floor($diff / (60 * 60));

    $cond = '';
    $array = [];
    $title = [];
    if ($jam >= 21) {
      $title = ['Ngemil enak nih!', 'Yuk pesan ada rekomendasi ngemil enak nih.'];
      $array = ['roti', 'martabak', 'pisang', 'coklat', 'milkshake', 'kopi', 'teh', 'jeruk', 'sate', 'tiramisu', 'keju'];
    } else if ($jam >= 18) {
      $title = ['Makan malam yuk!', 'Makan enak setelah jalani rutinitas'];
      $array = ['nasi', 'sate', 'kopi', 'teh', 'bakso', 'mie', 'bakmi', 'susu', 'jeruk'];
    } else if ($jam >= 10) {
      $title = ['Udah makan siang belum?', 'Ada pilihan makan siang enak nih.'];
      $array = ['nasi', 'teh', 'rendang', 'rames', 'ayam', 'ikan', 'daging', 'kambing', 'sapi', 'jus', 'susu', 'kopi', 'bakmi'];
    } else if ($jam >= 6) {
      $title = ['Yuk sarapan dulu!', 'Sarapan ya biar semangat menjalani hari'];
      $array = ['sate', 'mie', 'mi', 'nasi', 'nasi goreng', 'kopi', 'roti', 'soto', 'lontong', 'teh', 'susu'];
    } else {
      $title = ['Masih buka!', 'Laper? Yuk pesan masih ada yang buka nih'];
      $array = ['roti', 'martabak', 'pisang', 'coklat', 'milkshake', 'kopi', 'teh', 'jeruk', 'sate', 'tiramisu', 'keju'];
    }

    if ($array) {
      $cond = ' AND (';
      foreach ($array as $f) {
        $cond .= 'food.foodName LIKE "%' . $f . '%" OR ';
      }
      $cond = substr($cond, 0, -4);
      $cond .= ')';
    }

    return [
      'title' => $title,
      'cond' => $cond
    ];
  }

  public function get()
  {
    $page = 1;
    $kota = '';
    $searchSql = '';
    if(isset($_GET['page'])) {
      $page = $_GET['page'];
    }
    if(isset($_GET['kota'])) {
      $remove_words = ['kabupaten ', 'kabupaten', 'kab ', 'kab', 'kab. ', 'kab.', 'kota ', 'kota'];
      $kota = $_GET['kota'];
      $kota = strtolower($kota);
  
      foreach($remove_words as $w) {
        if(strpos($kota, $w) || strpos($kota, $w) > -1) {
          $kota = str_replace($w, '', $kota);
          break;
        }
      }

      $kota = ' AND merchant.merchantCity LIKE "%' . $kota . '%"';
    }
    if(isset($_GET['cari']) && $_GET['cari'] !== '') {
      $search = strtolower($_GET['cari']);
      $search = explode(' ', $search);
      $searchSql = ' AND (';
      foreach($search as $s) {
        $searchSql .= 'food.foodName LIKE "%' . $s . '%" OR ';
      }
      $searchSql = substr($searchSql, 0, -4) . ')';
    }
    $day = $this->get_index_day(date('l'));
    $current_time = date('H:i:s');
    $length = 10;
    $start = ($page - 1) * $length;
    $query = 'SELECT food.foodId, food.foodName, food.foodPrice, food.foodDiscount, food.foodPicture, merchant.merchantId, merchant.merchantName FROM food LEFT JOIN merchant ON food.merchantId=merchant.merchantId LEFT JOIN ocs ON merchant.merchantId=ocs.merchantId WHERE ocs.ocsDay="' . $day . '" AND (CASE WHEN ocs.ocsClose < ocs.ocsOpen THEN (ocs.ocsOpen<="' . $current_time . '" OR ocs.ocsClose>="' . $current_time . '") ELSE (ocs.ocsOpen<="' . $current_time . '" AND ocs.ocsClose>="' . $current_time . '") END)'. $kota . $searchSql .' ORDER BY food.foodPrice LIMIT ' . $start . ',' . $length;
    $db = Database::getInstance();

    $data = $db->query($query);

    echo json_encode($data);
  }
}
