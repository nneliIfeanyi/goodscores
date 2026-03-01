<?php require APPROOT . '/views/inc/header.php'; ?>
<?php require APPROOT . '/views/inc/navbar.php'; ?>
<?php require APPROOT . '/views/inc/sidebar.php'; ?>

<main id="main" class="main">

  <div class="pagetitle">
    <h1>Edit Question | <?php echo $data['params']->subject; ?></h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= URLROOT; ?>/users/classes"><?php echo $data['params']->class; ?></a></li>
        <li class="breadcrumb-item"><?php echo $data['params']->term; ?></li>
        <li class="breadcrumb-item active"><?php echo $data['params']->year; ?></li>
      </ol>
    </nav>
  </div><!-- End Page Title -->
  <section class="section">
    <div class="row">
      <div class="col-lg-10">
        <form action="<?php echo URLROOT; ?>/submissions/edit2/<?= $data['post']->id; ?>" method="POST">
          <?php if (!empty($data['post']->img) && empty($_SESSION['daigram'])) : ?>
            <div class="d-flex justify-content-center">
              <div class="mt-2 mb-4">
                <img src="<?php echo URLROOT . '/' . $data['post']->img; ?>" class="img-fluid rounded-3" alt="daigram">
                <input type="hidden" name="daigram" value="<?= $data['post']->img ?>">
              </div>
            </div>
            <label style="font-size: x-small;">To change question diagram | click camera icon.</label>
          <?php elseif (!empty($data['post']->img) && !empty($_SESSION['daigram'])) : ?>
            <div class="d-flex justify-content-center">
              <div class="mt-2 mb-4">
                <img src="<?php echo URLROOT . '/' . $_SESSION['daigram']; ?>" class="img-fluid rounded-3" alt="daigram">
              </div>
            </div>
          <?php elseif (empty($data['post']->img) && !empty($_SESSION['daigram'])) : ?>
            <div class="d-flex justify-content-center">
              <div class="mt-2 mb-4">
                <img src="<?php echo URLROOT . '/' . $_SESSION['daigram']; ?>" class="img-fluid rounded-3" alt="daigram">
              </div>
            </div>
          <?php else : ?>
            <label style="font-size: small;">To append a diagram | click camera icon.</label>
          <?php endif; ?>
          <div class="alert alert-primary bg-primary text-light border-0 alert-dismissible fade show" role="alert">
            <strong>theory_questions | <?= $data['post']->questionID; ?></strong>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
          <!-- Theory question form submit -->
          <input type="hidden" name="questionID" value=" <?= $data['post']->questionID; ?>">
          <button id="toggle-question-info" type="button" class="btn btn-sm btn-secondary my-2">Edit question Info</button>
          <div id="question-info" style="display: none;" class="mt-1 mb-2">
            <label for="className">Add more information or instructions for this question.</label>
            <textarea class="tiny" name="sub_ins"><?php echo $data['post']->subIns; ?></textarea>
          </div>
          <div class="row mb-2">
            <div class="col-1"> <!-- Numbering badge -->
              <p class="badge bg-secondary"><?= $data['post']->questionID; ?>a</p>
            </div>
            <div class="col-11"><!-- Question  input -->
              <textarea class="tiny" name="questionA"><?= $data['post']->questionA; ?></textarea>
            </div>
          </div>
          <div class="row mb-2">
            <div class="col-1"> <!-- Numbering badge -->
              <p class="badge bg-secondary"><?= $data['post']->questionID; ?>b</p>
            </div>
            <div class="col-11"><!-- Question  input -->
              <textarea class="tiny" name="questionB"><?= $data['post']->questionB; ?></textarea>
            </div>
          </div>
          <div class="row mb-2">
            <div class="col-1"> <!-- Numbering badge -->
              <p class="badge bg-secondary"><?= $data['post']->questionID; ?>c</p>
            </div>
            <div class="col-11"><!-- Question  input -->
              <textarea class="tiny" name="questionC"><?= $data['post']->questionC; ?></textarea>
            </div>
          </div>
          <div class="row mb-2">
            <div class="col-1"> <!-- Numbering badge -->
              <p class="badge bg-secondary"><?= $data['post']->questionID; ?>d</p>
            </div>
            <div class="col-11"><!-- Question  input -->
              <textarea class="tiny" name="questionD"><?= $data['post']->questionD; ?></textarea>
            </div>
          </div>
          <div class="row my-3">
            <div class="col-2">
              <a href="<?php echo URLROOT; ?>/posts/daigram/<?= $data['post']->paperID; ?>" class="btn btn-outline-dark" data-bs-toggle="tooltip" data-bs-title="Append diagram">
                <i class="bi bi-camera"></i>
              </a>
            </div>
            <div class="col-8">
              <div class="d-grid">
                <input type="submit" name="submit" id="submit" value="SET" class="btn btn-outline-primary">
              </div>
            </div>
            <div class="col-2">
              <a href="<?php echo URLROOT; ?>/posts/show2/<?= $data['post']->paperID; ?>?class=<?= $data['params']->class; ?>&subject=<?= $data['params']->subject; ?>" class="btn btn-outline-dark" data-bs-toggle="tooltip" data-bs-title="Preview">
                <i class="bi bi-eye"></i>
              </a>
            </div>
          </div>

      </div><!-- End Accordion without outline borders -->
      </form>
    </div>
    </div>

    </div>
  </section>

</main><!-- End #main -->
</div>
</div>
</section>
</main><!-- End #main -->


<?php require APPROOT . '/views/inc/footer.php'; ?>
<script>
  tinymce.init({
    selector: 'textarea.tiny',
    height: 220,
    plugins: 'charmap emoticon wordcount pagebreak table codesample',
    menubar: 'edit insert format tools table',
    toolbar: 'undo redo dash | bold underline strikethrough | lineheight outdent indent | charmap table',
    newline_behavior: 'linebreak',
    statusbar: false,
    promotion: false,
    table_row_class_list: [{
      title: 'None',
      value: '',
    }, {
      title: 'Grey border',
      value: 'table_cell_grey_border',
    }, ],
    table_sizing_mode: 'relative',
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