<h1 class="text-center">Pendaftaran</h1>
<p>Tanggal: <?= Mod::timepiece($data['start']) ?> <?= $data['end'] !== $data['start'] ? 's/d ' . Mod::timepiece($data['end']) : '' ?></p>
<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Tanggal</th>
            <th>Nama</th>
            <th>NIK</th>
            <th>Alamat</th>
            <th>No. RM</th>
            <th>L/P</th>
            <th>Tgl kmbl</th>
        </tr>
    </thead>
    <tbody>
    <?php $no = 1; ?>
    <?php foreach($data['response'] as $d) : ?>
        <tr class="<?= substr($d['tanggal'], 0, 7) !== substr($data['response'][$no - 2]['tanggal'], 0, 7) ? 'border-top-2pt' : '' ?>">
            <td class="text-center"><?= $no ?></td>
            <td><?= Mod::timepiece($d['tanggal']) ?></td>
            <td><?= $d['nama'] !== '' && $d['nama'] !== NULL ? $d['nama'] : '-' ?></td>
            <td><?= $d['nik'] ?></td>
            <td><?= $d['alamat'] !== '' && $d['alamat'] !== NULL ? $d['alamat'] : '-' ?></td>
            <td><?= $d['norm'] !== '' && $d['norm'] !== NULL ? $d['norm'] : '-' ?></td>
            <td class="text-center"><?= $d['jenis_kelamin'] !== '' && $d['jenis_kelamin'] !== NULL ? strtoupper($d['jenis_kelamin']) : '-' ?></td>
            <td><?= Mod::timepiece($d['tanggal_kembali']) ?></td>
        </tr>
        <?php $no++; ?>
    <?php endforeach ?>
    </tbody>
</table>