<?php require APPROOT . '/views/inc/header.php'; ?>
<?php require APPROOT . '/views/inc/navbar.php'; ?>
<?php require APPROOT . '/views/inc/sidebar.php'; ?>

<main id="main" class="main">

    <div class="pagetitle">
        <h1>Students</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= URLROOT; ?>/students/dashboard">Home</a></li>
                <li class="breadcrumb-item">Students</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <section class="section dashboard">
        <div class="row">

            <!-- Left side columns -->
            <div class="col-lg-10">
                <div class="row">

                    <!-- Subjects Card -->
                    <div class="col-xxl-4 col-md-6">
                        <div class="card info-card sales-card">

                            <div class="filter">
                                <label class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></label>
                                <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                                    <li class="dropdown-header text-start">
                                        <h6>Go to</h6>
                                    </li>

                                    <li><a class="dropdown-item" href="<?php echo URLROOT; ?>/students/add">Add Student</a></li>
                                </ul>
                            </div>

                            <div class="card-body">
                                <h5 class="card-title">All <span>| Students</span></h5>

                                <div class="d-flex align-items-center">
                                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                        <a href="<?php echo URLROOT; ?>/students/add" data-bs-toggle="tooltip" data-bs-title="Add Student"><i class="bi bi-person-plus"></i></a>
                                    </div>
                                    <div class="ps-3">
                                        <h6><?php echo $data['count']; ?></h6>
                                        <!-- <span class="ext-success small pt-1 fw-bold">12%</span> <span class="text-muted small pt-2 ps-1">increase</span> -->

                                    </div>
                                </div>
                            </div>

                        </div>
                    </div><!-- End Subjects Card -->
                    <!-- Recent Activity -->
                    <div class="col-12">
                        <div class="card top-selling overflow-auto">

                            <div class="card-body pb-0">
                                <h5 class="card-title">Manage | Students</h5>
                                <!-- Bordered Tabs -->

                                <ul class="nav nav-tabs nav-tabs-bordered">
                                    <li class="nav-item">
                                        <button type="button" class="nav-link" data-bs-toggle="tab" data-bs-target="#students">All Students</button>
                                    </li>
                                    <?php foreach ($data['classes'] as $class) : ?>
                                        <li class="nav-item">
                                            <button type="button" class="nav-link" data-bs-toggle="tab" data-bs-target="#class<?= $class->id ?>"><?= $class->classname ?></button>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                                <div class="tab-content pt-2">
                                    <div class="tab-pane fade profile-overview" id="students">
                                        <h5 class="card-title">A Total Of <span class="fs-4 fw-bold text-secondary"><?= $data['count'] ?></span> Students</h5>
                                        <table class="table table-borderless">
                                            <thead>
                                                <tr>

                                                    <th scope="col">S|N</th>
                                                    <th scope="col">Photo</th>
                                                    <th scope="col">Fullname</th>
                                                    <th scope="col">Class</th>
                                                    <th scope="col">Passkey</th>
                                                    <th scope="col">View</th>
                                                </tr>
                                            </thead>
                                            <tbody>

                                                <?php

                                                if (!empty($this->studentModel->getStudents())) :
                                                    $students = $this->studentModel->getStudents();
                                                    $num = 1;
                                                    foreach ($students as $student) : ?>
                                                        <tr>
                                                            <td><?= $num ?></td>
                                                            <td>
                                                                <?php if (empty($student->photo)) : ?>
                                                                    <i class="bi bi-person fs-2 text-primary"></i>
                                                                <?php else : ?>
                                                                    <img src="<?= URLROOT . '/' . $student->photo ?>" height="80" width="90" alt="Student-photo">
                                                                <?php endif; ?>
                                                            </td>
                                                            <td><?= $student->surname ?> <?= $student->firstname ?> <?= $student->middlename ?></td>
                                                            <td><?= $student->class; ?></td>
                                                            <td><?= $student->passkey; ?></td>
                                                            <td class="d-flex gap-2">
                                                                <a href="<?= URLROOT ?>/students/edit/<?= $student->id ?>" class=""><i class="bi bi-pen"></i></a>
                                                                <a href="<?= URLROOT ?>/students/profile/<?= $student->id ?>" class=""><i class="bi bi-eye"></i></a>
                                                            </td>
                                                        </tr>

                                                    <?php $num++;
                                                    endforeach;
                                                else : ?>
                                                    <tr>
                                                        <td class="text-danger">No data set</td>
                                                    </tr>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <?php foreach ($data['classes'] as $class) :

                                        if (!empty($this->studentModel->countPerClass($class->classname))) {
                                            $total_per_class = $this->studentModel->countPerClass($class->classname);
                                        } else {
                                            $total_per_class = 0;
                                        }
                                    ?>
                                        <div class="tab-pane fade profile-overview" id="class<?= $class->id ?>">
                                            <h5 class="card-title">A Total Of <span class="fs-4 fw-bold text-secondary"><?= $total_per_class ?></span> Students</h5>
                                            <table class="table table-borderless">
                                                <thead>
                                                    <tr>

                                                        <th scope="col">S|N</th>
                                                        <th scope="col">Photo</th>
                                                        <th scope="col">Fullname</th>
                                                        <th scope="col">Passkey</th>
                                                        <th scope="col">View</th>
                                                    </tr>
                                                </thead>
                                                <tbody>

                                                    <?php

                                                    if (!empty($this->studentModel->studentsPerClass($class->classname))) :
                                                        $students = $this->studentModel->studentsPerClass($class->classname);
                                                        $num = 1;
                                                        foreach ($students as $student) : ?>
                                                            <tr>
                                                                <td><?= $num ?></td>
                                                                <td>
                                                                    <?php if (empty($student->photo)) : ?>
                                                                        <i class="bi bi-person fs-2 text-primary"></i>
                                                                    <?php else : ?>
                                                                        <img src="<?= URLROOT . '/' . $student->photo ?>" height="80" width="90" alt="Student-photo">
                                                                    <?php endif; ?>
                                                                </td>
                                                                <td><?= $student->surname ?> <?= $student->firstname ?> <?= $student->middlename ?></td>
                                                                <td><?= $student->passkey; ?></td>
                                                                <td class="d-flex gap-2">
                                                                    <a href="<?= URLROOT ?>/students/edit/<?= $student->id ?>" class=""><i class="bi bi-pen"></i></a>
                                                                    <a href="<?= URLROOT ?>/students/profile/<?= $student->id ?>" class=""><i class="bi bi-eye"></i></a>
                                                                </td>
                                                            </tr>

                                                        <?php $num++;
                                                        endforeach;
                                                    else : ?>
                                                        <tr>
                                                            <td class="text-danger">No data set</td>
                                                        </tr>
                                                    <?php endif; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    <?php endforeach; ?>
                                </div><!-- End Bordered Tabs -->

                            </div>

                        </div>
                    </div><!-- End Recent Activity -->

                </div>
            </div>
        </div>
    </section>

</main><!-- End #main -->


<?php require APPROOT . '/views/inc/footer.php'; ?>