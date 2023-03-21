<form id="form-edit-id-<?= $data['konsultasi']['id_konsul'] ?>" action="<?= Web::url('konsul.perbarui.' . md5($data['konsultasi']['id_konsul'])) ?>" method="post">
  <?= Web::key_field() ?>
  <?php
  $flash = Flasher::data();
  $now = $flash ? substr($flash['tanggal'], 8, 2) . '/' . substr($flash['tanggal'], 5, 2) . '/' . substr($flash['tanggal'], 0, 4) : substr($data['konsultasi']['tanggal'], 8, 2) . '/' . substr($data['konsultasi']['tanggal'], 5, 2) . '/' . substr($data['konsultasi']['tanggal'], 0, 4);
  $plus10days = $flash ? substr($flash['tanggal_kembali'], 8, 2) . '/' . substr($flash['tanggal_kembali'], 5, 2) . '/' . substr($flash['tanggal_kembali'], 0, 4) : substr($data['konsultasi']['tanggal_kembali'], 8, 2) . '/' . substr($data['konsultasi']['tanggal_kembali'], 5, 2) . '/' . substr($data['konsultasi']['tanggal_kembali'], 0, 4);
  ?>
  <div class="card shadow mb-4">
    <div class="card-body">
      <div class="row mb-3">
        <div class="col">
          <div class="form-group mb-0">
            <label class="small form-control-label" for="tanggal">Tanggal<span class="text-danger">*</span></label>
            <input type="text" name="tanggal" required id="tanggal" class="form-control form-control-alternative">
          </div>
        </div>
        <div class="col">
          <div class="form-group mb-0">
            <label for="tanggal_kembali" class="small form-control-label">Tanggal Kembali<span class="text-danger">*</span></label>
            <input required type="text" name="tanggal_kembali" id="tanggal_kembali" class="form-control form-control-alternative">
          </div>
        </div>
      </div>
      <div class="form-group">
        <label class="small form-control-label" for="id_dokter">Dokter Tujuan<span class="text-danger">*</span></label>
        <select required name="id_dokter" class="form-control form-control-alternative">
          <option value="">Pilih Dokter</option>
          <?php $id_dokter = $flash['id_dokter'] ?? $data['konsultasi']['id_dokter'] ?? ''; ?>
          <?php foreach ($data['dokter'] as $dokter) : ?>
            <option <?= $id_dokter === $dokter['id_dokter'] ? 'selected' : '' ?> value="<?= $dokter['id_dokter'] ?>"><?= $dokter['name'] ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="form-group">
        <label class="small form-control-label" for="nik">NIK<span class="text-danger">*</span></label>
        <input type="text" value="<?= $flash['nik'] ?? $data['konsultasi']['nik'] ?>" autocomplete="off" maxlength="16" placeholder="NIK" required name="nik" id="nik" class="form-control form-control-alternative">
      </div>
      <div class="form-group">
        <label class="small form-control-label" for="nama">Nama<span class="text-danger">*</span></label>
        <input type="text" autocomplete="off" value="<?= $flash['nama'] ?? $data['konsultasi']['nama'] ?>" maxlength="50" placeholder="Nama" required name="nama" id="nama" class="form-control form-control-alternative">
      </div>
      <div class="form-group">
        <label class="small form-control-label" for="alamat">Alamat</label>
        <textarea name="alamat" placeholder="Alamat" id="alamat" class="form-control form-control-alternative"><?= $flash['alamat'] ?? $data['konsultasi']['alamat'] ?></textarea>
      </div>
      <div class="form-group">
        <label for="tanggal_lahir" class="small form-control-label">Tanggal Lahir<span class="text-danger">*</span></label>
        <input required type="text" placeholder="Tanggal Lahir" value="<?= $flash['tanggal_lahir'] ?? $data['konsultasi']['tanggal_lahir'] ?>" name="tanggal_lahir" autocomplete="off" id="tanggal_lahir" class="datepicker form-control form-control-alternative">
      </div>
      <div class="form-group">
        <label class="small form-control-label" for="norm">No. Rekam Medis<span class="text-danger">*</span></label>
        <input type="text" maxlength="50" autocomplete="off" required placeholder="Nomor rekam medis" value="<?= $flash['norm'] ?? $data['konsultasi']['norm'] ?>" required name="norm" id="norm" class="form-control form-control-alternative">
      </div>
      <div class="form-group">
        <label class="small form-control-label" for="no_hp">No. HP<span class="text-danger">*</span></label>
        <input type="text" maxlength="15" autocomplete="off" required placeholder="Nomor telp/HP" value="<?= $flash['no_hp'] ?? $data['konsultasi']['no_hp'] ?>" required name="no_hp" id="no_hp" class="form-control form-control-alternative">
      </div>
      <div class="form-group">
        <label class="small form-control-label" for="jenis_kelamin">Jenis Kelamin<span class="text-danger">*</span></label>
        <select name="jenis_kelamin" required id="jenis_kelamin" class="form-control form-control-alternative">
          <option value="">Pilih jenis kelamin</option>
          <?php $gender_selected = $flash['nik'] ?? $data['konsultasi']['jenis_kelamin']; ?>
          <option <?= $gender_selected === 'l' ? 'selected' : '' ?> value="l">Laki-laki</option>
          <option <?= $gender_selected === 'p' ? 'selected' : '' ?> value="p">Perempuan</option>
        </select>
      </div>
    </div>
    <div class="card-footer text-right">
      <button type="button" class="btn btn-primary" onclick="
        bootbox.confirm({
          message: 'Apakah Anda yakin akan memperbarui data?',
          buttons: {
            confirm: {
              label: 'Perbarui',
              className: 'btn-primary btn-sm'
            },
            cancel: {
              label: 'Batal',
              className: 'btn-sencondary btn-sm'
            }
          },
          callback: function (result) {
            if(result) {
              $('form#form-edit-id-<?= $data['konsultasi']['id_konsul'] ?>').submit()
            }
          }
        })
      ">Simpan</button>
    </div>
  </div>
