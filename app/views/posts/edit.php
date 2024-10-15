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
      <div class="col-md-10 col-lg-8">

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
              <?php if ($data['params']->subject == 'Maths' || $data['params']->subject == 'maths' || $data['params']->subject == 'Mathematics' || $data['params']->subject == 'mathematics' || $data['params']->subject == 'Further Maths' || $data['params']->subject == 'further maths' || $data['params']->subject == 'Further Maths' || $data['params']->subject == 'Further Mathematics' || $data['params']->subject == 'further mathematics' || $data['params']->subject == 'Physics' || $data['params']->subject == 'physics') : ?>
                <textarea class="tiny" name="question" required><?php echo $data['post']->question; ?></textarea>
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
                <textarea class="form-control" name="question" required><?php echo $data['post']->question; ?></textarea>
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
              <div class="d-flex gap-2">
                <a href="<?php echo URLROOT; ?>/posts/daigram/<?php echo $data['post']->paperID; ?>" class="btn btn-outline-dark" data-bs-toggle="tooltip" data-bs-title="Change diagram">
                  <i class="bi bi-camera"></i>
                </a>
                <input type="submit" name="submit" value="Save Changes" class="btn btn-outline-primary">
                <a href="<?php echo URLROOT; ?>/posts/show/<?php echo $data['params']->paperID; ?>?class=<?= $data['params']->class; ?>&subject=<?= $data['params']->subject; ?>&section_alt=<?= $data['params']->section_alt; ?>" class="btn-outline-dark btn">
                  <i class="bi bi-chevron-left"></i> Back
                </a>
              </div>
            </form>

          </div>
        </div>

      </div>
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
<?php $mathsObj = 1;
if ($mathsObj == '1') : ?>
  <script>
    tinymce.init({
      selector: 'textarea.tiny2',
      height: 180,
      plugins: 'charmap',
      menubar: '',
      // toolbar: 'charmap',
      toolbar: 'dash charmap',
      setup: (editor) => {

        editor.ui.registry.addButton('dash', {
          text: '=',
          onAction: (_) => editor.insertContent(`&nbsp;=&nbsp;`)
        });

      },
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
<?php endif; ?>