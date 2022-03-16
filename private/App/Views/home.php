<div class="card shadow mb-4">
  <div class="card-header border-0">
    <div class="d-flex flex-column-sm mx--3 align-items-center-md">
      <div class="mx-3 flex-fill d-flex align-items-center">
        <h3 class="mb-0 mb-2-sm text-uppercase fw-800">Daftar Konsultasi</h3>
      </div>
      <div class="mx-3">
        <div class="mx--1 d-flex">
          <select id="length-konsultasi" style="width:55px" class="form-control form-control-sm form-control-alternative mb-0 mx-1">
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
                <input type="text" class="form-control form-control-alternative" placeholder="Cari" id="search-konsultasi">
              </div>
            </div>
          </div>
          <?php if (Auth::user('role') === 'konsul') : ?>
            <div class="mx-1 d-flex">
              <a href="<?= Web::url('konsul.daftar'); ?>" class="btn btn-sm btn-primary d-flex align-items-center"><span class="fas fa-plus-circle"></span><span class="d-none d-md-inline-block ml-1">Pendaftaran</span></a>
            </div>
          <?php endif ?>
          <div class="mx-1 d-flex">
            <button type="button" class="btn btn-gray btn-sm mr-0" data-toggle="modal" data-target="#report">
              <span class="fas fa-file-pdf"></span><span class="d-none d-md-inline-block ml-1">Buat Laporan</span>
            </button>
            <!-- Modal -->
            <div class="modal fade" id="report" tabindex="-1" role="dialog" aria-labelledby="reportLabel" aria-hidden="true">
              <div class="modal-dialog modal-sm" role="document">
                <form action="<?= Web::url('print') ?>" id="print" target="_blank" method="post">
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
                        <input type="text" name="start" required date-format="dd-mm-yyyy" value="<?= date('01-m-Y') ?>" id="start" class="form-control form-control-alternative datepicker">
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

  <table id='konsultasi-data' class="table align-items-center table-flush" style="width:100%">
    <thead class="thead-light">
      <tr>
        <th>#</th>
        <th>Tanggal</th>
        <th>Nama</th>
        <th>L/P</th>
        <th>Tgl Kmbl</th>
        <th>Aksi</th>
      </tr>
    </thead>
  </table>

  <script>
    $('#print').on('submit', function () {
      $('#report').modal('hide')
    })
  </script>

  <script>
    const dataTable = $('#konsultasi-data').DataTable({
      responsive: true,
      processing: true,
      serverSide: true,
      serverMethod: 'post',
      ajax: {
        url: '<?= Web::url('home.fetch') ?>',
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
          data: 'tanggal_kembali'
        },
        {
          data: 'pengaturan',
          orderable: false
        }
      ],
      order: [
        [1, 'desc']
      ],
      fixedHeader: true,
      lengthChange: false,
      language: {
        emptyTable: "Data konsultasi tidak tersedia",
        info: "Total _TOTAL_ konsultasi",
        infoEmpty: "",
        search: "Cari ",
        infoFiltered: "",
        zeroRecords: "Konsultasi tidak ditemukan",
        paginate: {
          first: '<span class="fas fa-angle-double-left"></span>',
          last: '<span class="fas fa-angle-double-right"></span>',
          previous: '<span class="fas fa-angle-left"></span>',
          next: '<span class="fas fa-angle-right"></span>'
        }
      }
    })

    $('#length-konsultasi').on('change', function() {
      dataTable.page.len(this.value).draw()
    })

    $('#search-konsultasi').on("keyup", function() {
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