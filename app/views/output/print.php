<?php require APPROOT . '/views/inc/header.php'; ?>
<?php //require APPROOT . '/views/inc/navbar.php';
?>
<?php //require APPROOT . '/views/inc/sidebar.php';
?>

<main id="main" class="main">
    <style>
        @media print {
            @page {
                margin: 0;
            }

            .pagetitle *,
            .header *,
            .d-grid * {
                display: none;
            }

            #print {
                margin: 0.1cm;
            }
        }
    </style>
    <!-- <div class="pagetitle">
        <h1>Exam Paper Print</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= URLROOT; ?>/users/dashboard">Home</a></li>
                <li class="breadcrumb-item">Paper Crosscheck</li>
            </ol>
        </nav>
    </div> -->
    <!-- End Page Title -->
    <div id="print" class="row" style="margin-top: -26px;">
        <div class="col-lg-9">
            <div class="row">
                <div class="text-center col-12">
                    <h3 class="fw-light fst-italic h2 m-0 mt-3">
                        <b><?= $data['sch']->name; ?></b><br />
                    </h3>
                    <span class="fs-5"><?= (empty($data['sch']->motto)) ? '' : $data['sch']->motto; ?></span>
                </div><!-- School Name and Motto Div Ends-->
                <div class="col-4">
                    <h6 style="font-weight: lighter;font-size:small;">
                        <span>Subject: </span><span style="font-size:medium;font-weight:550"><?= $data['params']->subject; ?></span><br />
                        <span>Session: </span><span style="font-size:medium;font-weight:550"><?= $data['params']->year; ?></span>
                    </h6>
                </div><!-- Subject and section Div Ends -->
                <div class="col-5 fst-italic">
                    <?php
                    if (TERM == '1st term') {
                        $term = '1<sup>st</sup> Term';
                    }
                    ?>
                    <span style="margin-left: 16px;"><?= $term; ?> Examination</span>
                </div><!-- Term Div Ends-->
                <div class="col-3">
                    <?php if (empty($data['params']->duration)) : ?>
                        <h6 style="font-weight: lighter;font-size:small;">
                            <!-- <span>Time:</span><span>value</span><br /> -->
                            <span>Class: </span><span style="font-size:medium;font-weight:550"><?= $data['params']->class ?></span>
                        </h6>
                    <?php else : ?>
                        <div style="font-weight: lighter;font-size:small;">
                            <span>Time: </span><span style="font-size:medium;font-weight:550"><?= $data['params']->duration; ?></span><br />
                            <span>Class: </span><span style="font-size:medium;font-weight:550"><?= $data['params']->class; ?></span>
                        </div>
                    <?php endif; ?>

                </div><!-- Duration and Class Div Ends-->
                <hr />
                <?php if (!empty($data['obj'])) : $num = 1;
                    if ($data['params1']->section == 'objectives_questions') {
                        $data['params1']->section = 'Objectives questions';
                    }

                ?>
                    <div class="text-center m-0" style="font-weight: lighter;font-size:small;">
                        <p class="m-0 fw-bold"><?= $data['params1']->tag; ?> | <?= $data['params1']->section; ?></p>
                        <span><?= $data['params1']->instruction; ?></span>
                    </div><br />
                    <div class="col-6 pe-1" style="overflow-x: hidden;">

                        <?php foreach ($data['obj'] as $obj) : ?>
                            <?php
                            if (empty($obj->opt3)) {
                                $obj->opt3 = '';
                            } else {
                                $obj->opt3 = '<b>(c)</b>  ' . $obj->opt3 . '&nbsp;';
                            }

                            if (empty($obj->opt4)) {
                                $obj->opt4 = '';
                            } else {
                                $obj->opt4 = '<b>(d)</b>  ' . $obj->opt4;
                            }
                            ?>
                            <?php if (!empty($obj->subInstruction)) : ?>
                                <div class="">
                                    <?= $obj->subInstruction; ?>
                                </div>
                            <?php endif; ?>
                            <?php if (!empty($obj->img)) : ?>
                                <div class="">
                                    <div class="my-1 mx-3">
                                        <img src="<?php echo URLROOT . '/' . $obj->img; ?>" class="rounded" width="80%" height="120px" alt="daigram">
                                    </div>
                                </div>
                            <?php endif; ?>
                            <div class="w-100 d-flex">
                                <?php
                                $question = str_replace('<p>', '', $obj->question);
                                $question = str_replace('</p>', '', $question);
                                // $question = str_replace('/', '', $question);
                                // $question = substr($question, 1, -1);
                                ?>
                                <span><b><?= $num; ?>)&nbsp;&nbsp;</b></span>
                                <span style="font-size: 14px;"><?= $question; ?></span>
                            </div>
                            <!-- End OBJ Numbering and Question Display -->
                            <div class="ms-4 mb-1">
                                <span style="font-size: 12px;" class="d-flex flex-wrap">
                                    <?php if (!empty($obj->opt1)) : ?>
                                        <b>(a)</b> <?= $obj->opt1; ?> &nbsp;&nbsp;
                                    <?php endif; ?>
                                    <?php if (!empty($obj->opt2)) : ?>
                                        <b>(b)</b> <?= $obj->opt2; ?> &nbsp;&nbsp;
                                    <?php endif; ?>
                                    <?= $obj->opt3; ?>
                                    <?= $obj->opt4; ?>
                                </span>
                            </div><!-- End OBJ Options Display -->

                    </div><!-- End first col-6 question display -->
                    <div class="col-6 mt-2 pe-1" style="overflow-x: hidden;">
                    <?php $num++;
                        endforeach; ?><!-- End OBJ foreach -->
                <?php endif; ?><!-- End Not Empty OBJ questions-->
                    </div><!-- End second col-6 question display -->
                    <div class="col-6 pe-1" style="overflow-x: hidden;">
                        <?php if (!empty($data['theory'])) : ?>
                            <div class="text-center m-0" style="font-weight: lighter;font-size:small;">
                                <p class="m-0 fw-bold"><?= $data['params2']->tag; ?> | <?= $data['params2']->section; ?></p>
                                <span style="text-decoration: underline;"><?= $data['params2']->instruction; ?></span>
                            </div><br />
                            <?php $n = 1;
                            foreach ($data['theory'] as $theory) : ?>
                                <div class="w-100 d-flex">
                                    <?php
                                    $question = str_replace('<p>', '', $theory->questionA);
                                    $question = str_replace('</p>', '', $question);
                                    ?>
                                    <span><b><?= $n; ?>)&nbsp;&nbsp;</b></span>
                                    <span style="font-size: 14px;"><?= $question; ?></span>
                                </div>
                    </div>
                    <div class="col-6 pe-1" style="overflow-x: hidden;">
                    <?php $n++;
                            endforeach; ?>
                <?php endif; ?>
                    </div>
            </div>
            <div class="d-grid my-4 mx-5">
                <button id="printBtn" class="btn me-3 mb-3 btn-outline-primary">
                    Print Exam Paper
                </button>
                <a href="<?= URLROOT; ?>/output/print2/<?= $data['paperID']; ?>" class="btn btn-outline-secondary">Switch Paper</a>
            </div>
        </div>
    </div>

</main><!-- End #main -->

<?php require APPROOT . '/views/inc/footer.php'; ?>
<script>
    var button = document.getElementById('printBtn');
    button.addEventListener('click', () => {
        print();
    })
</script>