<!DOCTYPE html>
<html>

<head>

  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

  <link rel="shortcut icon" href="<?= Web::assets('brand/favicon.png', 'images'); ?>" type="image/x-icon">

  <title><?= $title != '' ? $title . ' | ' . getenv('APP_NAME') : getenv('APP_NAME') ?></title>
  <meta content="<?= $desc; ?>" name="description" />

  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet">
  <link rel="stylesheet" href="<?= Web::assets('nucleo.css', 'css'); ?>">

  <link rel="stylesheet" href="<?= Web::assets('animate.min.css', 'css'); ?>">
  <link rel="stylesheet" href="<?= Web::assets('argon.min.css', 'css'); ?>">

  <script src="<?= Web::assets('jquery.min.js', 'js') ?>"></script>
  <script src="<?= Web::assets('bootstrap.bundle.min.js', 'js') ?>"></script>
  <script src="<?= Web::assets('bootstrap-notify.min.js', 'js') ?>"></script>
  <script src="<?= Web::assets('flash-message.min.js', 'js') ?>"></script>

  <script src="<?= Web::assets('argon.min.js', 'js'); ?>"></script>

</head>

<body class="bg-default">

  <div class="main-content">
    <!-- Navbar -->
    <nav class="navbar navbar-top navbar-horizontal navbar-expand-md navbar-dark mt-4">
      <div class="container px-4">
        <a class="navbar-brand" href="https://ekomardiatno.github.io">
          <img src="<?= Web::assets('brand/white.png', 'images'); ?>" />
        </a>
      </div>
    </nav>
    <!-- Header -->
    <div class="header bg-gradient-primary py-7 py-lg-8 pt-lg-9">
      <div class="container">
        <div class="header-body text-center mb-4 mb-lg-0">
          <!-- <div class="row justify-content-center">
            <div class="col-xl-5 col-lg-6 col-md-8 px-5">
              <h1 class="text-white">Welcome!</h1>
              <p class="text-lead text-white">Use these awesome forms to login or create new account in your project for free.</p>
            </div>
          </div> -->
        </div>
      </div>
      <div class="separator separator-bottom separator-skew zindex-100">
        <svg x="0" y="0" viewBox="0 0 2560 100" preserveAspectRatio="none" version="1.1" xmlns="http://www.w3.org/2000/svg">
          <polygon class="fill-default" points="2560 0 2560 100 0 100"></polygon>
        </svg>
      </div>
    </div>

    <div class="container mt--8 pb-5">
      <!-- Content -->
      <?php require_once $content; ?>
      <!-- End Content -->
    </div>

  </div>

  <footer class="py-5">
    <div class="container">
      <div class="row align-items-center justify-content-xl-between">
        <div class="col-xl-12">
          <div class="copyright text-center text-xl-left text-muted">
            &copy; <?= date('Y') ?> <a href="https://ekomardiatno.github.io" class="font-weight-bold ml-1" target="_blank">KOMA MVC</a>
          </div>
        </div>
      </div>
    </div>
  </footer>

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

</body>

</html>