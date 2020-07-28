<form action="<?= Web::url('profil.update.' . $data['id_user']) ?>" method="POST" id="change-profil">
  <div class="card-group-flex-row card-group-flex-row-md">
    <div class="card bg-secondary shadow mb-3">
      <div class="card-body">
        <?= Web::key_field() ?>
        <div class="form-group">
          <label class="small form-control-label" for="username">Username<span class="text-danger">*</span></label>
          <input value="<?= $data['username'] ?>" type="text" maxlength="50" placeholder="Masukkan username" required name="attr[username]" id="username" class="form-control form-control-sm form-control-alternative">
        </div>
        <div class="form-group">
          <label class="small form-control-label" for="name">Nama<span class="text-danger">*</span></label>
          <input value="<?= $data['name'] ?>" type="text" maxlength="50" placeholder="Masukkan nama" required name="attr[name]" id="name" class="form-control form-control-sm form-control-alternative">
        </div>
        <div class="form-group">
          <label class="small form-control-label" for="email">Email<span class="text-danger">*</span></label>
          <input value="<?= $data['email'] ?>" type="text" placeholder="Masukkan email" required name="attr[email]" id="email" class="form-control form-control-sm form-control-alternative">
        </div>
      </div>
    </div>
    <div class="card bg-secondary shadow mb-3">
      <div class="card-body">
        <div class="mb-3">
          <h4 class="mt-0 mb-1 text-uppercase fw-800">Ganti password</h4>
          <p class="font-italic small text-muted mb-0">Isikan password baru untuk mengganti password</p>
        </div>
        <div class="form-group">
          <label class="small form-control-label" for="new_password">Password baru</label>
          <input type="password" placeholder="Masukkan password baru" name="attr[password]" id="new_password" class="form-control form-control-sm form-control-alternative">
        </div>
        <div class="form-group">
          <label class="small form-control-label" for="re_new_password">Ulangi Password baru</label>
          <input type="password" placeholder="Ulangi masukkan password baru" id="re_new_password" class="form-control form-control-sm form-control-alternative">
        </div>
      </div>
    </div>
  </div>
  <div class="card shadow mb-3 bg-secondary">
    <div class="card-body p-3">
      <div class="row align-items-center flex-row-reverse">
        <div class="col-12 col-lg-6">
          <h4 class="mt-0 mb-1 text-uppercase fw-800">Perhatian!</h4>
          <p class="font-italic small text-muted mb-0">Masukkan password saat ini untuk mengubah data profil</p>
        </div>
        <div class="col-12 col-lg-6">
          <div class="form-group mb-1">
            <label class="small form-control-label" for="password">Password saat ini<span class="text-danger">*</span></label>
            <input type="password" placeholder="Masukkan password" required name="password" id="password" class="form-control form-control-sm form-control-alternative">
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="card shadow mb-3 bg-secondary">
    <div class="card-body p-3">
      <div class="row align-items-center flex-row-reverse">
      <div class="col-12 col-lg-6">
        <a href="<?= Web::url('logout') ?>" class="btn btn-danger btn-sm mb-1">Logout</a>
        <p class="font-italic small text-muted mb-0">Klik untuk keluar</p>
      </div>
      <div class="col-12 col-lg-6">
        <button type="button" class="btn btn-primary btn-sm btn-save mb-1" data-toggle="modal" data-target="#modalSimpan">Simpan</button>
        <p class="font-italic small text-muted mb-0">Klik untuk menyimpan perubahan</p>
        <!-- Modal -->
        <div class="modal fade" id="modalSimpan" tabindex="-1" role="dialog" aria-labelledby="labelModalSimpan" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="labelModalSimpan">Peringatan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
                <h3 class="m-0 font-weight-bold">Yakin ingin mengubah data?</h3>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">Ubah</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</form>

<script>
  let form = $('#change-profil')
  let input = form.find('.form-control')
  input.on('keyup', function() {
    isReady($(this).parents('form'))
  })
  isReady(form)

  function isReady(form) {
    let filled = 0
    let required = form.find('input[required]')
    let newPass = form.find('[id=new_password]')
    let reNewPass = form.find('[id=re_new_password]')
    required.each(function() {
      this.value != '' ? filled += 1 : null
    })
    if (filled < required.length || newPass.val() !== '' && newPass.val() !== reNewPass.val()) {
      form.find('.btn-save').prop('disabled', true)
      return false
    }
    form.find('.btn-save').prop('disabled', false)
    return true
  }

  form.on('submit', function(e) {
    if (!isReady($(this))) {
      e.preventDefault()
    }
  })


  let timeOut
  form.find('[id=re_new_password]').on('keyup', function() {
    clearTimeout(timeOut)
    $(this).parents('.form-group').find('.msg').remove()
    timeOut = setTimeout(function() {
      if ($(this).val() !== '' && $(this).val() !== $(this).parents('.form-group').prev('.form-group').find('input').val()) {
        $(this).parents('.form-group').append('<p class="msg small text-danger mb-0 mt-1 font-italic">Password tidak sama</p>')
      }
    }.bind(this), 500)
  })

  form.find('.btn-save').on('click', function() {
    if (!isReady($(this))) {
      e.preventDefault()
      flashMessage('ni ni-fat-remove', 'Gagal! Periksa kembali form', '<?= $msg['type']; ?>', '<?= $msg['y']; ?>', '<?= $msg['x']; ?>')
    }
  })
</script>