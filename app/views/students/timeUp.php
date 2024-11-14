<?php require APPROOT . '/views/inc/header.php'; ?>

<body>

    <main>
        <div class="container">

            <section class="section error-404 min-vh-100 d-flex flex-column align-items-center justify-content-center">
                <h1>TIME UP</h1>
                <h2>Your allocated time has expired!</h2>
                <a class="btn" href="<?= URLROOT; ?>/students/dashboard"><i class="bi bi-chevron-left"></i> Return</a>
                <img src="<?= URLROOT; ?>/assets/img/not-found.svg" class="img-fluid py-5" alt="Page Not Found">

            </section>

        </div>
    </main><!-- End #main -->
    <?php require APPROOT . '/views/inc/footer.php'; ?>