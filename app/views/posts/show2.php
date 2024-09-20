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

  <section class="section">
    <div class="row">
      <div class="col-lg-8">
        <?php if (!empty($data['theory'])) : ?>
          <div class="card">
            <div class="card-body">
              <h5 class="card-title rounded-3 px-3 text-bg-primary fw-bold">theory_questions</h5>
              <?php $numberin = 1;
              foreach ($data['theory'] as $theory) :
                $pull_each = $this->postModel->pullEach($theory->questionID, $theory->paperID);

              ?>
                <!-- Default Accordion -->
                <div class="accordion" id="parent">
                  <div class="accordion-item">
                    <?php if (!empty($pull_each->img)) : ?>
                      <div class="d-flex justify-content-center">
                        <div class="mt-2 mb-4">
                          <img src="<?php echo URLROOT . '/' . $pull_each->img; ?>" class="rounded-3" height="90px" alt="daigram">
                        </div>
                      </div>
                    <?php endif; ?>
                    <?php if (!empty($pull_each->Ai)) : ?>
                      <h2 class="accordion-header" id="headA">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#body<?= $numberin; ?>a" aria-expanded="true" aria-controls="collapseOne">
                          <strong>(<?= $numberin; ?>)</strong><span class="fs-6">a</span> &nbsp;&nbsp;<?= $pull_each->questionA; ?>
                        </button>
                      </h2>
                    <?php elseif (empty($pull_each->Ai)) : ?>
                      <h2 class="accordion-header" id="headD">
                        <p class="ms-3 pt-2 ps-1 fs-6">
                          <strong>(<?= $numberin; ?>)</strong>a &nbsp;&nbsp;&nbsp;<?= $pull_each->questionA; ?>
                        </p>
                      </h2>
                    <?php endif; ?>
                    <!-- Question A collapse body -->
                    <div id="body<?= $numberin; ?>a" class="accordion-collapse collapse" data-bs-parent="#parent">
                      <div class="ms-2 px-2">
                        <?php if (!empty($pull_each->Ai)) : ?>
                          <div class="accordion-body">
                            i)&nbsp;&nbsp;&nbsp;<?= $pull_each->Ai; ?>
                          </div>
                        <?php endif; ?>
                        <?php if (!empty($pull_each->Aii)) : ?>
                          <div class="accordion-body">
                            ii)&nbsp;&nbsp;&nbsp;<?= $pull_each->Aii; ?>
                          </div>
                        <?php endif; ?>
                        <?php if (!empty($pull_each->Aiii)) : ?>
                          <div class="accordion-body">
                            iii)&nbsp;&nbsp;&nbsp;<?= $pull_each->Aiii; ?>
                          </div>
                        <?php endif; ?>
                        <?php if (!empty($pull_each->Aiv)) : ?>
                          <div class="accordion-body">
                            iv)&nbsp;&nbsp;&nbsp;<?= $pull_each->Aiv; ?>
                          </div>
                        <?php endif; ?>
                      </div>
                    </div>
                    <!-- Question B Display -->
                    <?php if (!empty($pull_each->questionB) && !empty($pull_each->Bi)) : ?>
                      <h2 class="accordion-header" id="headB">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#body<?= $numberin; ?>b" aria-expanded="true" aria-controls="collapseOne">
                          <strong>(<?= $numberin; ?>)</strong><span class="fs-6">b</span> &nbsp;&nbsp;&nbsp;<?= $pull_each->questionB; ?>
                        </button>
                      </h2>
                    <?php elseif (!empty($pull_each->questionB) && empty($pull_each->Bi)) : ?>
                      <h2 class="accordion-header" id="headD">
                        <p class="ms-3 pt-2 ps-1 fs-6">
                          <strong>(<?= $numberin; ?>)</strong>b &nbsp;&nbsp;&nbsp;<?= $pull_each->questionB; ?>
                        </p>
                      </h2>
                    <?php endif; ?>
                    <!-- B collapse -->
                    <div id="body<?= $numberin; ?>b" class="accordion-collapse collapse" data-bs-parent="#parent">
                      <div class="ms-2 px-2">
                        <?php if (!empty($pull_each->Bi)) : ?>
                          <div class="accordion-body">
                            i)&nbsp;&nbsp;&nbsp;<?= $pull_each->Bi; ?>
                          </div>
                        <?php endif; ?>
                        <?php if (!empty($pull_each->Bii)) : ?>
                          <div class="accordion-body">
                            ii)&nbsp;&nbsp;&nbsp;<?= $pull_each->Bii; ?>
                          </div>
                        <?php endif; ?>
                        <?php if (!empty($pull_each->Biii)) : ?>
                          <div class="accordion-body">
                            iii)&nbsp;&nbsp;&nbsp;<?= $pull_each->Biii; ?>
                          </div>
                        <?php endif; ?>
                        <?php if (!empty($pull_each->Biv)) : ?>
                          <div class="accordion-body">
                            iv)&nbsp;&nbsp;&nbsp;<?= $pull_each->Biv; ?>
                          </div>
                        <?php endif; ?>
                      </div>
                    </div>
                    <!-- Question C Display -->
                    <?php if (!empty($pull_each->questionC) && !empty($pull_each->Ci)) : ?>
                      <h2 class="accordion-header" id="headC">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#body<?= $numberin; ?>c" aria-expanded="true" aria-controls="collapseOne">
                          <strong>(<?= $numberin; ?>)</strong><span class="fs-6">c</span> &nbsp;&nbsp;&nbsp;<?= $pull_each->questionC; ?>
                        </button>
                      </h2>
                    <?php elseif (!empty($pull_each->questionC) && empty($pull_each->Ci)) : ?>
                      <h2 class="accordion-header" id="headD">
                        <p class="ms-3 ps-1 pt-2 fs-6">
                          <strong>(<?= $numberin; ?>)</strong>c &nbsp;&nbsp;&nbsp;<?= $pull_each->questionC; ?>
                        </p>
                      </h2>
                    <?php endif; ?>
                    <!-- Question C collapse -->
                    <div id="body<?= $numberin; ?>c" class="accordion-collapse collapse" data-bs-parent="#parent">
                      <div class="ms-2 px-2">
                        <?php if (!empty($pull_each->Ci)) : ?>
                          <div class="accordion-body">
                            i)&nbsp;&nbsp;&nbsp;<?= $pull_each->Ci; ?>
                          </div>
                        <?php endif; ?>
                        <?php if (!empty($pull_each->Cii)) : ?>
                          <div class="accordion-body">
                            ii)&nbsp;&nbsp;&nbsp;<?= $pull_each->Cii; ?>
                          </div>
                        <?php endif; ?>
                        <?php if (!empty($pull_each->Ciii)) : ?>
                          <div class="accordion-body">
                            iii)&nbsp;&nbsp;&nbsp;<?= $pull_each->Ciii; ?>
                          </div>
                        <?php endif; ?>
                      </div>
                    </div>

                    <!-- Question D Display -->
                    <?php if (!empty($pull_each->questionD)) : ?>
                      <h2 class="accordion-header" id="headD">
                        <p class="ms-3 ps-1 pt-2 fs-6">
                          <strong>(<?= $numberin; ?>)</strong>d &nbsp;&nbsp;&nbsp;<?= $pull_each->questionD; ?>
                        </p>
                      </h2>
                    <?php endif; ?>
                    <!-- End Question D Display -->
                    <div class="d-flex justify-content-center mb-2">
                      <a href="<?php echo URLROOT; ?>/posts/edit2/<?php echo $pull_each->id; ?>" class="btn btn-success btn-sm mt-3 rounded-0"><i class="bi bi-pen"></i> Edit</a>
                      <form action="<?php echo URLROOT; ?>/posts/delete2/<?php echo $pull_each->id; ?>" method="POST">
                        <input type="hidden" name="paperID" value="<?= $data['params']->paperID; ?>">
                        <input type="hidden" name="class" value="<?= $data['params']->class; ?>">
                        <input type="hidden" name="subject" value="<?= $data['params']->subject; ?>">
                        <input type="submit" name="" value="Delete" class="btn btn-danger btn-sm mt-3 rounded-0">
                      </form>
                    </div>
                  </div>
                </div><!-- End Default Accordion Example -->
              <?php $numberin++;
              endforeach; ?>
              <div class="row mt-3">
                <div class="col-12">
                  <div class="d-flex gap-3">
                    <form action="<?php echo URLROOT; ?>/submissions/set/theory_questions" method="POST">
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
            </div>
          </div><!-- End card div -->
        <?php else : ?>
          <p class="fw-bold">No Data | No Questions Set
          <form action="<?php echo URLROOT; ?>/submissions/set/theory_questions" method="POST">
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
      </div><!-- End col-lg-8 div -->
    </div>
  </section>
</main>

<?php require APPROOT . '/views/inc/footer.php'; ?>