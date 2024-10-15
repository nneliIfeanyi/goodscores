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
            if ($data['num_rows'] >= $data['total_subject_num_rows']) {
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
              <div class="d-flex gap-3 ">
                <?php if ($this->postModel->getParamsByPaperID($data['paperID'], 'objectives_questions')) : ?>
                  <a class="btn btn-outline-primary" href="<?php echo URLROOT; ?>/posts/add/<?= $data['paperID']; ?>">Go to Objectives Questions <i class="bi bi-chevron-right"></i></a>
                  <a class="btn btn-outline-secondary" href="<?php echo URLROOT; ?>/posts/show2/<?= $data['paperID']; ?>?class=<?= $data['class']; ?>&subject=<?= $data['subject']; ?>">Preview <i class="bi bi-eye"></i></a>
                  <a class="btn" href="<?php echo URLROOT; ?>/users/dashboard">Go to Dashboard <i class="bi bi-chevron-right"></i></a>
                <?php else : ?>
                  <a class="btn btn-outline-primary" href="<?php echo URLROOT; ?>/posts/show2/<?= $data['paperID']; ?>?class=<?= $data['class']; ?>&subject=<?= $data['subject']; ?>">Preview <i class="bi bi-eye"></i></a>
                  <a class="btn" href="<?php echo URLROOT; ?>/users/dashboard">Go to Dashboard <i class="bi bi-chevron-right"></i></a>
                <?php endif; ?>
              </div>
            <?php
            } else { // Not completed | Still in progress

            ?>
              <div class="alert alert-primary bg-primary text-light border-0 alert-dismissible fade show" role="alert">
                <strong>theory_questions <?php echo $data['num_rows'] + (1) . ' of ' . $data['total_subject_num_rows']; ?></strong>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>
              <!-- Theory question form submit -->
              <form action="<?php echo URLROOT; ?>/processing/add2/<?= $data['paperID']; ?>" method="POST">
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
                        <textarea class="tiny" name="question-A" required><p></p></textarea>
                        <button class="accordion-button p-0 collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseOne" aria-expanded="false" aria-controls="flush-collapseOne">
                        </button><!-- Accordion toggle button -->
                        <div id="flush-collapseOne" class="accordion-collapse collapse" data-bs-parent="#accordionFlushExample">
                          <div class="accordion-body"><!-- Question A children div begins here -->
                            <div class="row mb-2">
                              <div class="col-1"><!-- Numbering A-i badge -->
                                <p class="badge bg-secondary">i</p>
                              </div>
                              <div class="col-10"><!-- Numbering A-i input parent -->
                                <input class="form-control form-control-lg" name="A-i">
                              </div>
                            </div>
                            <div class="row mb-2">
                              <div class="col-1"><!-- Numbering A-ii badge -->
                                <p class="badge bg-secondary">ii</p>
                              </div>
                              <div class="col-10"><!-- Numbering A-ii input parent -->
                                <input class="form-control form-control-lg" name="A-ii">
                              </div>
                            </div>
                            <div class="row mb-2">
                              <div class="col-1"><!-- Numbering A-iii badge -->
                                <p class="badge bg-secondary">iii</p>
                              </div>
                              <div class="col-10"><!-- Numbering A-iii input parent -->
                                <input class="form-control form-control-lg" name="A-iii">
                              </div>
                            </div>
                            <div class="row">
                              <div class="col-1"><!-- Numbering A-iv badge -->
                                <p class="badge bg-secondary">iv</p>
                              </div>
                              <div class="col-10">
                                <input class="form-control form-control-lg" name="A-iv">
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
                        <textarea class="tiny" name="question-B"></textarea>
                        <button class="accordion-button p-0 collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#<?= $data['num_rows']; ?>b" aria-expanded="false" aria-controls="flush-collapseOne">
                        </button><!-- Numbering B toggle button -->
                        <div id="<?= $data['num_rows']; ?>b" class="accordion-collapse collapse" data-bs-parent="#accordionFlushExample">
                          <div class="accordion-body"><!-- Question B children div begins here -->
                            <div class="row mb-2">
                              <div class="col-1">
                                <p class="badge bg-secondary">i</p>
                              </div>
                              <div class="col-10">
                                <input class="form-control form-control-lg" name="B-i">
                              </div>
                            </div>
                            <div class="row mb-2">
                              <div class="col-1">
                                <p class="badge bg-secondary">ii</p>
                              </div>
                              <div class="col-10">
                                <input class="form-control form-control-lg" name="B-ii">
                              </div>
                            </div>
                            <div class="row mb-2">
                              <div class="col-1">
                                <p class="badge bg-secondary">iii</p>
                              </div>
                              <div class="col-10">
                                <input class="form-control form-control-lg" name="B-iii">
                              </div>
                            </div>
                            <div class="row">
                              <div class="col-1">
                                <p class="badge bg-secondary">iv</p>
                              </div>
                              <div class="col-10">
                                <input class="form-control form-control-lg" name="B-iv">
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
                        <textarea class="tiny" name="question-C"></textarea>
                        <button class="accordion-button p-0 collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#<?= $data['num_rows']; ?>c" aria-expanded="false" aria-controls="flush-collapseOne">
                        </button>
                        <div id="<?= $data['num_rows']; ?>c" class="accordion-collapse collapse" data-bs-parent="#accordionFlushExample">
                          <div class="accordion-body">
                            <div class="row mb-2">
                              <div class="col-1">
                                <p class="badge bg-secondary">i</p>
                              </div>
                              <div class="col-10">
                                <input class="form-control form-control-lg" name="C-i">
                              </div>
                            </div>
                            <div class="row mb-2">
                              <div class="col-1">
                                <p class="badge bg-secondary">ii</p>
                              </div>
                              <div class="col-10">
                                <input class="form-control form-control-lg" name="C-ii">
                              </div>
                            </div>
                            <div class="row mb-2">
                              <div class="col-1">
                                <p class="badge bg-secondary">iii</p>
                              </div>
                              <div class="col-10">
                                <input class="form-control form-control-lg" name="C-iii">
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
                        <textarea class="tiny" name="question-D"></textarea>
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
<!-- Page loader fade in on form submit -->
<script>
  $(':submit').each(function() {
    $(this).click(function() {
      $('#loader').fadeIn();
    });
  });
</script>
<?php if ($data['subject'] == 'Maths' || $data['subject'] == 'maths' || $data['subject'] == 'Mathematics' || $data['subject'] == 'mathematics' || $data['subject'] == 'Further Maths' || $data['subject'] == 'further maths' || $data['subject'] == 'Further Maths' || $data['subject'] == 'Further Mathematics' || $data['subject'] == 'further mathematics' || $data['subject'] == 'Physics' || $data['subject'] == 'physics') : ?>
  <script>
    tinymce.init({
      selector: 'textarea.tiny',
      height: 180,
      plugins: 'charmap',
      menubar: '',
      toolbar: 'charmap',
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
    });
  </script>
<?php else : ?>
  <script>
    tinymce.init({
      selector: 'textarea',
      height: 180,
      plugins: 'charmap',
      menubar: '',
      toolbar: 'dash charmap',
      setup: (editor) => {

        editor.ui.registry.addButton('dash', {
          text: '__________',
          onAction: (_) => editor.insertContent(`&nbsp;__________&nbsp;`)
        });

      },
    });
  </script>
<?php endif; ?>