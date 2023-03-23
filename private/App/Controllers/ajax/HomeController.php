<?php

class HomeController extends Controller
{
    private $db;
    private $monthShort;
    public function __construct()
    {
        parent::__construct();
        $this->db = Database::getInstance();
        $this->monthShort = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    }
    public function pasien()
    {
        $post = $this->request()->post;
        $sql = "SELECT nik AS value, CONCAT(nik, ' - ', nama) AS text FROM pasien WHERE nik LIKE '%" . $post['queries'] . "%' ORDER BY nama ASC LIMIT 10";
        $data = $this->db->query($sql);
        echo json_encode($data['data']);
    }

    public function konsul()
    {
        $post = $this->request()->post;
        $post['tanggal'] = substr($post['tanggal'], 7, 4) . '-' . sprintf("%02d", (array_search(substr($post['tanggal'], 3, 3), $this->monthShort) + 1)) . '-' . substr($post['tanggal'], 0, 2);
        $post['tanggal_kembali'] = substr($post['tanggal_kembali'], 7, 4) . '-' . sprintf("%02d", (array_search(substr($post['tanggal_kembali'], 3, 3), $this->monthShort) + 1)) . '-' . substr($post['tanggal_kembali'], 0, 2);
        $sql_count_data = 'SELECT COUNT(*) as count_data FROM konsul WHERE nik="' . $post['nik'] . '"';
        $sql_existed_data = "SELECT tanggal, tanggal_kembali FROM `konsul` WHERE nik = '" . $post['nik'] . "' AND ('" . $post['tanggal'] . "' BETWEEN tanggal AND tanggal_kembali OR '" . $post['tanggal_kembali'] . "' BETWEEN tanggal AND tanggal_kembali) ORDER BY tanggal ASC";
        $sql_konsul_last = "SELECT tanggal, tanggal_kembali FROM konsul WHERE nik='" . $post['nik'] . "' ORDER BY tanggal DESC LIMIT 1";
        $sql_konsul_first = "SELECT tanggal, tanggal_kembali FROM konsul WHERE nik='" . $post['nik'] . "' ORDER BY tanggal ASC LIMIT 1";
        $sql_pasien = "SELECT nik, nama, alamat, jenis_kelamin, tanggal_lahir, norm, no_hp FROM pasien WHERE nik='" . $post['nik'] . "'";
        $sql_old_data = 'SELECT tanggal, tanggal_kembali FROM konsul WHERE md5(id_konsul)="' . ($post['id'] ?? '') . '"';
        $old_data = isset($post['id']) !== '' ? $this->db->query($sql_old_data, 'ARRAY_ONE')['data'] : null;
        $count_data = isset($post['id']) !== '' ? $this->db->query($sql_count_data, 'ARRAY_ONE')['data'] : null;
        $existed_data = $this->db->query($sql_existed_data)['data'];
        $first_konsul = $this->db->query($sql_konsul_first, 'ARRAY_ONE')['data'];
        $last_konsul = $this->db->query($sql_konsul_last, 'ARRAY_ONE')['data'];
        $data_pasien = $this->db->query($sql_pasien, 'ARRAY_ONE')['data'];
        $available_date = false;
        if (count($existed_data) >= 2) {
            if (strtotime($post['tanggal']) >= strtotime($existed_data[0]['tanggal_kembali']) && strtotime($post['tanggal_kembali']) <= strtotime($existed_data[1]['tanggal'])) $available_date = true;
            if ($old_data) {
                if ($post['tanggal'] === $old_data['tanggal'] && $post['tanggal_kembali'] === $old_data['tanggal_kembali']) {
                    $available_date = true;
                }
                if ($last_konsul['tanggal'] === $existed_data[1]['tanggal'] && $old_data['tanggal'] === $existed_data[1]['tanggal']  && (strtotime($post['tanggal'])) >= strtotime($existed_data[1]['tanggal']) || (strtotime($post['tanggal'])) >= strtotime($existed_data[0]['tanggal_kembali'])) {
                    $available_date = true;
                }
                if ($first_konsul['tanggal'] === $existed_data[0]['tanggal'] && $old_data['tanggal'] === $existed_data[0]['tanggal'] && (strtotime($post['tanggal'])) <= strtotime($existed_data[0]['tanggal']) || (strtotime($post['tanggal_kembali'])) <= strtotime($existed_data[1]['tanggal'])) {
                    $available_date = true;
                }
            }
        } else if (count($existed_data) > 0) {
            if (strtotime($post['tanggal_kembali']) <= strtotime($existed_data[0]['tanggal']) || strtotime($post['tanggal']) >= strtotime($existed_data[0]['tanggal_kembali'])) $available_date = true;
            if ($old_data) {
                if ($post['tanggal'] === $old_data['tanggal'] && $post['tanggal_kembali'] === $old_data['tanggal_kembali']) {
                    $available_date = true;
                }
                if ($last_konsul['tanggal'] === $existed_data[0]['tanggal'] && strtotime($post['tanggal']) >= strtotime($existed_data[0]['tanggal']) || strtotime($post['tanggal']) <= strtotime($existed_data[0]['tanggal'])) {
                    $available_date = true;
                }
                if ($count_data > 1 && $first_konsul['tanggal'] === $existed_data[0]['tanggal'] && strtotime($post['tanggal_kembali']) <= strtotime($existed_data[0]['tanggal_kembali'])) {
                    $available_date = true;
                }
            }
        } else {
            $available_date = true;
        }

        if ($old_data && $count_data <= 1) {
            $available_date = true;
        }



        if ($last_konsul && strtotime($post['tanggal']) < strtotime($last_konsul['tanggal'])) $last_konsul = null;
        $data = [
            'isAvailable' => $available_date,
            'latestKonsul' => $last_konsul,
            'firstKonsul' => $first_konsul,
            'oldKonsul' => $old_data,
            'pasien' => $data_pasien,
            'countData' => $count_data['count_data'],
            'existedData' => $existed_data
        ];
        echo json_encode($data);
    }
}
