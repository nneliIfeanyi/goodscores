<?php require APPROOT . '/views/inc/header.php'; ?>
<?php require APPROOT . '/views/inc/navbar.php'; ?>
<?php require APPROOT . '/views/inc/sidebar.php'; ?>



<main id="main" class="main">

    <div class="pagetitle">
        <h1>Add Students</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= URLROOT; ?>/users/dashboard">Home</a></li>
                <li class="breadcrumb-item">Students</li>
                <li class="breadcrumb-item active">Add</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <section class="section">
        <div class="row">
            <div class="col-md-10 col-lg-8">

                <div class="card">
                    <div class="card-body">

                        <div class="alert alert-primary bg-primary text-light border-0 alert-dismissible fade show" role="alert">
                            <strong>Add Students To Database</strong>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        <form action="<?php echo URLROOT; ?>/students/add" method="POST">
                            <input type="hidden" name="term" value="<?= TERM; ?>">
                            <input type="hidden" name="year" value="<?= SCH_SESSION; ?>">
                            <div class="my-4">
                                <label for="className">Firstname</label>
                                <input type="text" name="firstname" class="form-control form-control-lg" data-parsley-trigger="keyup" />
                            </div>
                            <div class="my-4">
                                <label for="className">Middle name</label>
                                <input type="text" name="middlename" class="form-control form-control-lg" />
                            </div>
                            <div class="my-4">
                                <label for="className">Surname</label>
                                <input type="text" name="surname" class="form-control form-control-lg" />
                            </div>

                            <!-- <div class="my-4">
                                <label for="className">Date of Birth</label>
                                <input type="date" name="dob" class="form-control form-control-lg" />
                            </div> -->
                            <div class="my-4">
                                <select name="gender" class="form-control form-control-lg">
                                    <option value="">Gender</option>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                </select>
                            </div>
                            <div class="my-4">
                                <select name="class" class="form-control form-control-lg">
                                    <option value="">Select class</option>
                                    <?php foreach ($data['classes'] as $class) : ?>
                                        <option value="<?= $class->classname ?>"><?= $class->classname ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="my-4">
                                <label for="className">Reg. Number <small>(Reg. no is auto generated)</small></label>
                                <input type="text" name="regNo" value="prefix_<?= $data['regNo'] ?>" class="form-control form-control-lg" />
                            </div>
                            <div class="d-grid mx-4">
                                <input type="submit" value="Continue" class="btn btn-outline-primary">
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

</main><!-- End #main -->


<?php require APPROOT . '/views/inc/footer.php'; ?>