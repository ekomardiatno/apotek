<?php

use Dompdf\Dompdf;
class PrintController extends Controller {
    public function index() {
        $post = $this->request()->post;
        $post['start'] = substr($post['start'], 6, 4) . '-' . substr($post['start'], 3, 2) . '-' . substr($post['start'], 0, 2);
        $post['end'] = substr($post['end'], 6, 4) . '-' . substr($post['end'], 3, 2) . '-' . substr($post['end'], 0, 2);
        $dompdf = new Dompdf();
        $db = Database::getInstance();
        $sql = "SELECT a.tanggal, a.nik, b.nama, b.alamat, b.jenis_kelamin, b.norm, a.tanggal_kembali FROM konsul a LEFT JOIN pasien b ON b.nik=a.nik WHERE a.tanggal >= '". $post['start'] ."' && a.tanggal <= '". $post['end'] ."' ORDER BY a.tanggal OR b.nama ASC";
        $data = $db->query($sql);
        $this->_web->layout('print');
        $html = '<html>';
        $html .= '<head>';
        $html .= '<title>' . 'Laporan Tanggal ' . Mod::timepiece($post['start']) . ($post['end'] !== $post['start'] ? ' s.d. ' . Mod::timepiece($post['end']) : '') . '</title>';
        $html .= '<link href="' . Web::assets('report.css', 'css') . '" rel="stylesheet" />';
        $html .= '</head>';
        $html .= '<body>';
        $html .= '<h1 class="text-center">Pendaftaran</h1>';
        $html .= '<p>Tanggal, ' . Mod::timepiece($post['start']) . ($post['end'] !== $post['start'] ? ' s.d. ' . Mod::timepiece($post['end']) : '') . '</p>';
        $html .= '<table>';
        $html .= '<thead>';
        $html .= '<tr>';
        $html .= '<th>No</th>';
        if($post['start'] !== $post['end']) {
            $html .= '<th>Tanggal</th>';
        }
        $html .= '<th>Nama</th>';
        $html .= '<th>NIK</th>';
        $html .= '<th>Alamat</th>';
        $html .= '<th>No. RM</th>';
        $html .= '<th>L/P</th>';
        $html .= '<th>Tgl kmbl</th>';
        $html .= '</tr>';
        $html .= '</thead>';
        $html .= '<tbody>';
        $no = 1;
        foreach($data as $d) :
            $html .= '<tr class="' . ($no - 2 > -1 && substr($d['tanggal'], 0, 7) !== substr($data[$no - 2]['tanggal'], 0, 7) ? 'border-top-2pt' : '') . '">';
            $html .= '<td class="text-center">' . $no . '</td>';
            if($post['start'] !== $post['end']) {
                $html .= '<td>' . Mod::timepiece($d['tanggal']) . '</td>';
            }
            $html .= '<td>' . ($d['nama'] !== '' && $d['nama'] !== NULL ? $d['nama'] : '-') . '</td>';
            $html .= '<td>' . $d['nik'] . '</td>';
            $html .= '<td>' . ($d['alamat'] !== '' && $d['alamat'] !== NULL ? $d['alamat'] : '-') . '</td>';
            $html .= '<td>' . ($d['norm'] !== '' && $d['norm'] !== NULL ? $d['norm'] : '-') . '</td>';
            $html .= '<td class="text-center">' . ($d['jenis_kelamin'] !== '' && $d['jenis_kelamin'] !== NULL ? strtoupper($d['jenis_kelamin']) : '-') . '</td>';
            $html .= '<td>' . Mod::timepiece($d['tanggal_kembali']) . '</td>';
            $html .= '</tr>';
            $no++;
        endforeach;
        $html .= '</tbody>';
        $html .= '</table>';
        $html .= '</body>';
        $html .= '</html>';
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        $dompdf->stream('laporan-' . time() . '.pdf', array("Attachment" => false));
    }
}