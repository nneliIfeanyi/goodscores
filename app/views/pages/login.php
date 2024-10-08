<?php require APPROOT . '/views/inc/header.php'; ?>

<body>
  <!-- PAGE LOADER -->
  <div id="loader" class="overflow-hidden align-items-middle position-fixed top-0 left-0 w-100 h-100">
    <div class="loader-container position-relative d-flex align-items-center justify-content-center flex-column vw-100 vh-100 text-center" style="background: rgba(0, 0, 0, 0.6);z-index: 1500;">
      <span class="spinner-border text-primary"> </span>
    </div>
  </div>
    <?php echo flash('msg'); ?>
    <main>
        <div class="container">
    <!-- Ajax Response -->
    <div id="ajax-msg"></div>
    <!-- End Ajax Response -->
            <section class="section register min-vh-100 d-flex flex-column align-items-center justify-content-center py-4">
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-lg-4 col-md-6 d-flex flex-column align-items-center justify-content-center">

                            <div class="d-flex justify-content-center py-4">
                                <a href="<?php echo URLROOT; ?>/pages" class="logo d-flex align-items-center w-auto">
                                    <img src="<?php echo URLROOT; ?>/assets/img/logo.png" alt="">
                                    <span class=""><?php echo SITENAME; ?></span>
                                </a>
                            </div><!-- End Logo -->

                            <div class="card mb-3">

                                <div class="card-body">
                                    <div class="pt-4 pb-2">
                                        <h5 class="card-title text-center pb-0 fs-4">Welcome!</h5>
                                        <p class="text-center small">Enter your Organization/Institution username or email to login</p>
                                    </div>

                                    <form class="row g-3">

                                        <div class="col-12">
                                            <label for="yourUsername" class="form-label">Username | Email</label>
                                            <div class="input-group has-validation">
                                                <span class="input-group-text" id="inputGroupPrepend">@</span>
                                                <input type="input" name="username" list="schools" class="form-control  <?php echo (!empty($data['username_err'])) ? 'is-invalid' : ''; ?>" value="<?php echo (empty($_GET['email'])) ? $data['username'] : $_GET['email']; ?>" id="yourUsername">
                                                <!-- <datalist id="schools">
                                                    <?php foreach ($data['schools'] as $sch) : ?>
                                                        <option value="<?= $sch->username; ?>"></option>
                                                    <?php endforeach; ?>
                                                </datalist> -->
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
                                            <input class="btn btn-primary w-100" type="submit" id="submit" value="Login">
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
    <script>
        $('form').on('submit', function(event) {
            event.preventDefault();
            $.ajax({
                url: "<?php echo URLROOT; ?>/pages/login",
                method: "POST",
                data: new FormData(this),
                contentType: false,
                processData: false,
                beforeSend: function() {
                    $('#submit').attr('disabled', 'disabled');
                    $('#submit').val('Processing, Pls Wait ....');

                },
                success: function(data) {
                    $('#submit').attr('disabled', false);
                    $('#submit').val('Login');
                    $('#ajax-msg').html(data);
                }
            });

        });
    </script>