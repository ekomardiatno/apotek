<?php

class AddController extends Controller
{
    public function consult()
    {
        $this->_web->title('Pendaftaran');
        $this->_web->breadcrumb([
            [
                'add.consult', 'Pendaftaran'
            ]
        ]);
        $this->_web->view('consult');
    }

    public function post()
    {

        $post = $this->request()->post;
        $pasien = $this->model('pasien');
        $konsul = $this->model('konsul');
        $check_pasien = $pasien->read(
            null,
            [
                'params' => [
                    [
                        'column' => 'nik',
                        'value' => $post['nik']
                    ]
                ]
            ],
            'NUM_ROWS'
        );

        $post['tanggal'] = substr($post['tanggal'], 6, 4) . '-' . substr($post['tanggal'], 3, 2) . '-' . substr($post['tanggal'], 0, 2);
        $post['tanggal_kembali'] = substr($post['tanggal_kembali'], 6, 4) . '-' . substr($post['tanggal_kembali'], 3, 2) . '-' . substr($post['tanggal_kembali'], 0, 2);

        if(!$check_pasien) {
            if(
                !$pasien->insert(
                    [
                        'nik' => $post['nik'],
                        'nama' => $post['nama'],
                        'jenis_kelamin' => $post['jenis_kelamin'],
                        'alamat' => $post['alamat'],
                        'norm' => $post['norm'],
                        'tanggal_dibuat' => date('Y-m-d')
                    ]
                )
            ) {
                Flasher::setFlash('Ada kesalahan yang tidak diketahui, silakan coba lagi.', 'danger', 'ni ni-fat-remove');
                $this->redirect();
                die;
            }
        }

        if (
            $konsul->insert(
                [
                    'tanggal' => $post['tanggal'],
                    'nik' => $post['nik'],
                    'tanggal_kembali' => $post['tanggal_kembali']
                ]
            )
        ) {
            Flasher::setFlash('Pendaftaran konsultasi berhasil.', 'success', 'ni ni-check-bold');
        } else {
            Flasher::setFlash('Ada kesalahan yang tidak diketahui, silakan coba lagi.', 'danger', 'ni ni-fat-remove');
        }

        $this->redirect();
    }
}
