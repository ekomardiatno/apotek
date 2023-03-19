<div class="card shadow mb-4">
  <div class="card-header border-0">
    <div class="d-flex flex-column-sm mx--3 align-items-center-md">
      <div class="mx-3 flex-fill d-flex align-items-center">
        <h3 class="mb-0 mb-2-sm text-uppercase fw-800"><?= $data['data_obat']['nama_obat'] ?> - <?= $data['data_obat']['satuan_obat'] ?></h3>
      </div>
      <div class="mx-3">
        <div class="mx--1 d-flex">
          <select id="length-riwayat stok" style="width:55px" class="form-control form-control-sm form-control-alternative mb-0 mx-1">
            <option value="10">10</option>
            <option value="20">20</option>
            <option value="30">30</option>
            <option value="40">40</option>
            <option value="50">50</option>
          </select>
          <div class="flex-fill">
            <div class="form-group mb-0 mx-1 flex-fill">
              <div class="input-group input-group-sm input-group-alternative">
                <div class="input-group-prepend">
                  <span class="input-group-text" id="basic-addon1"><span class="fas fa-search"></span></span>
                </div>
                <input type="text" class="form-control form-control-alternative" placeholder="Cari" id="search-riwayat stok">
              </div>
            </div>
          </div>
          <div class="mx-1 d-flex">
            <a href="<?= Web::url($data['type'] === 'masuk' ? ('riwayatstok.' . $data['id'] . '.keluar') : ('riwayatstok.' . $data['id'] . '.masuk')); ?>" class="btn btn-sm btn-primary d-flex align-items-center"><span class="fas fa-history"></span><span class="d-none d-md-inline-block ml-1"><?= $data['type'] === 'masuk' ? 'Stok Keluar' : 'Stok Masuk' ?></span></a>
          </div>
        </div>
      </div>
    </div>
  </div>

  <table id='riwayat-stok-data' class="table align-items-center table-flush" style="width:100%">
    <thead class="thead-light">
      <tr>
        <th>#</th>
        <th>Nama Kategori</th>
        <th>Kuantitas</th>
        <th>Tanggal Dibuat</th>
        <th>Aksi</th>
      </tr>
    </thead>
  </table>

  <script>
    const dataTable = $('#riwayat-stok-data').DataTable({
      responsive: true,
      processing: true,
      serverSide: true,
      serverMethod: 'post',
      ajax: {
        url: '<?= Web::url('riwayatstok.fetch.' . $data['id'] . '.' . $data['type']) ?>',
        data: {
          _key: '<?= getenv('APP_KEY') ?>'
        }
      },
      columns: [{
          data: 'no',
          orderable: false
        },
        {
          data: '<?= $data['type'] === 'masuk' ? 'nama_stok_masuk_kategori' : 'nama_stok_keluar_kategori' ?>'
        },
        {
          data: '<?= $data['type'] === 'masuk' ? 'kuantitas_stok_masuk' : 'kuantitas_stok_keluar' ?>'
        },
        {
          data: 'tanggal_dibuat'
        },
        {
          data: 'pengaturan'
        }
      ],
      order: [
        [3, 'desc']
      ],
      fixedHeader: true,
      lengthChange: false,
      language: {
        emptyTable: "Data riwayat stok tidak tersedia",
        info: "Total _TOTAL_ riwayat stok",
        infoEmpty: "",
        search: "Cari ",
        infoFiltered: "",
        zeroRecords: "Riwayat stok tidak ditemukan",
        paginate: {
          first: '<span class="fas fa-angle-double-left"></span>',
          last: '<span class="fas fa-angle-double-right"></span>',
          previous: '<span class="fas fa-angle-left"></span>',
          next: '<span class="fas fa-angle-right"></span>'
        }
      }
    })

    $('#reload-data-konsul').on('click', function() {
      dataTable.ajax.reload()
    })

    $('#length-riwayat stok').on('change', function() {
      dataTable.page.len(this.value).draw()
    })

    $('#search-riwayat stok').on("keyup", function() {
      dataTable.search(this.value).draw()
    })
  </script>

</div>