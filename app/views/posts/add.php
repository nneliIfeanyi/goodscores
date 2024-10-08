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
                <strong>objectives_questions</strong>
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
                <?php if ($this->postModel->getParamsByPaperID($data['paperID'], 'theory_questions')) : ?>
                  <a class="btn btn-outline-primary" href="<?php echo URLROOT; ?>/posts/add2/<?= $data['paperID']; ?>">Go to Theory Questions <i class="bi bi-chevron-right"></i></a>
                <?php else : ?>
                  <a class="btn btn-outline-primary" href="<?php echo URLROOT; ?>/users/set/theory_questions">Go to Theory Questions <i class="bi bi-chevron-right"></i></a>
                <?php endif; ?>
              </div>
              <div class="mt-3">
                <a href="<?php echo URLROOT; ?>/users/dashboard">Go to Dashboard <i class="bi bi-chevron-right"></i></a>
              </div>
            <?php
            } else {
              if (empty($data['num_rows'])) {
                $data['num_rows'] = 1;
              } else {
                $data['num_rows'] = $data['num_rows'] + 1;
              }
            ?>
              <div class="alert alert-primary bg-primary text-light border-0 alert-dismissible fade show" role="alert">
                <strong>objectives_questions</strong>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>
              <form action="<?php echo URLROOT; ?>/processing/add/<?= $data['paperID']; ?>" method="POST">
                <input type="hidden" name="class" value="<?php echo $data['class']; ?>">
                <input type="hidden" name="subject" value="<?php echo $data['subject']; ?>">
                <input type="hidden" name="term" value="<?php echo $data['term']; ?>">
                <input type="hidden" name="section" value="objectives_questions">
                <input type="hidden" name="paperID" value="<?php echo $data['paperID']; ?>">
                <input type="hidden" name="year" value="<?php echo $data['year']; ?>">

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
                <textarea class="form-control" name="question" required><p><?php echo $data['num_rows'] . ' of ' . $data['total_subject_num_rows']; ?></p></textarea>
                <div class="row my-3">
                  <p class="col-2 d-lg-none">(A)</p>
                  <div class="col-10 col-lg-3">
                    <input type="text" class="form-control form-control-lg" placeholder="Option A" required name="opt1">
                  </div>
                  <p class="col-2 d-lg-none">(B)</p>
                  <div class="col-10 col-lg-3">
                    <input type="text" class="form-control form-control-lg" placeholder="Option B" required name="opt2">
                  </div>
                  <p class="col-2 d-lg-none">(C)</p>
                  <div class="col-10 col-lg-3">
                    <input type="text" class="form-control form-control-lg" placeholder="Option C" name="opt3">
                  </div>
                  <p class="col-2 d-lg-none">(D)</p>
                  <div class="col-10 col-lg-3">
                    <input type="text" class="form-control form-control-lg" placeholder="Option D" name="opt4">
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
                      <input type="submit" name="submit" value="SET" class="btn btn-outline-primary">
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
        selector: 'textarea',
        height: 180,
        plugins: '',
        menubar: '',
        toolbar: 'dash',
        setup: (editor) => {

            editor.ui.registry.addButton('dash', {
                text: '__________',
                onAction: (_) => editor.insertContent(`&nbsp;__________&nbsp;`)
            });

        },
  });
      </script>
<!-- <script>
    tinymce.init({
        selector: 'textarea',
        extended_valid_elements: 'mycustominline',
        custom_elements: '~mycustominline',
        height: 180,
        plugins: 'charmap',
        menubar: '',
        toolbar: 'dash charmap equa',
        setup: (editor) => {

            editor.ui.registry.addButton('dash', {
                text: '__________',
                onAction: (_) => editor.insertContent(`&nbsp;__________&nbsp;`)
            });

            editor.ui.registry.addButton('equa', {
                text: '=',
                onAction: (_) => editor.insertContent(`&nbsp;=&nbsp;`)
            });
        },
        
        charmap: [
            [0xb0, 'degree sign'],
            [0xb9, 'superscript one'],
            [0xb2, 'superscript two'],
            [0xb3, 'superscript three'],
            [0x221A, 'square root'],
            [0x3C0, 'pi'],
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
</script> -->