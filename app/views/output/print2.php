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
            }

            body {
                margin: 10mm 15mm 10mm 15mm;
            } */

            .pagetitle *,
            .header *,
            .d-grid * {
                display: none;
            }

            #print {
                margin: 0.2cm;
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
                    <div class="col-12">
                        <?php if (!empty($data['obj'])) : $num = 1;
                            if ($data['params1']->section == 'objectives_questions') {
                                $data['params1']->section = 'Objectives questions';
                            }

                        ?>
                            <div class="m-0 text-center" style="font-weight: lighter;font-size:small;">
                                <p class="m-0 fw-bold"><?= $data['params1']->tag; ?> | <?= $data['params1']->section; ?></p>
                                <span><?= $data['params1']->instruction; ?></span>
                            </div><br />
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
                                    <div class="ms-2">
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
                                    //$question = substr($question, 1, -1);
                                    ?>
                                    <span><b><?= $num; ?>)</b>&nbsp;&nbsp;</span>
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
                            <?php $num++;
                            endforeach; ?><!-- End OBJ foreach -->
                        <?php endif; ?><!-- End Not Empty OBJ questions-->
                        <?php if (!empty($data['theory'])) : ?>
                            <div class="text-center mt-3 m-0" style="font-weight: lighter;font-size:small;">
                                <p class="m-0 fw-bold"><?= $data['params2']->tag; ?> | <?= $data['params2']->section; ?></p>
                                <span style="text-decoration: underline;"><?= $data['params2']->instruction; ?></span>
                            </div>
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
                            <?php $n++;
                            endforeach; ?>
                        <?php endif; ?>
                    </div><!-- End col-12 question display -->
                </div>

            </div><!-- End printable div -->
            <div class="d-grid my-4 mx-5">
                <button id="printBtn" class="btn btn-outline-primary me-3 mb-3">
                    Print Exam Paper
                </button>
                <a href="<?= URLROOT; ?>/output/print/<?= $data['paperID']; ?>" class="btn btn-outline-secondary">Switch Paper</a>
            </div>
        </div>
    </main>
    <?php require APPROOT . '/views/inc/footer.php'; ?>
    <script>
        var button = document.getElementById('printBtn');
        button.addEventListener('click', () => {
            print();
        })
    </script>
    <!--
    
      charmap: [
        [0x3d, 'equal sign'],
        [0x2b, 'Plus sign'],
        [0x2212, 'Minus sign'],
        [0xd7, 'Multiplication sign'],
        [0xf7, 'division sign'],
        [0xb1, 'plus  or minus'],
        [0x25, 'percent sign'],
        [0x89, 'per mile sign'],
        [0xb0, 'degree sign'],
        [0xb9, 'superscript one'],
        [0xb2, 'superscript two'],
        [0xb3, 'superscript three'],
        [0x221A, 'square root'],
        [0x221B, 'cube root'],
        [0x221C, 'fourth root'],
        [0x3C0, 'pi'],
        [0x2217, 'asterisk operator'],
        [0xBD, 'one half'],
        [0xBC, 'one quarter'],
        [0xBE, 'three quarter'],
        [0x2153, 'two third'],
        [0x2154, 'one third'],
        [0x2208, 'element of'],
        [0x220B, 'member'],
        [0x2209, 'not element of'],
        [0x2203, 'there exist'],
        [0x2205, 'empty set'],
        [0x2207, 'nabla'],
        [0x221D, 'proportional to'],
        [0x221E, 'infinity'],
        [0x2220, 'angle'],
        [0x2229, 'intersection'],
        [0x222A, 'union'],
        [0x2264, 'less or equal to'],
        [0x2265, 'greater or equal to'],
        [0x2282, 'subset of'],
        [0x2283, 'superset of'],
        [0x2284, 'not a subset of'],
        [0x2286, 'subset of or equal to'],
        [0x2287, 'superset of or equal to'],
        [0x2260, 'not equal to'],
        [0x222B, 'integral'],
        [0x2211, 'summation'],
        [0x2044, 'fraction slash'],
      ]