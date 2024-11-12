<?php require APPROOT . '/views/inc/header.php'; ?>
<?php require APPROOT . '/views/inc/navbar.php'; ?>
<?php require APPROOT . '/views/inc/sidebar.php'; ?>
<main id="main" class="main">

  <div class="pagetitle">
    <h1><?php echo $data['params']->subject; ?></h1>
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
      <div class="col-lg-8">
        <?php if (!empty($data['theory'])) : ?>
          <div class="card">
            <div class="card-body">
              <h5 class="card-title rounded-3 px-3 text-bg-primary fw-bold">theory_questions</h5>
              <?php $numberin = 1;
              foreach ($data['theory'] as $theory) :
                //$pull_each = $this->postModel->pullEach($theory->questionID, $theory->paperID);

              ?>

                <?php if (!empty($theory->img)) : ?>
                  <div class="d-flex justify-content-center">
                    <div class="mt-2 mb-4">
                      <img src="<?php echo URLROOT . '/' . $theory->img; ?>" class="rounded-3" height="90px" alt="daigram">
                    </div>
                  </div>
                <?php endif; ?>

                <div class="d-flex">
                  <p class="">(<?= $numberin; ?>)a</p>&nbsp;&nbsp;<?= $theory->questionA; ?>
                </div>
                <?php if (!empty($theory->questionB)) : ?>
                  <div class="d-flex">
                    <p class="">(<?= $numberin; ?>)b</p>&nbsp;&nbsp;<?= $theory->questionB; ?>
                  </div>
                <?php endif; ?>
                <?php if (!empty($theory->questionC)) : ?>
                  <div class="d-flex">
                    <p class="">(<?= $numberin; ?>)c</p>&nbsp;&nbsp;<?= $theory->questionC; ?>
                  </div>
                <?php endif; ?>
                <?php if (!empty($theory->questionD)) : ?>
                  <div class="d-flex">
                    <p class="">(<?= $numberin; ?>)d</p>&nbsp;&nbsp;<?= $theory->questionD; ?>
                  </div>
                <?php endif; ?>
                <div class="d-flex justify-content-center mb-2">
                  <a href="<?php echo URLROOT; ?>/posts/edit2/<?php echo $theory->id; ?>" class="btn btn-success btn-sm mt-3 rounded-0"><i class="bi bi-pen"></i> Edit</a>
                  <form action="<?php echo URLROOT; ?>/posts/delete2/<?php echo $theory->id; ?>" method="POST">
                    <input type="hidden" name="paperID" value="<?= $data['params']->paperID; ?>">
                    <input type="hidden" name="class" value="<?= $data['params']->class; ?>">
                    <input type="hidden" name="subject" value="<?= $data['params']->subject; ?>">
                    <input type="submit" name="" value="Delete" class="btn btn-danger btn-sm mt-3 rounded-0">
                  </form>
                </div>
              <?php $numberin++;
              endforeach; ?>
            </div>
          </div>
          <div class="row mt-3">
            <div class="col-12">
              <div class="d-flex gap-1 flex-wrap">
                <a href="<?= URLROOT; ?>/posts/add2/<?= $data['params']->paperID; ?>" class="btn btn-outline-primary">Continue <i class="bi bi-chevron-right"></i></a>

                <?php if ($_SESSION['role'] == 'Admin') : ?>
                  <a href="<?= URLROOT; ?>/output/print/<?= $data['params']->paperID; ?>" class="btn btn-outline-secondary">Print <i class="bi bi-printer"></i></a>
                  <a href="<?= URLROOT; ?>/output/pdf/<?= $data['params']->paperID; ?>" class="btn btn-outline-success">Download <i class="bi bi-download"></i></a>
                <?php endif; ?>
                <a href="javascript:void()" onclick="history.back()" class="btn"><i class="bi bi-chevron-left"></i> Go Back</a>
              </div>
            </div>
          </div><!-- End card div -->
        <?php else : ?>
          <p class="fw-bold">No Data | No Questions Set<br>
            <a href="<?= URLROOT; ?>/posts/add2/<?= $data['params']->paperID; ?>" class="btn btn-outline-primary">Begin now</a>
          </p>
        <?php endif; ?>
      </div><!-- End col-lg-8 div -->
    </div>
  </section>
</main>

<?php require APPROOT . '/views/inc/footer.php'; ?>
<script>
  $(':submit').each(function() {
    $(this).click(function() {
      $('#loader').fadeIn();
    });
  });
</script>