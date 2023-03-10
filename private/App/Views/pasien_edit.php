<form id="form-edit-id-<?= $data['nik'] ?>" action="<?= Web::url('pasien.perbarui.' . md5($data['nik'])) ?>" method="post">
  <?= Web::key_field() ?>
  <div class="card shadow mb-4">
    <div class="card-body">
      <div class="form-group">
        <label class="small form-control-label" for="nik">NIK<span class="text-danger">*</span></label>
        <input type="text" value="<?= $data['nik'] ?>" autocomplete="off" maxlength="16" placeholder="Mis. 1234567890987654" required name="nik" id="nik" class="form-control form-control-alternative">
      </div>
      <div class="form-group">
        <label class="small form-control-label" for="nama">Nama<span class="text-danger">*</span></label>
        <input type="text" autocomplete="off" value="<?= $data['nama'] ?>" maxlength="50" placeholder="Mis. Rani Fauziah" required name="nama" id="nama" class="form-control form-control-alternative">
      </div>
      <div class="form-group">
        <label class="small form-control-label" for="alamat">Alamat</label>
        <textarea name="alamat" placeholder="Alamat" id="alamat" class="form-control form-control-alternative"><?= $data['alamat'] ?></textarea>
      </div>
      <div class="form-group">
        <label for="tanggal_lahir" class="small form-control-label">Tanggal Lahir<span class="text-danger">*</span></label>
        <input required type="text" placeholder="Tanggal Lahir" name="tanggal_lahir" id="tanggal_lahir" value="<?= $data['tanggal_lahir'] ?>" class="datepicker form-control form-control-alternative">
      </div>
      <div class="form-group">
        <label class="small form-control-label" for="norm">No. Rekam Medis<span class="text-danger">*</span></label>
        <input type="text" maxlength="50" autocomplete="off" required placeholder="Nomor rekam medis" value="<?= $data['norm'] ?>" required name="norm" id="norm" class="form-control form-control-alternative">
      </div>
      <div class="form-group">
        <label class="small form-control-label" for="jenis_kelamin">Jenis Kelamin<span class="text-danger">*</span></label>
        <select name="jenis_kelamin" required id="jenis_kelamin" class="form-control form-control-alternative">
          <option value="">Pilih jenis kelamin</option>
          <option <?= $data['jenis_kelamin'] === 'l' ? 'selected' : '' ?> value="l">Laki-laki</option>
          <option <?= $data['jenis_kelamin'] === 'p' ? 'selected' : '' ?> value="p">Perempuan</option>
        </select>
      </div>
    </div>
    <div class="card-footer text-right">
      <button type="button" class="btn btn-warning" onclick="
        bootbox.confirm({
          message: 'Apakah Anda yakin akan menghapus data?',
          buttons: {
            confirm: {
              label: 'Perbarui',
              className: 'btn-warning btn-sm'
            },
            cancel: {
              label: 'Batal',
              className: 'btn-sencondary btn-sm'
            }
          },
          callback: function (result) {
            if(result) {
              $('form#form-edit-id-<?= $data['nik'] ?>').submit()
            }
          }
        })
      ">Simpan</button>
    </div>
  </div>
</form>