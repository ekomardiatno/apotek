<form action="" method="post">
  <div class="card shadow mb-4">
    <div class="card-body">
      <div class="form-group">
        <label class="small form-control-label" for="tanggal">Tanggal<span class="text-danger">*</span></label>
        <input type="text" name="tanggal" required id="tanggal" class="form-control form-control-alternative datepicker">
      </div>
      <div class="form-group">
        <label class="small form-control-label" for="nik">NIK<span class="text-danger">*</span></label>
        <input type="text" maxlength="16" placeholder="Cth: 1402021601950001" required name="nik" id="nik" class="form-control form-control-alternative">
      </div>
      <div class="form-group">
        <label class="small form-control-label" for="nama">Nama<span class="text-danger">*</span></label>
        <input type="text" maxlength="100" placeholder="Cth: Rani Fauziah" required name="nama" id="nama" class="form-control form-control-alternative">
      </div>
      <div class="form-group">
        <label class="small form-control-label" for="alamat">Alamat</label>
        <textarea name="alamat" placeholder="Alamat" id="alamat" class="form-control form-control-alternative"></textarea>
      </div>
      <div class="form-group">
        <label class="small form-control-label" for="norm">No. Rekam Medis<span class="text-danger">*</span></label>
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
        <?php
          $now = date('Y-m-d');
          $date = date('d-m-Y', strtotime('+10 days', strtotime($now)));
        ?>
        <input readonly required type="text" name="tanggal_kembali" id="tanggal_kembali" value='<?= $date ?>' class="form-control form-control-alternative">
      </div>
    </div>
    <div class="card-footer text-right">
      <button class="btn btn-secondary" type="reset">Reset</button>
      <button class="btn btn-success" type="submit">Daftarkan</button>
    </div>
  </div>
</form>

<script>
  $('#nik').autocomplete('https://ekomardiatno.site/copek/api/multi', {
		_key: '1e8ea951cc65b9df925d6f82a947df5fY3l4c3BSUEZtVVJ4Vnd6R3ZzbU5yQlZKZ0RNVVpBSXlLTDdGUTRxY3hmRktrMHNoT2xuTHkyMjRRa1dhZ3ZES1V2WWhBNlg1dE5Tc1hTUDdyZXRYSDVWTC9LaEg1bE5JeTRZbzNMTGNjdzQ5cEpsUWd6cDVVd0lXM3FBT1dXZ2xzU0crUjhDQ3puT3FsTFQ1OXJaamlnPT0='
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
</script>