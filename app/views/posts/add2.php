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
          <div class="d-flex gap-3 flex-wrap">
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
              <div class="lead ms-3">
                <label style="font-size:small;">If question has daigram | Use camera icon before set question.</label>
              </div>
            <?php endif; ?>
            <div class="row mb-2">
              <div class="col-12"><!-- Question  input -->
                <textarea class="tiny" name="question">
                    <b>a)</b>&nbsp;<br/>
                    <b>b)</b>&nbsp;<br/>
                    </textarea>
              </div>
            </div>

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
    height: 400,
    plugins: 'charmap emoticon wordcount table pagebreak',
    menubar: 'edit insert format tools table',
    toolbar: 'undo redo dash | bold underline strikethrough | lineheight outdent indent | charmap pagebreak',
    newline_behavior: 'linebreak',
    setup: (editor) => {

      editor.ui.registry.addButton('dash', {
        text: '__________',
        onAction: (_) => editor.insertContent(`__________`)
      });

    },
  });
</script>