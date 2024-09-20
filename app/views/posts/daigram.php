<?php require APPROOT . '/views/inc/header.php'; ?>
<?php require APPROOT . '/views/inc/navbar.php'; ?>
<?php require APPROOT . '/views/inc/sidebar.php'; ?>

<main id="main" class="main">

    <section class="section">
        <div class="row">
            <div class="col-md-10 col-lg-8">

                <div class="card">
                    <div class="card-body">

                        <div class="alert alert-primary bg-primary text-light border-0 alert-dismissible fade show" role="alert">
                            <strong>Add Diagram</strong>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        <form action="<?php echo URLROOT; ?>/posts/daigram/<?= $data['paperID']; ?>" method="post" enctype="multipart/form-data">
                            <div class="form-group mb-3">
                                <input type="file" name="photo" accept="image*/" required class="form-control form-control-lg">
                            </div>

                            <div class="d-flex justify-content-around mt-4">
                                <div class="">
                                    <input type="submit" name="upload" class="btn btn-primary" value="Upload">
                                </div>
                        </form>
                        <div>
                            <button type="button" class="btn btn-dark" onclick="history.back()">
                                <i class="bi bi-chevron-left"></i> Back
                            </button>
                        </div>
                    </div>

                </div>
            </div>

        </div>
        </div>
    </section>

</main><!-- End #main -->

<?php require APPROOT . '/views/inc/footer.php'; ?>