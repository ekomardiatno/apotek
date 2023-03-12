<div class="card shadow mb-4">
  <div class="card-header border-0">
    <div class="d-flex flex-column-sm mx--3 align-items-center-md">
      <div class="mx-3 flex-fill d-flex align-items-center">
        <h3 class="mb-0 mb-2-sm text-uppercase fw-800">Daftar Resep</h3>
      </div>
      <div class="mx-3">
        <div class="mx--1 d-flex">
          <select id="length-resep" style="width:55px" class="form-control form-control-sm form-control-alternative mb-0 mx-1">
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
                <input type="text" class="form-control form-control-alternative" placeholder="Cari" id="search-resep">
              </div>
            </div>
          </div>
          <?php if (Auth::user('role') === 'konsul') : ?>
            <div class="mx-1 d-flex">
              <a href="<?= $data ? Web::url('resep') : Web::url('resep.all'); ?>" class="btn btn-sm btn-primary d-flex align-items-center"><span class="fas fa-list"></span><span class="d-none d-md-inline-block ml-1"><?= $data ? 'Resep Baru' : 'Lihat Semua' ?></span></a>
            </div>
          <?php endif ?>
        </div>
      </div>
    </div>
  </div>

  <table id='resep-data' class="table align-items-center table-flush" style="width:100%">
    <thead class="thead-light">
      <tr>
        <th>#</th>
        <th>Tanggal/Waktu</th>
        <th>Nama Pasien</th>
        <th>Umur</th>
        <th>Jenis Kelamin</th>
        <th>Resep</th>
        <?php if (Auth::user('role') === 'konsul') : ?>
          <th>Nama Dokter</th>
        <?php endif; ?>
        <?php if (Auth::user('role') === 'konsul' || Auth::user('role') === 'dokter') : ?>
          <th>Aksi</th>
        <?php endif; ?>
      </tr>
    </thead>
  </table>

  <script>
    const dataTable = $('#resep-data').DataTable({
      responsive: true,
      processing: true,
      serverSide: true,
      serverMethod: 'get',
      ajax: {
        url: '<?= Web::url('resep.fetch' . ($data ? '.' . $data : '')) ?>',
        data: {
          _key: '<?= getenv('APP_KEY') ?>'
        }
      },
      columns: [{
          data: 'no',
          orderable: false
        },
        {
          data: 'tanggal'
        },
        {
          data: 'nama'
        },
        {
          data: 'jenis_kelamin'
        },
        {
          data: 'umur'
        },
        {
          data: 'data_resep',
          orderable: false
        },
        <?php if (Auth::user('role') === 'konsul') : ?> {
            data: 'nama_dokter',
            orderable: false
          },
        <?php endif; ?>
        <?php if (Auth::user('role') === 'konsul' || Auth::user('role') === 'dokter') : ?> {
            data: 'pengaturan',
            orderable: false
          }
        <?php endif; ?>
      ],
      order: [
        [1, '<?= Auth::user('role') === 'dokter' || $data ? 'desc' : 'asc' ?>']
      ],
      fixedHeader: true,
      lengthChange: false,
      language: {
        emptyTable: "Data resep tidak tersedia",
        info: "Total _TOTAL_ resep",
        infoEmpty: "",
        search: "Cari ",
        infoFiltered: "",
        zeroRecords: "Resep tidak ditemukan",
        paginate: {
          first: '<span class="fas fa-angle-double-left"></span>',
          last: '<span class="fas fa-angle-double-right"></span>',
          previous: '<span class="fas fa-angle-left"></span>',
          next: '<span class="fas fa-angle-right"></span>'
        }
      }
    })

    $('#length-resep').on('change', function() {
      dataTable.page.len(this.value).draw()
    })

    $('#search-resep').on("keyup", function() {
      dataTable.search(this.value).draw()
    })

    $('body').on('click', '.hapus-data', function(e) {
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
        callback: (result) => {
          if (result) {
            const form = document.createElement('form')
            form.method = 'post'
            form.action = $(this).data('action')
            const fields = [{
                name: '_key',
                value: $(this).data('key')
              },
              {
                name: 'id_konsul',
                value: $(this).data('id')
              }
            ]
            for (let i = 0; i < fields.length; i++) {
              const hiddenField = document.createElement('input')
              hiddenField.type = 'hidden'
              hiddenField.name = fields[i].name
              hiddenField.value = fields[i].value

              form.appendChild(hiddenField)
            }

            document.body.appendChild(form)
            form.submit()
          }
        }
      })
    })
  </script>

</div>