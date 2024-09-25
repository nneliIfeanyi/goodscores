<?php require APPROOT . '/views/inc/header.php'; ?>
<?php require APPROOT . '/views/inc/navbar.php'; ?>
<?php require APPROOT . '/views/inc/sidebar.php'; ?>

<main id="main" class="main">

  <div class="pagetitle">
    <h1>Edit Question | <?php echo $data['params']->subject; ?></h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="index.html"><?php echo $data['params']->class; ?></a></li>
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
              <strong>theory_questions | <?= $data['post']->questionID; ?></strong>
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <form action="<?php echo URLROOT; ?>/submissions/edit2/<?= $data['post']->id; ?>" method="POST">
                <!-- Accordion without outline borders is overall parent for the question is a child of th e form -->
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
                  <?php else:?>
                    <label style="font-size: x-small;">To append a diagram | click camera icon.</label>

                <?php endif; ?>
                <div class="accordion accordion-flush" id="accordionFlushExample">
                  <div class="accordion-item border-0"><!-- Numbering A Parent div -->
                    <div class="row mb-2">
                      <div class="col-1"> <!-- Numbering A badge -->
                        <p class="badge bg-secondary">A</p>
                      </div>
                      <div class="col-11"><!-- Numbering A input -->
                        <textarea class="form-control" name="question-A" required><?= $data['post']->questionA; ?></textarea>
                        <button class="accordion-button p-0 collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseOne" aria-expanded="false" aria-controls="flush-collapseOne">
                        </button><!-- Accordion toggle button -->
                        <div id="flush-collapseOne" class="accordion-collapse collapse" data-bs-parent="#accordionFlushExample">
                          <div class="accordion-body"><!-- Question A children div begins here -->
                            <div class="row mb-2">
                              <div class="col-1"><!-- Numbering A-i badge -->
                                <p class="badge bg-secondary">i</p>
                              </div>
                              <div class="col-10"><!-- Numbering A-i input parent -->
                                <textarea class="form-control" name="A-i"><?= $data['post']->Ai; ?></textarea>
                              </div>
                            </div>
                            <div class="row mb-2">
                              <div class="col-1"><!-- Numbering A-ii badge -->
                                <p class="badge bg-secondary">ii</p>
                              </div>
                              <div class="col-10"><!-- Numbering A-ii input parent -->
                                <textarea class="form-control" name="A-ii"><?= $data['post']->Aii; ?></textarea>
                              </div>
                            </div>
                            <div class="row mb-2">
                              <div class="col-1"><!-- Numbering A-iii badge -->
                                <p class="badge bg-secondary">iii</p>
                              </div>
                              <div class="col-10"><!-- Numbering A-iii input parent -->
                                <textarea class="form-control" name="A-iii"><?= $data['post']->Aiii; ?></textarea>
                              </div>
                            </div>
                            <div class="row">
                              <div class="col-1"><!-- Numbering A-iv badge -->
                                <p class="badge bg-secondary">iv</p>
                              </div>
                              <div class="col-10"><!-- Numbering A-iv input parent-->
                                <textarea class="form-control" name="A-iv"><?= $data['post']->Aiv; ?></textarea>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div><!-- Numbering A parent div ends -->

                  <div class="accordion-item border-0"><!-- Numbering B parent div -->
                    <div class="row mb-2">
                      <div class="col-1"><!-- Numbering B badge -->
                        <p class="badge bg-secondary">B</p>
                      </div>
                      <div class="col-11"><!-- Numbering B input -->
                        <textarea class="form-control" name="question-B"><?= $data['post']->questionB; ?></textarea>
                        <button class="accordion-button p-0 collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#Collapse_b" aria-expanded="false" aria-controls="flush-collapseOne">
                        </button><!-- Numbering B toggle button -->
                        <div id="Collapse_b" class="accordion-collapse collapse" data-bs-parent="#accordionFlushExample">
                          <div class="accordion-body"><!-- Question B children div begins here -->
                            <div class="row mb-2">
                              <div class="col-1">
                                <p class="badge bg-secondary">i</p>
                              </div>
                              <div class="col-10">
                                <textarea class="form-control" name="B-i"><?= $data['post']->Bi; ?></textarea>
                              </div>
                            </div>
                            <div class="row mb-2">
                              <div class="col-1">
                                <p class="badge bg-secondary">ii</p>
                              </div>
                              <div class="col-10">
                                <textarea class="form-control" name="B-ii"><?= $data['post']->Bii; ?></textarea>
                              </div>
                            </div>
                            <div class="row mb-2">
                              <div class="col-1">
                                <p class="badge bg-secondary">iii</p>
                              </div>
                              <div class="col-10">
                                <textarea class="form-control" name="B-iii"><?= $data['post']->Biii; ?></textarea>
                              </div>
                            </div>
                            <div class="row">
                              <div class="col-1">
                                <p class="badge bg-secondary">iv</p>
                              </div>
                              <div class="col-10">
                                <textarea class="form-control" name="B-iv"><?= $data['post']->Biv; ?></textarea>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div><!-- Question B parent div ends here -->
                  <div class="accordion-item border-0">
                    <div class="row mb-2">
                      <div class="col-1">
                        <p class="badge bg-secondary">C</p>
                      </div>
                      <div class="col-11">
                        <textarea class="form-control" name="question-C"><?= $data['post']->questionC; ?></textarea>
                        <button class="accordion-button p-0 collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#Collapse_c" aria-expanded="false" aria-controls="flush-collapseOne">
                        </button>
                        <div id="Collapse_c" class="accordion-collapse collapse" data-bs-parent="#accordionFlushExample">
                          <div class="accordion-body">
                            <div class="row mb-2">
                              <div class="col-1">
                                <p class="badge bg-secondary">i</p>
                              </div>
                              <div class="col-10">
                                <textarea class="form-control" name="C-i"><?= $data['post']->Ci; ?></textarea>
                              </div>
                            </div>
                            <div class="row mb-2">
                              <div class="col-1">
                                <p class="badge bg-secondary">ii</p>
                              </div>
                              <div class="col-10">
                                <textarea class="form-control" name="C-ii"><?= $data['post']->Cii; ?></textarea>
                              </div>
                            </div>
                            <div class="row mb-2">
                              <div class="col-1">
                                <p class="badge bg-secondary">iii</p>
                              </div>
                              <div class="col-10">
                                <textarea class="form-control" name="C-iii"><?= $data['post']->Ciii; ?></textarea>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="accordion-item border-0"><!-- Question D div begins here have no children -->
                    <div class="row mb-2">
                      <div class="col-1">
                        <p class="badge bg-secondary">D</p>
                      </div>
                      <div class="col-11">
                        <textarea class="form-control" name="question-D"><?= $data['post']->questionD; ?></textarea>
                      </div>
                    </div>
                  </div><!-- Question D div ends here -->
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
      </div>
    </section>
  </main><!-- End #main -->

<?php require APPROOT . '/views/inc/footer.php'; ?>