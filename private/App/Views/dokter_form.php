<form action="<?= isset($data['id_dokter']) ? Web::url('dokter.ubah') : Web::url('dokter.pos') ?>" method="post">
  <?= Web::key_field() ?>
  <?php
  $flash = Flasher::data();
  ?>
  <div class="card shadow mb-4">
    <?= isset($data['id_dokter']) ? '<input type="hidden" name="id_dokter" value="' . $data['id_dokter'] . '" />' : '' ?>
    <?= isset($data['id_user']) ? '<input type="hidden" name="id_user" value="' . $data['id_user'] . '" />' : '' ?>
    <div class="card-body">
      <div class="form-group">
        <label class="small form-control-label" for="name">Nama Dokter<span class="text-danger">*</span></label>
        <input type="text" autocomplete="off" maxlength="100" placeholder="Nama Dokter" required name="name" id="name" value="<?= $flash['name'] ?? $data['name'] ?? '' ?>" class="form-control form-control-alternative">
      </div>
      <div class="form-group">
        <label class="small form-control-label" for="kategori_dokter">Kategori Dokter<span class="text-danger">*</span></label>
        <input type="text" autocomplete="off" maxlength="50" placeholder="Kategori Dokter" required name="kategori_dokter" value="<?= $flash['kategori_dokter'] ?? $data['kategori_dokter'] ?? '' ?>" id="kategori_dokter" class="form-control form-control-alternative">
      </div>
      <div class="form-group">
        <label class="small form-control-label" for="sip_dokter">SIP Dokter<span class="text-danger">*</span></label>
        <input type="text" autocomplete="off" maxlength="100" placeholder="SIP Dokter" required name="sip_dokter" value="<?= $flash['sip_dokter'] ?? $data['sip_dokter'] ?? '' ?>" id="sip_dokter" class="form-control form-control-alternative">
      </div>
      <div class="form-group">
        <label class="small form-control-label" for="username">Username<span class="text-danger">*</span></label>
        <input type="text" autocomplete="off" maxlength="25" placeholder="Username" required name="username" id="username" value="<?= $flash['username'] ?? $data['username'] ?? '' ?>" class="form-control form-control-alternative username-form">
      </div>
      <div class="form-group">
        <label class="small form-control-label" for="email">Email<span class="text-danger">*</span></label>
        <input type="email" autocomplete="off" maxlength="50" placeholder="Email" required name="email" id="email" value="<?= $flash['email'] ?? $data['email'] ?? '' ?>" class="form-control form-control-alternative">
      </div>
      <div class="form-group">
        <label class="small form-control-label" for="no_hp">No. HP</label>
        <input type="no_hp" autocomplete="off" maxlength="15" placeholder="No. HP" name="no_hp" id="no_hp" value="<?= $flash['no_hp'] ?? $data['no_hp'] ?? '' ?>" class="form-control form-control-alternative">
      </div>
      <div class="form-group">
        <label class="small form-control-label">Jadwal Praktek</label>
        <div class="table-responsive">
          <table class="table">
            <thead>
              <tr>
                <th>Hari</th>
                <th>Jam Mulai</th>
                <th>Jam Selesai</th>
              </tr>
            </thead>
            <tbody>
              <?php $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu']; ?>
              <?php $schd = $flash['jadwal_praktek'] ?? $data['jadwal_praktek'] ?? null ?>
              <?php foreach (($schd ?? $days) as $i => $day) : ?>
                <tr>
                  <td class="vertical-align-middle"><?= $schd ? $days[$i] : $day ?></td>
                  <td>
                    <input autocomplete="off" name="jadwal_praktek[<?= $i ?>][jam_mulai]" id="jam_prakter_<?= $i ?>_mulai" placeholder="Jam Mulai" class="form-control timepicker" value="<?= $day['jam_mulai'] ?? '' ?>" />
                    <script>
                      $('#jam_prakter_<?= $i ?>_mulai').timepicker({
                        uiLibrary: 'bootstrap5',
                        mode: '24hr'
                      })
                    </script>
                  </td>
                  <td>
                    <input autocomplete="off" name="jadwal_praktek[<?= $i ?>][jam_selesai]" id="jam_prakter_<?= $i ?>_selesai" placeholder="Jam Selesai" class="form-control timepicker" value="<?= $day['jam_selesai'] ?? '' ?>" />
                    <script>
                      $('#jam_prakter_<?= $i ?>_selesai').timepicker({
                        uiLibrary: 'bootstrap5',
                        mode: '24hr'
                      })
                    </script>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <div class="card-footer text-right">
      <?php if (isset($data['id_dokter'])) : ?>
        <a class="btn btn-secondary" href="<?= Web::url('dokter') ?>">Batal</a>
      <?php else : ?>
        <button class="btn btn-secondary" type="reset">Reset</button>
      <?php endif ?>
      <button class="btn btn-primary" type="submit"><?= isset($data['id_dokter']) ? 'Ubah' : 'Tambah' ?></button>
    </div>
  </div>
</form>