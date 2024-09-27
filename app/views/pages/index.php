<?php require APPROOT . '/views/inc/header.php'; ?>

<body>

    <main>
        <div class="container">

            <section class="section register min-vh-100 d-flex flex-column align-items-center justify-content-center py-4">
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-lg-4 col-md-6 d-flex flex-column align-items-center justify-content-center">

                            <div class="d-flex justify-content-center py-4">
                                <a href="" class="logo d-flex align-items-center w-auto">
                                    <img src="<?php echo URLROOT; ?>/assets/img/logo.png" alt="">
                                    <span class=""><?php echo SITENAME; ?></span>
                                </a>
                            </div><!-- End Logo -->

                            <div class="card mb-3">

                                <div class="card-body">
                                    <div class="pt-4 pb-2">
                                        <h5 class="card-title text-center pb-0 fs-4">Welcome!</h5>
                                        <p class="text-center small">Enter your Organization/Institution username to login</p>
                                    </div>

                                    <form class="row g-3 needs-validation" method="POST" action="<?php echo URLROOT; ?>/pages/login">

                                        <div class="col-12">
                                            <label for="yourUsername" class="form-label">Username</label>
                                            <div class="input-group has-validation">
                                                <span class="input-group-text" id="inputGroupPrepend">@</span>
                                                <input type="input" autocomplete="off" name="username" list="schools" class="form-control  <?php echo (!empty($data['username_err'])) ? 'is-invalid' : ''; ?>" value="<?php echo $data['username'] ?>" id="yourUsername">
                                                <datalist id="schools">
                                                    <?php foreach ($data['schools'] as $sch) : ?>
                                                        <option value="<?= $sch->username; ?>"></option>
                                                    <?php endforeach; ?>
                                                </datalist>
                                                <div class="invalid-feedback"><?php echo $data['username_err'] ?></div>
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="remember" value="true" id="rememberMe">
                                                <label class="form-check-label" for="rememberMe">Remember me</label>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <button class="btn btn-primary w-100" type="submit">Login</button>
                                        </div>
                                        <div class="col-12">
                                            <p class="small mb-0">Don't have account? <a href="<?php echo URLROOT; ?>/pages/register">Create one</a></p>
                                        </div>
                                    </form>

                                </div>
                            </div>



                        </div>
                    </div>
                </div>

            </section>

        </div>
    </main><!-- End #main -->

    <?php require APPROOT . '/views/inc/footer.php'; ?>