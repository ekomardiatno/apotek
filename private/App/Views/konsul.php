<form action="<?= Web::url('konsul.pos') ?>" method="post">
  <?= Web::key_field() ?>
  <?php
    $now = date('d-m-Y');
    $date = date('d-m-Y', strtotime('+10 days', strtotime($now)));
  ?>
  <div class="card shadow mb-4">
    <div class="card-body">
      <div class="form-group">
        <label class="small form-control-label" for="tanggal">Tanggal<span class="text-danger">*</span></label>
        <input type="text" name="tanggal" required date-format="dd-mm-yyyy" id="tanggal" value="<?= $now ?>" class="form-control form-control-alternative datepicker">
      </div>
      <div class="form-group">
        <label class="small form-control-label" for="nik">NIK<span class="text-danger">*</span></label>
        <input type="text" autocomplete="off" maxlength="16" placeholder="Mis. 1402021601950001" required name="nik" id="nik" class="form-control form-control-alternative">
      </div>
      <div class="form-group">
        <label class="small form-control-label" for="nama">Nama<span class="text-danger">*</span></label>
        <input type="text" autocomplete="off" maxlength="50" placeholder="Mis. Rani Fauziah" required name="nama" id="nama" class="form-control form-control-alternative">
      </div>
      <div class="form-group">
        <label class="small form-control-label" for="alamat">Alamat</label>
        <textarea name="alamat" placeholder="Alamat" id="alamat" class="form-control form-control-alternative"></textarea>
      </div>
      <div class="form-group">
        <label class="small form-control-label" for="norm">No. Rekam Medis<span class="text-danger">*</span></label>
        <input type="text" maxlength="50" autocomplete="off" required placeholder="Nomor rekam medis" required name="norm" id="norm" class="form-control form-control-alternative">
      </div>
      <div class="form-group">
        <label class="small form-control-label" for="jenis_kelamin">Jenis Kelamin<span class="text-danger">*</span></label>
        <select name="jenis_kelamin" required id="jenis_kelamin" class="form-control form-control-alternative">
          <option value="">Pilih jenis kelamin</option>
          <option value="l">Laki-laki</option>
          <option value="p">Perempuan</option>
        </select>
      </div>
      <div class="form-group">
        <label for="tanggal_kembali" class="small form-control-label">Tanggal Kembali Konsul<span class="text-danger">*</span></label>
        <input required date-format="dd-mm-yyyy" type="text" name="tanggal_kembali" id="tanggal_kembali" value='<?= $date ?>' class="datepicker form-control form-control-alternative">
      </div>
    </div>
    <div class="card-footer text-right">
      <button class="btn btn-secondary" type="reset">Reset</button>
      <button class="btn btn-success" type="submit">Daftar</button>
    </div>
  </div>
</form>

<script>
  var timeout
  function checkingPasien(value) {

    if($('form .card-body').find('.alert')[0] || $('form button[type="submit"]').is(':disabled')) {
      $('form .card-body').find('.alert')[0].remove()
      $('form button[type="submit"]').prop('disabled', false)
    }
    $('body').prepend(
        '<div class="loading full">'+
          '<div class="box">'+
            '<span class="fas fa-spinner"></span>'+
          '</div>'+
        '</div>'
      )
      let data = new FormData()
      data.append('_key', '<?= getenv('APP_KEY') ?>')
      data.append('nik', value)
      fetch(`<?= Web::url('ajax.home.konsul') ?>`, {
        method: 'POST',
        body: data
      })
        .then(res => res.json())
        .then(res => {
          if(res.pasien !== null) {
            $('[name="nama"]').val(res.pasien.nama)
            $('[name="alamat"]').val(res.pasien.alamat)
            $('[name="jenis_kelamin"]').val(res.pasien.jenis_kelamin)
            $('[name="norm"]').val(res.pasien.norm)
          }
          if(res.konsul !== null) {
            let date = new Date(res.konsul.tanggal_kembali)
            date.setHours(0)
            date.setMinutes(0)
            date.setSeconds(0)
            let now = new Date($('[name="tanggal"]').val().substring(6,10) + '-' + $('[name="tanggal"]').val().substring(3,5) + '-' + $('[name="tanggal"]').val().substring(0,2))
            now.setHours(0)
            now.setMinutes(0)
            now.setSeconds(0)
            let diff = (now.getTime() - date.getTime()) / (24 * 60 * 60 * 1000)
            diff = Math.round(diff)
            setTimeout(() => {
              $('body').find('.loading')[0].remove()
              if(diff < 0) {
                $('form').find('.card .card-body').prepend(
                  '<div class="alert alert-danger d-flex">'+
                    '<div class="flex-fill">'+
                    'Telah melakukan konsultasi pada '+ dateFormat(res.konsul.tanggal) +', konsultasi selanjutnya tanggal:<h3 class="text-white mb-0">'+ dateFormat(res.konsul.tanggal_kembali) +'</h3>'+
                    '</div>'+
                    '<div class="ml-3">'+
                      '<h1 class="fas fa-exclamation-circle text-white"></h1>'+
                    '</div>'+
                  '</div>'
                )
                $('form').find('[type="submit"]').prop('disabled', true)
              }
            }, 1000)
          } else {
            setTimeout(() => {
              $('body').find('.loading')[0].remove()
            }, 500)
          }

        })
        .catch(err => {
          console.log('Error', err)
        })
  }
  $('#nik').autocomplete('<?= Web::url('ajax.home.pasien') ?>', {
    body: {
      _key: '<?= getenv('APP_KEY') ?>'
    },
    onStart: () => {
      if($('form .card-body').find('.alert')[0] || $('form button[type="submit"]').is(':disabled')) {
        $('form button[type="reset"]').click()
      }
    },
    onTyping: (text) => {
      if($('form .card-body').find('.alert')[0] || $('form button[type="submit"]').is(':disabled')) {
        $('form .card-body').find('.alert')[0].remove()
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
        checkingPasien(value)
      }, 0)
    }
	})
  $('#tanggal').on('change', function () {
    let $this = $(this)
    let value = $this.val()
    let y = value.substring(6,10)
    let m = value.substring(3,5)
    let d = value.substring(0,2)
    let date = new Date(parseInt(y), parseInt(m) - 1, parseInt(d))
    date = new Date(date.getTime() + (10*24*60*60*1000))
    y = date.getFullYear()
    m = date.getMonth() + 1
    d = date.getDate()
    m = ('0' + m).slice(-2)
    d = ('0' + d).slice(-2)
    $('#tanggal_kembali').val(`${d}-${m}-${y}`)
    if($('#nik').val() !== '') {
      clearTimeout(timeout)
      timeout = setTimeout(() => {
        checkingPasien($('#nik').val())
      }, 0)
    }
  })
  $('form button[type="reset"]').on('click',function () {
    $('form .card-body').find('.alert')[0].remove()
    $('form button[type="submit"]').prop('disabled', false)
  })
</script>