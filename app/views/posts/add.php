<?php require APPROOT . '/views/inc/header.php'; ?>
<?php require APPROOT . '/views/inc/navbar.php';
?>
<?php require APPROOT . '/views/inc/sidebar.php';
?>

<main id="main" class="main">

  <div class="pagetitle">
    <h1>Set Questions | <?php echo $data['subject']; ?></h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="index.html"><?php echo $data['class']; ?></a></li>
        <li class="breadcrumb-item"><?php echo $data['term']; ?></li>
        <li class="breadcrumb-item active"><?php echo $data['year']; ?></li>
      </ol>
    </nav>
  </div><!-- End Page Title -->

  <section class="section">
    <div class="row">
      <div class="col-md-10 col-lg-8">

        <div class="card">
          <div class="card-body">
            <?php
            if ($data['num_rows'] > $data['total_subject_num_rows']) {
            ?>
              <div class="alert alert-primary bg-primary text-light border-0 alert-dismissible fade show" role="alert">
                <strong><?php echo $data['section']; ?></strong>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>
              <textarea class="form-control" disabled name="question" required placeholder="COMPLETED"></textarea>
              <div class="row my-3">
                <div class="col-6 col-md-3">
                  <input type="text" disabled class="form-control" placeholder="Opt-A" required name="opt1">
                </div>
                <div class="col-6 col-md-3">
                  <input type="text" disabled class="form-control" placeholder="Opt-B" required name="opt2">
                </div>
                <div class="col-6 col-md-3">
                  <input type="text" disabled class="form-control" placeholder="Opt-C" name="opt3">
                </div>
                <div class="col-6 col-md-3">
                  <input type="text" disabled class="form-control" placeholder="Opt-D" name="opt4">
                </div>
              </div>
              <form action="<?php echo URLROOT; ?>/submissions/set/theory_questions" method="POST">
                <input type="hidden" name="question" value="">
                <input type="hidden" name="opt1" value="">
                <input type="hidden" name="opt2" value="">
                <input type="hidden" name="opt3" value="">
                <input type="hidden" name="opt4" value="">
                <input type="hidden" name="class" value="<?php echo $_SESSION['class']; ?>">
                <input type="hidden" name="subject" value="<?php echo $_SESSION['subject']; ?>">
                <input type="hidden" name="year" value="<?php echo SCH_SESSION; ?>">
                <input type="hidden" name="term" value="<?php echo TERM; ?>">
                <input type="hidden" name="section" value="theory_questions">
                <div class="d-grid">
                  <input type="submit" name="set" value="Go to Theory Questions" class="btn btn-outline-primary">
                </div>
              </form>
            <?php
            } else {

            ?>
              <div class="alert alert-primary bg-primary text-light border-0 alert-dismissible fade show" role="alert">
                <strong><?php echo $data['section']; ?></strong>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>
              <form action="<?php echo URLROOT; ?>/submissions/set/<?php echo $_SESSION['section']; ?>" method="POST">
                <input type="hidden" name="class" value="<?php echo $_SESSION['class']; ?>">
                <input type="hidden" name="subject" value="<?php echo $_SESSION['subject']; ?>">
                <input type="hidden" name="term" value="<?php echo $_SESSION['term']; ?>">
                <input type="hidden" name="section" value="<?php echo $_SESSION['section']; ?>">
                <input type="hidden" name="paperID" value="<?php echo $_SESSION['paperID']; ?>">
                <?php if (!empty($_SESSION['daigram'])) : ?>
                  <div class="d-flex justify-content-center">
                    <div class="mt-2 mb-4">
                      <img src="<?php echo URLROOT . '/' . $_SESSION['daigram']; ?>" class="img-fluid rounded-3" alt="daigram">
                    </div>
                  </div>
                <?php else : ?>
                  <div class="lead">
                    <label style="font-size: x-small;">If question has daigram | Use camera icon before set question.</label>
                  </div>
                <?php endif; ?>
                <textarea class="form-control" name="question" required placeholder="<?php echo $data['num_rows'] . ' of ' . $data['total_subject_num_rows']; ?>"></textarea>
                <div class="row my-3">
                  <p class="col-2 d-lg-none">(A)</p>
                  <div class="col-10 col-lg-3">
                    <input type="text" class="form-control" placeholder="Option A" required name="opt1">
                  </div>
                  <p class="col-2 d-lg-none">(B)</p>
                  <div class="col-10 col-lg-3">
                    <input type="text" class="form-control" placeholder="Option B" required name="opt2">
                  </div>
                  <p class="col-2 d-lg-none">(C)</p>
                  <div class="col-10 col-lg-3">
                    <input type="text" class="form-control" placeholder="Option C" name="opt3">
                  </div>
                  <p class="col-2 d-lg-none">(D)</p>
                  <div class="col-10 col-lg-3">
                    <input type="text" class="form-control" placeholder="Option D" name="opt4">
                  </div>
                </div>
                <div class="row">
                  <div class="col-2">
                    <a href="<?php echo URLROOT; ?>/posts/daigram/<?php echo $_SESSION['paperID']; ?>" class="btn btn-outline-dark" data-bs-toggle="tooltip" data-bs-title="Append diagram">
                      <i class="bi bi-camera"></i>
                    </a>
                  </div>
                  <div class="col-8">
                    <div class="d-grid">
                      <input type="submit" name="submit" value="SET" class="btn btn-outline-primary">
                    </div>
                  </div>
                  <div class="col-2">
                    <a href="<?php echo URLROOT; ?>/posts/show/<?php echo $_SESSION['paperID']; ?>?class=<?= $_SESSION['class']; ?>&subject=<?= $_SESSION['subject']; ?>&year=<?= $_SESSION['year']; ?>&term=<?= $_SESSION['term']; ?>" class="btn btn-outline-dark" data-bs-toggle="tooltip" data-bs-title="Preview">
                      <i class="bi bi-eye"></i>
                    </a>
                  </div>
                </div>
              </form>
            <?php
            }

            ?>
          </div>
        </div>

      </div>
    </div>
  </section>

</main><!-- End #main -->

<?php require APPROOT . '/views/inc/footer.php'; ?>