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
      <div class="col-md-10 col-lg-10 alert border-0 p-0  fade show" role="alert">
        <div class="row">
          <div class="col-lg-11 me-auto">
            <?php
            if ($data['num_rows'] >= $data['total_subject_num_rows']) {
            ?>
              <div class="alert alert-primary bg-primary text-light border-0 fade show" role="alert">
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
                <strong>Question <?php echo $data['num_rows'] . ' of ' . $data['total_subject_num_rows']; ?></strong>
              </div>
              <button id="toggle-question-info" type="button" class="btn btn-sm btn-secondary my-2">Add question Info</button>
              <form method="POST" action="<?= URLROOT; ?>/processing/add/<?= $data['paperID']; ?>">
                <input type="hidden" name="isSubjective" value="no">
                <div id="question-info" style="display: none;" class="mt-1 mb-2">
                  <label for="className">Add more information or instructions for this question.</label>
                  <textarea class="tiny" name="sub_ins"><p></p></textarea>
                </div>
                <?php if (!empty($_SESSION['daigram'])) : ?>
                  <div class="d-flex justify-content-center">
                    <div class="mt-2 mb-4">
                      <img src="<?php echo URLROOT . '/' . $_SESSION['daigram']; ?>" class="img-fluid rounded-3" alt="daigram">
                    </div>
                  </div>
                <?php else : ?>
                  <div class="lead ms-2">
                    <label style="font-size: medium;">Set question here <span style="font-size: x-small;">(<label style="font-size: x-small;">To append a diagram | click camera icon.</label>)</span></label>
                  </div>
                <?php endif; ?>
                <textarea class="tiny" name="question"><p></p></textarea>
                <div class="row my-3">
                  <div class="col-lg-6">
                    <label for="A"><span style="font-size: x-small;">option</span>(A)</label>
                    <textarea id="A" class="tiny" type="text" class="tiny" name="opt1"><p></p></textarea>
                  </div>
                  <div class="col-lg-6">
                    <label for="A"><span style="font-size: x-small;">option</span>(B)</label>
                    <textarea class="tiny" type="text" class="tiny" name="opt2"><p></p></textarea>
                  </div>
                  <div class="col-lg-6">
                    <label for="A"><span style="font-size: x-small;">option</span>(C)</label>
                    <textarea class="tiny" type="text" class="tiny" name="opt3"><p></p></textarea>
                  </div>
                  <div class="col-lg-6">
                    <label for="A"><span style="font-size: x-small;">option</span>(D)</label>
                    <textarea class="tiny" type="text" class="tiny" name="opt4"><p></p></textarea>
                  </div>
                </div>
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
    </div>
  </section>

</main><!-- End #main -->

<?php require APPROOT . '/views/inc/footer.php'; ?>
<script>
  tinymce.init({
    selector: 'textarea.tiny',
    height: 210,
    plugins: 'charmap emoticon wordcount table pagebreak codesample',
    menubar: 'edit insert format tools',
    toolbar: 'undo redo dash | superscript subscript bold underline strikethrough | lineheight outdent indent | charmap table codesample',
    newline_behavior: 'linebreak',
    statusbar: false,
    promotion: false,
    setup: (editor) => {

      editor.ui.registry.addButton('dash', {
        text: '__________',
        onAction: (_) => editor.insertContent(`__________`)
      });

    },
  });
</script>

<script>
  // toggle question info visibility
  $(document).ready(function() {
    $('#toggle-question-info').click(function() {
      $('#question-info').toggle();
    });
  });
</script>