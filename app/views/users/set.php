<?php require APPROOT . '/views/inc/header.php'; ?>
<?php require APPROOT . '/views/inc/navbar.php'; ?>
<?php require APPROOT . '/views/inc/sidebar.php'; ?>



<main id="main" class="main">

  <div class="pagetitle">
    <h1>Set Questions</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= URLROOT; ?>/users/dashboard">Home</a></li>
        <li class="breadcrumb-item">Exam</li>
        <li class="breadcrumb-item active">Section</li>
      </ol>
    </nav>
  </div><!-- End Page Title -->

  <section class="section">
    <div class="row">
      <div class="col-md-10 col-lg-8">

        <div class="card">
          <div class="card-body">

            <div class="alert alert-primary bg-primary text-light border-0 alert-dismissible fade show" role="alert">
              <strong>Define Section Parameters!</strong>
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php if ($_COOKIE['cbt'] == '1') : ?>
              <form action="<?php echo URLROOT; ?>/submissions/set2/<?php echo $data['param']; ?>" method="POST">
              <?php else : ?>
                <form action="<?php echo URLROOT; ?>/submissions/set/<?php echo $data['param']; ?>" method="POST">
                <?php endif; ?>
                <input type="hidden" name="term" value="<?= TERM; ?>">
                <input type="hidden" name="year" value="<?= SCH_SESSION; ?>">

                <?php if ($data['param'] == 'objectives_questions') : ?>
                  <div class="my-4">
                    <label for="className">Section tag <span style="font-size: small;">(eg.Section A)</span></label>
                    <input type="text" name="section_tag" class="form-control form-control-lg" data-parsley-trigger="keyup" />
                  </div>
                  <div class="my-4">
                    <label for="className">Section name</label>
                    <input type="text" name="section_name" disabled class="form-control form-control-lg" value="<?= $data['param']; ?>" />
                  </div>
                  <div class="my-4">
                    <select class="form-control form-control-lg" name="class" required>
                      <option value="">Select Class</option>
                      <?php if (empty($data['classes'])) : ?>
                        <option value="">No added class</option>
                      <?php else : ?>
                        <?php foreach ($data['classes'] as $class) : ?>
                          <option value="<?php echo $class->classname; ?>"><?php echo $class->classname; ?></option>
                        <?php endforeach; ?>
                      <?php endif; ?>
                    </select>
                  </div><!--===== Class Ends =====-->
                  <div class="my-4">
                    <select class="form-control form-control-lg" name="subject" required>
                      <option value="">Select Subject</option>
                      <?php if (empty($data['subjects'])) : ?>
                        <option value="">No added subject</option>
                      <?php else : ?>
                        <?php foreach ($data['subjects'] as $subject) : ?>
                          <option value="<?php echo $subject->subject; ?>"><?php echo $subject->subject; ?></option>
                        <?php endforeach; ?>
                      <?php endif; ?>
                    </select>
                  </div><!--===== Subject Ends =====-->
                  <div class="my-4">
                    <label for="className">Number of questions for this section</label>
                    <input type="number" name="num_rows" required class="form-control form-control-lg" data-parsley-trigger="keyup" />
                  </div><!--===== Objectives section Num_rows Ends =====-->
                  <div class="my-4">
                    <label for="className">Instruction for this section <span style="font-size: small;">(eg. Answer all questions in this section)</span></label>
                    <input name="instruction" class="form-control form-control-lg" data-parsley-trigger="keyup" />
                  </div><!--===== Objectives section Instruction Ends =====-->

                <?php elseif ($data['param'] == 'theory_questions') : ?>
                  <div class="my-4">
                    <label for="className">Section tag <span style="font-size: small;">(eg.Section A)</span></label>
                    <input type="text" name="section_tag" class="form-control form-control-lg" data-parsley-trigger="keyup" />
                  </div>
                  <div class="my-4">
                    <label for="className">Section name</label>
                    <input type="text" name="section_name" disabled class="form-control form-control-lg" value="<?= $data['param']; ?>" />
                  </div>
                  <div class="my-4">
                    <select class="form-control form-control-lg" name="class" required>
                      <option value="">Select Class</option>
                      <?php if (empty($data['classes'])) : ?>
                        <option value="">No added class</option>
                      <?php else : ?>
                        <?php foreach ($data['classes'] as $class) : ?>
                          <option value="<?php echo $class->classname; ?>"><?php echo $class->classname; ?></option>
                        <?php endforeach; ?>
                      <?php endif; ?>
                    </select>
                  </div><!--===== Class Ends =====-->
                  <div class="my-4">
                    <select class="form-control form-control-lg" name="subject" required>
                      <option value="">Select Subject</option>
                      <?php if (empty($data['subjects'])) : ?>
                        <option value="">No added subject</option>
                      <?php else : ?>
                        <?php foreach ($data['subjects'] as $subject) : ?>
                          <option value="<?php echo $subject->subject; ?>"><?php echo $subject->subject; ?></option>
                        <?php endforeach; ?>
                      <?php endif; ?>
                    </select>
                  </div><!--===== Subject Ends =====-->
                  <input type="hidden" name="duration" value="">
                  <div class="my-4">
                    <label for="className">Number of theory questions</label>
                    <input type="number" name="num_rows" required class="form-control form-control-lg" data-parsley-trigger="keyup" />
                  </div><!--===== Theory section Num_rows Ends =====-->
                  <div class="my-4">
                    <label for="className">Exam instruction for theory section <span style="font-size: small;">(eg. Answer only 1 question in this section)</span></label>
                    <input name="instruction" class="form-control form-control-lg" data-parsley-trigger="keyup" />
                  </div><!--===== Theory section Instruction Ends =====-->
                <?php elseif ($data['param'] == 'custom') : ?>
                  <input type="hidden" name="instruction" value="">
                  <input type="hidden" name="section_tag" value="">
                  <input type="hidden" name="num_rows" value="">
                  <div class="my-4">
                    <select class="form-control form-control-lg" name="class" required>
                      <option value="">Select Class</option>
                      <?php if (empty($data['classes'])) : ?>
                        <option value="">No added class</option>
                      <?php else : ?>
                        <?php foreach ($data['classes'] as $class) : ?>
                          <option value="<?php echo $class->classname; ?>"><?php echo $class->classname; ?></option>
                        <?php endforeach; ?>
                      <?php endif; ?>
                    </select>
                  </div><!--===== Class Ends =====-->
                  <div class="my-4">
                    <select class="form-control form-control-lg" name="subject" required>
                      <option value="English Language">English Language</option>
                      <?php if (empty($data['subjects'])) : ?>
                        <option value="English Language">English Language</option>
                      <?php else : ?>
                        <?php foreach ($data['subjects'] as $subject) : ?>
                          <option value="<?php echo $subject->subject; ?>"><?php echo $subject->subject; ?></option>
                        <?php endforeach; ?>
                      <?php endif; ?>
                    </select>
                  </div><!--===== Subject Ends =====-->

                <?php elseif ($data['param'] == 'others') : ?>
                  <div class="my-4">
                    <label for="className">Section tag <span style="font-size: small;">(eg.Section A)</span></label>
                    <input type="text" name="section_tag" required class="form-control form-control-lg" data-parsley-trigger="keyup" />
                  </div>
                  <div class="my-4">
                    <label for="className">Section name</label>
                    <input type="text" name="section_alt" class="form-control form-control-lg" value="" />
                  </div>
                  <div class="my-4">
                    <select class="form-control form-control-lg" name="class" required>
                      <option value="<?= $_GET['class']; ?>"><?= $_GET['class']; ?></option>
                    </select>
                  </div><!--===== Class Ends =====-->
                  <div class="my-4">
                    <select class="form-control form-control-lg" name="subject" required>
                      <option value="<?= $_GET['subject']; ?>"><?= $_GET['subject']; ?></option>
                    </select>
                  </div><!--===== Subject Ends =====-->
                  <div class="my-4">
                    <label for="className">Number of questions for this section</label>
                    <input type="number" name="num_rows" required class="form-control form-control-lg" data-parsley-trigger="keyup" />
                  </div>
                  <div class="my-4">
                    <label for="className">Exam instruction for this section <span style="font-size: small;">(eg. Answer only 1 question in this section)</span></label>
                    <input name="instruction" class="form-control form-control-lg" data-parsley-trigger="keyup" />
                  </div><!--===== Others section Instruction Ends =====-->
                <?php endif; ?>

                <div class="d-grid">
                  <input type="submit" name="set" value="Continue" class="btn btn-outline-primary">
                </div><!--===== Submit Button Ends =====-->
                </form><!--===== Set Question Form Ends =====-->

          </div>
        </div>

      </div>

      <div class="col-lg-4">

        <div class="card">
          <div class="card-body">
            <h5 class="card-title">Usage Tips</h5>

            <div class="alert alert-primary alert-dismissible fade show" role="alert">
              <i class="bi bi-star me-1 text-warning"></i>
              First add class and subjects you teach before set questions.
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>

          </div>
        </div>
      </div>
    </div>
  </section>

</main><!-- End #main -->


<?php require APPROOT . '/views/inc/footer.php'; ?>
<!-- Page loader fade in on form submit -->
<script>
  $(':submit').each(function() {
    $(this).click(function() {
      $('#loader').fadeIn();
    });
  });
</script>