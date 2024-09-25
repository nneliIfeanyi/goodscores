<?php require APPROOT . '/views/inc/header.php'; ?>
<?php require APPROOT . '/views/inc/navbar.php'; ?>
<?php require APPROOT . '/views/inc/sidebar.php'; ?>



<main id="main" class="main">

    <div class="pagetitle">
        <h1><?= $data['class']->classname; ?> </h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.html">Home</a></li>
                <li class="breadcrumb-item">Users</li>
                <li class="breadcrumb-item active">Classes</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <section class="section">
        <div class="row">
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-body">

                        <div class="alert alert-primary bg-primary text-light border-0 alert-dismissible fade show" role="alert">
                            <strong>Edit Class</strong>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        <form action="<?php echo URLROOT; ?>/submissions/edit_class/<?= $data['class']->id; ?>" method="POST">

                            <div class="my-4">
                                <label for="className">Class Name</label>
                                <input type="text" id="className" name="classname" required class="form-control form-control-lg" value="<?= $data['class']->classname; ?>" data-parsley-trigger=" keyup" />
                            </div>
                            <div class="my-4">
                                <label for="className">Number of objective questions</label>
                                <input type="number" id="className" name="obj_num_rows" required class="form-control form-control-lg" value="<?= $data['class']->num_rows; ?>" data-parsley-trigger=" keyup" />
                            </div>

                            <div class="my-4">
                                <label for="className">Number of theory questions</label>
                                <input type="number" id="className" name="theory_num_rows" class="form-control form-control-lg" value="<?= $data['class']->num_rows2; ?>" data-parsley-trigger=" keyup" />
                            </div>

                            <div class="my-4">
                                <label for="className">How many theory questions to answer</label>
                                <input type="number" name="choice" class="form-control form-control-lg" value="<?= $data['class']->choice; ?>" data-parsley-trigger=" keyup" />
                            </div>

                            <div class="my-4">
                                <label for="className">Exam duration</label>
                                <input name="duration" required class="form-control form-control-lg" value="<?= $data['class']->duration; ?>" data-parsley-trigger="keyup" />
                            </div>

                            <div class="d-flex gap-4">
                                <input type="submit" name="set" value="Save changes" class="btn btn-primary">
                                <button type="button" onclick="history.back()" class="btn btn-outline-primary"><i class="bi bi-chevron-left"></i> Go back</button>
                            </div>
                        </form>

                    </div>
                </div>

            </div>
        </div>
    </section>

</main><!-- End #main -->


<?php require APPROOT . '/views/inc/footer.php'; ?>