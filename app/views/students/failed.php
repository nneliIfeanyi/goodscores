<?php require APPROOT . '/views/students/inc/header.php'; ?>
<?php require APPROOT . '/views/students/inc/navbar.php'; ?>
<?php require APPROOT . '/views/students/inc/sidebar.php'; ?>



<main id="main" class="main">

    <div class="pagetitle">
        <h1 class="text-danger fw-semibold">Failed!</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= URLROOT; ?>/students/dashboard">Home</a></li>
                <li class="breadcrumb-item">Students</li>
                <li class="breadcrumb-item active">Dashboard</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <div class="row">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-body pb-3">
                    <h5 class="card-title"><?= $data['subject']; ?> | CA Test</h5>

                    <div class="badge rounded-circle p-5 bg-danger">
                        <p class="fw-bold fs-4"> <?= $data['score']; ?>%</p>
                    </div>
                    <p><span class="fs-2 fw-bold">FAILED!</span><br /> Based on 50% and below.</p>
                    <div class="d-grid  mt-4 me-4">
                        <a href="<?= URLROOT; ?>/students/dashboard" class="btn btn-outline-primary"><i class="bi bi-chevron-left"></i> Return</a>
                    </div>
                </div>

            </div>
        </div>
    </div>



</main>
<?php require APPROOT . '/views/inc/footer.php'; ?>