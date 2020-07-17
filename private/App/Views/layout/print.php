<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= getenv('APP_NAME') ?></title>
    <style>
        body {
            font-size: 12pt;
        }

        p, h1 {
            margin-top: 0;
        }

        h1 {
            margin-bottom: 10pt;
        }

        p {
            margin-bottom: 5pt;
        }

        .text-center {
            text-align: center !important;
        }

        .border-top-2pt td {
            border-top-width: 2pt;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table tr td, table tr th {
            border: 1pt solid black;
            padding: 5pt;
        }

        table tr th {
            border-bottom-width: 2pt;
            text-transform: uppercase;
        }

        table thead {
            background-color: #ddd;
        }

        table tbody tr:nth-of-type(even) {
            background-color: #f5f5f5;
        }
    </style>
</head>
<body>
    <!-- Content -->
    <?php require_once $content; ?>
    <!-- End Content -->
</body>
</html>