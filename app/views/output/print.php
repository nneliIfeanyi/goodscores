<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title><?php echo SITENAME; ?></title>
    <meta name="keyword" content="Online exam creation for teachers, Automated grading system for educators, Exam management software for schools, Online quiz platform for students, Educational assessment tools for teachers, Educational technology, Student evaluation, Create online exams" />
    <meta name="description" content="Goodscores - The ultimate tool for teachers to create, manage and grade exams/assessments online. The leading online platform for educators." />
    <meta name="robots" content="index, nofollow" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="apple-mobile-web-app-title" content="goodscores" />
    <meta name="application-name" content="Goodscores" />
    <meta name="msapplication-TileName" content="Goodscores" />
    <meta name="msapplication-TileImage" content="<?php echo URLROOT; ?>/icons/mask3.png" width="20" height="20" />
    <!-- Site Manifest -->
    <link href="<?php echo URLROOT; ?>/site.webmanifest" rel="manifest">
    <meta name="apple-mobile-web-app-status-bar" content="#0d6efd">
    <meta name="theme-color" content="#0d6efd">
    <!-- Favicons -->
    <link href="<?php echo URLROOT; ?>/icons/mask3.png" rel="icon">
    <link href="<?php echo URLROOT; ?>/assets/img/apple-touch-icon.png" rel="apple-touch-icon">

    <!-- Google Fonts -->
    <link href="https://fonts.gstatic.com" rel="preconnect">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

    <!-- Vendor CSS Files -->
    <link href="<?php echo URLROOT; ?>/assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo URLROOT; ?>/assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="<?php echo URLROOT; ?>/assets/vendor/simple-datatables/style.css" rel="stylesheet">

    <!-- Template Main CSS File -->
    <link href="<?php echo URLROOT; ?>/assets/css/style.css" rel="stylesheet">
    <link href="<?php echo URLROOT; ?>/assets/css/styles.css" rel="stylesheet">
    <style type="text/css">
        @media print {
            @page {
                margin: 0mm;
            }

            /* 
            html {
                margin: 0px;
            }*/

            body {
                margin: 10mm 0mm 0mm 0mm;
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
</head>

<body>
    <main class="row">
        <div class="shadow col-lg-7 mx-auto">
            <div id="print" class="m-3 p-2">
                <div class="row">
                    <div class="text-center col-12">
                        <h3 class="fw-light fst-italic h2 m-0">
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

                        <?php if (!empty($data['theory'])) : ?>
                            <div class="text-center m-0" style="font-weight: lighter;font-size:small;">
                                <p class="m-0 fw-bold"><?= $data['params2']->tag; ?> | <?= $data['params2']->section; ?></p>
                                <span style="text-decoration: underline;"><?= $data['params2']->instruction; ?></span>
                            </div><br />
                            <div class="col-6 pe-1" style="overflow-x: hidden;">
                                <?php $n = 1;
                                foreach ($data['theory'] as $theory) : ?>
                                    <?php if (!empty($theory->img)) : ?>
                                        <div class="">
                                            <div class="my-1 mx-3">
                                                <img src="<?php echo URLROOT . '/' . $theory->img; ?>" class="rounded" width="80%" height="120px" alt="daigram">
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                    <div class="w-100 d-flex">
                                        <?php
                                        $questionA = str_replace('<p>', '', $theory->questionA);
                                        $questionA = str_replace('</p>', '', $questionA);
                                        ?>
                                        <span><b><?= $n; ?>)a&nbsp;&nbsp;</b></span>
                                        <span style="font-size: 14px;"><?= $questionA; ?></span>
                                    </div>
                                    <?php if (!empty($theory->questionB)) : ?>
                                        <div class="w-100 d-flex">
                                            <?php
                                            $questionB = str_replace('<p>', '', $theory->questionB);
                                            $questionB = str_replace('</p>', '', $questionB);
                                            ?>
                                            <span><b><?= $n; ?>)b&nbsp;&nbsp;</b></span>
                                            <span style="font-size: 14px;"><?= $questionB; ?></span>
                                        </div>
                                    <?php endif; ?>
                                    <?php if (!empty($theory->questionC)) : ?>
                                        <div class="w-100 d-flex">
                                            <?php
                                            $questionC = str_replace('<p>', '', $theory->questionC);
                                            $questionC = str_replace('</p>', '', $questionC);
                                            ?>
                                            <span><b><?= $n; ?>)c&nbsp;&nbsp;</b></span>
                                            <span style="font-size: 14px;"><?= $questionC; ?></span>
                                        </div>
                                    <?php endif; ?>
                                    <?php if (!empty($theory->questionD)) : ?>
                                        <div class="w-100 d-flex">
                                            <?php
                                            $questionD = str_replace('<p>', '', $theory->questionD);
                                            $questionD = str_replace('</p>', '', $questionD);
                                            ?>
                                            <span><b><?= $n; ?>)d&nbsp;&nbsp;</b></span>
                                            <span style="font-size: 14px;"><?= $questionD; ?></span>
                                        </div>
                                    <?php endif; ?>
                            </div>
                            <div class="col-6 pe-1" style="overflow-x: hidden;">
                            <?php $n++;
                                endforeach; ?>
                        <?php endif; ?>
                            </div>
                </div>

            </div>
            <div class="d-grid">
                <div class="d-flex justify-content-center my-4">
                    <button id="printBtn" class="btn me-3 btn-outline-primary">
                        Print Exam Paper
                    </button>
                    <a href="<?= URLROOT; ?>/output/print2/<?= $data['paperID']; ?>" class="btn me-3 btn-outline-secondary">Switch Paper</a>
                    <a href="<?= URLROOT; ?>/users/dashboard" class="btn me-3 btn-secondary"><i class="bi bi-chevron-left"></i> Back</a>
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