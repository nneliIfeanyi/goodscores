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
      <div class="col-md-10 col-lg-10 alert border-0 p-0 alert-dismissible fade show" role="alert">
        <div class="row">
          <div class="col-lg-11 me-auto">
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            <?php
            if ($data['num_rows'] >= $data['total_subject_num_rows']) {
            ?>
              <div class="alert alert-primary bg-primary text-light border-0 alert-dismissible fade show" role="alert">
                <strong>objectives_questions</strong>
              </div>
              <textarea class="form-control" disabled required placeholder="COMPLETED"></textarea>
              <div class="row my-3">
                <div class="col-6 col-md-3">
                  <input type="text" disabled class="form-control" placeholder="Opt-A">
                </div>
                <div class="col-6 col-md-3">
                  <input type="text" disabled class="form-control" placeholder="Opt-B">
                </div>
                <div class="col-6 col-md-3">
                  <input type="text" disabled class="form-control" placeholder="Opt-C">
                </div>
                <div class="col-6 col-md-3">
                  <input type="text" disabled class="form-control" placeholder="Opt-D">
                </div>
              </div>
              <div class="d-flex gap-2 flex-wrap">
                <?php if ($this->postModel->getParamsByPaperID($data['paperID'], 'theory_questions')) : ?>
                  <a class="btn btn-outline-primary" href="<?php echo URLROOT; ?>/posts/add2/<?= $data['paperID']; ?>">Go to Theory Questions <i class="bi bi-chevron-right"></i></a>
                  <a class="btn btn-outline-secondary" href="<?php echo URLROOT; ?>/posts/show/<?= $data['paperID']; ?>?class=<?= $data['class']; ?>&subject=<?= $data['subject']; ?>">Preview <i class="bi bi-eye"></i></a>
                  <a class="btn" href="<?php echo URLROOT; ?>/users/dashboard">Go to Dashboard <i class="bi bi-chevron-right"></i></a>
                <?php else : ?>
                  <a class="btn btn-outline-primary" href="<?php echo URLROOT; ?>/posts/show/<?= $data['paperID']; ?>?class=<?= $data['class']; ?>&subject=<?= $data['subject']; ?>">Preview <i class="bi bi-eye"></i></a>
                  <a class="btn" href="<?php echo URLROOT; ?>/users/dashboard">Go to Dashboard <i class="bi bi-chevron-right"></i></a>

                  <a href="<?= URLROOT; ?>/users/set/theory_questions?class=<?= $data['params']->class; ?>&subject=<?= $data['params']->subject; ?>" class="btn btn-success">Append Theory Section</a>

                <?php endif; ?>
              </div>
            <?php
            } else {
              if (empty($data['num_rows'])) {
                $data['num_rows'] = 1;
              } else {
                $data['num_rows'] = $data['num_rows'] + 1;
              }
            ?>
              <div class="p-3 rounded-3 bg-primary text-light">
                <strong><?= $data['tag']; ?> | Question <?php echo $data['num_rows'] . ' of ' . $data['total_subject_num_rows']; ?></strong>
              </div>
              <form method="POST" action="<?= URLROOT; ?>/processing/add/<?= $data['paperID']; ?>">
                <input type="hidden" name="isSubjective" value="no">
                <div class="mt-4 mb-2">
                  <label for="className">Sub-section Instruction</label>
                  <textarea class="tiny2" name="sub_ins"><p></p></textarea>
                  <!-- <input name="sub_ins" class="form-control form-control-lg" /> -->
                </div>
                <?php if (!empty($_SESSION['daigram'])) : ?>
                  <div class="d-flex justify-content-center">
                    <div class="mt-2 mb-4">
                      <img src="<?php echo URLROOT . '/' . $_SESSION['daigram']; ?>" class="img-fluid rounded-3" alt="daigram">
                    </div>
                  </div>
                <?php else : ?>
                  <div class="lead ms-2">
                    <label style="font-size: medium;">If question has daigram | Use camera icon before set question.</label>
                  </div>
                <?php endif; ?>
                <textarea class="tiny" name="question"><p></p></textarea>

                <div class="row my-3">
                  <div class="col-lg-6">
                    <label for="A">(A)</label>
                    <textarea id="A" class="tiny2" type="text" class="tiny" name="opt1"><p></p></textarea>
                  </div>
                  <div class="col-lg-6">
                    <label for="A">(B)</label>
                    <textarea class="tiny2" type="text" class="tiny" name="opt2"><p></p></textarea>
                  </div>
                  <div class="col-lg-6">
                    <label for="A">(C)</label>
                    <textarea class="tiny2" type="text" class="tiny" name="opt3"><p></p></textarea>
                  </div>
                  <div class="col-lg-6">
                    <label for="A">(D)</label>
                    <textarea class="tiny2" type="text" class="tiny" name="opt4"><p></p></textarea>
                  </div>
                </div>

                <!-- <div class="row my-3">
                    <p class="col-2 d-lg-none">(A)</p>
                    <div class="col-10 col-lg-6">
                      <input type="text" class="form-control form-control-lg" placeholder="Option A" name="opt1">
                    </div>
                    <p class="col-2 d-lg-none">(B)</p>
                    <div class="col-10 col-lg-6">
                      <input type="text" class="form-control form-control-lg" placeholder="Option B" name="opt2">
                    </div>
                    <p class="col-2 d-lg-none">(C)</p>
                    <div class="col-10 col-lg-6">
                      <input type="text" class="form-control form-control-lg" placeholder="Option C" name="opt3">
                    </div>
                    <p class="col-2 d-lg-none">(D)</p>
                    <div class="col-10 col-lg-6">
                      <input type="text" class="form-control form-control-lg" placeholder="Option D" name="opt4">
                    </div>
                  </div> -->
                <div class="d-flex flex-row gap-3 py-3">
                  <div class="form-check border border-secondary">
                    <input type="radio" name="ans" value="A" class="form-check-input" id="A">
                    <label for="A" class="form-check-label">A</label>
                  </div>
                  <div class="form-check border border-secondary">
                    <input type="radio" name="ans" value="B" class="form-check-input" id="B">
                    <label for="B" class="form-check-label">B</label>
                  </div>
                  <div class="form-check border border-secondary">
                    <input type="radio" name="ans" value="C" class="form-check-input" id="C">
                    <label for="C" class="form-check-label">C</label>
                  </div>
                  <div class="form-check border border-secondary">
                    <input type="radio" name="ans" value="D" class="form-check-input" id="D">
                    <label for="D" class="form-check-label">D</label>
                  </div>
                </div>
                <div class="row">
                  <div class="col-2">
                    <a href="<?php echo URLROOT; ?>/posts/daigram/<?= $data['paperID']; ?>" class="btn btn-outline-dark" data-bs-toggle="tooltip" data-bs-title="Append diagram">
                      <i class="bi bi-camera"></i>
                    </a>
                  </div>
                  <div class="col-8">
                    <div class="d-grid">
                      <input type="submit" id="submit" value="SET" class="btn btn-outline-primary">
                    </div>
                  </div>
                  <div class="col-2">
                    <a href="<?php echo URLROOT; ?>/posts/show/<?= $data['paperID']; ?>?class=<?= $data['class']; ?>&subject=<?= $data['subject']; ?>" class="btn btn-outline-dark" data-bs-toggle="tooltip" data-bs-title="Preview">
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
      <!-- ===== isSubjective Question ===== -->
      <?php if ($data['num_rows'] <= $data['total_subject_num_rows']) : ?>
        <div class="col-md-10 col-lg-8 alert alert-dismissible p-0 fade show" role="alert">
          <div class="bg-secondary mt-5 text-light border-0 p-3 rounded-3">
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            <strong>Set Subjective Question</strong>
          </div>
          <form method="POST" action="<?= URLROOT; ?>/processing/add/<?= $data['paperID']; ?>">
            <input type="hidden" name="isSubjective" value="yes">
            <div class="mt-4 mb-2">
              <label for="">Sub-section Instruction</label>
              <textarea class="tiny2" name="sub_ins"><p></p></textarea>
            </div>
            <?php if (!empty($_SESSION['daigram'])) : ?>
              <div class="d-flex justify-content-center">
                <div class="mt-2 mb-4">
                  <img src="<?php echo URLROOT . '/' . $_SESSION['daigram']; ?>" class="img-fluid rounded-3" alt="daigram">
                </div>
              </div>
            <?php else : ?>
              <div class="lead">
                <label style="font-size: small;">If question has daigram | Use camera icon before set question.</label>
              </div>
            <?php endif; ?>

            <textarea class="tiny" name="question" required><p></p></textarea>

            <div class="my-4">
              <label for="className">Expected answer</label>
              <textarea class="tiny2" name="ans"><p></p></textarea>
            </div>
            <!-- <div class="my-4">
                <label for="className">Expected answer</label>
                <input type="text" name="ans" class="form-control form-control-lg" data-parsley-trigger="keyup" />
              </div> -->
            <div class="row">
              <div class="col-2">
                <a href="<?php echo URLROOT; ?>/posts/daigram/<?= $data['paperID']; ?>" class="btn btn-outline-dark" data-bs-toggle="tooltip" data-bs-title="Append diagram">
                  <i class="bi bi-camera"></i>
                </a>
              </div>
              <div class="col-8">
                <div class="d-grid">
                  <input type="submit" id="submit" value="SET" class="btn btn-outline-primary">
                </div>
              </div>
              <div class="col-2">
                <a href="<?php echo URLROOT; ?>/posts/show/<?= $data['paperID']; ?>?class=<?= $data['class']; ?>&subject=<?= $data['subject']; ?>" class="btn btn-outline-dark" data-bs-toggle="tooltip" data-bs-title="Preview">
                  <i class="bi bi-eye"></i>
                </a>
              </div>
            </div>
          </form>
        </div>
      <?php endif; ?>
      <!-- ===== End isSubjective Question ===== -->
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
<script>
  tinymce.init({
    selector: 'textarea.tiny',
    height: 210,
    plugins: 'charmap emoticon wordcount table pagebreak',
    menubar: 'edit insert format tools',
    toolbar: 'undo redo dash | superscript subscript bold underline strikethrough | lineheight outdent indent | charmap table',
    newline_behavior: 'linebreak',
    setup: (editor) => {

      editor.ui.registry.addButton('dash', {
        text: '__________',
        onAction: (_) => editor.insertContent(`__________`)
      });

    },
  });
</script>
<script>
  tinymce.init({
    selector: 'textarea.tiny2',
    height: 180,
    plugins: 'charmap',
    menubar: '',
    toolbar: 'undo redo dash | superscript subscript bold underline strikethrough | lineheight outdent indent | charmap',
    newline_behavior: 'linebreak',
    setup: (editor) => {

      editor.ui.registry.addButton('dash', {
        text: '=',
        onAction: (_) => editor.insertContent(`&nbsp;=&nbsp;`)
      });

    },
  });
</script>