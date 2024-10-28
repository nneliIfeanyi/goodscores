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

            <table class="table table-borderless">
                <thead>
                    <tr>

                        <th scope="col">Subject</th>
                        <th>Tag</th>
                        <th scope="col">Duration</th>
                        <th>Status</th>
                        <th>Score</th>
                        <th>Remark</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody>

                    <?php
                    if (!empty($data['recent'])) :

                        foreach ($data['recent'] as $recent) :
                            if (!$this->studentModel->checkIfExamTaken($recent->paperID)) :
                    ?>

                                <tr>
                                    <td><?= $recent->subject ?></td>
                                    <td class="badge text-bg-primary"><?= $recent->publishedAS ?></td>
                                    <td><?= $recent->duration ?> Minutes</td>
                                    <td><span class="badge bg-warning">Pending</span></td>
                                    <td></td>
                                    <td></td>
                                    <td>
                                        <span class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#section<?= $recent->id; ?>"><i class="bi bi-chevron-right"></i></span>
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
                            <?php else :
                                $details = $this->studentModel->checkIfExamTaken($recent->paperID);
                            ?>
                                <tr>
                                    <td><?= $recent->subject ?></td>
                                    <td class="badge text-bg-primary"><?= $recent->publishedAS ?></td>
                                    <td><?= $recent->duration ?> Minutes</td>
                                    <td><span class="badge bg-success">Completed</span></td>
                                    <td><?= $details->score; ?>%</td>
                                    <?php if ($details->score >= 50) : ?>
                                        <td><span class="badge bg-success">Passed!</span></td>
                                    <?php else : ?>
                                        <td><span class="badge bg-danger">Failed!</span></td>
                                    <?php endif; ?>
                                    <td class="">
                                        <!-- <span class="btn btn-primary btn-sm"><i class="bi bi-chevron-right"></i></span> -->
                                    </td>
                                </tr>
                            <?php endif; ?>
                        <?php endforeach;
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



</main>
<?php require APPROOT . '/views/inc/footer.php'; ?>