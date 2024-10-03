<?php require APPROOT . '/views/inc/header.php'; ?>
<?php require APPROOT . '/views/inc/navbar.php'; ?>
<?php require APPROOT . '/views/inc/sidebar.php'; ?>

<main id="main" class="main">

    <div class="pagetitle">
        <h1>Review</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= URLROOT; ?>/users/dashboard">Home</a></li>
                <li class="breadcrumb-item">Users</li>
                <li class="breadcrumb-item active"><?php echo $data['section']; ?></li>
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
                        <form action="<?php echo URLROOT; ?>/submissions/set/<?php echo $data['section']; ?>" method="POST">
                            <input type="hidden" name="term" value="<?= TERM; ?>">
                            <input type="hidden" name="year" value="<?= SCH_SESSION; ?>">
                            <div class="my-4">
                                <select class="form-control form-control-lg" name="class" required>
                                    <option value="<?php echo $data['params']->class; ?>"><?php echo $data['params']->class; ?></option>
                                    <?php if (empty($data['classes'])) : ?>
                                        <option value="">No added class</option>
                                    <?php else : ?>
                                        <?php foreach ($data['classes'] as $class) : ?>
                                            <option value="<?php echo $class->classname; ?>"><?php echo $class->classname; ?></option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div><!--===== Class Ends =====-->
                            <div class="my-4">
                                <select class="form-control form-control-lg" name="subject" required>
                                    <option value="<?php echo $data['params']->subject; ?>"><?php echo $data['params']->subject; ?></option>
                                    <?php if (empty($data['subjects'])) : ?>
                                        <option value="">No added subject</option>
                                    <?php else : ?>
                                        <?php foreach ($data['subjects'] as $subject) : ?>
                                            <option value="<?php echo $subject->subject; ?>"><?php echo $subject->subject; ?></option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div><!--===== Subject Ends =====-->
                            <?php if ($data['section'] == 'objectives_questions') : ?>
                                <div class="my-4">
                                    <label for="className">Number of objective questions</label>
                                    <input type="number" name="num_rows" required class="form-control form-control-lg" data-parsley-trigger="keyup" />
                                </div><!--===== Objectives section Num_rows Ends =====-->
                                <div class="my-4">
                                    <label for="className">Exam instruction for objective section</label>
                                    <input name="instruction" class="form-control form-control-lg" data-parsley-trigger="keyup" />
                                </div><!--===== Objectives section Instruction Ends =====-->
                            <?php elseif ($data['section'] == 'theory_questions') : ?>
                                <div class="my-4">
                                    <label for="className">Number of theory questions</label>
                                    <input type="number" name="num_rows" required class="form-control form-control-lg" data-parsley-trigger="keyup" />
                                </div><!--===== Theory section Num_rows Ends =====-->
                                <div class="my-4">
                                    <label for="className">Exam instruction for theory section</label>
                                    <input name="instruction" class="form-control form-control-lg" data-parsley-trigger="keyup" />
                                </div><!--===== Theory section Instruction Ends =====-->
                            <?php endif; ?>
                            <div class="my-4">
                                <label for="className">Exam Duration</label>
                                <input name="duration" class="form-control form-control-lg" data-parsley-trigger="keyup" />

                            </div><!--===== Duration Ends =====-->

                            <div class="d-grid">
                                <input type="submit" name="set" value="Continue" class="btn btn-outline-primary">
                            </div><!--===== Submit Button Ends =====-->
                        </form><!--===== Set Question Form Ends =====-->

                    </div>
                </div>

            </div>
        </div>
    </section>

</main><!-- End #main -->


<?php require APPROOT . '/views/inc/footer.php'; ?>