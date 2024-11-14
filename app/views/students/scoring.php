<?php require APPROOT . '/views/inc/header.php';
require APPROOT . '/views/inc/navbar.php';
require APPROOT . '/views/inc/sidebar.php';
?>

<main id="main" class="main">

    <div class="pagetitle">
        <h1>Scoring</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= URLROOT; ?>/users/dashboard">Home</a></li>
                <li class="breadcrumb-item">Students</li>
                <li class="breadcrumb-item active">Scoring</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <section class="section">
        <div class="row">
            <div class="col-lg-6">
                <?php if ($_COOKIE['cbt'] == '1') : ?>
                    <div class="card">
                        <div class="card-body">

                            <div class="alert alert-primary bg-secondary text-light border-0 alert-dismissible fade show" role="alert">
                                <strong>Enter Assessment Scores</strong>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                            <form action="<?php echo URLROOT; ?>/students/scoring/<?= $data['id']; ?>" method="POST">

                                <div class="my-4 row">
                                    <!-- <label for="">Enter Duration</label> -->
                                    <div class="col-6">
                                        <div class="input-group mb-3">
                                            <input type="number" value="<?= (empty($data['CA1'])) ? '' : $data['CA1']; ?>" min="0" max="20" name="CA1" class="form-control form-control-lg" />
                                            <span class="input-group-text" id="basic-addon2">1st CA</span>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="input-group mb-3">
                                            <input type="number" value="<?= (empty($data['CA2'])) ? '' : $data['CA2']; ?>" name="CA2" min="0" max="20" class="form-control form-control-lg" />
                                            <span class="input-group-text" id="basic-addon2">2nd CA</span>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="input-group mb-3">
                                            <input type="number" value="<?= (empty($data['exam'])) ? '' : $data['exam']; ?>" name="exam" min="0" max="60" class="form-control form-control-lg" />
                                            <span class="input-group-text" id="basic-addon2">Exam</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex">
                                    <input type="submit" name="set" value="Save Changes" class="btn btn-outline-primary me-3">
                                    <a href="javascript:void()" onclick="history.back()" class="btn btn-secondary"><i class="bi bi-chevron-left"></i> Back</a>
                                </div>
                            </form>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

</main><!-- End #main -->


<?php require APPROOT . '/views/inc/footer.php'; ?>