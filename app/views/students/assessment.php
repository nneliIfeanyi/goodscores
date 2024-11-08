<?php require APPROOT . '/views/inc/header.php'; ?>
<?php require APPROOT . '/views/inc/navbar.php'; ?>
<?php require APPROOT . '/views/inc/sidebar.php'; ?>

<main id="main" class="main">

    <div class="pagetitle">
        <h1>Assessment</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= URLROOT; ?>/students/dashboard">Home</a></li>
                <li class="breadcrumb-item">Students</li>
                <li class="breadcrumb-item">Assessment</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <section class="section dashboard">
        <div class="card">

            <div class="card-body">
                <h5 class="card-title">Current Term | <span class=""><?= TERM; ?></span></h5>
                <form action="<?php echo URLROOT; ?>/students/assessment" method="POST">
                    <div class="row">
                        <div class="col-md-6 col-lg-3">
                            <div class="">
                                <select class="form-control form-control-lg" name="term">
                                    <option value=""><?= (empty($data['term'])) ? 'Select Term' : $data['term']; ?></option>
                                    <option value="1st term">1st term</option>
                                    <option value="2nd term">2nd term</option>
                                    <option value="3rd term">3rd term</option>
                                </select>
                            </div><!--===== Subject Ends =====-->
                        </div>
                        <div class="col-md-6 col-lg-3">
                            <div class="">
                                <select class="form-control form-control-lg" name="subject">
                                    <option value=""><?= (empty($data['subject'])) ? 'Select Subject' : $data['subject']; ?></option>
                                    <?php if (empty($data['subjects'])) : ?>
                                        <option value="">No added subject</option>
                                    <?php else : ?>
                                        <?php foreach ($data['subjects'] as $subject) : ?>
                                            <option value="<?php echo $subject->subject; ?>"><?php echo $subject->subject; ?></option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div><!--===== Subject Ends =====-->
                        </div>
                        <div class="col-md-6 col-lg-3">
                            <div class="">
                                <select class="form-control form-control-lg" name="class">
                                    <option value=""><?= (empty($data['class'])) ? 'Select Class' : $data['class']; ?></option>
                                    <?php if (empty($data['classes'])) : ?>
                                        <option value="">No added subject</option>
                                    <?php else : ?>
                                        <?php foreach ($data['classes'] as $class) : ?>
                                            <option value="<?php echo $class->classname; ?>"><?php echo $class->classname; ?></option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div><!--===== Subject Ends =====-->
                        </div>
                        <div class="col-md-6 col-lg-3">
                            <div class="d-grid">
                                <button type="submit" class="btn btn-outline-primary py-2 mt-1">
                                    Continue <i class="bi bi-send"></i>
                                </button>
                            </div>
                        </div>

                    </div>
                </form>
                <hr />
                <div class="table-responsive">
                    <table class="table table-borderless">
                        <thead>
                            <tr>

                                <th scope="col">S|N</th>
                                <th scope="col">Photo</th>
                                <th scope="col">Fullname</th>
                                <th scope="col">1st CA</th>
                                <th scope="col">2nd CA</th>
                                <th scope="col">Exam</th>
                            </tr>
                        </thead>
                        <tbody>

                            <?php

                            if (!empty($data['scores'])) :
                                $num = 1;
                                foreach ($data['scores'] as $score) :
                                    $student = $this->studentModel->findStudentById($score->student_id);
                            ?>
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
                                        <td><span class="ms-2 fw-semibold"><?= $score->CA1; ?></span></td>
                                        <td><span class="ms-2 fw-semibold"><?= $score->CA2; ?></span></td>
                                        <td><span class="ms-2 fw-semibold"><?= $score->exam; ?></span></td>
                                        <td class="d-flex gap-2">
                                            <a href="<?= URLROOT ?>/students/scoring/<?= $score->id ?>"><i class="bi bi-pen"></i></a>
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
            </div>
        </div>
    </section>

</main><!-- End #main -->


<?php require APPROOT . '/views/inc/footer.php'; ?>