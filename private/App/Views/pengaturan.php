<div class="card shadow mb-4">
  <form action="<?= Web::url('pengaturan.simpan') ?>" method="post">
    <?= Web::key_field() ?>
    <div class="card-body">
      <?php foreach ($data as $i => $val) : ?>
        <div class="form-group">
          <label class="small form-control-label" for="<?= $val['key_pengaturan'] ?>"><?= $val['label_pengaturan'] ?></label>
          <?php if ($val['key_pengaturan'] === 'BRAND_ADDRESS') : ?>
            <textarea name="<?= $val['key_pengaturan'] ?>"" id="<?= $val['key_pengaturan'] ?>"" placeholder="<?= $val['label_pengaturan'] ?>" class="form-control form-control-alternative"><?= $val['value_pengaturan'] ?></textarea>
          <?php else : ?>
            <input type="text" autocomplete="off" placeholder="<?= $val['label_pengaturan'] ?>" name="<?= $val['key_pengaturan'] ?>"" id="<?= $val['key_pengaturan'] ?>"" value="<?= $val['value_pengaturan'] ?>" class="form-control form-control-alternative">
          <?php endif; ?>
        </div>
      <?php endforeach ?>
    </div>
    <div class="card-footer text-right">
      <button class="btn btn-primary" type="submit">Simpan</button>
    </div>
  </form>
</div>