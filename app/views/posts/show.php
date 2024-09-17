<?php require APPROOT . '/views/inc/header.php'; ?>
<?php require APPROOT . '/views/inc/navbar.php'; ?>
<?php require APPROOT . '/views/inc/sidebar.php'; ?>
<main id="main" class="main">

  <div class="pagetitle">
    <h1><?php echo $data['params']->subject; ?></h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><?php echo $data['params']->term; ?></li>
        <li class="breadcrumb-item"><?php echo $data['params']->class; ?></li>
        <li class="breadcrumb-item active"><?php echo $data['params']->year; ?></li>
      </ol>
    </nav>
  </div><!-- End Page Title -->
  <section class="row">
    <div class="col-lg-8">
      <h5 class="fw-bold text-primary"><?php echo $data['params']->section; ?></h5>
      <?php if (!empty($data['obj'])) : ?>
        <?php $n = 1;
        foreach ($data['obj'] as $obj) : ?>

          <!-- Accordion without outline borders -->
          <div class="accordion accordion-flush border-bottom" id="accordionFlushExample">
            <div class="accordion-item bg-light px-1">
              <h2 class="accordion-header" id="flush-headingOne">
                <?php if (!empty($obj->img)) : ?>
                  <div class="d-flex justify-content-center">
                    <div class="mt-2 mb-4">
                      <img src="<?php echo URLROOT . '/' . $obj->img; ?>" class="rounded-3" height="90px" alt="daigram">
                    </div>
                  </div>
                <?php endif; ?>
                <button class="lead accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#<?php echo $obj->id; ?>" aria-expanded="false" aria-controls="flush-collapseOne">
                  <i>(<?= $n; ?>)</i>&nbsp;&nbsp;<?php echo $obj->question; ?>
                </button>
              </h2>
              <div id="<?php echo $obj->id; ?>" class="accordion-collapse collapse py-0" aria-labelledby="flush-headingOne" data-bs-parent="#accordionFlushExample">
                <div class="accordion-body row">
                  <p class="col-2 d-lg-none">(A)</p>
                  <div class="col-10 col-lg-3">
                    <p data-bs-toggle="tooltip" data-bs-title="Option A"><?php echo $obj->opt1; ?></p>
                  </div>

                  <p class="col-2 d-lg-none">(B)</p>
                  <div class="col-10 col-lg-3">
                    <p data-bs-toggle="tooltip" data-bs-title="Option B"><?php echo $obj->opt2; ?></p>
                  </div>


                  <?php if (!empty($obj->opt3)) : ?>
                    <p class="col-2 d-lg-none">(C)</p>
                    <div class="col-10 col-lg-3">
                      <p data-bs-toggle="tooltip" data-bs-title="Option C"><?php echo $obj->opt3; ?></p>
                    </div>
                  <?php endif; ?>


                  <?php if (!empty($obj->opt4)) : ?>
                    <p class="col-2 d-lg-none">(D)</p>
                    <div class="col-10 col-lg-3">
                      <p data-bs-toggle="tooltip" data-bs-title="Option D"><?php echo $obj->opt4; ?></p>
                    </div>
                  <?php endif; ?>

                  <div class="d-flex justify-content-center">
                    <a href="<?php echo URLROOT; ?>/posts/edit/<?php echo $obj->id; ?>" class="btn btn-success btn-sm mt-3 rounded-0"><i class="bi bi-pen"></i> Edit</a>
                    <form action="<?php echo URLROOT; ?>/posts/delete/<?php echo $obj->id; ?>" method="POST">
                      <input type="hidden" name="paperID" value="<?= $data['params']->paperID; ?>">
                      <input type="submit" name="" value="Delete" class="btn btn-danger btn-sm mt-3 rounded-0">
                    </form>
                  </div>
                </div>
              </div>
            </div><!-- End Accordion without outline borders -->
          </div>

        <?php $n++;
        endforeach; ?>
        <?php if (!empty($_GET['class'])) : ?>
          <div class="row mt-3">
            <div class="col-12">
              <div class="d-flex gap-3">
                <form action="<?php echo URLROOT; ?>/submissions/set/objectives_questions" method="POST">
                  <input type="hidden" name="question" value="">
                  <input type="hidden" name="opt1" value="">
                  <input type="hidden" name="opt2" value="">
                  <input type="hidden" name="opt3" value="">
                  <input type="hidden" name="opt4" value="">
                  <input type="hidden" name="class" value="<?= $data['class']; ?>">
                  <input type="hidden" name="subject" value="<?= $data['subject']; ?>">
                  <input type="hidden" name="term" value="<?= $data['term']; ?>">
                  <input type="hidden" name="year" value="<?= $data['year']; ?>">
                  <input type="submit" value="Continue" class="btn btn-outline-primary">
                </form>
              </div>
            </div>
          </div>
        <?php else : ?>
          <p class="fw-bold">No Data | No Questions Set
          <form action="<?php echo URLROOT; ?>/submissions/set/objectives_questions" method="POST">
            <input type="hidden" name="question" value="">
            <input type="hidden" name="opt1" value="">
            <input type="hidden" name="opt2" value="">
            <input type="hidden" name="opt3" value="">
            <input type="hidden" name="opt4" value="">
            <input type="hidden" name="class" value="<?= $data['class']; ?>">
            <input type="hidden" name="subject" value="<?= $data['subject']; ?>">
            <input type="hidden" name="term" value="<?= $data['term']; ?>">
            <input type="hidden" name="year" value="<?= $data['year']; ?>">
            <input type="submit" value="Begin Now" class="btn btn-outline-primary">
          </form>
          </p>
        <?php endif; ?>
      <?php endif; ?>
    </div>
  </section>
</main>
<?php require APPROOT . '/views/inc/footer.php'; ?>