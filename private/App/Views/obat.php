<div class="card shadow mb-4">
  <div class="card-header border-0">
    <div class="d-flex flex-column-sm mx--3 align-items-center-md">
      <div class="mx-3 flex-fill d-flex align-items-center">
        <h3 class="mb-0 mb-2-sm text-uppercase fw-800">Daftar Obat</h3>
      </div>
      <div class="mx-3">
        <div class="mx--1 d-flex">
          <select id="length-obat" style="width:55px" class="form-control form-control-sm form-control-alternative mb-0 mx-1">
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
                <input type="text" class="form-control" placeholder="Cari" id="search-obat">
              </div>
            </div>
          </div>
          <?php if (Auth::user('role') === 'farma') : ?>
            <div class="mx-1 d-flex">
              <a href="<?= Web::url('obat.tambah'); ?>" class="btn btn-sm btn-primary d-flex align-items-center"><span class="fas fa-plus-circle"></span><span class="d-none d-md-inline-block ml-1">Tambah Obat</span></a>
            </div>
          <?php endif ?>
        </div>
      </div>
    </div>
  </div>
  <!-- Projects table -->
  <table id="obat-data" class="table align-items-center table-flush" style="width:100%">
    <thead class="thead-light">
      <tr>
        <th scope="col">#</th>
        <th scope="col">Nama Obat</th>
        <th scope="col">Satuan</th>
        <th scope="col">Stok</th>
        <th scope="col">Deskripsi</th>
        <?php if (Auth::user('role') === 'farma') : ?>
          <th scope="col">Aksi</th>
        <?php endif; ?>
      </tr>
    </thead>
  </table>

  <div class="modal fade" id="stokObat" tabindex="-1" role="dialog" aria-labelledby="stokObatLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
      <form action="<?= Web::url('obat.stok') ?>" method="post">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="stokObatLabel">Stok Obat</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <?= Web::key_field() ?>
            <div class="form-group form-group-stok-kategori">
              <label class="small form-control-label" for="nama_kategori">Kategori<span class="text-danger">*</span></label>
              <input type="hidden" name="id_kategori" />
              <input type="text" autocomplete="off" required id="nama_kategori" maxlength="150" name="nama_kategori" placeholder="Pilih atau masukan kategori baru" class="form-control form-control-alternative">
            </div>
            <div class="form-group">
              <label for="kuantitas" class="small form-control-label">Kuantitas<span class="text-danger">*</span></label>
              <input type="number" required name="kuantitas" id="kuantitas" min="0" class="form-control form-control-alternative" placeholder="Kuantitas">
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-primary">Simpan</button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <script>
    const dataTable = $('#obat-data').DataTable({
      responsive: true,
      processing: true,
      serverSide: true,
      serverMethod: 'get',
      ajax: {
        url: '<?= Web::url('obat.fetch') ?>',
        data: {
          _key: '<?= getenv('APP_KEY') ?>'
        }
      },
      columns: [{
          data: 'no',
          orderable: false
        },
        {
          data: 'nama_obat'
        },
        {
          data: 'satuan_obat'
        },
        {
          data: 'stok_obat',
          orderable: false
        },
        {
          data: 'deskripsi_obat',
          orderable: false
        },
        <?php if (Auth::user('role') === 'farma') :
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
        emptyTable: "Data obat tidak tersedia",
        info: "Total _TOTAL_ obat",
        infoEmpty: "",
        search: "Cari ",
        infoFiltered: "",
        zeroRecords: "Obat tidak ditemukan",
        paginate: {
          first: '<span class="fas fa-angle-double-left"></span>',
          last: '<span class="fas fa-angle-double-right"></span>',
          previous: '<span class="fas fa-angle-left"></span>',
          next: '<span class="fas fa-angle-right"></span>'
        }
      }
    })

    $('#length-obat').on('change', function() {
      dataTable.page.len(this.value).draw()
    })

    $('#search-obat').on("keyup", function() {
      dataTable.search(this.value).draw()
    })
  </script>

  <script>
    $('body').on('click', '.stock-btn', e => {
      if ([...e.target.classList].map(a => a).indexOf('stock-btn') < 0) {
        e = e.target.parentNode
      } else {
        e = e.target
      }
      const data = e.dataset
      const form = $('#stokObat').find('form')
      form.prepend(`<input type='hidden' name='id_obat' value='${data.id}'/>`)
      form.prepend(`<input type='hidden' name='type' value='${data.type}'/>`)
      form.prepend(`<input type='hidden' name='stok_obat' value='${data.qty}'/>`)
      const idKeyName = data.type === 'add' ? 'id_stok_masuk_kategori' : 'id_stok_keluar_kategori'
      const nameKeyName = data.type === 'add' ? 'nama_stok_masuk_kategori' : 'nama_stok_keluar_kategori'
      let inputKategori = $('#stokObat').find('input#nama_kategori')
      let inputKuantitas = $('#stokObat').find('input#kuantitas')
      if (inputKuantitas.length > 0) {
        console.log(inputKuantitas, data.qty)
        inputKuantitas[0].max = data.type !== 'add' ? data.qty : ''
      }
      if ($('#stokObat').find('h5.modal-title').length > 0) {
        $('#stokObat').find('h5.modal-title')[0].textContent = `${data.name} - ${data.type === 'add' ? 'Stok Masuk' : 'Stok Keluar'}`
      }
      if (inputKategori.length < 1) return
      fetch(data.type === 'add' ? `<?= Web::url('stokmasukkategori') ?>` : `<?= Web::url('stokkeluarkategori') ?>`)
        .then(res => res.json())
        .then(res => {
          inputKategori = inputKategori[0]
          let inputIdKategori = (inputKategori.previousElementSibling?.nodeName || '') === 'INPUT' ? inputKategori.previousElementSibling : null
          const showingDropdown = e => {
            if (e.target.nextElementSibling) inputKategori.nextElementSibling?.remove()
            const value = e.target.value
            const div = document.createElement('div')
            div.className = 'dropdown-menu position-absolute left-0 w-100 show'
            let datas = res.filter(a => (a[nameKeyName].toLowerCase().indexOf(value.toLowerCase()) > -1))
            for (let data of datas) {
              const a = document.createElement('a')
              a.className = 'dropdown-item cursor-pointer'
              if (inputIdKategori && inputIdKategori.value === data[idKeyName]) {
                a.className = a.className + ' bg-primary text-white'
              }
              a.textContent = data[nameKeyName]
              a.addEventListener('click', a => {
                inputKategori.value = data[nameKeyName]
                if (inputIdKategori) inputIdKategori.value = data[idKeyName]
                inputKategori.nextElementSibling?.remove()
              })
              div.append(a)
            }
            if (datas.length > 0)
              $(e.target).after(div)
          }
          inputKategori.addEventListener('focus', showingDropdown)
          inputKategori.addEventListener('keyup', showingDropdown)
          window.addEventListener('click', e => {
            let target = false
            let a = e.target
            if (!a.classList?.contains('form-group-stok-kategori') || false)
              while (a) {
                if (a?.classList?.contains('form-group-stok-kategori') || false) return
                a = a.parentNode
              }

            const valueByInputKategori = res.filter(a => a[nameKeyName] === inputKategori.value)
            if (valueByInputKategori.length < 1) {
              if (inputIdKategori) inputIdKategori.value = ''
            } else {
              if (inputIdKategori) inputIdKategori.value = valueByInputKategori[0][idKeyName]
            }
            inputKategori.nextElementSibling?.remove()
          })
          $('#stokObat').modal()
        })
        .catch(err => {
          flashMessage('ni ni-fat-remove', 'Gagal menemukan data!', 'danger', 'top', 'right')
        })
    })
  </script>
</div>