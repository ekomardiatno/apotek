<div class="row">
  <div class="col-md-7">
    <div class="card shadow mb-4">
      <div class="card-header">
        <h3 class="mb-0">Data Pasien</h3>
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
            <?php
            foreach ($data['konsul'] as $d) :
            ?>
              <li>
                <div class="d-flex justify-content-between mx--2 mb-2">
                  <span class="mx-2 font-weight-bold">Konsultasi</span>
                  <span class="mx-2 text-primary font-weight-bold"><?= Mod::timepiece($d['tanggal']) ?></span>
                </div>
                <div class="d-flex flex-column">
                  <p class="small text-muted mb-0">Konsultasi berikutnya</p>
                  <p class="mb-0"><?= Mod::timepiece($d['tanggal_kembali']) ?></p>
                </div>
              </li>
            <?php
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