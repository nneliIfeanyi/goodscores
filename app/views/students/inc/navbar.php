<body>
  <!-- PAGE LOADER -->
  <div id="loader" class="overflow-hidden align-items-middle position-fixed top-0 left-0 w-100 h-100">
    <div class="loader-container position-relative d-flex align-items-center justify-content-center flex-column vw-100 vh-100 text-center" style="background: rgba(0, 0, 0, 0.6);z-index: 1500;">
      <span class="spinner-border text-primary"> </span>
    </div>
  </div>
  <!-- Ajax Response -->
  <div id="ajax-msg"></div>
  <!-- End Ajax Response -->
  <!-- ======= Flash Message ======= -->
  <?php echo flash('msg'); ?>
  <!-- ======= Header ======= -->
  <header id="header" class="header fixed-top d-flex align-items-center">

    <div class="d-flex align-items-center justify-content-between">
      <a href="index.html" class="logo d-flex align-items-center">
        <img src="assets/img/logo.png" alt="">
        <span class="d-none d-lg-block"><?= $_COOKIE['sch_username']; ?></span>
      </a>
      <i class="bi bi-list toggle-sidebar-btn"></i>
    </div><!-- End Logo -->

    <nav class="header-nav ms-auto">
      <ul class="d-flex align-items-center">

        <li class="nav-item dropdown">

          <!-- <a class="nav-link nav-icon" href="#" data-bs-toggle="dropdown">
            <i class="bi bi-bell"></i>
            <span class="badge bg-primary badge-number">1</span>
          </a>End Notification Icon -->

          <!-- <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow notifications">
            <li class="dropdown-header">
              You have 1 new notifications
              <a href="#"><span class="badge rounded-pill bg-primary p-2 ms-2">View all</span></a>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>

            <li class="notification-item">
              <i class="bi bi-exclamation-circle text-warning"></i>
              <div>
                <h4>Lorem Ipsum</h4>
                <p>Quae dolorem earum veritatis oditseno</p>
                <p>30 min. ago</p>
              </div>
            </li> -->

          <!-- <li>
              <hr class="dropdown-divider">
            </li> -->



          <!-- <li class="dropdown-footer">
              <a href="#">Show all notifications</a>
            </li> 

      </ul> End Notification Dropdown Items 

      </li> End Notification Nav -->



        <li class="nav-item dropdown pe-3">
          <label class="nav-link nav-profile d-flex align-items-center pe-0">
            <?php if (!empty($_SESSION['profile_photo'])) : ?>
              <img src="<?= URLROOT; ?>/<?= $_SESSION['profile_photo']; ?>" alt="Profile" class="rounded-circle">
            <?php else : ?>
              <span class="d-none d-md-block ps-2"><?php echo $_SESSION['fullname']; ?></span>
            <?php endif; ?>
          </label><!-- End Profile Image Icon -->
        </li><!-- End Profile Nav -->

      </ul>
    </nav><!-- End Icons Navigation -->

  </header><!-- End Header -->