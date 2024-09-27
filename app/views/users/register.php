<?php require APPROOT . '/views/inc/header.php'; ?>

<body>

  <main>
    <div class="container">

      <section class="section register min-vh-100 d-flex flex-column align-items-center justify-content-center py-4">
        <div class="container">
          <div class="row justify-content-center">
            <div class="col-lg-4 col-md-6 d-flex flex-column align-items-center justify-content-center">

              <div class="d-flex justify-content-center py-4" data-bs-toggle="tooltip" data-bs-title="<?php echo $_COOKIE['sch_name']; ?>">
                <a href="index.html" class="logo d-flex align-items-center w-auto">
                  <img src="<?php echo URLROOT; ?>/assets/img/logo.png" alt="">
                  <span class=""><?php echo $_COOKIE['sch_username']; ?></span>
                </a>
              </div><!-- End Logo -->

              <div class="card mb-3">

                <div class="card-body">

                  <div class="pt-4 pb-2">
                    <h5 class="card-title text-center pb-0 fs-4">Create Teacher's Account</h5>
                    <p class="text-center small">Enter your personal details to create account</p>
                  </div>

                  <form class="row g-3" method="POST" action="<?php echo URLROOT; ?>/users/register">
                    <div class="col-12">
                      <label for="yourName" class="form-label">Your Name</label>
                      <input type="text" name="name" class="form-control" id="yourName" value="<?php echo $data['name']; ?>" required>
                    </div>

                    <div class="col-12">
                      <label for="yourEmail" class="form-label">Your Email</label>
                      <input type="email" name="email" class="form-control" id="yourEmail" value="<?php echo $data['email']; ?>" required>
                    </div>

                    <div class="col-12">
                      <label for="phoneNumber" class="form-label">Phone</label>
                      <input type="number" name="phone" class="form-control" id="phoneNumber" value="<?php echo $data['phone']; ?>" required>
                    </div>

                    <div class="col-12">
                      <label for="yourUsername" class="form-label">Username</label>
                      <div class="input-group has-validation">
                        <span class="input-group-text" id="inputGroupPrepend">@</span>
                        <input type="text" name="username" class="form-control  <?php echo (!empty($data['username_err'])) ? 'is-invalid' : ''; ?>" id="yourUsername" value="<?php echo $data['username']; ?>" required>
                        <div class="invalid-feedback"><?php echo $data['username_err']; ?></div>
                      </div>
                    </div>

                    <div class="col-12">
                      <label for="yourPassword" class="form-label">Password</label>
                      <input type="password" name="password" class="form-control  <?php echo (!empty($data['password_err'])) ? 'is-invalid' : ''; ?>" id="yourPassword" value="<?php echo $data['password']; ?>" required>
                      <div class="invalid-feedback"><?php echo $data['password_err']; ?></div>
                    </div>

                    <div class="col-12">
                      <label for="yourPassword" class="form-label">Comfirm Password</label>
                      <input type="password" name="confirm_password" class="form-control <?php echo (!empty($data['confirm_password_err'])) ? 'is-invalid' : ''; ?>" id="yourPassword" value="<?php echo $data['confirm_password']; ?>" required>
                      <div class="invalid-feedback"><?php echo $data['confirm_password_err']; ?></div>
                    </div>

                    <div class="col-12">
                      <button class="btn btn-primary w-100" type="submit">Create Account</button>
                    </div>
                    <div class="col-12">
                      <p class="small mb-0">Already have an account? <a href="<?php echo URLROOT; ?>/users/login">Log in</a></p>
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

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <?php require APPROOT . '/views/inc/footer.php'; ?>