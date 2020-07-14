<form action="<?= Web::url('add.post') ?>" method="post">
  <?= Web::key_field() ?>
  <?php
    $now = date('d-m-Y');
    $date = date('d-m-Y', strtotime('+10 days', strtotime($now)));
  ?>
  <div class="card shadow mb-4">
    <div class="card-body">
      <div class="form-group">
        <label class="small form-control-label" for="tanggal">Tanggal<span class="text-danger">*</span></label>
        <input type="text" name="tanggal" required id="tanggal" value="<?= $now ?>" class="form-control form-control-alternative datepicker">
      </div>
      <div class="form-group">
        <label class="small form-control-label" for="nik">NIK<span class="text-danger">*</span></label>
        <input type="text" autocomplete="off" maxlength="16" placeholder="Cth: 1402021601950001" required name="nik" id="nik" class="form-control form-control-alternative">
      </div>
      <div class="form-group">
        <label class="small form-control-label" for="nama">Nama<span class="text-danger">*</span></label>
        <input type="text" autocomplete="off" maxlength="50" placeholder="Cth: Rani Fauziah" required name="nama" id="nama" class="form-control form-control-alternative">
      </div>
      <div class="form-group">
        <label class="small form-control-label" for="alamat">Alamat</label>
        <textarea name="alamat" placeholder="Alamat" id="alamat" class="form-control form-control-alternative"></textarea>
      </div>
      <div class="form-group">
        <label class="small form-control-label" autocomplete="off" for="norm">No. Rekam Medis<span class="text-danger">*</span></label>
        <input type="text" maxlength="50" required placeholder="Nomor rekam medis" required name="norm" id="norm" class="form-control form-control-alternative">
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
        <input required type="text" name="tanggal_kembali" id="tanggal_kembali" value='<?= $date ?>' class="datepicker form-control form-control-alternative">
      </div>
    </div>
    <div class="card-footer text-right">
      <button class="btn btn-secondary" type="reset">Reset</button>
      <button class="btn btn-success" type="submit">Daftarkan</button>
    </div>
  </div>
</form>

<script>
  $('#nik').autocomplete('<?= Web::url('ajax.home.pasien') ?>', {
    body: {
      _key: 'aecf4790d1cd03c186f56025cd85275aeW9Md3ZWb2lMTnptMEgrWGd2NkVCT2xZeVR5bzBRL05JRDlmSVdqK3FXYnVHb0FuTkxVQ3RxZ1dGcjhVMTNaR0FFT2RmZDRaZ3lnM0w1Sjk2SXcvdVJZRmluRmdPVlQ5YzdnSXAxaHNmamNpWkhGY01kaXZtYmNuOWt4SW1JclQ='
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
    },
    onSelect: (value) => {
      $('body').prepend(
        '<div class="loading full">'+
          '<div class="box">'+
            '<span class="fas fa-spinner"></span>'+
          '</div>'+
        '</div>'
      )
      let data = new FormData()
      data.append('_key', 'aecf4790d1cd03c186f56025cd85275aeW9Md3ZWb2lMTnptMEgrWGd2NkVCT2xZeVR5bzBRL05JRDlmSVdqK3FXYnVHb0FuTkxVQ3RxZ1dGcjhVMTNaR0FFT2RmZDRaZ3lnM0w1Sjk2SXcvdVJZRmluRmdPVlQ5YzdnSXAxaHNmamNpWkhGY01kaXZtYmNuOWt4SW1JclQ=')
      data.append('nik', value)
      fetch(`<?= Web::url('ajax.home.konsul') ?>`, {
        method: 'POST',
        body: data
      })
        .then(res => res.json())
        .then(res => {
          if(res !== null) {
            $('[name="nama"]').val(res.nama)
            $('[name="alamat"]').val(res.alamat)
            $('[name="jenis_kelamin"]').val(res.jenis_kelamin)
            $('[name="norm"]').val(res.norm)
            let date = new Date(res.tanggal_kembali)
            date.setHours(0)
            date.setMinutes(0)
            date.setSeconds(0)
            let now = new Date()
            now.setHours(0)
            now.setMinutes(0)
            now.setSeconds(0)
            let diff = (now.getTime() - date.getTime()) / (24 * 60 * 60 * 1000)
            diff = Math.round(diff)
            setTimeout(() => {
              $('body').find('.loading')[0].remove()
              if(diff < 0) {
                $('form').find('.card .card-body').prepend('<div class="alert alert-danger">Sudah pernah melakukan konsultasi, konsultasi selanjutnya tanggal '+ res.tanggal_kembali +'</div>')
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
	})
  $('#tanggal').on('change', function () {
    let $this = $(this)
    let value = $this.val()
    let y = value.substring(6,10)
    let m = value.substring(3,5)
    let d = value.substring(0,2)
    let date = new Date(parseInt(y), parseInt(m), parseInt(d))
    date = new Date(date.getTime() + (10*24*60*60*1000))
    y = date.getFullYear()
    m = date.getMonth()
    d = date.getDate()
    m = ('0' + m).slice(-2)
    d = ('0' + d).slice(-2)
    $('#tanggal_kembali').val(`${d}-${m}-${y}`)
  })
  $('form button[type="reset"]').on('click',function () {
    $('form .card-body').find('.alert')[0].remove()
    $('form button[type="submit"]').prop('disabled', false)
  })
</script>