<div class="row">
  <div class="col-md-7">
    <div class="card shadow mb-4">
      <div class="card-header d-flex align-items-center">
        <h3 class="mb-0 flex-fill">Data Pasien</h3>
      </div>
      <div class="card-body">
        <?php
        $pasien_keys = array_keys($data['pasien']);
        $key_name = [
          'nik' => 'NIK',
          'nama' => 'Nama Pasien',
          'alamat' => 'Alamat Pasien',
          'jenis_kelamin' => 'Jenis Kelamin',
          'norm' => 'Nomor Rekam Medis',
          'tanggal_dibuat' => 'Terdaftar Sejak'
        ];
        foreach ($pasien_keys as $k) :
        ?>
          <div class="d-flex mb-3">
            <div style="flex: 1">
              <p class="small text-muted mb-0 mt-1 font-weight-bold"><?= $key_name[$k] ?></p>
            </div>
            <div style="flex: 2">
              <p class="mb-0"><?= $data['pasien'][$k] ?></p>
            </div>
          </div>
        <?php
        endforeach;
        ?>
        <?php if (Auth::user('role') === 'konsul') : ?>
          <hr class="mb-0">
          <div class="row mx--2 mb--2 pt-3">
            <div class="col px-0 mx-2 mb-2">
              <a href="<?= Web::url('pasien.edit.' . md5($data['pasien']['nik'])) ?>" class="btn btn-primary shadow-none d-block font-weight-normal" style="font-size:.8rem"><i class="fas fa-edit"></i> Ubah Data Pasien</a>
            </div>
            <div class="col px-0 mx-2 mb-2">
              <a href="<?= Web::url('konsul.daftar.' . md5($data['pasien']['nik'])) ?>" class="btn btn-success shadow-none d-block font-weight-normal" style="font-size:.8rem"><i class="fas fa-plus"></i> Daftar Konsultasi</a>
            </div>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
  <div class="col-md-5">
    <div class="card shadow mb-4">
      <div class="card-header">
        <h3 class="mb-0">Riwayat Konsultasi</h3>
      </div>
      <div style="max-height: 400px;overflow-y: auto;" class="card-body">
        <?php
        if (count($data['konsul']) <= 0) {
        ?>
          <div class="d-flex flex-column align-items-center justify-content-center h-100">
            <p class="text-muted">Data tidak ditemukan</p>
          </div>
        <?php
        } else {
        ?>
          <ul class="timeline">
            <li>
              <div class="d-flex flex-column">
                <p class="small text-muted mb-0">Jadwal konsultasi berikutnya</p>
                <p class="mb-0 font-weight-bold"><?= Mod::timepiece($data['konsul'][0]['tanggal_kembali']) ?></p>
              </div>
            </li>
            <?php
            $i = 0;
            foreach ($data['konsul'] as $d) :
            ?>
              <li>
                <div class="d-flex mx--2 mb-2">
                  <div class="d-flex flex-column mx-2 flex-fill">
                    <span>Konsultasi ke-<?= (count($data['konsul']) - $i) ?></span>
                    <?php
                    $prev_date = $data['konsul'][$i + 1]['tanggal_kembali'] ?? null;
                    if ($prev_date) {
                      $start_date = new DateTime($data['konsul'][$i + 1]['tanggal_kembali']);
                      $end_date = new DateTime($d['tanggal']);
                      $interval = $start_date->diff($end_date);
                      if ($interval->days > 0) {
                    ?>
                        <span class="small text-muted mt-1">Terlambat <b><?= $interval->days ?> hari</b> dari jadwal kembali pada konsultasi ke-<?= count($data['konsul']) - ($i + 1) ?></span>
                    <?php
                      }
                    }
                    ?>
                  </div>
                  <div class="mx-2 text-right" style="flex-shrink:0">
                    <span class="font-italic text-primary"><?= Mod::timepiece($d['tanggal']) ?></span>
                  </div>
                </div>
              </li>
            <?php
              $i++;
            endforeach;
            ?>
          </ul>
        <?php
        }
        ?>
      </div>
    </div>
  </div>
</div>