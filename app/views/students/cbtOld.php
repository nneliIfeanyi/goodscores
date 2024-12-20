<?php require APPROOT . '/views/students/inc/header.php'; ?>

<body>
    <style>
        @import url(https://fonts.googleapis.com/css?family=Open+Sans:800);

        #countDown {
            font-size: 16px;
            font-weight: bold;
            margin-top: -7px;
            text-align: center;
            opacity: 0.6;
        }

        .blinking {
            animation: blink 1s infinite;
        }

        @keyframes blink {
            0% {
                opacity: 1;
            }

            50% {
                opacity: 0.3;
            }

            100% {
                opacity: 1;
            }
        }
    </style>
    <!-- PAGE LOADER -->
    <div id="loader" class="overflow-hidden align-items-middle position-fixed top-0 left-0 w-100 h-100">
        <div class="loader-container position-relative d-flex align-items-center justify-content-center flex-column vw-100 vh-100 text-center" style="background: rgba(0, 0, 0, 0.6);z-index: 1500;">
            <span class="spinner-border text-primary"> </span>
        </div>
    </div>
    <header id="header" class="header fixed-top d-flex align-items-center">

        <div class="d-flex align-items-center justify-content-between">
            <a href="#" class="logo d-flex align-items-center">
                <img src="assets/img/logo.png" alt="">
                <span class=""><?= $_COOKIE['sch_username']; ?></span>
            </a>
            <!-- <i class="bi bi-list toggle-sidebar-btn"></i> -->
        </div><!-- End Logo -->

        <nav class="header-nav ms-auto">
            <ul class="d-flex align-items-center">

                <li class="nav-item dropdown">

                    <span class="nav-link nav-icon">
                        <i class="bi bi-bell"></i>
                        <span id="countDown" class="badge bg-primary py-1 badge-number"></span>
                    </span><!-- End Notification Icon -->
                </li>
                <li class="nav-item dropdown pe-3">
                    <label class="nav-link nav-profile d-flex align-items-center pe-0">
                        <?php if (!empty($_SESSION['profile_photo'])) : ?>
                            <img src="<?= URLROOT; ?>/<?= $_SESSION['profile_photo']; ?>" alt="Profile" class="rounded-circle">
                        <?php else : ?>
                            <span class="d-none d-md-block ps-2"><?php echo $_SESSION['fullname']; ?></span>
                        <?php endif; ?>
                    </label><!-- End Profile Image Icon -->
                </li><!-- End Profile Nav -->

            </ul>
        </nav><!-- End Icons Navigation -->

    </header><!-- End Header -->
    <section style="overflow-x: scroll;width:100%;">
        <div class="pagetitle">
            <h1>CBT</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= URLROOT; ?>/students/dashboard">Home</a></li>
                    <li class="breadcrumb-item">Students</li>
                    <li class="breadcrumb-item active">CBT</li>
                </ol>
            </nav>
        </div><!-- End Page Title -->
        <div class="row">
            <div class="col-lg-9 mx-auto">
                <div id="card" class="card">
                    <div class="card-body mt-2 pb-0">
                        <?php if ($data['param']->section == 'objectives_questions') {
                            $data['param']->section = 'Objective Questions';
                        }
                        ?>
                        <div class="pagetitle border-bottom">

                            <nav class="m-0">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><?= $data['param']->class; ?></li>
                                    <li class="breadcrumb-item"><?= $data['param']->subject; ?></li>
                                    <li class="breadcrumb-item active"><?= $data['core']->publishedAS; ?></li>
                                </ol>
                            </nav>
                            <h3 class="pb-3 h4"><?= $data['param']->section ?> | <?= $data['param']->instruction ?></h3>
                        </div>
                        <form action="<?= URLROOT; ?>/students/submit_cbt/<?= $data['core']->paperID; ?>" method="POST">
                            <input type="hidden" name="class" value="<?= $data['core']->class; ?>">
                            <input type="hidden" name="subject" value="<?= $data['core']->subject; ?>">
                            <input type="hidden" name="paperID" id="name" value="<?= $data['core']->paperID; ?>">
                            <input type="hidden" name="cbtTag" value="<?= $data['core']->publishedAS; ?>">
                            <!-- <input type="hidden" name="isSubjective" value="<?= $data['core']->isSubjective; ?>"> -->
                            <?php
                            if (!empty($data['cbt'])) :
                                $n = 1;
                                foreach ($data['cbt'] as $recent) : ?>
                                    <div class="mb-3">
                                        <input type="hidden" name="img<?= $n; ?>" value="<?= $recent->img; ?>">
                                        <input type="hidden" name="subInstruction<?= $n; ?>" value="<?= $recent->subInstruction; ?>">
                                        <input type="hidden" name="isSubjective<?= $n; ?>" value="<?= $recent->isSubjective; ?>">
                                        <?php if (!empty($recent->img)) : ?>
                                            <div class="">
                                                <div class="mt-2 me-4">
                                                    <img src="<?php echo URLROOT . '/' . $recent->img; ?>" class="rounded" width="60%" height="120px" alt="daigram">
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                        <!-- Question Div -->
                                        <div class="d-flex flex-row m-0">
                                            <p class="fw-bold me-2"><?= $n; ?> </p>
                                            <?= $recent->question; ?>
                                            <input type="hidden" name="question<?= $n; ?>" value="<?= $recent->question; ?>">
                                        </div>
                                        <!-- OPtions Div -->
                                        <div class="fw-bold m-0" style="font-size: 14px;margin-top: -9px;">

                                            <input type="hidden" name="default<?= $n; ?>" value="<?= $recent->ans; ?>">
                                            <?php if ($recent->isSubjective == 'yes') : ?>
                                                <input type="hidden" name="<?= $n; ?>optA" value="">
                                                <input type="hidden" name="<?= $n; ?>optB" value="">
                                                <input type="hidden" name="<?= $n; ?>optC" value="">
                                                <input type="hidden" name="<?= $n; ?>optD" value="">
                                                <div class="my-2">
                                                    <label for="className">Answer</label>
                                                    <input type="text" name="ans<?= $n; ?>" class="form-control form-control-lg" />
                                                </div>
                                            <?php endif; ?>
                                            <div class="mx-3 my-1">
                                                <?php if (!empty($recent->opt1)) : ?>
                                                    <input type="hidden" name="<?= $n; ?>optA" value="<?= $recent->opt1; ?>">
                                                    <div class=" border pe-1">
                                                        <input type="radio" name="ans<?= $n; ?>" value="a" class="form-check-input fs-2" id="A<?= $n; ?>">
                                                        <label for="A<?= $n; ?>" class="form-check-label mt-3"><?= $recent->opt1; ?></label>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            <div class="mx-3 my-1">
                                                <?php if (!empty($recent->opt2)) : ?>
                                                    <input type="hidden" name="<?= $n; ?>optB" value="<?= $recent->opt2; ?>">
                                                    <div class=" border pe-1">
                                                        <input type="radio" name="ans<?= $n; ?>" value="b" class="form-check-input fs-2" id="B<?= $n; ?>">
                                                        <label for="B<?= $n; ?>" class="form-check-label mt-3"><?= $recent->opt2; ?></label>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            <div class="mx-3 my-1">
                                                <?php if (!empty($recent->opt3)) : ?>
                                                    <input type="hidden" name="<?= $n; ?>optC" value="<?= $recent->opt3; ?>">
                                                    <div class=" border  pe-1">
                                                        <input type="radio" name="ans<?= $n; ?>" value="c" class="form-check-input fs-2" id="C<?= $n; ?>">
                                                        <label for="C<?= $n; ?>" class="form-check-label mt-3"><?= $recent->opt3; ?></label>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            <div class="mx-3 my-1">
                                                <?php if (!empty($recent->opt4)) : ?>
                                                    <input type="hidden" name="<?= $n; ?>optD" value="<?= $recent->opt4; ?>">
                                                    <div class="border  pe-1">
                                                        <input type="radio" name="ans<?= $n; ?>" value="d" class="form-check-input fs-2" id="D<?= $n; ?>">
                                                        <label for="D<?= $n; ?>" class="form-check-label mt-3"><?= $recent->opt4; ?></label>
                                                    </div>
                                                <?php else: ?>
                                                    <input type="hidden" name="<?= $n; ?>optD" value="">
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php $n++;
                                endforeach; ?>
                                <div class="d-grid my-3">
                                    <input type="submit" id="submit" value="Submit" class="btn btn-outline-primary">
                                </div>
                            <?php else : ?>
                                <label class="text-danger">No data set</label>
                            <?php endif; ?>
                        </form>

                    </div>

                </div>
            </div>
        </div>
        </div>
    </section>
    <?php require APPROOT . '/views/inc/footer.php'; ?>
    <!-- Page loader fade in on form submit -->
    <script>
        $(':submit').each(function() {
            $(this).click(function() {
                $('#loader').fadeIn();
            });
        });
    </script>
    <script>
        // Set the date we're counting down to
        var countDownDate = new Date("<?= date('M d'); ?>, <?= date('Y'); ?> <?= $data['duration']; ?>").getTime();
        // var countDownDate = new Date("Aug 19, 2024 10:00:25").getTime();
        // Update the count down every 1 second
        var x = setInterval(function() {

            // Get today's date and time
            var now = new Date().getTime();

            // Find the distance between now and the count down date
            var distance = countDownDate - now;
            // Time calculations for days, hours, minutes and seconds
            var days = Math.floor(distance / (1000 * 60 * 60 * 24));
            var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            var seconds = Math.floor((distance % (1000 * 60)) / 1000);
            // Display the result in the element with id="demo"
            var display = document.getElementById("countDown");
            display.innerHTML = hours + ":" + minutes + ":" + seconds;

            // If the count down is finished, write some text
            if (distance < 0) {
                location.reload();
                clearInterval(x);
                display.innerHTML = "EXPIRED";
            } else if (minutes < 3) { // 3 minutes warning
                // Change text color to warning color
                display.style.color = 'tomato';
                // Add blinking effect
                display.classList.add('blinking');
            }
        }, 1000);
    </script>
    <script>
        // query current page visibility state: prerender, visible, hidden
        var pageVisibility = document.visibilityState;

        // subscribe to visibility change events
        document.addEventListener('visibilitychange', function() {
            // fires when user switches tabs, apps, goes to homescreen, etc.
            // if (document.visibilityState == 'hidden') {
            //   let url = '<?= URLROOT; ?>/students/submit_cbt/<?= $data['core']->paperID; ?>';
            //  setTimeout(submitCBT, 60000);

            //  function submitCBT() {
            //     location.href = url;
            //   }
            // }

            // fires when app transitions from prerender, user returns to the app / tab.
            // if (document.visibilityState == 'visible') {

            // }
        });
    </script>