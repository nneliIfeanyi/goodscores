<?php require APPROOT . '/views/inc/header.php'; ?>

<body>
  <!-- PAGE LOADER -->
  <div id="loader" class="overflow-hidden align-items-middle position-fixed top-0 left-0 w-100 h-100">
    <div class="loader-container position-relative d-flex align-items-center justify-content-center flex-column vw-100 vh-100 text-center" style="background: rgba(0, 0, 0, 0.6);z-index: 1500;">
      <span class="spinner-border text-primary"> </span>
    </div>
  </div>

    <nav id="nav" class="navbar navbar-nav navbar-expand-lg bg-light shadow-sm">
        <div class="container">
            <a href="index.html" class="navbar-brand text-primary d-flex align-items-center justify-content-start">
                <img src="<?= URLROOT; ?>/assets/img/goodscores.png" width="170" height="40" class="" />
                <!-- <h1 class="fs-3 text-primary">Goodscores</h1> -->
            </a>
            <button class="navbar-toggler" type="button" data-bs-theme="lightt" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNavDropdown">
                <ul class="navbar-nav ms-auto align-items-lg-center">
                    <!-- <li class="nav-item active me-3">
                        <a href="<?= URLROOT; ?>/pages" aria-current="page" class="nav-link">Home</a>
                    </li> -->
                    <!-- <li class="nav-item">
                        <a href="pages/pricing.html" class="nav-link">Pricing</a>
                    </li>
                    <li class="nav-item">
                        <a href="pages/faq.html" class="nav-link">FAQs</a>
                    </li>
                    <li class="nav-item me-3">
                        <a href="pages/support.html" class="nav-link">Supports</a>
                    </li> -->
                    <li class="nav-item ms-5">
                        <a href="<?= URLROOT; ?>/pages/login" class="btn btn-primary px-5">Login</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav><!-- END - NAV -->
    <section class="hero-section w-100 h-100 d-flex align-items-center justify-content-center text-center text-lg-start pt-5 pb-3 mt-5">
        <div class="container">
            <div class="hero-contents row">
                <div class="hero-texts col-lg-8">
                    <h1 class="pt-3 fw-bold px-2">Set Examination Questions With Ease</h1>
                    <p class="lead my-3 px-3">Say goodbye to tedius paperwork and lost files. <strong>Goodscores</strong> helps schools efficiently manage assessments, saving teachers valueable time and reducing administrative burdens. </p>
                    <a href="<?= URLROOT; ?>/pages/register" class="btn btn-primary px-3 btn-lg mt-3">Get Started Today!</a>
                </div>
                <div class="hero-img col-lg-4 d-none d-lg-block">
                    <img src="<?= URLROOT; ?>/assets/img/img-1.jpg" class="img-fluid w-100 h-100" />
                </div>
            </div>
        </div>
    </section><!-- END - .hero-section -->
    <section class="feature-section w-100 h-100 d-flex align-items-center justify-content-center text-center bg-light py-3">
        <div class="container">
            <!-- <h4>FEATURES</h4> -->
            <div class="feature-contents row mt-5">
                <div class="col-lg-3 col-md-6 my-3">
                    <span class="bi bi-pen-fill p-2 rounded-circle fs-1 bg-primary-subtle mb-3"></span>
                    <h5>Easy To Use</h5>
                    <p>Create and organize exam questions with ease.</p>
                </div>
                <div class="col-lg-3 col-md-6 my-3">
                    <span class="bi bi-file-pdf-fill p-2 rounded-circle fs-1 bg-primary-subtle mb-3"></span>
                    <h5>PDF Export</h5>
                    <p>Save questions as PDF files for offline use.</p>
                </div>
                <div class="col-lg-3 col-md-6 my-3">
                    <span class="bi bi-person-bounding-box p-2 rounded-circle fs-1 bg-primary-subtle mb-3"></span>
                    <h5>Secure and Reliable</h5>
                    <p>Trusted platform for teachers and educators.</p>
                </div>
                <div class="col-lg-3 col-md-6 my-3">
                    <span class="bi bi-phone-fill p-2 rounded-circle fs-1 bg-primary-subtle mb-3"></span>
                    <h5>User Friendly Interface</h5>
                    <p>Intuitive design for efficient navigation.</p>
                </div>
            </div>
        </div>
    </section><!-- END - .feature-section -->

    <section class="how-it-works-section w-100 h-100 d-flex align-items-center justify-content-center text-start bg-light pt-5 pb-3">
        <div class="container">
            <h2 class="text-center fw-bold">Getting Started</h2>
            <div class="how-it-works-contents row mt-3">
                <div class="col-lg-3 col-md-6 d-flex align-items-start justify-content-start my-3">
                    <span class="bi bi-1-circle p-2 fs-4 bg-primary-subtle rounded"></span>
                    <div class="mx-3">
                        <h6 class="fw-bold">Sign Up</h6>
                        <p class="small">Create account in minute and get instant access.</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 d-flex align-items-start justify-content-start my-3">
                    <span class="bi bi-2-circle p-2 fs-4 bg-primary-subtle rounded"></span>
                    <div class="mx-3">
                        <h6 class="fw-bold">Register Users</h6>
                        <p class="small">Add and authenticate teachers or school administrators.</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 d-flex align-items-start justify-content-start my-3">
                    <span class="bi bi-3-circle p-2 fs-4 bg-primary-subtle rounded"></span>
                    <div class="mx-3">
                        <h6 class="fw-bold">Define Exam Parameters</h6>
                        <p class="small">Add classes and subjects for each user or teacher.</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 d-flex align-items-start justify-content-start my-3">
                    <span class="bi bi-4-circle p-2 fs-4 bg-primary-subtle rounded"></span>
                    <div class="mx-3">
                        <h6 class="fw-bold">Set Questions</h6>
                        <p class="small">Enjoy a streamlined smooth navigation from objectives to theory sections.</p>
                    </div>
                </div>
            </div>
        </div>
    </section><!-- END - .how-it-works-section -->

    <section class="benefit-section w-100 h-100 d-flex align-items-center justify-content-center text-center bg-light py-5">
        <div class="container">
            <h3 class="fw-bold">Why Good Scores</h3>
            <div class="benefit-contents row mt-3">
                <div class="col-lg-3 col-md-6 my-3">
                    <span class="bi bi-rocket fs-1 rounded-circle p-2 bg-primary-subtle mb-3"></span>
                    <h5 class="m-0">Save Time</h5>
                    <p class="p-2">Streamline question creation and management.</p>
                </div>
                <div class="col-lg-3 col-md-6 my-3">
                    <span class="bi bi-person-arms-up fs-1 rounded-circle p-2 bg-primary-subtle mb-3"></span>
                    <h5 class="m-0">Reduce Stress</h5>
                    <p>Easily organize and export questions.</p>
                </div>
                <div class="col-lg-3 col-md-6 my-3">
                    <span class="bi bi-search fs-1 rounded-circle p-2 bg-primary-subtle mb-3"></span>
                    <h5 class="m-0">Improve Accuracy</h5>
                    <p>Minimize errors and inconsistencies</p>
                </div>
                <div class="col-lg-3 col-md-6 my-3">
                    <span class="bi bi-mortarboard fs-1 rounded-circle p-2 bg-primary-subtle mb-3"></span>
                    <h5>Enhance Student Experience</h5>
                    <p>Provide clear and concise assessments.</p>
                </div>
            </div>
        </div>
    </section><!-- END - .benefit-section -->
    <footer class="d-flex flex-column align-items-center justify-content-center text-center text-md-start py-2 mt-2 mb-0">
        <div class="container">
            <div class="footer-contents row align-items-center">
                <div class="col-md-8 my-2">
                    <p class="mb-0 fw-semibold">Stay connected for more updates, tips and insights!</p>
                </div>
                <div class="col-md-4 gap-3 d-flex align-items-center justify-content-center justify-content-md-end my-2">
                    <a href="" class="fs-5" target="_blank">
                        <span class="fa-stack">
                            <i class="bi bi-twitter"></i>
                        </span>
                    </a>
                    <a href="" class="fs-5" target="_blank">
                        <span class="fa-stack">
                            <i class="bi bi-facebook"></i>
                        </span>
                    </a>
                    <a href="" class="fs-5" target="_blank">
                        <span class="fa-stack">
                            <i class="bi bi-instagram"></i>
                        </span>
                    </a>
                </div>
            </div>
        </div>
    </footer><!-- END - footer -->

    <?php require APPROOT . '/views/inc/footer.php'; ?>