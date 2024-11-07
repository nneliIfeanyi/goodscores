<?php require APPROOT . '/views/students/inc/header.php';
require APPROOT . '/views/students/inc/navbar.php';
require APPROOT . '/views/students/inc/sidebar.php';
?>



<main id="main" class="main">

    <!-- End Page Title -->
    <div class="row text-center">
        <div class="col-lg-6">
            <div class="pagetitle">
                <h1 class="text-success fw-bold h1">Success!</h1>
            </div>
            <div class="card">
                <div class="card-body pb-3">
                    <h5 class="card-title"><?= $data['subject']; ?> | CA Test</h5>

                    <div class="badge rounded-circle p-5 bg-success">
                        <p class="fw-bold fs-1"><?= $data['score']; ?>%</p>
                    </div>
                    <p><span class="fs-2 fw-bold">PASSED!</span><br /> Based on 50% and above.</p>

                </div>

            </div>
        </div>
    </div>
    <?php
    if (!empty($data['cbt'])) :
        $n = 1;
        foreach ($data['cbt'] as $recent) : ?>
            <div class="border-bottom mb-2">
                <!-- Question Div -->
                <div class="d-flex flex-row gap-2 m-0">
                    <p class="fw-semibold"><?= $n; ?> </p>
                    <?= $recent->question; ?>
                </div>
                <p class="text-success fst-italic p-2 m-0 p-0">Answer : <span class="fw-bold"><?= $recent->ans; ?></p>
                <!-- OPtions Div -->
                <div class="d-flex flex-wrap fw-bold m-0 py-3" style="font-size: 14px;">
                    <?php if ($recent->isSubjective == 'yes') : ?>
                        <div class="">
                            <input value="<?= $recent->response; ?>" class="form-control <?= ($recent->response == $recent->ans) ? 'border-success' : 'border-danger'; ?>" />
                        </div>
                    <?php endif; ?>
                    <div class="mx-3 my-1">
                        <?php if (!empty($recent->opt1)) : ?>
                            <div class="form-check <?= ($recent->response == 'a' && $recent->ans == 'a') ? 'border-success' : ''; ?> <?= ($recent->response == 'a' && $recent->ans != 'a') ? 'border-danger' : ''; ?> <?= ($recent->response != 'a' && $recent->ans == 'a') ? 'border-success' : ''; ?> border pe-1 ">
                                <input type="radio" <?= ($recent->response == 'a') ? 'checked' : ''; ?> class="form-check-input fs-2" id="A">
                                <label style="margin-left: -11px;" for="A" class="form-check-label mt-3"><?= $recent->opt1; ?></label>
                            </div><i class="bi "></i>
                        <?php endif; ?>
                    </div>
                    <div class="mx-3 my-1">
                        <?php if (!empty($recent->opt2)) : ?>
                            <div class="form-check border <?= ($recent->response == 'b' && $recent->ans == 'b') ? 'border-success' : ''; ?><?= ($recent->response == 'b' && $recent->ans != 'b') ? 'border-danger' : ''; ?> <?= ($recent->response != 'b' && $recent->ans == 'b') ? 'border-success' : ''; ?> pe-1">
                                <input type="radio" <?= ($recent->response == 'b') ? 'checked' : ''; ?> class="form-check-input fs-2" id="B">
                                <label style="margin-left: -11px;" for="B" class="form-check-label mt-3"><?= $recent->opt2; ?></label>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="mx-3 my-1">
                        <?php if (!empty($recent->opt3)) : ?>
                            <div class="form-check border <?= ($recent->response == 'c' && $recent->ans == 'c') ? 'border-success' : ''; ?> <?= ($recent->response == 'c' && $recent->ans != 'c') ? 'border-danger' : ''; ?> <?= ($recent->response != 'c' && $recent->ans == 'c') ? 'border-success' : ''; ?> pe-1">
                                <input type="radio" <?= ($recent->response == 'c') ? 'checked' : ''; ?> class="form-check-input fs-2" id="C">
                                <label style="margin-left: -11px;" for="C" class="form-check-label mt-3"><?= $recent->opt3; ?></label>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="ms-3 my-1">
                        <?php if (!empty($recent->opt4)) : ?>
                            <div class="form-check border <?= ($recent->response == 'd' && $recent->ans == 'd') ? 'border-success' : ''; ?><?= ($recent->response == 'd' && $recent->ans != 'd') ? 'border-danger' : ''; ?> <?= ($recent->response != 'd' && $recent->ans == 'd') ? 'border-success' : ''; ?> pe-1">
                                <input type="radio" <?= ($recent->response == 'd') ? 'checked' : ''; ?> class="form-check-input fs-2" id="D">
                                <label style="margin-left: -11px;" for="D" class="form-check-label mt-3"><?= $recent->opt4; ?></label>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php $n++;
        endforeach; ?>
        <div class="row">
            <div class="col-md-8 col-lg-6 my-5 pb-3">
                <a href="<?= URLROOT; ?>/students/dashboard" class="btn btn-outline-primary w-100"><i class="bi bi-chevron-left"></i> Return</a>
            </div>

        </div>
    <?php else : ?>
        <label class="text-danger">No data set</label>
    <?php endif; ?>

</main>
<?php require APPROOT . '/views/inc/footer.php'; ?>