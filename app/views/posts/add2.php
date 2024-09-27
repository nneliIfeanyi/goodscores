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
        <li class="breadcrumb-item"><a href="<?= URLROOT; ?>/users/classes"><?php echo $data['class']; ?></a></li>
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
            if ($data['num_rows'] == $data['total_subject_num_rows']) {
            ?>
              <div class="alert alert-primary bg-primary text-light border-0 alert-dismissible fade show" role="alert">
                <strong>theory_questions</strong>
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
              <div class="d-grid">
                <form action="<?php echo URLROOT; ?>/submissions/set/objectives_questions" method="POST">
                  <input type="hidden" name="class" value="<?php echo $data['class']; ?>">
                  <input type="hidden" name="subject" value="<?php echo $data['subject']; ?>">
                  <input type="hidden" name="year" value="<?php echo $data['year']; ?>">
                  <input type="hidden" name="term" value="<?php echo $data['term']; ?>">
                  <div class="d-grid">
                    <input type="submit" name="set" value="Go to Objectives Questions" class="btn btn-outline-primary">
                  </div>
                </form>
              </div>
              </form>
            <?php
            } else { // Not completed | Still in progress

            ?>
              <div class="alert alert-primary bg-primary text-light border-0 alert-dismissible fade show" role="alert">
                <strong>theory_questions <?php echo $data['num_rows'] + (1) . ' of ' . $data['total_subject_num_rows']; ?></strong>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>
              <!-- Theory question form submit -->
              <form action="<?php echo URLROOT; ?>/posts/add2/<?= $data['paperID']; ?>" method="POST">
                <input type="hidden" name="questionID" value="<?= $data['num_rows'] + (1); ?>">
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
                <!-- Accordion without outline borders is overall parent for the question is a child of th e form -->
                <div class="accordion accordion-flush" id="accordionFlushExample">
                  <div class="accordion-item border-0"><!-- Numbering A Parent div -->
                    <div class="row mb-2">
                      <div class="col-1"> <!-- Numbering A badge -->
                        <p class="badge bg-secondary"><?= $data['num_rows'] + (1); ?>a</p>
                      </div>
                      <div class="col-11"><!-- Numbering A input -->
                        <textarea class="form-control" name="question-A" required></textarea>
                        <button class="accordion-button p-0 collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseOne" aria-expanded="false" aria-controls="flush-collapseOne">
                        </button><!-- Accordion toggle button -->
                        <div id="flush-collapseOne" class="accordion-collapse collapse" data-bs-parent="#accordionFlushExample">
                          <div class="accordion-body"><!-- Question A children div begins here -->
                            <div class="row mb-2">
                              <div class="col-1"><!-- Numbering A-i badge -->
                                <p class="badge bg-secondary">i</p>
                              </div>
                              <div class="col-10"><!-- Numbering A-i input parent -->
                                <textarea class="form-control" name="A-i"></textarea>
                              </div>
                            </div>
                            <div class="row mb-2">
                              <div class="col-1"><!-- Numbering A-ii badge -->
                                <p class="badge bg-secondary">ii</p>
                              </div>
                              <div class="col-10"><!-- Numbering A-ii input parent -->
                                <textarea class="form-control" name="A-ii"></textarea>
                              </div>
                            </div>
                            <div class="row mb-2">
                              <div class="col-1"><!-- Numbering A-iii badge -->
                                <p class="badge bg-secondary">iii</p>
                              </div>
                              <div class="col-10"><!-- Numbering A-iii input parent -->
                                <textarea class="form-control" name="A-iii"></textarea>
                              </div>
                            </div>
                            <div class="row">
                              <div class="col-1"><!-- Numbering A-iv badge -->
                                <p class="badge bg-secondary">iv</p>
                              </div>
                              <div class="col-10"><!-- Numbering A-iv input parent-->
                                <textarea class="form-control" name="A-iv"></textarea>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div><!-- Numbering A parent div ends -->

                  <div class="accordion-item border-0"><!-- Numbering B parent div -->
                    <div class="row mb-2">
                      <div class="col-1"><!-- Numbering B badge -->
                        <p class="badge bg-secondary"><?= $data['num_rows'] + (1); ?>b</p>
                      </div>
                      <div class="col-11"><!-- Numbering B input -->
                        <textarea class="form-control" name="question-B"></textarea>
                        <button class="accordion-button p-0 collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#<?= $data['num_rows']; ?>b" aria-expanded="false" aria-controls="flush-collapseOne">
                        </button><!-- Numbering B toggle button -->
                        <div id="<?= $data['num_rows']; ?>b" class="accordion-collapse collapse" data-bs-parent="#accordionFlushExample">
                          <div class="accordion-body"><!-- Question B children div begins here -->
                            <div class="row mb-2">
                              <div class="col-1">
                                <p class="badge bg-secondary">i</p>
                              </div>
                              <div class="col-10">
                                <textarea class="form-control" name="B-i"></textarea>
                              </div>
                            </div>
                            <div class="row mb-2">
                              <div class="col-1">
                                <p class="badge bg-secondary">ii</p>
                              </div>
                              <div class="col-10">
                                <textarea class="form-control" name="B-ii"></textarea>
                              </div>
                            </div>
                            <div class="row mb-2">
                              <div class="col-1">
                                <p class="badge bg-secondary">iii</p>
                              </div>
                              <div class="col-10">
                                <textarea class="form-control" name="B-iii"></textarea>
                              </div>
                            </div>
                            <div class="row">
                              <div class="col-1">
                                <p class="badge bg-secondary">iv</p>
                              </div>
                              <div class="col-10">
                                <textarea class="form-control" name="B-iv"></textarea>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div><!-- Question B parent div ends here -->
                  <div class="accordion-item border-0">
                    <div class="row mb-2">
                      <div class="col-1">
                        <p class="badge bg-secondary"><?= $data['num_rows'] + (1); ?>c</p>
                      </div>
                      <div class="col-11">
                        <textarea class="form-control" name="question-C"></textarea>
                        <button class="accordion-button p-0 collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#<?= $data['num_rows']; ?>c" aria-expanded="false" aria-controls="flush-collapseOne">
                        </button>
                        <div id="<?= $data['num_rows']; ?>c" class="accordion-collapse collapse" data-bs-parent="#accordionFlushExample">
                          <div class="accordion-body">
                            <div class="row mb-2">
                              <div class="col-1">
                                <p class="badge bg-secondary">i</p>
                              </div>
                              <div class="col-10">
                                <textarea class="form-control" name="C-i"></textarea>
                              </div>
                            </div>
                            <div class="row mb-2">
                              <div class="col-1">
                                <p class="badge bg-secondary">ii</p>
                              </div>
                              <div class="col-10">
                                <textarea class="form-control" name="C-ii"></textarea>
                              </div>
                            </div>
                            <div class="row mb-2">
                              <div class="col-1">
                                <p class="badge bg-secondary">iii</p>
                              </div>
                              <div class="col-10">
                                <textarea class="form-control" name="C-iii"></textarea>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="accordion-item border-0"><!-- Question D div begins here have no children -->
                    <div class="row mb-2">
                      <div class="col-1">
                        <p class="badge bg-secondary"><?= $data['num_rows'] + (1); ?>d</p>
                      </div>
                      <div class="col-11">
                        <textarea class="form-control" name="question-D"></textarea>
                      </div>
                    </div>
                  </div><!-- Question D div ends here -->
                  <div class="row my-3">
                    <div class="col-2">
                      <a href="<?php echo URLROOT; ?>/posts/daigram/<?= $data['paperID']; ?>" class="btn btn-outline-dark" data-bs-toggle="tooltip" data-bs-title="Append diagram">
                        <i class="bi bi-camera"></i>
                      </a>
                    </div>
                    <div class="col-8">
                      <div class="d-grid">
                        <input type="submit" name="submit" id="submit" value="SET" class="btn btn-outline-primary">
                      </div>
                    </div>
                    <div class="col-2">
                      <a href="<?php echo URLROOT; ?>/posts/show2/<?php echo $data['paperID']; ?>?class=<?= $data['class']; ?>&subject=<?= $data['subject']; ?>" class="btn btn-outline-dark" data-bs-toggle="tooltip" data-bs-title="Preview">
                        <i class="bi bi-eye"></i>
                      </a>
                    </div>
                  </div>
                </div><!-- End Accordion without outline borders -->
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
<!-- <script>
  var course = document.querySelector('#setTheory');
  $('#setTheory').on('submit', function(event) {
    event.preventDefault();

    $.ajax({
      url: "<?php echo URLROOT; ?>/posts/add2",
      method: "POST",
      data: new FormData(this),
      contentType: false,
      processData: false,
      beforeSend: function() {
        $('#submit').attr('disabled', 'disabled');
        $('#submit').val('Saving details, pls wait ......');

      },
      success: function(data) {
        //course.reset();
        $('#submit').attr('disabled', false);
        $('#submit').val('Add course to UI');
        $('#success-msg').html(data);
      }
    })

  })
</script> -->