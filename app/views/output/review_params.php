<?php require APPROOT . '/views/inc/header.php'; ?>
<?php require APPROOT . '/views/inc/navbar.php'; ?>
<?php require APPROOT . '/views/inc/sidebar.php'; ?>

<main id="main" class="main">

    <div class="pagetitle">
        <h1>Review</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><?php echo $data['params']->class; ?></li>
                <li class="breadcrumb-item"><?php echo $data['params']->subject; ?></li>
                <li class="breadcrumb-item active">Exam Paper</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <section class="section">
        <div class="row">
            <div class="col-md-10 col-lg-8">

                <div class="card">
                    <div class="card-body">

                        <div class="alert alert-primary bg-primary text-light border-0 alert-dismissible fade show" role="alert">
                            <strong>Review Examination Parameters!</strong>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>


                        <?php if (!empty($data['params1'])) : ?>
                            <?php if (empty($data['params1']->instruction) || empty($data['params1']->duration)) : ?>
                                <form action="<?php echo URLROOT; ?>/submissions/review/objectives_questions" method="POST">
                                    <input type="hidden" name="term" value="<?= $data['term']; ?>">
                                    <input type="hidden" name="year" value="<?= $data['year']; ?>">
                                    <input type="hidden" name="subject" value="<?php echo $data['params']->subject; ?>">
                                    <input type="hidden" name="class" value="<?php echo $data['params']->class; ?>">
                                    <input type="hidden" name="paperID" value="<?php echo $data['paperID']; ?>">
                                    <input type="hidden" value="<?= $data['params']->num_rows; ?>" name="num_rows" />
                                    <div class="my-4">
                                        <label for="className">Exam instruction for objective section</label>
                                        <input name="instruction" value="<?= $data['params1']->instruction; ?>" class="form-control form-control-lg" data-parsley-trigger="keyup" />
                                    </div><!--===== Objectives section Instruction Ends =====-->
                                    <div class="my-4">
                                        <label for="className">Exam Duration</label>
                                        <input name="duration" value="<?= $data['params1']->duration; ?>" class="form-control form-control-lg" data-parsley-trigger="keyup" />

                                    </div><!--===== Duration Ends =====-->
                                    <div class="d-grid">
                                        <input type="submit" name="set" value="Update Objectives Section" class="btn btn-outline-primary">
                                    </div><!--===== Submit Button Ends =====-->
                                </form>
                            <?php endif; ?>
                        <?php endif; ?>
                        <hr>
                        <?php if (!empty($data['params2'])) : ?>
                            <?php if (empty($data['params2']->instruction)) : ?>
                                <form action="<?php echo URLROOT; ?>/submissions/review/theory_questions" method="POST">
                                    <input type="hidden" name="term" value="<?= $data['term']; ?>">
                                    <input type="hidden" name="year" value="<?= $data['year']; ?>">
                                    <input type="hidden" name="subject" value="<?php echo $data['params']->subject; ?>">
                                    <input type="hidden" name="class" value="<?php echo $data['params']->class; ?>">
                                    <input type="hidden" name="paperID" value="<?php echo $data['paperID']; ?>">
                                    <input type="hidden" value="<?= $data['params2']->num_rows; ?>" name="num_rows" />
                                    <div class="my-4">
                                        <label for="className">Exam instruction for theory section</label>
                                        <input name="instruction" value="<?= $data['params2']->instruction; ?>" class="form-control form-control-lg" data-parsley-trigger="keyup" />
                                    </div><!--===== Theory section Instruction Ends =====-->
                                    <div class="d-grid">
                                        <input type="submit" name="set" value="Update Theory Section" class="btn btn-outline-primary">
                                    </div><!--===== Submit Button Ends =====-->
                                </form><!--===== Set Question Form Ends =====-->
                            <?php endif; ?>
                        <?php endif; ?>

                    </div>
                </div>

            </div>
        </div>
    </section>

</main><!-- End #main -->


<?php require APPROOT . '/views/inc/footer.php'; ?>