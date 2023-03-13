<?php $flash = Flasher::data(); ?>
<?php $data_resep = $flash['resep'] ?? $data['resep'] ?? [] ?>
<?php $pasien = $data['pasien'] ?? null ?>
<?php $props = ['nama' => 'Nama Pasien', 'jenis_kelamin' => 'Jenis Kelamin', 'umur' => 'Umur', 'alamat' => 'Alamat'] ?>
<div class="row">
  <?php if ($pasien) : ?>
    <div class="col-md-4">
      <div class="card shadow mb-4">
        <div class="card-body mb--3">
          <?php foreach ($pasien as $key => $value) : ?>
            <?php
            switch ($key) {
              case 'jenis_kelamin':
                $value = $value === 'l' ? 'Laki-laki' : 'Perempuan';
            }
            ?>
            <label class="mb-1 small"><?= $props[$key] ?></label>
            <p><?= $value !== '' ? $value : '-' ?></p>
          <?php endforeach ?>
        </div>
      </div>
    </div>
  <?php endif ?>
  <div class="<?= $pasien ? 'col-md-8' : 'col' ?>">
    <form action="<?= isset($data['id_resep']) ? Web::url('resep.update') : Web::url('resep.pos') ?>" method="post">
      <?= Web::key_field() ?>
      <?php if (isset($data['id_konsul'])) : ?>
        <input type="hidden" name="id_konsul" value="<?= $data['id_konsul'] ?>" />
      <?php endif ?>
      <?php if (isset($data['id_resep'])) : ?>
        <input type="hidden" name="id_resep" value="<?= $data['id_resep'] ?>" />
      <?php endif ?>
      <div class="card shadow mb-4">
        <div class="card-body">
          <div class="d-flex align-items-center mb-3" style="gap:1rem">
            <div class="mr-auto font-weight-bold">Buat Resep</div>
            <select class="form-control form-control-alternative" id="pilih-resep-obat" style="width: 260px">
              <option value="">Pilih obat</option>
              <?php foreach ($data['obat'] as $obat) : ?>
                <option value="<?= $obat['id_obat'] ?>"><?= $obat['nama_obat'] ?><?= $obat['satuan_obat'] !== '' ? ' (' . $obat['satuan_obat'] . ')' : '' ?></option>
              <?php endforeach ?>
            </select>
            <button type="button" class="btn btn-primary" id="tambah-resep-obat"><span class="fas fa-plus"></span></button>
          </div>
          <table class="table align-items-center table-flush" id="tabel-resep-obat" style="width:100%">
            <thead class="thead-light">
              <tr>
                <th scope="col">Nama Obat</th>
                <th width="40" scope="col">Qty.<span class="text-danger">*</span></th>
                <th scope="col">Dosis<span class="text-danger">*</span></th>
                <th width="30" scope="col"></th>
              </tr>
            </thead>
            <tbody>
              <?php if (count($data_resep) < 1) : ?>
                <tr id="hapus-ini-nanti">
                  <td colspan="4" class="text-center">Silakan tambahkan obat</td>
                </tr>
              <?php else : ?>
                <?php foreach ($data_resep as $resep) : ?>
                  <tr>
                    <td class="p-2">
                      <input type="hidden" name="data[id_obat][]" value="<?= $resep['id_obat'] ?>">
                      <input type="hidden" name="data[nama_obat][]" value="<?= $resep['nama_obat'] ?>">
                      <span class="font-weight-bold"><?= $resep['nama_obat'] ?></span>
                    </td>
                    <td class="p-2">
                      <input class="form-control form-control-sm" type="number" value="<?= $resep['kuantitas'] ?>" required="" min="1" placeholder="Qty." name="data[kuantitas][]">
                    </td>
                    <td class="p-2">
                      <input class="form-control form-control-sm" type="text" value="<?= $resep['dosis'] ?>" required="" placeholder="Dosis" name="data[dosis][]">
                    </td>
                    <td class="p-2">
                      <button type="button" class="btn btn-danger btn-sm" onclick="hapusBarisObat(this)"><span class="fas fa-times"></span></button>
                    </td>
                  </tr>
                <?php endforeach ?>
              <?php endif ?>
            </tbody>
          </table>
        </div>
        <div class="card-footer d-flex">
          <a href="<?= isset($data['id_resep']) ? Web::url('resep') : Web::url('konsul'); ?>" class="btn btn-secondary ml-auto">Batal</a>
          <button type="submit" class="btn btn-primary" id="tombol-simpan-resep">Simpan</button>
        </div>
      </div>
    </form>
  </div>
