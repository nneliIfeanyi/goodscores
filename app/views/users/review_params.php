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
            <div class="col-lg-6">

                <div class="card">
                    <div class="card-body">

                        <div class="alert alert-primary bg-primary text-light border-0 alert-dismissible fade show" role="alert">
                            <strong>Review Examination Parameters!</strong>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        <form action="<?php echo URLROOT; ?>/submissions/review/<?= $data['section']; ?>" method="POST">
                            <input type="hidden" name="paperID" value="<?= $data['params']->paperID; ?>">
                            <div class="my-4">
                                <label for="className">Section tag <span style="font-size: small;">(eg.Section A)</span></label>
                                <input type="text" name="section_tag" value="<?= $data['params']->tag; ?>" required class="form-control form-control-lg" data-parsley-trigger="keyup" />
                            </div>
                            <?php if ($data['section'] == 'others') : ?>
                                <div class="my-4">
                                    <label for="className">Section name</label>
                                    <input type="text" name="section_alt" class="form-control form-control-lg" value="<?= $data['params']->section_alt; ?>" />
                                </div>
                            <?php else : ?>
                                <div class="my-4">
                                    <label for="className">Section name</label>
                                    <input type="text" disabled name="section_name" class="form-control form-control-lg" value="<?= $data['section']; ?>" />
                                </div>
                            <?php endif; ?>
                            <div class="my-4">
                                <select class="form-control form-control-lg" name="class">
                                    <option value="<?php echo $data['params']->class; ?>"><?php echo $data['params']->class; ?></option>
                                </select>
                            </div><!--===== Class Ends =====-->
                            <div class="my-4">
                                <select class="form-control form-control-lg" name="subject">
                                    <option value="<?php echo $data['params']->subject; ?>"><?php echo $data['params']->subject; ?></option>
                                </select>
                            </div><!--===== Subject Ends =====-->
                            <?php if ($data['section'] == 'objectives_questions') : ?>
                                <div class="my-4">
                                    <label for="className">Number of objective questions</label>
                                    <input type="number" value="<?= $data['params']->num_rows; ?>" name="num_rows" required class="form-control form-control-lg" data-parsley-trigger="keyup" />
                                </div><!--===== Objectives section Num_rows Ends =====-->
                                <div class="my-4">
                                    <label for="className">Exam instruction for objective section</label>
                                    <input name="instruction" value="<?= $data['params']->instruction; ?>" class="form-control form-control-lg" data-parsley-trigger="keyup" />
                                </div>
                            <?php elseif ($data['section'] == 'theory_questions') : ?>
                                <div class="my-4">
                                    <label for="className">Number of theory questions</label>
                                    <input type="number" value="<?= $data['params']->num_rows; ?>" name="num_rows" required class="form-control form-control-lg" data-parsley-trigger="keyup" />
                                </div><!--===== Theory section Num_rows Ends =====-->
                                <div class="my-4">
                                    <label for="className">Exam instruction for this section</label>
                                    <input name="instruction" value="<?= $data['params']->instruction; ?>" class="form-control form-control-lg" data-parsley-trigger="keyup" />
                                </div><!--===== Theory section Instruction Ends =====-->
                            <?php elseif ($data['section'] == 'others') : ?>
                                <div class="my-4">
                                    <label for="className">Number of questions</label>
                                    <input type="number" value="<?= $data['params']->num_rows; ?>" name="num_rows" required class="form-control form-control-lg" data-parsley-trigger="keyup" />
                                </div>
                                <div class="my-4">
                                    <label for="className">Exam instruction for this section</label>
                                    <input name="instruction" value="<?= $data['params']->instruction; ?>" class="form-control form-control-lg" data-parsley-trigger="keyup" />
                                </div><!--===== Theory section Instruction Ends =====-->
                            <?php endif; ?>

                            <div class="d-grid">
                                <input type="submit" name="set" value="Save Changes" class="btn btn-outline-primary">
                            </div><!--===== Submit Button Ends =====-->
                        </form><!--===== Set Question Form Ends =====-->
                        <div class="d-grid mt-3">
                            <a href="<?= URLROOT; ?>/users/set/others?class=<?= $data['params']->class; ?>&subject=<?= $data['params']->subject; ?>" class="btn btn-success">Append Section</a>
                        </div>
                        <div class="d-grid mt-3">
                            <span data-bs-toggle="modal" data-bs-target="#section<?= $data['params']->paperID; ?>" class="btn btn-danger">Delete this section</span>
                        </div>
                        <!-- Delete Modal -->
                        <div class="modal fade" id="section<?= $data['params']->paperID; ?>" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Are you sure to delete <span class="text-primary"> <?php echo $data['params']->class; ?> <?php echo $data['params']->subject; ?> <?php echo $data['params']->section; ?>?</span></h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        This action cannot be reversed!
                                    </div>
                                    <div class="modal-footer">
                                        <div class="d-flex gap-4">
                                            <button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal" aria-label="Close">Cancel</button>
                                            <form action="<?php echo URLROOT; ?>/posts/delete_section/<?= $data['params']->paperID; ?>" method="POST">
                                                <input type="hidden" name="section" value="<?= $data['params']->section; ?>">
                                                <input type="hidden" name="section_alt" value="<?= $data['params']->section_alt; ?>">
                                                <input class="btn btn-danger" type="submit" name="submit" value="Yes Continue">
                                            </form>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div><!-- End Delete Modal-->
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <?php if ($_COOKIE['cbt'] == '1') : ?>
                    <div class="card">
                        <div class="card-body">

                            <div class="alert alert-primary bg-secondary text-light border-0 alert-dismissible fade show" role="alert">
                                <strong>Alter CBT Settings</strong>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                            <form action="<?php echo URLROOT; ?>/submissions/core_paper_edit/<?= $data['params']->paperID; ?>" method="POST">

                                <div class="my-4 row">
                                    <label for="">Enter Duration</label>
                                    <div class="col-6">
                                        <div class="input-group mb-3">
                                            <input type="number" value="<?= $data['hr']; ?>" name="hr" required class="form-control form-control-lg" />
                                            <span class="input-group-text" id="basic-addon2">Hr</span>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="input-group mb-3">
                                            <input type="number" value="<?= $data['min']; ?>" name="min" required class="form-control form-control-lg" />
                                            <span class="input-group-text" id="basic-addon2">min</span>
                                        </div>
                                    </div>
                                    <!-- <label for="">Enter Duration in hours <span style="font-size: smaller;"></span></label>
                                    <div class="col-3">
                                        <input type="number" value="<?= $data['core']->duration; ?>" name="duration" required class="form-control form-control-lg" />
                                    </div> -->
                                </div>
                                <div class="my-4">
                                    <label>Publish As</label>
                                    <select class="form-control form-control-lg" required name="publishAS">
                                        <option value="<?= $data['core']->publishedAS; ?>"><?= $data['core']->publishedAS; ?></option>
                                        <option value="1st CA">1st CA</option>
                                        <option value="2nd CA">2nd CA</option>
                                        <option value="3rd CA">3rd CA</option>
                                        <option value="Exam">Exam</option>
                                        <option value="Take Home Assignment">Take Home Assignment</option>
                                    </select>
                                </div>
                                <div class="d-grid">
                                    <input type="submit" name="set" value="Save Changes" class="btn btn-outline-primary">
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