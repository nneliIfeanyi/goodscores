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
      <div class="col-md-10 col-lg-7">

        <div class="card">
          <div class="card-body">

            <div class="alert alert-primary bg-primary text-light border-0 alert-dismissible fade show" role="alert">
              <strong><?php echo $data['params']->tag; ?></strong>
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php if (!empty($data['post']->img) && empty($_SESSION['daigram'])) : ?>
              <div class="d-flex justify-content-center">
                <div class="mt-2 mb-4">
                  <img src="<?php echo URLROOT . '/' . $data['post']->img; ?>" class="img-fluid rounded-3" alt="daigram">
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
              <label style="font-size: x-small;">To append a diagram | click camera icon.</label>

            <?php endif; ?>
            <form action="<?php echo URLROOT; ?>/posts/edit/<?php echo $data['post']->id; ?>" method="POST">
              <input type="hidden" name="paperID" value="<?php echo $data['post']->paperID; ?>">
              <input type="hidden" name="daigram" value="<?= $data['post']->img; ?>">
              <input type="hidden" name="section_alt" value="<?= $data['params']->section_alt; ?>">
              <input type="hidden" name="isSubjective" value="no">
              <div class="my-4">
                <label for="className">Sub Instruction</label>
                <input type="text" name="sub_ins" value="<?php echo $data['post']->subInstruction; ?>" class="form-control form-control-lg" />
              </div>
              <?php if ($data['params']->subject == 'Maths' || $data['params']->subject == 'maths' || $data['params']->subject == 'Mathematics' || $data['params']->subject == 'mathematics' || $data['params']->subject == 'Further Maths' || $data['params']->subject == 'further maths' || $data['params']->subject == 'Further Maths' || $data['params']->subject == 'Further Mathematics' || $data['params']->subject == 'further mathematics' || $data['params']->subject == 'Physics' || $data['params']->subject == 'physics') : ?>
                <textarea class="tiny" name="question"><?php echo $data['post']->question; ?></textarea>
                <?php
                $mathsObj = 1;
                if ($mathsObj == '1') {
                ?>
                  <div class="row my-3">
                    <p class="col-2 d-lg-none">(A)</p>
                    <div class="col-10 col-lg-6">
                      <textarea class="tiny2" type="text" class="tiny" name="opt1"><?php echo $data['post']->opt1; ?></textarea>
                    </div>
                    <p class="col-2 d-lg-none">(B)</p>
                    <div class="col-10 col-lg-6">
                      <textarea class="tiny2" type="text" class="tiny" name="opt2"><?php echo $data['post']->opt2; ?></textarea>
                    </div>
                    <p class="col-2 d-lg-none">(C)</p>
                    <div class="col-10 col-lg-6">
                      <textarea class="tiny2" type="text" class="tiny" name="opt3"><?php echo $data['post']->opt3; ?></textarea>
                    </div>
                    <p class="col-2 d-lg-none">(D)</p>
                    <div class="col-10 col-lg-6">
                      <textarea class="tiny2" type="text" class="tiny" name="opt4"><?php echo $data['post']->opt4; ?></textarea>
                    </div>
                  </div>
                <?php
                }
                ?>
              <?php else : ?>
                <textarea class="form-control" name="question"><?php echo $data['post']->question; ?></textarea>
                <div class="row my-3">
                  <p class="col-2 d-lg-none">(A)</p>
                  <div class="col-10 col-lg-6">
                    <input type="text" class="form-control form-control-lg" value="<?php echo $data['post']->opt1; ?>" placeholder="Option A" required name="opt1">
                  </div>
                  <p class="col-2 d-lg-none">(B)</p>
                  <div class="col-10 col-lg-6">
                    <input type="text" class="form-control form-control-lg" value="<?php echo $data['post']->opt2; ?>" placeholder="Option B" required name="opt2">
                  </div>
                  <p class="col-2 d-lg-none">(C)</p>
                  <div class="col-10 col-lg-6">
                    <input type="text" class="form-control form-control-lg" value="<?php echo $data['post']->opt3; ?>" placeholder="Option C" name="opt3">
                  </div>
                  <p class="col-2 d-lg-none">(D)</p>
                  <div class="col-10 col-lg-6">
                    <input type="text" class="form-control form-control-lg" value="<?php echo $data['post']->opt4; ?>" placeholder="Option D" name="opt4">
                  </div>
                </div>
              <?php endif; ?>
              <div class="d-flex flex-row gap-3 py-3">
                <div class="form-check border border-secondary">
                  <input type="radio" name="ans" value="A" <?php echo ($data['post']->ans == 'a') ? 'checked' : ''; ?> class="form-check-input" id="A">
                  <label for="A" class="form-check-label">a</label>
                </div>
                <div class="form-check border border-secondary">
                  <input type="radio" name="ans" value="B" <?php echo ($data['post']->ans == 'b') ? 'checked' : ''; ?> class="form-check-input" id="B">
                  <label for="B" class="form-check-label">b</label>
                </div>
                <div class="form-check border border-secondary">
                  <input type="radio" name="ans" value="C" <?php echo ($data['post']->ans == 'c') ? 'checked' : ''; ?> class="form-check-input" id="C">
                  <label for="C" class="form-check-label">c</label>
                </div>
                <div class="form-check border border-secondary">
                  <input type="radio" name="ans" value="D" <?php echo ($data['post']->ans == 'd') ? 'checked' : ''; ?> class="form-check-input" id="D">
                  <label for="D" class="form-check-label">d</label>
                </div>
              </div>
              <div class="d-flex gap-2">
                <a href="<?php echo URLROOT; ?>/posts/daigram/<?php echo $data['post']->paperID; ?>" class="btn btn-outline-dark" data-bs-toggle="tooltip" data-bs-title="Change diagram">
                  <i class="bi bi-camera"></i>
                </a>
                <input type="submit" name="submit" value="Save Changes" class="btn btn-outline-primary">
                <a href="<?php echo URLROOT; ?>/posts/show/<?php echo $data['params']->paperID; ?>?class=<?= $data['params']->class; ?>&subject=<?= $data['params']->subject; ?>" class="btn-outline-dark btn">
                  <i class="bi bi-chevron-left"></i> Back
                </a>
              </div>
            </form>

          </div>
        </div>

      </div>
      <!-- ===== isSubjective Question ===== -->

      <?php if ($_COOKIE['cbt'] == '1') : ?>
        <div class="col-md-10 col-lg-5">
          <div class="alert alert-primary bg-secondary text-light border-0 alert-dismissible fade show" role="alert">
            <strong><?php echo $data['params']->tag; ?></strong>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
          <?php if (!empty($data['post']->img) && empty($_SESSION['daigram'])) : ?>
            <div class="d-flex justify-content-center">
              <div class="mt-2 mb-4">
                <img src="<?php echo URLROOT . '/' . $data['post']->img; ?>" class="img-fluid rounded-3" alt="daigram">
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
            <label style="font-size: x-small;">To append a diagram | click camera icon.</label>

          <?php endif; ?>
          <form action="<?php echo URLROOT; ?>/posts/edit/<?php echo $data['post']->id; ?>" method="POST">
            <input type="hidden" name="paperID" value="<?php echo $data['post']->paperID; ?>">
            <input type="hidden" name="daigram" value="<?= $data['post']->img; ?>">
            <input type="hidden" name="section_alt" value="<?= $data['params']->section_alt; ?>">
            <input type="hidden" name="isSubjective" value="yes">
            <div class="my-4">
              <label for="className">Sub Instruction</label>
              <input type="text" name="sub_ins" value="<?php echo $data['post']->subInstruction; ?>" class="form-control form-control-lg" />
            </div>
            <?php if ($data['params']->subject == 'Maths' || $data['params']->subject == 'maths' || $data['params']->subject == 'Mathematics' || $data['params']->subject == 'mathematics' || $data['params']->subject == 'Further Maths' || $data['params']->subject == 'further maths' || $data['params']->subject == 'Further Maths' || $data['params']->subject == 'Further Mathematics' || $data['params']->subject == 'further mathematics' || $data['params']->subject == 'Physics' || $data['params']->subject == 'physics') : ?>
              <textarea class="tiny" name="question"><?php echo $data['post']->question; ?></textarea>
              <?php
              $mathsObj = 1;
              if ($mathsObj == '1') {
              ?>
                <div class="my-4">
                  <label for="className">Expected answer</label>
                  <textarea class="tiny2" name="ans"><?= $data['post']->ans; ?></textarea>
                </div>
              <?php
              }
              ?>
            <?php else : ?>
              <textarea class="form-control" name="question"><?php echo $data['post']->question; ?></textarea>
              <div class="my-4">
                <label for="className">Expected answer</label>
                <input type="text" name="ans" value="<?= $data['post']->ans; ?>" class="form-control form-control-lg" data-parsley-trigger="keyup" />
              </div>

            <?php endif; ?>
            <div class="d-flex gap-2">
              <a href="<?php echo URLROOT; ?>/posts/daigram/<?php echo $data['post']->paperID; ?>" class="btn btn-outline-dark" data-bs-toggle="tooltip" data-bs-title="Change diagram">
                <i class="bi bi-camera"></i>
              </a>
              <input type="submit" name="submit" value="Save Changes" class="btn btn-outline-primary">
              <a href="<?php echo URLROOT; ?>/posts/show/<?php echo $data['params']->paperID; ?>?class=<?= $data['params']->class; ?>&subject=<?= $data['params']->subject; ?>" class="btn-outline-dark btn">
                <i class="bi bi-chevron-left"></i> Back
              </a>
            </div>
          </form>
        </div>
        <!-- ===== End isSubjective Question ===== -->
      <?php endif; ?>
    </div>
  </section>

