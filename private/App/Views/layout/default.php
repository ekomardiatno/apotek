<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

  <link rel="shortcut icon" href="<?= Web::assets('brand/favicon.png', 'images'); ?>" type="image/x-icon">

  <title><?= $title != '' ? $title . ' | ' . getenv('APP_NAME') : getenv('APP_NAME') ?></title>
  <meta content="<?= $desc; ?>" name="description" />
  <base href="<?= Web::url() ?>">

  <link href="<?= Web::assets('open-sans.css', 'css') ?>" rel="stylesheet">
  <link rel="stylesheet" href="<?= Web::assets('nucleo.css', 'css') ?>">
  <link rel="stylesheet" href="<?= Web::assets('bootstrap-datepicker.min.css', 'css') ?>">
  <link rel="stylesheet" href="<?= Web::assets('Chart.min.css', 'css') ?>">
  <link rel="stylesheet" href="<?= Web::assets('nouislider.min.css', 'css') ?>">
  <link rel="stylesheet" href="<?= Web::assets('animate.min.css', 'css') ?>">
  <link rel="stylesheet" href="<?= Web::assets('datatables.min.css', 'css') ?>">
  <link rel="stylesheet" href="<?= Web::assets('argon.css', 'css') ?>">
  <link rel="stylesheet" href="<?= Web::assets('font-awesome.min.css', 'css') ?>">

  <script src="<?= Web::assets('jquery.min.js', 'js') ?>"></script>
  <script src="<?= Web::assets('bootstrap.bundle.min.js', 'js') ?>"></script>
  <script src="<?= Web::assets('nouislider.min.js', 'js') ?>"></script>
  <script src="<?= Web::assets('bootstrap-datepicker.min.js', 'js') ?>"></script>
  <script src="<?= Web::assets('bootstrap-datepicker.id.min.js', 'locales') ?>"></script>
  <script src="<?= Web::assets('bootstrap-notify.min.js', 'js') ?>"></script>
  <script src="<?= Web::assets('datatables.min.js', 'js') ?>"></script>
  <script src="<?= Web::assets('flash-message.min.js', 'js') ?>"></script>
  <script src="<?= Web::assets('input-foto.js', 'js') ?>"></script>
  <script src="<?= Web::assets('argon.min.js', 'js') ?>"></script>
  <script src="<?= Web::assets('autocomplete.js', 'js') ?>"></script>
  <script src="<?= Web::assets('bootbox.min.js', 'js') ?>"></script>
  <script src="<?= Web::assets('bootbox.locales.min.js', 'js') ?>"></script>
  <script>
    function dateFormat(date) {
      let y = parseInt(date.substr(0, 4))
      let m = parseInt(date.substr(5, 2))
      let d = parseInt(date.substr(8, 2))

      const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

      return `${d} ${months[m - 1]} ${y}`
    }
  </script>

  <style>
    .navi-link ul {
      margin: 0;
      padding: 0;
      list-style: none;
      display: flex;
      flex-direction: row;
    }

    .navi-link ul li a {
      color: #fff;
      padding: .25rem .5rem;
      font-size: .8rem;
      font-weight: 700;
      text-transform: uppercase;
    }

    .navi-link ul li a.active {
      color: #fba840;
    }

    ul.timeline {
      list-style-type: none;
      position: relative;
      padding-left: 1.25rem;
    }

    ul.timeline:before {
      content: ' ';
      background: #d4d9df;
      display: inline-block;
      position: absolute;
      left: 9px;
      width: 2px;
      top: 2px;
      bottom: 6px;
      z-index: 400;
    }

    ul.timeline>li {
      margin-bottom: 1rem;
      padding-left: 20px;
      position: relative;
    }

    ul.timeline>li:last-child {
      margin-bottom: 0;
    }

    ul.timeline>li:before {
      content: ' ';
      background: white;
      display: inline-block;
      position: absolute;
      border-radius: 50%;
      border: 4px solid #2b7cf7;
      left: -20px;
      top: 2px;
      width: 20px;
      height: 20px;
      z-index: 400;
    }

    .main-content.default {
      min-height: 100vh
    }

    .cursor-pointer,
    .btn {
      cursor: pointer;
    }
  </style>

