<?php require APPROOT . '/views/students/inc/header.php'; ?>

<body>
    <style>
        @import url(https://fonts.googleapis.com/css?family=Open+Sans:800);

        .countdown-container {
            max-width: 300px;
            margin: 10px auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        #countdown-display {
            font-size: 14px;
            font-weight: bold;
            margin-top: -7px;
            text-align: center;
        }

        .blinking {
            animation: blink 1s infinite;
        }

        @keyframes blink {
            0% {
                opacity: 1;
            }

            50% {
                opacity: 0.6;
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
    <!-- Ajax Response -->
    <div id="ajax-msg"></div>
    <!-- End Ajax Response -->
    <!-- ======= Flash Message ======= -->
    <?php echo flash('msg'); ?>
    <!-- ======= Header ======= -->
    <header id="header" class="header fixed-top d-flex align-items-center">

        <div class="d-flex align-items-center justify-content-between">
            <a href="#" class="logo d-flex align-items-center">
                <img src="assets/img/logo.png" alt="">
                <span class="d-none d-lg-block"><?= $_COOKIE['sch_username']; ?></span>
            </a>
            <!-- <i class="bi bi-list toggle-sidebar-btn"></i> -->
        </div><!-- End Logo -->

        <nav class="header-nav ms-auto">
            <ul class="d-flex align-items-center">

                <li class="nav-item dropdown">

                    <span class="nav-link nav-icon">
                        <i class="bi bi-bell"></i>
                        <span id="countdown-display" class="badge bg-primary badge-number"></span>
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
            <div class="card">
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
                        <h1 class="pb-3 h4"><?= $data['param']->section ?> | <?= $data['param']->instruction ?></h1>
                    </div>
                    <form action="<?= URLROOT; ?>/students/submit_cbt/<?= $data['core']->paperID; ?>" method="POST">
                        <input type="hidden" name="subject" value="<?= $data['core']->subject; ?>">
                        <input type="hidden" name="paperID" id="name" value="<?= $data['core']->paperID; ?>">
                        <input type="hidden" name="cbtTag" value="<?= $data['core']->publishedAS; ?>">
                        <?php
                        if (!empty($data['cbt'])) :
                            $n = 1;
                            foreach ($data['cbt'] as $recent) : ?>
                                <div class="border-bottom mb-2">
                                    <!-- Question Div -->
                                    <div class="d-flex flex-row gap-2 m-0">
                                        <p class="badge bg-primary"><?= $n; ?> </p>
                                        <?= $recent->question; ?>
                                    </div>
                                    <!-- OPtions Div -->
                                    <div class="d-flex flex-row gap-5 fw-bold m-0 pb-2" style="font-size: 14px;">
                                        &nbsp; &nbsp; &nbsp;
                                        <input type="hidden" name="default<?= $n; ?>" value="<?= $recent->ans; ?>">
                                        <div class="form-check border pe-1">
                                            <input type="radio" name="ans<?= $n; ?>" value="A" class="form-check-input fs-2" id="A">
                                            <label style="margin-left: -11px;" for="A" class="form-check-label mt-3"><?= $recent->opt1; ?></label>
                                        </div>
                                        <div class="form-check border pe-1">
                                            <input type="radio" name="ans<?= $n; ?>" value="B" class="form-check-input fs-2" id="B">
                                            <label style="margin-left: -11px;" for="B" class="form-check-label mt-3"><?= $recent->opt2; ?></label>
                                        </div>
                                        <?php if (!empty($recent->opt3)) : ?>
                                            <div class="form-check border  pe-1">
                                                <input type="radio" name="ans<?= $n; ?>" value="C" class="form-check-input fs-2" id="C">
                                                <label style="margin-left: -11px;" for="C" class="form-check-label mt-3"><?= $recent->opt3; ?></label>
                                            </div>
                                        <?php endif; ?>
                                        <?php if (!empty($recent->opt4)) : ?>
                                            <div class="form-check border  pe-1">
                                                <input type="radio" name="ans<?= $n; ?>" value="D" class="form-check-input fs-2" id="D">
                                                <label style="margin-left: -11px;" for="D" class="form-check-label mt-3"><?= $recent->opt4; ?></label>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php $n++;
                            endforeach; ?>
                            <div class="d-grid my-3" style="margin: 0 200px;">
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
    <?php require APPROOT . '/views/inc/footer.php'; ?>
    <script>
        const countdownDisplay = document.getElementById('countdown-display');
        let intervalId = null;
        let countdownTime = 0;

        document.addEventListener('DOMContentLoaded', startCountdown);

        function startCountdown() {
            const hours = 0;
            const minutes = <?= $data['core']->duration ?>;

            countdownTime = hours * 3600 + minutes * 60;
            intervalId = setInterval(updateCountdown, 1000);
            updateDisplay();
        }

        function updateCountdown() {
            countdownTime--;
            updateDisplay();
            // Check if countdown is expired
            if (countdownTime <= 0) {
                // Change text and color
                countdownDisplay.textContent = 'TIME EXPIRED';
                countdownDisplay.style.color = 'red';
                countdownDisplay.classList.remove('blinking');
                location.reload();
                localStorage.setItem('expired', countdownDisplay.textContent);
                // Stop interval
                clearInterval(intervalId);
            } else if (countdownTime <= 5 * 60) { // 5 minutes warning
                // Change text color to warning color
                countdownDisplay.style.color = 'orange';
                // Add blinking effect
                countdownDisplay.classList.add('blinking');
            }

        }

        function updateDisplay() {
            const hours = Math.floor(countdownTime / 3600);
            const minutes = Math.floor((countdownTime % 3600) / 60);
            const seconds = countdownTime % 60;

            const timeString = `${padZero(minutes)}:${padZero(seconds)}`;
            countdownDisplay.textContent = timeString;
        }

        function padZero(value) {
            return (value < 10 ? '0' : '') + value;
        }




        // 
        document.addEventListener('DOMContentLoaded', () => {
            const expiredexSaved = localStorage.getItem('expired');
            if (expiredexSaved) {
                // console.log(expiredexSaved)
                countdownDisplay.textContent = expiredexSaved;
                countdownDisplay.style.color = 'red';
            }
        });
    </script>

    <!--<script>
        // query current page visibility state: prerender, visible, hidden
        var pageVisibility = document.visibilityState;

        // subscribe to visibility change events
        document.addEventListener('visibilitychange', function() {
            // fires when user switches tabs, apps, goes to homescreen, etc.
            if (document.visibilityState == 'hidden') {
                location.href = '<?= URLROOT; ?>/students/submit_cbt/<?= $data['core']->paperID; ?>';
            }

            // fires when app transitions from prerender, user returns to the app / tab.
            // if (document.visibilityState == 'visible') {

            // }
        });
    </script>-->