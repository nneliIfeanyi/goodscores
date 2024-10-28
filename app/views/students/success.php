<?php require APPROOT . '/views/students/inc/header.php'; ?>
<?php require APPROOT . '/views/students/inc/navbar.php'; ?>
<?php require APPROOT . '/views/students/inc/sidebar.php'; ?>



<main id="main" class="main">

    <!-- End Page Title -->
    <div class="row text-center">
        <div class="col-lg-6">
            <div class="pagetitle">
                <h1 class="text-success fw-bold h1">Success!</h1>
            </div>
            <div class="card">
                <div class="card-body pb-3">
                    <h5 class="card-title"><?= $data['subject']; ?> | CA Test</h5>

                    <div class="badge rounded-circle p-5 bg-success">
                        <p class="fw-bold fs-1"><?= $data['score']; ?>%</p>
                    </div>
                    <p><span class="fs-2 fw-bold">PASSED!</span><br /> Based on 50% and above.</p>
                    <div class="d-grid  mt-4 me-4">
                        <a href="<?= URLROOT; ?>/students/dashboard" class="btn btn-outline-primary"><i class="bi bi-chevron-left"></i> Return</a>
                    </div>
                </div>

            </div>
        </div>
    </div>



</main>
<?php require APPROOT . '/views/inc/footer.php'; ?>