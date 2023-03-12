<?php

use Dompdf\Dompdf;

class PrintController extends Controller
{
    public function index()
    {
        $post = $this->request()->post;
        $post['start'] = substr($post['start'], 6, 4) . '-' . substr($post['start'], 3, 2) . '-' . substr($post['start'], 0, 2);
        $post['end'] = substr($post['end'], 6, 4) . '-' . substr($post['end'], 3, 2) . '-' . substr($post['end'], 0, 2);
        $db = Database::getInstance();
        $sql = "SELECT a.tanggal, a.nik, b.nama, b.alamat, b.jenis_kelamin, b.norm, a.tanggal_kembali, b.tanggal_lahir FROM konsul a LEFT JOIN pasien b ON b.nik=a.nik WHERE a.tanggal >= '" . $post['start'] . "' AND a.tanggal <= '" . $post['end'] . "' ORDER BY a.tanggal OR b.nama ASC";
        $data = $db->query($sql)['data'];
        $this->_web->layout('print');
        $html = '<html>';
        $html .= '<head>';
        $html .= '<title>' . 'Laporan Tanggal ' . Mod::timepiece($post['start']) . ($post['end'] !== $post['start'] ? ' s.d. ' . Mod::timepiece($post['end']) : '') . '</title>';
        $html .= '<link href="' . Web::assets('report.css', 'css') . '" rel="stylesheet" />';
        $html .= '</head>';
        $html .= '<body>';
        $html .= '<h1 class="text-center">Pendaftaran Konsultasi</h1>';
        $html .= '<p>Tanggal, ' . Mod::timepiece($post['start']) . ($post['end'] !== $post['start'] ? ' s.d. ' . Mod::timepiece($post['end']) : '') . '</p>';
        $html .= '<table>';
        $html .= '<thead>';
        $html .= '<tr>';
        $html .= '<th width="1%">No</th>';
        if ($post['start'] !== $post['end']) {
            $html .= '<th>Tanggal</th>';
        }
        $html .= '<th>Nama</th>';
        $html .= '<th width="1%">NIK</th>';
        $html .= '<th>No. RM</th>';
        $html .= '<th width="1%">L/P</th>';
        $html .= '<th width="1%">Umur</th>';
        $html .= '<th>Tgl kmbl</th>';
        $html .= '<th>Alamat</th>';
        $html .= '</tr>';
        $html .= '</thead>';
        $html .= '<tbody>';
        if (count($data) <= 0) {
            $html .= '<tr>';
            $html .= '<td colspan="' . ($post['start'] !== $post['end'] ? 8 : 7) . '" class="text-center">Data tidak tersedia</td>';
            $html .= '</tr>';
        }
        $no = 1;
        foreach ($data as $d) :
            $tanggal_lahir = new DateTime($d['tanggal_lahir']);
            $tanggal_now = new DateTime();
            $diff_tanggal = $tanggal_now->diff($tanggal_lahir);
            $html .= '<tr class="' . ($no - 2 > -1 && substr($d['tanggal'], 0, 7) !== substr($data[$no - 2]['tanggal'], 0, 7) ? 'border-top-2pt' : '') . '">';
            $html .= '<td class="text-center">' . $no . '</td>';
            if ($post['start'] !== $post['end']) {
                $html .= '<td>' . Mod::timepiece($d['tanggal']) . '</td>';
            }
            $html .= '<td>' . ($d['nama'] !== '' && $d['nama'] !== NULL ? $d['nama'] : '-') . '</td>';
            $html .= '<td>' . $d['nik'] . '</td>';
            $html .= '<td>' . ($d['norm'] !== '' && $d['norm'] !== NULL ? $d['norm'] : '-') . '</td>';
            $html .= '<td class="text-center">' . ($d['jenis_kelamin'] !== '' && $d['jenis_kelamin'] !== NULL ? strtoupper($d['jenis_kelamin']) : '-') . '</td>';
            $html .= '<td class="text-center">' . $diff_tanggal->y . '</td>';
            $html .= '<td>' . Mod::timepiece($d['tanggal_kembali']) . '</td>';
            $html .= '<td>' . ($d['alamat'] !== '' && $d['alamat'] !== NULL ? $d['alamat'] : '-') . '</td>';
            $html .= '</tr>';
            $no++;
        endforeach;
        $html .= '</tbody>';
        $html .= '</table>';
        $html .= '</body>';
        $html .= '</html>';
        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        $dompdf->stream('laporan-' . time() . '.pdf', array("Attachment" => false));
    }
}
