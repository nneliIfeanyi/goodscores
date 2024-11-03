<?php require APPROOT . '/views/inc/header.php'; ?>
<?php require APPROOT . '/views/inc/navbar.php';
?>
<?php require APPROOT . '/views/inc/sidebar.php';
?>

<main id="main" class="main">
    <style>
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
                margin: 0.1cm;
            }
        }
    </style>
    <div class="pagetitle">
        <h1>Exam Paper Print</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= URLROOT; ?>/users/dashboard">Home</a></li>
                <li class="breadcrumb-item">Paper Crosscheck</li>
            </ol>
        </nav>
    </div>
    <!-- End Page Title -->

    <div id="print" class="row" style="margin-top: -46px;">
        <div class="col-lg-9">
            <div class="card card-body">
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
                    </div><!-- End col-12 question display -->
                </div>

            </div><!-- End card card-body display -->
            <div class="d-grid my-4 mx-5">
                <button id="printBtn" class="btn btn-outline-primary me-3 mb-3">
                    Print Exam Paper
                </button>
                <a href="<?= URLROOT; ?>/output/print/<?= $data['paperID']; ?>" class="btn btn-outline-secondary">Switch Paper</a>
            </div>
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