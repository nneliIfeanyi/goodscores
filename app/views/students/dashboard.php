<?php require APPROOT . '/views/students/inc/header.php'; ?>
<?php require APPROOT . '/views/students/inc/navbar.php'; ?>
<?php require APPROOT . '/views/students/inc/sidebar.php'; ?>



<main id="main" class="main">

    <div class="pagetitle">
        <h1>Dashboard</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= URLROOT; ?>/students/dashboard">Home</a></li>
                <li class="breadcrumb-item">Students</li>
                <li class="breadcrumb-item active">Dashboard</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <div class="card">
        <div class="card-body pb-0">
            <h5 class="card-title">Recent | Published</h5>
            <div class="table-responsive">
                <table class="table table-borderless">
                    <thead>
                        <tr>

                            <th scope="col">Subject</th>
                            <th>Tag</th>
                            <th scope="col">Duration</th>
                            <th>Status</th>
                            <th>Score</th>
                            <th scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody>

                        <?php
                        if (!empty($data['recent'])) :

                            foreach ($data['recent'] as $recent) : ?>
                                <?php if (!$this->studentModel->checkIfExamTaken($recent->paperID)) : ?>


                                    <tr>
                                        <td><?= $recent->subject ?></td>
                                        <td class="badge text-bg-primary"><?= $recent->publishedAS ?></td>
                                        <td><?= $recent->duration ?> min.</td>
                                        <td><span class="badge bg-warning">Pending</span></td>

                                        <td></td>
                                        <td>
                                            <span class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#section<?= $recent->id; ?>">start <i class="bi bi-chevron-right"></i></span>
                                        </td>
                                    </tr>
                                    <!-- Modal -->
                                    <div class="modal fade" id="section<?= $recent->id; ?>" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h4 class="modal-title fw-bold">Welcome On Board !</h4>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <p class="lead">This Paper will last for <strong class="text-primary"><?= $recent->duration ?> Minutes!</strong></p>
                                                    <p class="text-muted">Ensure you submit your work before the time runs out.</p>
                                                    <p class="fw-semibold">Best of luck!</p>
                                                </div>
                                                <div class="modal-footer">
                                                    <div class="d-flex gap-4">
                                                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal" aria-label="Close">Cancel</button>
                                                        <a href="<?= URLROOT; ?>/students/timeCalc/<?= $recent->paperID; ?>" class="btn btn-primary">Start <i class="bi bi-chevron-right"></i></a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div><!-- End Modal-->
                                <?php else:
                                    $score = $this->studentModel->checkRowExist($recent->subject, $recent->class, $recent->term);
                                ?>
                                    <tr>
                                        <td><?= $recent->subject ?></td>
                                        <td class="badge text-bg-primary"><?= $recent->publishedAS ?></td>
                                        <td><?= $recent->duration ?> min.</td>
                                        <td><span class="badge bg-success">Completed</span></td>
                                        <?php if ($recent->publishedAS == 'CA1'): ?>
                                            <td><?= $score->CA1 ?></td>
                                        <?php elseif ($recent->publishedAS == 'CA2'): ?>
                                            <td><?= $score->CA2 ?></td>
                                        <?php elseif ($recent->publishedAS == 'exam'): ?>
                                            <td><?= $score->exam ?></td>
                                        <?php endif; ?>
                                        <td>
                                            <a href="<?= URLROOT; ?>/students/submit_cbt/<?= $recent->paperID; ?>" class="btn btn-outline-success btn-sm"><i class="bi bi-eye"></i> View</a>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <tr>
                                <td class="text-danger">No data set</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    </div>



</main>
<?php require APPROOT . '/views/inc/footer.php'; ?>