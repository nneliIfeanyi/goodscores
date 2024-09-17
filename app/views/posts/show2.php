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

        <div class="card">
          <div class="card-body">
            <h5 class="card-title">Default Accordion</h5>
            <?php $numberin = 1;
            foreach ($data['theory'] as $theory) :
              $pull_each = $this->postModel->pullEach($theory->questionID, $theory->paperID);

            ?>
              <!-- Default Accordion -->
              <div class="accordion" id="parent">
                <div class="accordion-item">
                  <?php if (!empty($pull_each->Ai)) : ?>
                    <h2 class="accordion-header" id="headA">
                      <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#body<?= $numberin; ?>a" aria-expanded="true" aria-controls="collapseOne">
                        <strong>(<?= $numberin; ?>)</strong><span class="fs-6">a</span> &nbsp;&nbsp;<?= $pull_each->questionA; ?>
                      </button>
                    </h2>
                  <?php elseif (empty($pull_each->Ai)) : ?>
                    <h2 class="accordion-header" id="headD">
                      <p class="ms-3 pt-2 fs-6">
                        <strong>(<?= $numberin; ?>)</strong>a &nbsp;&nbsp;&nbsp;<?= $pull_each->questionA; ?>
                      </p>
                    </h2>
                  <?php endif; ?>
                  <!-- Question collapse body -->
                  <div id="body<?= $numberin; ?>a" class="accordion-collapse collapse" data-bs-parent="#parent">
                    <?php if (!empty($pull_each->Ai)) : ?>
                      <div class="accordion-body">
                        <strong>(i)</strong>&nbsp;&nbsp;&nbsp;<?= $pull_each->Ai; ?>
                      </div>
                    <?php endif; ?>
                    <?php if (!empty($pull_each->Aii)) : ?>
                      <div class="accordion-body">
                        <strong>(ii)</strong>&nbsp;&nbsp;&nbsp;<?= $pull_each->Aii; ?>
                      </div>
                    <?php endif; ?>
                    <?php if (!empty($pull_each->Aiii)) : ?>
                      <div class="accordion-body">
                        <strong>(iii)</strong>&nbsp;&nbsp;&nbsp;<?= $pull_each->Aiii; ?>
                      </div>
                    <?php endif; ?>
                    <?php if (!empty($pull_each->Aiv)) : ?>
                      <div class="accordion-body">
                        <strong>(iv)</strong>&nbsp;&nbsp;&nbsp;<?= $pull_each->Aiv; ?>
                      </div>
                    <?php endif; ?>
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
                      <p class="ms-3 pt-2 fs-6">
                        <strong>(<?= $numberin; ?>)</strong>b &nbsp;&nbsp;&nbsp;<?= $pull_each->questionB; ?>
                      </p>
                    </h2>
                  <?php endif; ?>
                  <!-- B collapse -->
                  <div id="body<?= $numberin; ?>b" class="accordion-collapse collapse" data-bs-parent="#parent">
                    <?php if (!empty($pull_each->Bi)) : ?>
                      <div class="accordion-body">
                        <strong>(i)</strong>&nbsp;&nbsp;&nbsp;<?= $pull_each->Bi; ?>
                      </div>
                    <?php endif; ?>
                    <?php if (!empty($pull_each->Bii)) : ?>
                      <div class="accordion-body">
                        <strong>(ii)</strong>&nbsp;&nbsp;&nbsp;<?= $pull_each->Bii; ?>
                      </div>
                    <?php endif; ?>
                    <?php if (!empty($pull_each->Biii)) : ?>
                      <div class="accordion-body">
                        <strong>(iii)</strong>&nbsp;&nbsp;&nbsp;<?= $pull_each->Biii; ?>
                      </div>
                    <?php endif; ?>
                    <?php if (!empty($pull_each->Biv)) : ?>
                      <div class="accordion-body">
                        <strong>(iv)</strong>&nbsp;&nbsp;&nbsp;<?= $pull_each->Biv; ?>
                      </div>
                    <?php endif; ?>
                  </div>
                  <?php if (!empty($pull_each->questionC) && !empty($pull_each->Ci)) : ?>
                    <!-- Question C Display -->
                    <h2 class="accordion-header" id="headC">
                      <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#body<?= $numberin; ?>c" aria-expanded="true" aria-controls="collapseOne">
                        <strong>(<?= $numberin; ?>)</strong><span class="fs-6">c</span> &nbsp;&nbsp;&nbsp;<?= $pull_each->questionC; ?>
                      </button>
                    </h2>
                  <?php elseif (!empty($pull_each->questionC) && empty($pull_each->Ci)) : ?>
                    <h2 class="accordion-header" id="headD">
                      <p class="ms-3 pt-2 fs-6">
                        <strong>(<?= $numberin; ?>)</strong>c &nbsp;&nbsp;&nbsp;<?= $pull_each->questionC; ?>
                      </p>
                    </h2>
                  <?php endif; ?>
                  <div id="body<?= $numberin; ?>c" class="accordion-collapse collapse" data-bs-parent="#parent">
                    <?php if (!empty($pull_each->Ci)) : ?>
                      <div class="accordion-body">
                        <strong>(i)</strong>&nbsp;&nbsp;&nbsp;<?= $pull_each->Ci; ?>
                      </div>
                    <?php endif; ?>
                    <?php if (!empty($pull_each->Cii)) : ?>
                      <div class="accordion-body">
                        <strong>(ii)</strong>&nbsp;&nbsp;&nbsp;<?= $pull_each->Cii; ?>
                      </div>
                    <?php endif; ?>
                    <?php if (!empty($pull_each->Ciii)) : ?>
                      <div class="accordion-body">
                        <strong>(iii)</strong>&nbsp;&nbsp;&nbsp;<?= $pull_each->Ciii; ?>
                      </div>
                    <?php endif; ?>
                  </div>

                  <!-- Question D Display -->
                  <?php if (!empty($pull_each->questionD)) : ?>
                    <h2 class="accordion-header" id="headD">
                      <p class="ms-3 pt-2 fs-6">
                        <strong>(<?= $numberin; ?>)</strong>d &nbsp;&nbsp;&nbsp;<?= $pull_each->questionD; ?>
                      </p>
                    </h2>
                  <?php endif; ?>
                  <!-- End Question D Display -->

                </div>
              </div><!-- End Default Accordion Example -->
            <?php $numberin++;
            endforeach; ?>
          </div>
        </div>
      </div>
    </div>
  </section>
</main>

<?php require APPROOT . '/views/inc/footer.php'; ?>