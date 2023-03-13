<div class="card shadow mb-4">
  <div class="card-header border-0">
    <div class="d-flex flex-column-sm mx--3 align-items-center-md">
      <div class="mx-3 flex-fill d-flex align-items-center">
        <h3 class="mb-0 mb-2-sm text-uppercase fw-800">Daftar Pasien</h3>
      </div>
      <div class="mx-3">
        <div class="mx--1 d-flex">
          <select id="length-pasien" style="width:55px" class="form-control form-control-sm form-control-alternative mb-0 mx-1">
            <option value="10">10</option>
            <option value="20">20</option>
            <option value="30">30</option>
            <option value="40">40</option>
            <option value="50">50</option>
          </select>
          <div class="mx-1 flex-fill">
            <div class="form-group mb-0">
              <div class="input-group input-group-sm input-group-alternative">
                <div class="input-group-prepend">
                  <span class="input-group-text" id="basic-addon1"><span class="fas fa-search"></span></span>
                </div>
                <input type="text" class="form-control" placeholder="Cari" id="search-pasien">
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- Projects table -->
  <table id="pasien-data" class="table align-items-center table-flush" style="width:100%">
    <thead class="thead-light">
      <tr>
        <th scope="col">#</th>
        <th scope="col">Nama</th>
        <th scope="col">NIK</th>
        <th scope="col">No. RM</th>
        <th scope="col">L/P</th>
        <th scope="col">Tanggal Lahir</th>
        <?php if (Auth::user('role') === 'konsul') : ?>
          <th scope="col">Aksi</th>
        <?php endif; ?>
      </tr>
    </thead>
  </table>

  <script>
    const dataTable = $('#pasien-data').DataTable({
      responsive: true,
      processing: true,
      serverSide: true,
      serverMethod: 'get',
      ajax: {
        url: '<?= Web::url('pasien.fetch') ?>',
        data: {
          _key: '<?= getenv('APP_KEY') ?>'
        }
      },
      columns: [{
          data: 'no',
          orderable: false
        },
        {
          data: 'nama'
        },
        {
          data: 'nik'
        },
        {
          data: 'norm'
        },
        {
          data: 'jenis_kelamin'
        },
        {
          data: 'tanggal_lahir'
        },
        <?php if (Auth::user('role') === 'konsul') :
          echo "{
            data: 'pengaturan',
            orderable: false
          }";
        endif; ?>
      ],
      order: [
        [1, 'asc']
      ],
      fixedHeader: true,
      lengthChange: false,
      language: {
        emptyTable: "Data pasien tidak tersedia",
        info: "Total _TOTAL_ pasien",
        infoEmpty: "",
        search: "Cari ",
        infoFiltered: "",
        zeroRecords: "Pasien tidak ditemukan",
        paginate: {
          first: '<span class="fas fa-angle-double-left"></span>',
          last: '<span class="fas fa-angle-double-right"></span>',
          previous: '<span class="fas fa-angle-left"></span>',
          next: '<span class="fas fa-angle-right"></span>'
        }
      }
    })

    $('#length-pasien').on('change', function() {
      dataTable.page.len(this.value).draw()
    })

    $('#search-pasien').on("keyup", function() {
      dataTable.search(this.value).draw()
    })
  </script>
</div>