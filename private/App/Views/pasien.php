<div class="card shadow mb-4">
  <div class="card-header border-0">
    <div class="d-flex flex-column-sm mx--3 align-items-center-md">
      <div class="mx-3 flex-fill d-flex align-items-center">
        <h3 class="mb-0 mb-2-sm text-uppercase fw-800">Daftar Pasien</h3>
      </div>
      <div class="mx-3">
        <div class="mx--1 d-flex">
          <div class="mx-1 flex-fill">
            <div class="form-group mb-0">
              <div class="input-group input-group-sm input-group-alternative">
                <div class="input-group-prepend">
                  <span class="input-group-text" id="basic-addon1"><span class="fas fa-search"></span></span>
                </div>
                <input type="text" class="form-control" placeholder="Cari" id="search-datatables">
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- Projects table -->
  <table class="table datatables align-items-center table-flush">
    <thead class="thead-light">
      <tr>
        <th scope="col">No</th>
        <th scope="col">NIK</th>
        <th scope="col">Nama</th>
        <th scope="col">Alamat</th>
        <th scope="col">No. RM</th>
        <th scope="col">L/P</th>
        <th scope="col">Terdaftar pada</th>
        <th scope="col">Atur Data</th>
      </tr>
    </thead>
    <tbody>
      <?php $no = 1; ?>
      <?php foreach($data as $d): ?>
        <tr>
          <td><?= $no ?></td>
          <td><?= $d['nik'] ?></td>
          <td><?= $d['nama'] ?></td>
          <td><?= $d['alamat'] !== '' ? strlen($d['alamat']) > 15 ? substr($d['alamat'], 0, 14) . '...' : $d['alamat'] : '-' ?></td>
          <td><?= $d['norm'] ?></td>
          <td><?= strtoupper($d['jenis_kelamin']) ?></td>
          <td><?= Mod::timepiece($d['tanggal_dibuat']) ?></td>
          <td>
            <a href="<?= Web::url('pasien.edit.' . $d['nik']) ?>" class="btn btn-warning btn-sm"><span class="fas fa-edit"></span><span class="d-none d-md-inline-block ml-1">Edit</span></a>
            <form class="d-inline-block" id="form-delete-id-<?= $d['nik'] ?>" action="<?= Web::url('pasien.hapus') ?>" method="post">
              <?= Web::key_field() ?>
              <input type="hidden" name="nik" value="<?= $d['nik'] ?>">
              <button type="button" class="btn btn-danger btn-sm" onclick="
                bootbox.confirm({
                  message: 'Apakah Anda yakin akan menghapus data?',
                  buttons: {
                    confirm: {
                      label: 'Hapus',
                      className: 'btn-secondary btn-sm'
                    },
                    cancel: {
                      label: 'Batal',
                      className: 'btn-primary btn-sm'
                    }
                  },
                  callback: function (result) {
                    if(result) {
                      $('form#form-delete-id-<?= $d['nik'] ?>').submit()
                    }
                  }
                })
              "><span class="fas fa-trash-alt"></span><span class="d-none d-md-inline-block ml-1">Hapus</span></button>
            </form>
          </td>
        </tr>
        <?php $no++; ?>
      <?php endforeach ?>
    </tbody>
  </table>
</div>