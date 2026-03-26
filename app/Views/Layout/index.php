<?php /** @var \CodeIgniter\View\View $this */ ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>TKM Unsurya</title>
    <meta content='width=device-width, initial-scale=1.0, shrink-to-fit=no' name='viewport' />
    <meta name="csrf-token-name" content="<?= csrf_token() ?>">
    <meta name="csrf-token" content="<?= csrf_hash() ?>">
    <meta name="csrf-header" content="<?= csrf_header() ?>">
    <link rel="icon" href="<?= base_url('/assets/img/kaiadmin/favicon.ico') ?>" type="image/x-icon" />

    <!-- Fonts and icons -->
    <script src="<?= base_url('/assets/js/plugin/webfont/webfont.min.js') ?>"></script>
    <script>
        WebFont.load({
            google: {
                "families": ["Public Sans:300,400,500,600,700"]
            },
            custom: {
                "families": ["Font Awesome 5 Solid", "Font Awesome 5 Regular", "Font Awesome 5 Brands", "simple-line-icons"],
                urls: ['<?= base_url('/assets/css/fonts.min.css') ?>']
            },
            active: function() {
                sessionStorage.fonts = true;
            }
        });
    </script>

    <!-- CSS Files -->
    <link rel="stylesheet" href="<?= base_url('/assets/css/bootstrap.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('/assets/css/plugins.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('/assets/css/kaiadmin.min.css') ?>">

</head>

<body>
    <div class="wrapper">
        <?= $this->include('Layout/sidebar'); ?>

        <div class="main-panel">
            <?= $this->include('Layout/header'); ?>
            <div class="container">
                <div class="page-inner">
                    <?= $this->rendersection('Dashboard'); ?>

                </div>
            </div>

            <?= $this->include('Layout/footer'); ?>
        </div>

    </div>
    <!--   Core JS Files   -->
    <script src="<?= base_url('/assets/js/core/jquery-3.7.1.min.js') ?>"></script>
    <script src="<?= base_url('/assets/js/core/popper.min.js') ?>"></script>
    <script src="<?= base_url('/assets/js/core/bootstrap.min.js') ?>"></script>

    <!-- jQuery Scrollbar -->
    <script src="<?= base_url('/assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js') ?>"></script>

    <!-- jQuery Sparkline -->
    <script src="<?= base_url('/assets/js/plugin/jquery.sparkline/jquery.sparkline.min.js') ?>"></script>

    <!-- Chart Circle -->
    <script src="<?= base_url('/assets/js/plugin/chart-circle/circles.min.js') ?>"></script>

    <!-- Datatables -->
    <script src="<?= base_url('/assets/js/plugin/datatables/datatables.min.js') ?>"></script>

    <!-- Bootstrap Notify -->
    <script src="<?= base_url('/assets/js/plugin/bootstrap-notify/bootstrap-notify.min.js') ?>"></script>

    <!-- jQuery Vector Maps -->
    <script src="<?= base_url('/assets/js/plugin/jsvectormap/jsvectormap.min.js') ?>"></script>
    <script src="<?= base_url('/assets/js/plugin/jsvectormap/world.js') ?>"></script>

    <!-- Sweet Alert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Kaiadmin JS -->
    <script src="<?= base_url('/assets/js/kaiadmin.min.js') ?>"></script>
    <script>
        $('#lineChart').sparkline([102, 109, 120, 99, 110, 105, 115], {
            type: 'line',
            height: '70',
            width: '100%',
            lineWidth: '2',
            lineColor: '#177dff',
            fillColor: 'rgba(23, 125, 255, 0.14)'
        });

        $('#lineChart2').sparkline([99, 125, 122, 105, 110, 124, 115], {
            type: 'line',
            height: '70',
            width: '100%',
            lineWidth: '2',
            lineColor: '#f3545d',
            fillColor: 'rgba(243, 84, 93, .14)'
        });

        $('#lineChart3').sparkline([105, 103, 123, 100, 95, 105, 115], {
            type: 'line',
            height: '70',
            width: '100%',
            lineWidth: '2',
            lineColor: '#ffa534',
            fillColor: 'rgba(255, 165, 52, .14)'
        });
    </script>
</body>

</html>