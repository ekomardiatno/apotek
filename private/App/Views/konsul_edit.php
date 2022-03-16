<form id="form-edit-id-<?= $data['id_konsul'] ?>" action="<?= Web::url('konsul.perbarui.' . md5($data['id_konsul'])) ?>" method="post">
  <?= Web::key_field() ?>
  <div class="card shadow mb-4">
    <div class="card-body">
      <div class="form-group">
        <label class="small form-control-label" for="tanggal">Tanggal<span class="text-danger">*</span></label>
        <input type="text" name="tanggal" required date-format="dd-mm-yyyy" id="tanggal" value="<?= substr($data['tanggal'], 8, 2) . '-' . substr($data['tanggal'], 5, 2) . '-' . substr($data['tanggal'], 0, 4) ?>" class="form-control form-control-alternative datepicker">
      </div>
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
      <div class="form-group">
        <label for="tanggal_kembali" class="small form-control-label">Tanggal Kembali Konsul<span class="text-danger">*</span></label>
        <input required date-format="dd-mm-yyyy" type="text" name="tanggal_kembali" id="tanggal_kembali" value='<?= substr($data['tanggal_kembali'], 8, 2) . '-' . substr($data['tanggal_kembali'], 5, 2) . '-' . substr($data['tanggal_kembali'], 0, 4) ?>' class="datepicker form-control form-control-alternative">
      </div>
    </div>
    <div class="card-footer text-right">
      <button type="button" class="btn btn-warning" onclick="
        bootbox.confirm({
          message: 'Apakah Anda yakin akan memperbarui data?',
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
              $('form#form-edit-id-<?= $data['id_konsul'] ?>').submit()
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
    format: 'dd-mm-yyyy',
    autoclose: true
  }
  const tanggal_comp = $('#tanggal').datepicker(datepickerOptions)
  const tanggal_kembali_comp = $('#tanggal_kembali').datepicker(datepickerOptions)

  $('#tanggal').on('change', function() {
    let $this = $(this)
    let value = $this.val()
    let y = value.substring(6, 10)
    let m = value.substring(3, 5)
    let d = value.substring(0, 2)
    let date = new Date(parseInt(y), parseInt(m) - 1, parseInt(d))
    date = new Date(date.getTime() + (10 * 24 * 60 * 60 * 1000))
    y = date.getFullYear()
    m = date.getMonth() + 1
    d = date.getDate()
    m = ('0' + m).slice(-2)
    d = ('0' + d).slice(-2)
    if ($('#tanggal_kembali').val() !== `${d}-${m}-${y}`)
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
    let y = value.substring(6, 10)
    let m = value.substring(3, 5)
    let d = value.substring(0, 2)
    let date = new Date(parseInt(y), parseInt(m) - 1, parseInt(d))
    date = new Date(date.getTime() - (10 * 24 * 60 * 60 * 1000))
    y = date.getFullYear()
    m = date.getMonth() + 1
    d = date.getDate()
    m = ('0' + m).slice(-2)
    d = ('0' + d).slice(-2)
    if ($('#tanggal').val() !== `${d}-${m}-${y}`)
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
    data.append('id', '<?= md5($data['id_konsul']) ?>')
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