</form>

<script>
  $('form').find('button[type="button"]').prop('disabled', true)
  var timeout
  const datepickerOptions = {
    format: 'dd-M-yyyy',
    autoclose: true
  }
  const now = '<?= $now ?>'
  const plus10day = '<?= $plus10days ?>'
  const tanggal_comp = $('#tanggal').datepicker(datepickerOptions)
  const tanggal_kembali_comp = $('#tanggal_kembali').datepicker(datepickerOptions)
  tanggal_comp.datepicker('setDate', now)
  tanggal_kembali_comp.datepicker('setDate', plus10day)

  const monthShort = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']

  $('#tanggal').on('change', function() {
    let $this = $(this)
    let value = $this.val()
    let y = value.substring(7, 11)
    let m = monthShort.indexOf(value.substring(3, 6))
    let d = value.substring(0, 2)
    let date = new Date(parseInt(y), parseInt(m), parseInt(d))
    date = new Date(date.getTime() + (10 * 24 * 60 * 60 * 1000))
    y = date.getFullYear()
    m = date.getMonth() + 1
    d = date.getDate()
    m = ('0' + m).slice(-2)
    d = ('0' + d).slice(-2)
    if ($('#tanggal_kembali').val() !== `${d}-${monthShort[parseInt(m) - 1]}-${y}`)
      tanggal_kembali_comp.datepicker('setDate', `${d}/${m}/${y}`)
    if ($('#nik').val() !== '') {
      clearTimeout(timeout)
      timeout = setTimeout(() => {
        checkingPasien()
      }, 0)
    }
  })

  $('#tanggal_kembali').on('change', function() {
    let $this = $(this)
    let value = $this.val()
    let y = value.substring(7, 11)
    let m = monthShort.indexOf(value.substring(3, 6))
    let d = value.substring(0, 2)
    let date = new Date(parseInt(y), parseInt(m), parseInt(d))
    date = new Date(date.getTime() - (10 * 24 * 60 * 60 * 1000))
    y = date.getFullYear()
    m = date.getMonth() + 1
    d = date.getDate()
    m = ('0' + m).slice(-2)
    d = ('0' + d).slice(-2)
    if ($('#tanggal').val() !== `${d}-${monthShort[parseInt(m) - 1]}-${y}`)
      tanggal_comp.datepicker('setDate', `${d}/${m}/${y}`)
    if ($('#nik').val() !== '') {
      clearTimeout(timeout)
      timeout = setTimeout(() => {
        checkingPasien()
      }, 0)
    }
  })

  function checkingPasien() {
    const value = $('#nik').val()
    if ($('form .card-body').find('.alert').length > 0 || $('form button[type="submit"]').is(':disabled')) {
      if ($('form .card-body').find('.alert').length > 0) $('form .card-body').find('.alert')[0].remove()
      $('form button[type="submit"]').prop('disabled', false)
    }
    $('body').prepend(
      '<div class="loading full">' +
      '<div class="box">' +
      '<span class="fas fa-spinner"></span>' +
      '</div>' +
      '</div>'
    )
    let data = new FormData()
    data.append('_key', '<?= getenv('APP_KEY') ?>')
    data.append('nik', value)
    data.append('tanggal', $('#tanggal').val())
    data.append('tanggal_kembali', $('#tanggal_kembali').val())
    data.append('id', '<?= md5($data['konsultasi']['id_konsul']) ?>')
    fetch(`<?= Web::url('ajax.home.konsul') ?>`, {
        method: 'POST',
        body: data
      })
      .then(res => res.json())
      .then(res => {
        $('form').find('button[type="button"]').prop('disabled', true)
        if ($('body').find('.loading').length > 0) $('body').find('.loading')[0].remove()
        if (res.pasien) {
          $('[name="nama"]').val(res.pasien.nama)
          $('[name="alamat"]').val(res.pasien.alamat)
          $('[name="jenis_kelamin"]').val(res.pasien.jenis_kelamin)
          $('[name="norm"]').val(res.pasien.norm)
        }
        if (res.isAvailable) $('form').find('button[type="button"]').prop('disabled', false)
        if (!res.isAvailable) $('form').find('.card .card-body').prepend(
          '<div class="alert alert-danger d-flex align-items-center">' +
          '<div class="flex-fill">' +
          (res.latestKonsul ? 'Telah melakukan konsultasi pada ' + dateFormat(res.latestKonsul.tanggal) + ', konsultasi selanjutnya tanggal:<h3 class="text-white mb-0">' + dateFormat(res.latestKonsul.tanggal_kembali) + '</h3>' : 'Telah melakukan proses konsultasi pada rentang tanggal tersebut') +
          '</div>' +
          '<div class="ml-3">' +
          '<h1 class="fas fa-exclamation-circle text-white mb-0"></h1>' +
          '</div>' +
          '</div>'
        )

      })
      .catch(err => {
        if ($('body').find('.loading').length > 0) $('body').find('.loading')[0].remove()
        $('form').find('button[type="button"]').prop('disabled', true)
        bootbox.alert('Gagal mendapatkan informasi pasien')
      })
  }

  checkingPasien()

  $('#nik').autocomplete('<?= Web::url('ajax.home.pasien') ?>', {
    body: {
      _key: '<?= getenv('APP_KEY') ?>'
    },
    onStart: () => {
      // if ($('form .card-body').find('.alert').length > 0 || $('form button[type="submit"]').is(':disabled')) {
      //   $('form button[type="reset"]').click()
      // }
    },
    onTyping: (text) => {
      if ($('form .card-body').find('.alert').length > 0 || $('form button[type="submit"]').is(':disabled')) {
        if ($('form .card-body').find('.alert').length > 0) $('form .card-body').find('.alert')[0].remove()
        $('form button[type="submit"]').prop('disabled', false)
      }

      $('[name="nama"]').val() !== '' &&
        $('[name="nama"]').val('')
      $('[name="alamat"]').val() !== '' &&
        $('[name="alamat"]').val('')
      $('[name="norm"]').val() !== '' &&
        $('[name="norm"]').val('')
      $('[name="jenis_kelamin"]').val() !== '' &&
        $('[name="jenis_kelamin"]').val('')
    },
    onSelect: (value) => {
      clearTimeout(timeout)
      timeout = setTimeout(() => {
        checkingPasien()
      }, 0)
    }
  })
</script>