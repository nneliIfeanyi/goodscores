<?php require APPROOT . '/views/inc/header.php'; ?>
<?php require APPROOT . '/views/inc/navbar.php';
?>
<?php require APPROOT . '/views/inc/sidebar.php';
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
                margin: 0.3cm;
            }

            p {
                list-style: none;
            }
        }

        p {
            margin: 0;
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
    <div id="print" class="row">
        <div class="col-lg-9">
            <div class="card card-body">
                <div class="row">
                    <div class="text-center col-12">
                        <h3 class="fw-light h2 m-0 mt-3">
                            <b><?= $data['sch']->name; ?></b><br />
                        </h3>
                        <span class="fs-5"><?= (empty($data['sch']->motto)) ? '' : $data['sch']->motto; ?></span>
                    </div>
                    <div class="col-4">
                        <h6><span>Subject: </span><span><?= $data['params']->subject; ?></span><br />
                            <span>Session: </span><span><?= $data['params']->year; ?></span>
                        </h6>
                    </div>
                    <div class="col-5">
                        <?php
                        if (TERM == '1st term') {
                            $term = '1<sup>st</sup> Term';
                        }
                        ?>
                        <span style="margin-left: 16px;"><?= $term; ?> Examination</span>
                    </div>
                    <div class="col-3">
                        <?php if (empty($data['params']->duration)) : ?>
                            <h6>
                                <!-- <span>Time:</span><span>value</span><br /> -->
                                <span>Class: </span><span><?= $data['params']->class ?></span>
                            </h6>
                        <?php else : ?>
                            <h6><span>Time: </span><span><?= $data['params']->duration; ?></span><br />
                                <span>Class: </span><span><?= $data['params']->class; ?></span>
                            </h6>
                        <?php endif; ?>

                    </div>
                    <hr />
                    <div class="col-12">
                        <?php if (!empty($data['obj'])) : $num = 1;
                            if ($data['params1']->section == 'objectives_questions') {
                                $data['params1']->section = 'Objectives questions';
                            }

                        ?>
                            <div class="text-center">
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
                                    $fes = explode('>', $obj->opt4);

                                    $obj->opt4 = '<b>(d)</b>  ' . $obj->opt4;
                                }
                                ?>
                                <table>
                                    <tr>
                                        <td>
                                            <p><?= $num; ?>)&nbsp;</p>
                                        </td>
                                        <td><?= $obj->question; ?></td>
                                    </tr>
                                </table>
                                <div class="ms-3 mb-1 fs-6"><span class="d-flex"><b>(a)</b> <?= $obj->opt1; ?> &nbsp;<b>(b)</b> <?= $obj->opt2; ?> &nbsp;<?= $obj->opt3; ?> <?= $obj->opt4; ?>
                                    </span></div>
                            <?php $num++;
                            endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="d-grid my-4 mx-4">
                    <button id="printBtn" class="btn btn-outline-primary">
                        Print Exam Paper
                    </button>
                </div>
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