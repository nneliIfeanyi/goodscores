<?php require APPROOT . '/views/students/inc/header.php';
if ($this->isLoggedIn2()) {
    require APPROOT . '/views/inc/navbar.php';
    require APPROOT . '/views/inc/sidebar.php';
} else {
    require APPROOT . '/views/students/inc/navbar.php';
    require APPROOT . '/views/students/inc/sidebar.php';
}

?>


<main id="main" class="main">

    <div class="pagetitle">
        <h1>Edit Student</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= URLROOT; ?>/students/dashboard">Home</a></li>
                <li class="breadcrumb-item">Students</li>
                <li class="breadcrumb-item active">Edit</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <section class="section">
        <div class="row">
            <div class="col-md-10 col-lg-8">

                <div class="card">
                    <div class="card-body">

                        <div class="alert alert-primary bg-primary text-light border-0 alert-dismissible fade show" role="alert">
                            <strong>Update details for <?= $data['student']->surname ?> <?= $data['student']->firstname ?> <?= $data['student']->middlename ?></strong>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        <form action="<?php echo URLROOT; ?>/students/edit/<?= $data['student']->id ?>" method="POST">
                            <input type="hidden" name="term" value="<?= TERM; ?>">
                            <input type="hidden" name="year" value="<?= SCH_SESSION; ?>">
                            <div class="my-4">
                                <label for="firstName">Firstname</label>
                                <input id="firstName" type="text" name="firstname" value="<?= $data['student']->firstname; ?>" class="form-control form-control-lg" data-parsley-trigger="keyup" />
                            </div>
                            <div class="my-4">
                                <label for="middleName">Middle name</label>
                                <input id="middleName" type="text" name="middlename" value="<?= $data['student']->middlename; ?>" class="form-control form-control-lg" />
                            </div>
                            <div class="my-4">
                                <label for="surName">Surname</label>
                                <input id="surName" type="text" name="surname" value="<?= $data['student']->surname ?>" class="form-control form-control-lg" />
                            </div>

                            <div class="my-4">
                                <label for="dateOfBirth">Date of Birth</label>
                                <input id="dateOfBirth" type="date" name="dob" value="<?= $data['student']->dob ?>" class="form-control form-control-lg" />
                            </div>
                            <div class="my-4">
                                <label for="Gender">Gender</label>
                                <select id="Gender" name="gender" class="form-control form-control-lg">
                                    <option value="<?= $data['student']->gender ?>"><?= $data['student']->gender ?></option>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                </select>
                            </div>
                            <div class="my-4">
                                <label for="email">Email</label>
                                <input id="email" type="email" name="email" value="<?= $data['student']->email ?>" class="form-control form-control-lg" />
                            </div>
                            <div class="my-4">
                                <label for="phoneNumber">Phone</label>
                                <input id="phoneNumber" type="text" name="phone" value="<?= $data['student']->phone ?>" class="form-control form-control-lg" />
                            </div>
                            <div class="my-4">
                                <label for="Class">Class</label>
                                <select id="Class" name="class" class="form-control form-control-lg">
                                    <option value="<?= $data['student']->class ?>"><?= $data['student']->class ?></option>
                                    <?php foreach ($data['classes'] as $class) : ?>
                                        <option value="<?= $class->classname ?>"><?= $class->classname ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="my-4">
                                <label for="Religion">Religion</label>
                                <input id="Religion" type="text" name="religion" value="<?= $data['student']->religion ?>" class="form-control form-control-lg" />
                            </div>
                            <div class="my-4">
                                <label for="address">Resident Address</label>
                                <input id="address" type="text" name="address" value="<?= $data['student']->address ?>" class="form-control form-control-lg" />
                            </div>
                            <div class="my-4">
                                <label for="stateOfOrigin">State of origin</label>
                                <input id="stateOfOrigin" type="text" name="state" value="<?= $data['student']->state ?>" class="form-control form-control-lg" />
                            </div>
                            <div class="my-4">
                                <label for="country">Country</label>
                                <input id="country" type="text" name="country" value="<?= $data['student']->country ?>" class="form-control form-control-lg" />
                            </div>
                            <div class="my-4">
                                <label for="regNo">Reg. Number</label>
                                <input id="regNo" type="text" name="regNo" value="<?= $data['student']->passkey ?>" disabled class="form-control form-control-lg" />
                            </div>
                            <div class="d-flex gap-2">
                                <input type="submit" value="Save changes" class="btn btn-outline-primary">
                                <a href="javascript:void()" onclick="history.back()" class="btn btn-secondary"><i class="bi bi-chevron-left"></i> Go Back</a>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

</main><!-- End #main -->


<?php require APPROOT . '/views/inc/footer.php'; ?>