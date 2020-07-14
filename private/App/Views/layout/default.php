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
  <link rel="stylesheet" href="<?= Web::assets('dataTables.bootstrap4.min.css', 'css') ?>">
  <link rel="stylesheet" href="<?= Web::assets('responsive.bootstrap4.min.css', 'css') ?>">
  <link rel="stylesheet" href="<?= Web::assets('argon.min.css', 'css') ?>">
  <link rel="stylesheet" href="<?= Web::assets('all.min.css', 'css') ?>">

  <script src="<?= Web::assets('jquery.min.js', 'js') ?>"></script>
  <script src="<?= Web::assets('bootstrap.bundle.min.js', 'js') ?>"></script>
  <script src="<?= Web::assets('nouislider.min.js', 'js') ?>"></script>
  <script src="<?= Web::assets('bootstrap-datepicker.min.js', 'js') ?>"></script>
  <script src="<?= Web::assets('bootstrap-notify.min.js', 'js') ?>"></script>
  <script src="<?= Web::assets('jquery.dataTables.min.js', 'js') ?>"></script>
  <script src="<?= Web::assets('dataTables.bootstrap4.min.js', 'js') ?>"></script>
  <script src="<?= Web::assets('dataTables.responsive.min.js', 'js') ?>"></script>
  <script src="<?= Web::assets('responsive.bootstrap4.min.js', 'js') ?>"></script>
  <script src="<?= Web::assets('flash-message.min.js', 'js') ?>"></script>
  <script src="<?= Web::assets('input-foto.js', 'js') ?>"></script>
  <script src="<?= Web::assets('argon.min.js', 'js') ?>"></script>
  <script src="<?= Web::assets('autocomplete.js', 'js') ?>"></script>

<body>
  <div class="main-content">

    <div class="header bg-gradient-primary pb-8 pt-5 pt-md-5">
      <div class="container px-lg-3 px-xl-6 mt--5">
        <div class="py-2">
          <img height="40" src="<?= Web::assets('brand/white.png', 'images'); ?>"/>
        </div>
        <hr invert-color class="mt-0">
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

    <footer class="footer">
      <div class="row align-items-center justify-content-lg-between">
        <div class="col-lg">
          <div class="copyright text-center text-lg-left mb-1 mb-lg-0">
            &copy; 2020 <a href="<?= Web::url() ?>" class="font-weight-bold ml-1" target="_blank"><?= getenv('APP_NAME') ?></a>
          </div>
        </div>
        <div class="col-lg">
          <div class="copyright text-center text-lg-right">
            Powered by <span class="font-weight-bold">KOMA-MVC</span>
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
      let datepicker = $(this).datepicker({
        disableTouchKeyboard: true,
        autoclose: false,
        format: 'dd-mm-yyyy'
      })
      if ($(this).val() === '') {
        datepicker.datepicker("setDate", new Date())
      }
    })
  </script>

</body>

</html>