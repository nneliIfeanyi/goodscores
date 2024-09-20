<?php require APPROOT . '/views/inc/header.php'; ?>
<?php require APPROOT . '/views/inc/navbar.php'; ?>
<?php require APPROOT . '/views/inc/sidebar.php'; ?>



<main id="main" class="main">

  <div class="pagetitle">
    <h1>Set Questions</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="index.html">Home</a></li>
        <li class="breadcrumb-item">Users</li>
        <li class="breadcrumb-item active"><?php echo $data['param']; ?></li>
      </ol>
    </nav>
  </div><!-- End Page Title -->

  <section class="section">
    <div class="row">
      <div class="col-md-10 col-lg-8">

        <div class="card">
          <div class="card-body">

            <div class="alert alert-primary bg-primary text-light border-0 alert-dismissible fade show" role="alert">
              <strong>Define Examination Parameters!</strong>
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <form action="<?php echo URLROOT; ?>/submissions/set/<?php echo $data['param']; ?>" method="POST">
              <input type="hidden" name="question" value="">
              <input type="hidden" name="opt1" value="">
              <input type="hidden" name="opt2" value="">
              <input type="hidden" name="opt3" value="">
              <input type="hidden" name="opt4" value="">
              <input type="hidden" name="term" value="<?= TERM; ?>">
              <input type="hidden" name="year" value="<?= SCH_SESSION; ?>">
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