</div>

<script>
  <?php if (count($data_resep) < 1) : ?>
    $('#tombol-simpan-resep').attr('disabled', true)
  <?php endif ?>

  function hapusBarisObat(e) {
    const tr = $(e.target ?? e).parents('tr')
    if (tr.length < 1) return
    const tableBody = tr[0].parentNode
    const parentChildLength = tableBody.children.length
    tr[0].remove()
    if (parentChildLength < 2) {
      $('#tombol-simpan-resep').attr('disabled', true)
      $(tableBody).append(`
          <tr id="hapus-ini-nanti">
            <td colspan="4" class="text-center">Silakan tambahkan obat</td>
          </tr>
        `)
    }
  }

  $('#tambah-resep-obat').on('click', e => {
    const select = $('#pilih-resep-obat').length > 0 ? $('#pilih-resep-obat')[0] : null
    if (!select) return flashMessage('ni ni-fat-remove', 'Tidak dapat menambahkan obat', 'danger', 'top', 'center')
    if (!select.value) return flashMessage('ni ni-fat-remove', 'Silakan pilih obat terlebih dahulu!', 'danger', 'top', 'center')
    const textSelectedOption = $(select).find('option:selected').text()
    const tr = document.createElement('tr')
    const tableBody = $('#tabel-resep-obat tbody')
    const tableBodyChildLength = tableBody[0].children.length
    if (tableBodyChildLength > 0 && tableBody[0].children[0].id === 'hapus-ini-nanti') {
      tableBody[0].children[0].remove()
    }
    if (tableBodyChildLength > 0 && tableBody.find(`input[value=${select.value}]`).length > 0) return flashMessage('ni ni-fat-remove', `${textSelectedOption} telah ditambahkan.`, 'danger', 'top', 'center')
    const td = () => {
      const el = document.createElement('td')
      el.className = 'p-2'
      return el
    }
    const element = (type) => document.createElement(type)
    const namaObatCol = td()
    const qtyCol = td()
    const dosisCol = td()
    const actionCol = td()
    const inputIdObat = element('input')
    inputIdObat.type = 'hidden'
    inputIdObat.name = `data[id_obat][]`
    inputIdObat.value = select.value
    namaObatCol.append(inputIdObat)
    const inputNamaObat = element('input')
    inputNamaObat.type = 'hidden'
    inputNamaObat.name = `data[nama_obat][]`
    inputNamaObat.value = textSelectedOption
    namaObatCol.append(inputNamaObat)
    const spanNamaObat = element('span')
    spanNamaObat.className = 'font-weight-bold'
    spanNamaObat.textContent = textSelectedOption
    namaObatCol.append(spanNamaObat)
    tr.append(namaObatCol)
    const qtyInput = element('input')
    qtyInput.className = 'form-control form-control-sm'
    qtyInput.type = 'number'
    qtyInput.required = true
    qtyInput.min = 1
    qtyInput.placeholder = 'Qty.'
    qtyInput.name = `data[kuantitas][]`
    qtyCol.append(qtyInput)
    tr.append(qtyCol)
    const dosisInput = element('input')
    dosisInput.className = 'form-control form-control-sm'
    dosisInput.type = 'text'
    dosisInput.required = true
    dosisInput.placeholder = 'Dosis'
    dosisInput.name = `data[dosis][]`
    dosisCol.append(dosisInput)
    tr.append(dosisCol)
    const removeButton = element('button')
    removeButton.type = 'button'
    removeButton.className = 'btn btn-danger btn-sm'
    removeButton.addEventListener('click', hapusBarisObat)
    const spanIconButton = element('span')
    spanIconButton.className = 'fas fa-times'
    removeButton.append(spanIconButton)
    actionCol.append(removeButton)
    tr.append(actionCol)
    tableBody[0].append(tr)
    select.value = ''
    $('#tombol-simpan-resep').attr('disabled', false)
  })
</script>