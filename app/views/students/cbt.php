<?php require APPROOT . '/views/students/inc/header.php'; ?>

<body>
    <style>
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
    <div class="question-container">
        <nav class="question-header navbar-expand text-bg-success fixed-top py-3  z-3">
            <div class="container">
                <div class="d-flex align-items-center justify-content-between">
                    <h1 class="question-heading fw-bold fs-6 mb-0 text-truncate"><?= $data['param']->subject; ?></h1>
                    <div class="question-time">Time: <span id="countDown">00:00:00</span></div>
                    <div class="indicator" class="border border-success rounded py-1 px-2">0/0</div>
                </div>
            </div>
        </nav>
        <section class="py-5 pt-5">
            <div class="container">
                <div class="row">
                    <!-- Left Section -->
                    <section class="col-md-8 question-lists z-1 py-5">
                        <form action="<?= URLROOT; ?>/students/submit_cbt/<?= $data['core']->paperID; ?>" method="POST">
                            <input type="hidden" name="class" value="<?= $data['core']->class; ?>">
                            <input type="hidden" name="subject" value="<?= $data['core']->subject; ?>">
                            <input type="hidden" name="paperID" id="name" value="<?= $data['core']->paperID; ?>">
                            <input type="hidden" name="cbtTag" value="<?= $data['core']->publishedAS; ?>">
                            <!-- <input type="hidden" name="isSubjective" value="<?= $data['core']->isSubjective; ?>"> -->
                            <?php if (!empty($data['cbt'])) :
                                $n = 1;
                                foreach ($data['cbt'] as $recent) : ?>
                                    <div class="question border-bottom pb-5">
                                        <div class="d-flex align-items-start justify-content-start">
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
                                            <div>
                                                <!-- QUESTION DIV HERE -->
                                                <div class="d-flex">
                                                    <span class="me-2 fw-semibold"><?= $n; ?></span>
                                                    <p class="fw-semi-bold"><?= $recent->question; ?></p>
                                                    <input type="hidden" name="question<?= $n; ?>" value="<?= $recent->question; ?>">
                                                </div>
                                                <!-- QUESTION DIV ENDS HERE -->
                                                <!-- SUBJECTIVE DIV -->
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
                                                <!-- SUBJECTIVE DIV ENDS -->
                                                <!-- OPTIONS DIV HERE -->
                                                <div class="answer-options opacity-75 lh-base ps-2">
                                                    <?php if (!empty($recent->opt1)) : ?>
                                                        <input type="hidden" name="<?= $n; ?>optA" value="<?= $recent->opt1; ?>">
                                                        <div class="d-flex align-items-start">
                                                            <input type="radio" name="ans<?= $n; ?>" value="a" id="A<?= $n; ?>" class="mt-2 form-check-input fs-4" />
                                                            <label class="py-2 ms-2" for="A<?= $n; ?>"><?= $recent->opt1; ?></label>
                                                        </div>
                                                    <?php endif; ?>
                                                    <?php if (!empty($recent->opt2)) : ?>
                                                        <input type="hidden" name="<?= $n; ?>optB" value="<?= $recent->opt2; ?>">
                                                        <div class="d-flex align-items-start">
                                                            <input type="radio" name="ans<?= $n; ?>" value="b" id="B<?= $n; ?>" class="mt-2 form-check-input fs-4" />
                                                            <label class="py-2 ms-2" for="B<?= $n; ?>"><?= $recent->opt2; ?></label>
                                                        </div>
                                                    <?php endif; ?>
                                                    <?php if (!empty($recent->opt3)) : ?>
                                                        <input type="hidden" name="<?= $n; ?>optC" value="<?= $recent->opt3; ?>">
                                                        <div class="d-flex align-items-start">
                                                            <input type="radio" name="ans<?= $n; ?>" value="c" id="C<?= $n; ?>" class="mt-2 form-check-input fs-4" />
                                                            <label class="py-2 ms-2" for="C<?= $n; ?>"><?= $recent->opt3; ?></label>
                                                        </div>
                                                    <?php endif; ?>
                                                    <?php if (!empty($recent->opt4)) : ?>
                                                        <input type="hidden" name="<?= $n; ?>optD" value="<?= $recent->opt4; ?>">
                                                        <div class="d-flex align-items-start">
                                                            <input type="radio" name="ans<?= $n; ?>" value="d" id="D<?= $n; ?>" class="mt-2 form-check-input fs-4" />
                                                            <label class="py-2 ms-2" for="D<?= $n; ?>"><?= $recent->opt4; ?></label>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                                <!-- OPTIONS DIV ENDS HERE -->
                                            </div>
                                        </div>
                                    </div>
                                <?php $n++;
                                endforeach; ?>
                            <?php else : ?>
                                <label class="text-danger">No data set</label>
                            <?php endif; ?>

                            <!-- Next/Previous Buttons -->
                            <div class="my-3 bg-light py-3">
                                <div class="container">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <button type="button" id="prev-btn" class="btn btn-outline-secondary py-2 px-5" onclick="plusSlides(-1)">Previous</button>
                                        <button type="button" id="next-btn" class="btn btn-outline-secondary py-2 px-5" onclick="plusSlides(1)">Next</button>
                                    </div>
                                    <button type="submit" id="submit-btn" class="btn btn-outline-success d-block w-100 py-2 px-5 my-3" onclick="">Submit</button>
                                </div>
                            </div>
                        </form>
                    </section>

                </div>
            </div>
        </section>
    </div>

    <?php require APPROOT . '/views/inc/footer.php'; ?>
    <script src="<?php echo URLROOT; ?>/assets/js/questions.js"></script>
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