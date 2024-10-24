<?php require APPROOT . '/views/students/inc/header.php'; ?>

<body>
    <style>
        @import url(https://fonts.googleapis.com/css?family=Open+Sans:800);

        .container {
            margin: auto;
            width: 400px;
        }

        .path--background {
            fill: rgb(34, 213, 201);
            stroke: #fff;
            stroke-width: 0px;
        }

        .pulse {
            fill: rgb(255, 74, 74) !important;
        }

        .path--foreground {
            fill: #eee;
            stroke: #eee;
            stroke-width: 2px;
        }

        .label {
            font: 90px "Open Sans";
            font-weight: 900;
            text-anchor: middle;
            fill: rgb(34, 213, 201);
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
            <a href="index.html" class="logo d-flex align-items-center">
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
                        <span class="badge bg-primary badge-number" id="counter"></span>
                    </span><!-- End Notification Icon -->
                </li>
                <li class="container">

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
                                <li class="breadcrumb-item active">CBT <?= $data['param']->tag ?></li>
                            </ol>
                        </nav>
                        <h1 class="pb-3"><?= $data['param']->section ?> | <?= $data['param']->instruction ?></h1>
                    </div>
                    <form action="<?= URLROOT; ?>/students/submit_cbt/<?= $data['core']->paperID; ?>" method="POST">
                        <input type="hidden" name="subject" value="<?= $data['core']->subject; ?>">
                        <input type="hidden" name="paperID" id="name" value="<?= $data['core']->paperID; ?>">
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
                                    <div class="d-flex flex-row gap-3 fw-bold m-0 pb-2" style="font-size: 14px;">
                                        &nbsp; &nbsp; &nbsp;
                                        <input type="hidden" name="default<?= $n; ?>" value="<?= $recent->ans; ?>">
                                        <div class="form-check border">
                                            <input type="radio" name="ans<?= $n; ?>" value="A" class="form-check-input" id="A">
                                            <label for="A" class="form-check-label"><?= $recent->opt1; ?></label>
                                        </div>
                                        <div class="form-check border">
                                            <input type="radio" name="ans<?= $n; ?>" value="B" class="form-check-input" id="B">
                                            <label for="B" class="form-check-label"><?= $recent->opt2; ?></label>
                                        </div>
                                        <?php if (!empty($recent->opt3)) : ?>
                                            <div class="form-check border">
                                                <input type="radio" name="ans<?= $n; ?>" value="C" class="form-check-input" id="C">
                                                <label for="C" class="form-check-label"><?= $recent->opt3; ?></label>
                                            </div>
                                        <?php endif; ?>
                                        <?php if (!empty($recent->opt4)) : ?>
                                            <div class="form-check border">
                                                <input type="radio" name="ans<?= $n; ?>" value="D" class="form-check-input" id="D">
                                                <label for="D" class="form-check-label"><?= $recent->opt4; ?></label>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php $n++;
                            endforeach;
                        else : ?>
                            <label class="text-danger">No data set</label>
                        <?php endif; ?>
                        <div class="d-grid my-3" style="margin: 0 200px;">
                            <input type="submit" id="submit" value="Submit" class="btn btn-outline-primary">
                        </div>
                    </form>

                </div>

            </div>
        </div>
    </div>
    </div>
    <?php require APPROOT . '/views/inc/footer.php'; ?>
    <!-- <script>
        function countdown(minutes) {
            var seconds = 60;
            var mins = minutes

            function tick() {
                //This script expects an element with an ID = "counter". You can change that to what ever you want. 
                var counter = document.getElementById("counter");
                var current_minutes = mins - 1
                seconds--;
                counter.innerHTML = current_minutes.toString() + ":" + (seconds < 10 ? "0" : "") + String(seconds);
                if (seconds > 0) {
                    setTimeout(tick, 1000);
                } else {
                    if (mins > 1) {
                        countdown(mins - 1);
                    }
                }
            }
            tick();
        }
        //You can use this script with a call to onclick, onblur or any other attribute you would like to use. 
        countdown(<?= $data['core']->duration ?>); //where n is the number of minutes required.
    </script> -->
    <script>
        var width = 400,
            height = 400,
            timePassed = 0,
            timeLimit = 30;

        var fields = [{
            value: timeLimit,
            size: timeLimit,
            update: function() {
                return timePassed = timePassed + 1;
            }
        }];

        var nilArc = d3.svg.arc()
            .innerRadius(width / 3 - 133)
            .outerRadius(width / 3 - 133)
            .startAngle(0)
            .endAngle(2 * Math.PI);

        var arc = d3.svg.arc()
            .innerRadius(width / 3 - 55)
            .outerRadius(width / 3 - 25)
            .startAngle(0)
            .endAngle(function(d) {
                return ((d.value / d.size) * 2 * Math.PI);
            });

        var svg = d3.select(".container").append("svg")
            .attr("width", width)
            .attr("height", height);

        var field = svg.selectAll(".field")
            .data(fields)
            .enter().append("g")
            .attr("transform", "translate(" + width / 2 + "," + height / 2 + ")")
            .attr("class", "field");
    </script>

    <script>
        // query current page visibility state: prerender, visible, hidden
        var pageVisibility = document.visibilityState;

        // subscribe to visibility change events
        document.addEventListener('visibilitychange', function() {
            // fires when user switches tabs, apps, goes to homescreen, etc.
            if (document.visibilityState == 'hidden') {
                location.href = '<?= URLROOT; ?>/students/submit_cbt/<?= $data['core']->paperID; ?>';
            }

            // fires when app transitions from prerender, user returns to the app / tab.
            if (document.visibilityState == 'visible') {

            }
        });
    </script>