</main><!-- End #main -->

<?php require APPROOT . '/views/inc/footer.php'; ?>
<script>
  $(':submit').each(function() {
    $(this).click(function() {
      $('#loader').fadeIn();
    });
  });
</script>
<?php if ($data['params']->subject == 'Maths' || $data['params']->subject == 'maths' || $data['params']->subject == 'Mathematics' || $data['params']->subject == 'mathematics' || $data['params']->subject == 'Further Maths' || $data['params']->subject == 'further maths' || $data['params']->subject == 'Further Maths' || $data['params']->subject == 'Further Mathematics' || $data['params']->subject == 'further mathematics' || $data['params']->subject == 'Physics' || $data['params']->subject == 'physics') : ?>
  <script>
    tinymce.init({
      selector: 'textarea.tiny',
      height: 400,
      plugins: 'charmap emoticon wordcount table pagebreak',
      menubar: 'edit insert format tools',
      toolbar: 'undo redo dash | bold superscript subscript underline strikethrough | lineheight outdent indent | charmap pagebreak',
      newline_behavior: 'linebreak',
      setup: (editor) => {

        editor.ui.registry.addButton('dash', {
          text: '__________',
          onAction: (_) => editor.insertContent(`__________`)
        });

      },
    });
  </script>
<?php else : ?>
  <script>
    tinymce.init({
      selector: 'textarea',
      height: 400,
      plugins: 'charmap emoticon wordcount table pagebreak',
      menubar: 'edit insert format tools',
      toolbar: 'undo redo dash | superscript subscript bold underline strikethrough | lineheight outdent indent | charmap pagebreak',
      newline_behavior: 'linebreak',
      setup: (editor) => {

        editor.ui.registry.addButton('dash', {
          text: '__________',
          onAction: (_) => editor.insertContent(`__________`)
        });

      },
    });
  </script>
<?php endif; ?>
<?php $mathsObj = 1;
if ($mathsObj == '1') : ?>
  <script>
    tinymce.init({
      selector: 'textarea.tiny2',
      height: 180,
      plugins: 'charmap emoticon wordcount pagebreak',
      menubar: 'edit insert format tools table',
      toolbar: 'undo redo dash | superscript subscript bold underline strikethrough | lineheight outdent indent | charmap pagebreak',
      newline_behavior: 'linebreak',
      setup: (editor) => {

        editor.ui.registry.addButton('dash', {
          text: '=',
          onAction: (_) => editor.insertContent(`&nbsp;=&nbsp;`)
        });

      },
    });
  </script>
<?php endif; ?>