<body>
  <div class="main-content default">

    <div class="header bg-gradient-primary pb-8 pt-5 pt-md-5">
      <div class="container px-lg-3 px-xl-6 mt--5">
        <div class="d-flex flex-row align-items-center justify-content-between mx--2 mb-4 mt-3">
          <div class="py-2 mx-2">
            <img height="40" src="<?= Web::assets('brand/white.png', 'images'); ?>" />
          </div>
          <div class="navi-link ml-2 mr-0">
            <ul>
              <li>
                <a href="<?= Web::url() ?>"><span class="fas fa-notes-medical"></span><span class="d-none d-md-inline-block ml-2">Konsultasi</span></a>
              </li>
              <?php if (Auth::user('role') !== 'dokter') : ?>
                <li>
                  <a href="<?= Web::url('pasien') ?>"><span class="fas fa-user-injured"></span><span class="d-none d-md-inline-block ml-2">Pasien</span></a>
                </li>
              <?php endif; ?>
              <?php if (Auth::user('role') === 'farma') : ?>
                <li>
                  <a href="<?= Web::url('obat') ?>"><span class="fas fa-capsules"></span><span class="d-none d-md-inline-block ml-2">Obat</span></a>
                </li>
              <?php endif; ?>
              <?php if (Auth::user('role') === 'dokter' || Auth::user('role') === 'konsul') : ?>
                <li>
                  <a href="<?= Web::url('resep') ?>"><span class="fas fa-capsules"></span><span class="d-none d-md-inline-block ml-2">Resep</span></a>
                </li>
              <?php endif; ?>
              <?php if (Auth::user('role') === 'farma') : ?>
                <li>
                  <a href="<?= Web::url('dokter') ?>"><span class="fas fa-user-md"></span><span class="d-none d-md-inline-block ml-2">Dokter</span></a>
                </li>
              <?php endif; ?>
              <li>
                <a href="<?= Web::url('profil') ?>"><span class="fas fa-user-cog"></span><span class="d-none d-md-inline-block ml-2"><?= Auth::user('name') ?></span></a>
              </li>
              <?php if (Auth::user('role') === 'farma') : ?>
                <li>
                  <a href="<?= Web::url('pengaturan') ?>"><span class="fas fa-cog"></span><span class="d-none d-md-inline-block ml-2">Pengaturan</span></a>
                </li>
              <?php endif ?>
            </ul>
          </div>
        </div>
      </div>
    </div>

    <div class="container px-lg-3 px-xl-6 mt--8">
      <div class="row">
        <div class="col-md-12">
          <?php
          if ($breadcrumb !== null || $title !== '') :
          ?>
            <div class="row align-items-center">
              <div class="col mb-3">
                <?php
                if ($title !== '') :
                ?>
                  <h6 class="h2 text-white d-inline-block mb-2 mb-lg-0 breadcrumb-title mr-md-3"><?= $title ?></h6>
                <?php
                endif
                ?>

                <?php
                if ($breadcrumb !== null) :
                ?>
                  <nav aria-label="breadcrumb" class="d-block d-lg-inline-block">
                    <ol class="breadcrumb breadcrumb-links breadcrumb-dark">
                      <li class="breadcrumb-item"><a href="<?= Web::url() ?>"><i class="fas fa-home"></i></a></li>
                      <?php
                      $breadcrumbLength = count($breadcrumb);
                      $number = 1;
                      foreach ($breadcrumb as $b) :
                      ?>
                        <li class="breadcrumb-item <?= $breadcrumbLength === $number ? 'active' : '' ?>" <?= $breadcrumbLength === $number ? 'aria-current="page"' : '' ?>>
                          <?= $breadcrumbLength !== $number ? '<a href="' . Web::url($b[0]) . '">' . $b[1] . '</a>' : $b[1] ?>
                        </li>
                      <?php
                        $number++;
                      endforeach
                      ?>
                    </ol>
                  </nav>
                <?php
                endif
                ?>
              </div>
            </div>
          <?php
          endif
          ?>
          <!-- Content -->
          <?php require_once $content; ?>
          <!-- End Content -->

        </div>
      </div>
    </div>

    <footer class="footer px-0 mt-1">
      <div class="container px-lg-3 px-xl-6">
        <div class="row align-items-center justify-content-lg-between">
          <div class="col-sm">
            <div class="copyright text-center text-sm-left mb-1 mb-sm-0">
              &copy; <?= date('Y') ?> <a href="<?= Web::url() ?>" class="font-weight-bold ml-1" target="_blank"><?= getenv('APP_NAME') ?></a>
            </div>
          </div>
          <div class="col-sm">
            <div class="copyright text-center text-sm-right">
              Powered by <a href="https://ekomardiatno.github.io" class="text-default"><span class="font-weight-bold">KOMA-MVC</span></a>
            </div>
          </div>
        </div>
      </div>
    </footer>
  </div>

  <?php
  $msg = Flasher::flash();
  if ($msg != null) {
  ?>
    <script>
      $(function() {
        flashMessage('<?= $msg['icon']; ?>', '<?= $msg['msg']; ?>', '<?= $msg['type']; ?>', '<?= $msg['y']; ?>', '<?= $msg['x']; ?>')
      })
    </script>
  <?php } ?>


  <script>
    let baseHref = $('base').attr('href')
    let windowUrl = window.location.href
    $('#sidenav-collapse-main .navbar-nav .nav-item').each(function() {
      let navLink = $(this).children('.nav-link')
      let aHref = navLink.attr('href')
      if (baseHref !== aHref) {
        let baseHrefSlice = windowUrl.replace(baseHref, '')
        let aHrefSlice = aHref.replace(baseHref, '')
        baseHrefSlice.indexOf(aHrefSlice) > -1 ?
          navLink.addClass('active') :
          null
      } else {
        aHref === windowUrl ?
          navLink.addClass('active') :
          null
      }
    })
  </script>

  <script>
    $('.datatables').each(function() {
      $.fn.DataTable.ext.pager.numbers_length = 4;
      let datatables = $(this).DataTable({
        responsive: true,
        pageLength: 10,
        lengthChange: false,
        // searching: false,
        language: {
          emptyTable: "Data tidak tersedia",
          info: "_START_ - _END_ dari _TOTAL_ data",
          infoEmpty: "",
          search: "Cari ",
          infoFiltered: "",
          zeroRecords: "Kata kunci tidak ditemukan",
          paginate: {
            first: '<span class="fas fa-angle-double-left"></span>',
            last: '<span class="fas fa-angle-double-right"></span>',
            previous: '<span class="fas fa-angle-left"></span>',
            next: '<span class="fas fa-angle-right"></span>'
          }
        }
      })
      $(this).parents('.card').find('#search-datatables').on("keyup", function() {
        datatables.search(this.value).draw()
      })
    })
  </script>

  <script>
    $('.datepicker').each(function() {
      let format = 'yyyy-mm-dd'
      if ($(this).attr('date-format')) {
        format = $(this).attr('date-format')
      }
      let datepicker = $(this).datepicker({
        disableTouchKeyboard: true,
        autoclose: true,
        language: 'id',
        format: format
      })
      // if ($(this).val() === '') {
      //   datepicker.datepicker("setDate", new Date())
      // }
    })
  </script>

  <script>
    $('.navi-link li a').each(function() {
      let href = this.href
      let url = window.location.href
      let baseUrl = $('base').attr('href')

      if (url === href) {
        $(this).addClass('active')
      }
    })
  </script>

  <script>
    $('body').on('click', 'button[type=submit]', function(e) {
      let form = $(e.target).parents('form')
      if (form.length < 1) return
      form = form[0]
      const inputs = [...form].filter(a => (a.name && a.name !== '_key'))
      let inputValid = true
      for (let input of inputs) {
        if (!input.checkValidity()) {
          inputValid = false
          break
        }
      }
      if (!inputValid) return
      e.preventDefault()
      bootbox.confirm({
        message: 'Apakah Anda yakin ingin menyimpan data?',
        buttons: {
          confirm: {
            label: 'Simpan',
            className: 'btn-secondary btn-sm'
          },
          cancel: {
            label: 'Batal',
            className: 'btn-primary btn-sm'
          }
        },
        callback: (result) => {
          if (result) {
            form.submit()
          }
        }
      })
    })
  </script>

  <script>
    $('.username-form').on('keyup', e => {
      e.target.value = e.target.value.replace(/[^a-zA-Z0-9_]/g, '')
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
                name: $(this).data('keyid'),
                value: $(this).data('id')
              }
            ]
            for (let i = 0; i < fields.length; i++) {
              const hiddenField = document.createElement('input')
              hiddenField.type = 'hidden'
              hiddenField.name = fields[i].name
              hiddenField.value = fields[i].value

              form.append(hiddenField)
            }

            document.body.append(form)
            form.submit()
          }
        }
      })
    })
  </script>

</body>

</html>