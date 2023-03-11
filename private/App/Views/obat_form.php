<form action="<?= isset($data['id_obat']) ? Web::url('obat.ubah') : Web::url('obat.pos') ?>" method="post">
  <?= Web::key_field() ?>
  <?php
  $flash = Flasher::data();
  ?>
  <div class="card shadow mb-4">
    <?= isset($data['id_obat']) ? '<input type="hidden" name="id_obat" value="' . $data['id_obat'] . '" />' : '' ?>
    <div class="card-body">
      <div class="form-group">
        <label class="small form-control-label" for="nama_obat">Nama Obat<span class="text-danger">*</span></label>
        <input type="text" autocomplete="off" maxlength="150" placeholder="Nama Obat" required name="nama_obat" id="nama_obat" value="<?= $flash['nama_obat'] ?? $data['nama_obat'] ?? '' ?>" class="form-control form-control-alternative">
      </div>
      <div class="form-group">
        <label class="small form-control-label" for="satuan_obat">Satuan Obat<span class="text-danger">*</span></label>
        <input type="text" autocomplete="off" maxlength="50" placeholder="Satuan Obat" required name="satuan_obat" value="<?= $flash['satuan_obat'] ?? $data['satuan_obat'] ?? '' ?>" id="satuan_obat" class="form-control form-control-alternative">
      </div>
      <div class="form-group">
        <label class="small form-control-label" for="deskripsi_obat">Deskripsi Obat</label>
        <textarea name="deskripsi_obat" placeholder="Deskripsi Obat" id="deskripsi_obat" maxlength="300" class="form-control form-control-alternative"><?= $flash['deskripsi_obat'] ?? $data['deskripsi_obat'] ?? '' ?></textarea>
      </div>
    </div>
    <div class="card-footer text-right">
      <?php if (isset($data['id_obat'])) : ?>
        <a class="btn btn-secondary" href="<?= Web::url('obat') ?>">Batal</a>
      <?php else : ?>
        <button class="btn btn-secondary" type="reset">Reset</button>
      <?php endif ?>
      <button class="btn btn-primary" type="submit"><?= isset($data['id_obat']) ? 'Ubah' : 'Tambah' ?></button>
    </div>
  </div>
</form>