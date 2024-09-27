<?php require APPROOT . '/views/inc/header.php'; ?>

<body>
  <!-- PAGE LOADER -->
  <div id="loader" class="overflow-hidden align-items-middle position-fixed top-0 left-0 w-100 h-100">
    <div class="loader-container position-relative d-flex align-items-center justify-content-center flex-column vw-100 vh-100 text-center" style="background: rgba(0, 0, 0, 0.7);z-index: 500;">
      <span class="spinner-border text-primary"> </span>
    </div>
  </div>
  <main>
    <div class="container">

      <section class="section register min-vh-100 d-flex flex-column align-items-center justify-content-center py-4">
        <div class="container">
          <div class="row justify-content-center">
            <div class="col-lg-4 col-md-6 d-flex flex-column align-items-center justify-content-center">

              <div class="d-flex justify-content-center py-4" data-bs-toggle="tooltip" data-bs-title="<?php echo $_COOKIE['sch_name']; ?>">
                <a href="" class="logo d-flex align-items-center w-auto">
                  <img src="<?php echo URLROOT; ?>/assets/img/logo.png" alt="">
                  <span class=""><?php echo $_COOKIE['sch_username']; ?></span>
                </a>
              </div><!-- End Logo -->

              <div class="card mb-3">

                <div class="card-body">
                  <?php echo flash('msg'); ?>
                  <div class="pt-4 pb-2">
                    <h5 class="card-title text-center pb-0 fs-4">Teacher's Section</h5>
                    <p class="text-center small">Enter your username & password to login</p>
                  </div>

                  <form class="row g-3 needs-validation" method="POST" action="<?php echo URLROOT; ?>/users/login">

                    <div class="col-12">
                      <label for="yourUsername" class="form-label">Username</label>
                      <div class="input-group has-validation">
                        <span class="input-group-text" id="inputGroupPrepend">@</span>
                        <input type="text" name="username" class="form-control  <?php echo (!empty($data['username_err'])) ? 'is-invalid' : ''; ?>" value="<?php echo $data['username'] ?>" id="yourUsername">
                        <div class="invalid-feedback"><?php echo $data['username_err'] ?></div>
                      </div>
                    </div>

                    <div class="col-12">
                      <label for="yourPassword" class="form-label">Password</label>
                      <input type="password" name="password" class="form-control <?php echo (!empty($data['password_err'])) ? 'is-invalid' : ''; ?>" value="<?php echo $data['password'] ?>" id="yourPassword">
                      <div class="invalid-feedback"><?php echo $data['password_err'] ?></div>
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
                      <p class="small mb-0">Don't have account? <a href="<?php echo URLROOT; ?>/users/register">Create teacher's account</a></p>
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