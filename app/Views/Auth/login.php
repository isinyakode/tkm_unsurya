<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Login & Register - Kaiadmin Bootstrap 5 Admin Dashboard</title>
    <meta content="width=device-width, initial-scale=1.0, shrink-to-fit=no" name="viewport" />
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
                urls: ['assets/css/fonts.min.css']
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

<body class="login bg-primary">
    <div class="wrapper wrapper-login">
        <div class="container container-login animated fadeIn m-0">
            <h3 class="text-center">Sign In</h3>
            <div class="login-form">
                <?php if (session()->getFlashdata('error')): ?>
                    <p style="color: red;"><?= session()->getFlashdata('error') ?></p>
                <?php endif; ?>
                <form action="<?= base_url('login/attempt') ?>" method="post">
                    <?= csrf_field() ?>
                    <div class="form-sub">
                        <div class="form-floating form-floating-custom mb-3">
                            <input autocomplete="off"
                                id="nim"
                                name="nim"
                                type="text"
                                class="form-control"
                                placeholder="nim"
                                required />
                            <label for="nim">nim</label>
                        </div>
                        <div class="form-floating form-floating-custom mb-3">
                            <input autocomplete="off"
                                id="password"
                                name="password"
                                type="password"
                                class="form-control"
                                placeholder="password"
                                required />
                            <label for="password">Password</label>
                            <div class="show-password">
                                <i class="icon-eye"></i>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="d-flex form-sub">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="rememberme" />
                                <label class="form-check-label" for="rememberme">Remember Me</label>
                            </div>
                        </div>
                    </div>
                    <div class="form-action">
                        <button type="submit" class="btn btn-primary w-100 btn-login">Sign In</button>
                    </div>
                </form>
            </div>
        </div>

    </div>
    <script src="<?= base_url('/assets/js/core/jquery-3.7.1.min.js') ?>"></script>

    <script src="<?= base_url('/assets/js/core/popper.min.js') ?>"></script>
    <script src="<?= base_url('/assets/js/core/bootstrap.min.js') ?>"></script>
    <script src="<?= base_url('/assets/js/kaiadmin.min.js') ?>"></script>
</body>

</html>