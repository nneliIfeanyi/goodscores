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
    <!-- Ajax Response -->
    <div id="ajax-msg"></div>
    <!-- End Ajax Response -->
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

                  <form class="row g-3">
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

                    <div class="col-12 position-relative">
                      <label for="yourPassword" class="form-label">Password</label>
                      <i class="bi bi-eye password-visible-btn position-absolute top-50 end-0 px-4 mt-1"></i>
                      <input type="password" name="password" class="form-control password pe-5" id="yourPassword" value="<?php echo $data['password']; ?>">
                    </div>
                    <span class="text-danger small" style="margin-top: -4px;"><?php echo $data['password_err']; ?></span>
                    <div class="col-12">
                      <label for="yourPassword" class="form-label">Comfirm Password</label>
                      <input type="password" name="confirm_password" class="form-control password" id="yourPassword" value="<?php echo $data['confirm_password']; ?>" required>
                      <span class="text-danger small"><?php echo $data['confirm_password_err']; ?></span>
                    </div>

                    <div class="col-12">
                      <input id="submit" class="btn btn-primary w-100" type="submit" value="Create Account">
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
  <script>
        $('form').on('submit', function(event) {
            event.preventDefault();
            $.ajax({
                url: "<?php echo URLROOT; ?>/users/register",
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
                    $('#submit').val('Create Account');
                    $('#ajax-msg').html(data);
                }
            });

        });
    </script>
  <!-- Show and Hide Password -->
  <script>
    $(document).ready(function() {
      $('.password-visible-btn').click(function() {
        const passwordInputs = $('input.password');
        $(this).toggleClass('bi-eye-slash');
        if ($(this).hasClass('bi-eye-slash')) {
          console.log('Hide Password')
          passwordInputs.attr('type', 'text');
          $(this).removeClass('bi-eye').addClass('bi-eye-slash');
        } else {
          console.log('Hide Pawssword')
          passwordInputs.attr('type', 'password');
          $(this).removeClass('bi-eye-slash').addClass('bi-eye');
        }
      });
    });
  </script>