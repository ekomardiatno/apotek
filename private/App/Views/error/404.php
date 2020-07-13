<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	
	<link rel="shortcut icon" href="<?php Web::assets('favicon.png', 'images'); ?>" type="image/x-icon">

    <title>404</title>

    <!-- Fonts Libs -->
    <link rel="stylesheet" href="<?php Web::assets('font-awesome.min.css', 'css'); ?>">
    <link rel="stylesheet" href="<?php Web::assets('font-raleway.css', 'css'); ?>">

    <!-- CSS Libs -->
    <link rel="stylesheet" href="<?php Web::assets('bootstrap.min.css', 'css'); ?>">

    <!-- CSS Custom -->
    <link rel="stylesheet" href="<?php Web::assets('styles.css', 'css'); ?>">

    <!-- JS libs -->
    <script src="<?php Web::assets('jquery.min.js', 'js'); ?>"></script>
    <script src="<?php Web::assets('bootstrap.min.js', 'js'); ?>"></script>
    <script src="<?php Web::assets('bootstrap-notify.js', 'js'); ?>"></script>
    <script src="<?php Web::assets('flash-message.js', 'js'); ?>"></script>

    <!-- JS Custom -->
    <script src="<?php Web::assets('scripts.js', 'js'); ?>"></script>
</head>
<body>
	<div class="height-100vh flex-center">
		<div class="container text-center">
			<h1 class="mt-0 fw-700">404!</h1>
			<h4>Halaman tidak ditermukan.</h4>
			<a href="<?= Web::url(); ?>">Kembali ke Beranda.</a>
		</div>
	</div>
</body>
</html>