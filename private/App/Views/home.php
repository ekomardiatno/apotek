<div class="card shadow mb-4">
  <div class="card-header border-0">
    <div class="d-flex flex-column-sm mx--3 align-items-center-md">
      <div class="mx-3 flex-fill d-flex align-items-center">
        <h3 class="mb-0 mb-2-sm text-uppercase fw-800">Daftar Konsultasi</h3>
      </div>
      <div class="mx-3">
        <div class="mx--1 d-flex">
          <div class="mx-1 flex-fill">
            <div class="form-group mb-0">
              <div class="input-group input-group-sm input-group-alternative">
                <div class="input-group-prepend">
                  <span class="input-group-text" id="basic-addon1"><span class="fas fa-search"></span></span>
                </div>
                <input type="text" class="form-control form-control-alternative" placeholder="Cari" id="search-datatables">
              </div>
            </div>
          </div>
          <?php if(Auth::user('role') === 'konsul'): ?>
          <div class="mx-1 d-flex">
            <a href="<?= Web::url('konsul.daftar'); ?>" class="btn btn-sm btn-primary d-flex align-items-center"><span class="fas fa-plus-circle"></span><span class="d-none d-md-inline-block ml-1">Pendaftaran</span></a>
          </div>
          <?php endif ?>
          <div class="mx-1 d-flex">
            <button type="button" class="btn btn-warning btn-sm mr-0" data-toggle="modal" data-target="#report">
              <span class="fas fa-file-pdf"></span><span class="d-none d-md-inline-block ml-1">Buat Laporan</span>
            </button>
            <!-- Modal -->
            <div class="modal fade" id="report" tabindex="-1" role="dialog" aria-labelledby="reportLabel" aria-hidden="true">
              <div class="modal-dialog modal-sm" role="document">
                <form action="<?= Web::url('print') ?>" target="_blank" method="post">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h5 class="modal-title" id="reportLabel">Buat Laporan</h5>
                      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                      </button>
                    </div>
                    <div class="modal-body">
                      <?= Web::key_field() ?>
                      <div class="form-group">
                        <label class="small form-control-label" for="start">Dari</label>
                        <input type="text" name="start" required date-format="dd-mm-yyyy" id="start" class="form-control form-control-alternative datepicker">
                      </div>
                      <div class="form-group">
                        <label class="small form-control-label" for="end">Sampai</label>
                        <input type="text" name="end" required date-format="dd-mm-yyyy" id="end" class="form-control form-control-alternative datepicker">
                      </div>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                      <button type="submit" class="btn btn-warning">Buat</button>
                    </div>
                  </div>
                </form>
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
        <th scope="col">Tanggal</th>
        <th scope="col">Nama</th>
        <th scope="col">NIK</th>
        <th scope="col">Alamat</th>
        <th scope="col">No. RM</th>
        <th scope="col">L/P</th>
        <th scope="col">Tgl Kmbl.</th>
        <?php if(Auth::user('role') === 'konsul'): ?>
          <th scope="col">Atur Data</th>
        <?php endif ?>
      </tr>
    </thead>
    <tbody>
      <?php $no = 1; ?>
      <?php foreach($data as $d) : ?>
        <tr>
          <td><?= $no; ?></td>
          <td><?= Mod::timepiece($d['tanggal']) ?></td>
          <td><a href="<?= Web::url('pasien.detail.' . $d['nik']) ?>"><?= $d['nama'] !== NULL ? $d['nama'] : '-' ?></a></td>
          <td><?= $d['nik'] !== NULL ? $d['nik'] : '-' ?></td>
          <td><?= $d['alamat'] !== NULL || $d['alamat'] !== '' ? strlen($d['alamat']) > 15 ? substr($d['alamat'], 0, 14) . '...' : $d['alamat'] : '-' ?></td>
          <td><?= $d['norm'] !== NULL ? $d['norm'] : '-' ?></td>
          <td><?= $d['jenis_kelamin'] !== NULL ? strtoupper($d['jenis_kelamin']) : '-' ?></td>
          <td><?= Mod::timepiece($d['tanggal_kembali']) ?></td>
          <?php if(Auth::user('role') === 'konsul'): ?>
            <td>
              <a href="<?= Web::url('konsul.edit.' . $d['id_konsul']) ?>" class="btn btn-warning btn-sm"><span class="fas fa-edit"></span><span class="d-none d-md-inline-block ml-1">Edit</span></a>
              <form class="d-inline-block" id="form-delete-id-<?= $d['id_konsul'] ?>" action="<?= Web::url('konsul.hapus') ?>" method="post">
                <?= Web::key_field() ?>
                <input type="hidden" name="id_konsul" value="<?= $d['id_konsul'] ?>">
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
                        $('form#form-delete-id-<?= $d['id_konsul'] ?>').submit()
                      }
                    }
                  })
                "><span class="fas fa-trash-alt"></span><span class="d-none d-md-inline-block ml-1">Hapus</span></button>
              </form>
            </td>
          <?php endif ?>
        </tr>
        <?php $no++; ?>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>