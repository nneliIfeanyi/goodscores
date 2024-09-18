<?php require APPROOT . '/views/inc/header.php'; ?>
<?php require APPROOT . '/views/inc/navbar.php'; ?>
<?php require APPROOT . '/views/inc/sidebar.php'; ?>



<main id="main" class="main">

  <div class="pagetitle">
    <h1>Dashboard</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="index.html">Home</a></li>
        <li class="breadcrumb-item">Dashboard</li>
      </ol>
    </nav>
  </div><!-- End Page Title -->

  <section class="section dashboard">
    <div class="row">

      <!-- Left side columns -->
      <div class="col-lg-10">
        <div class="row">

          <!-- Subjects Card -->
          <div class="col-xxl-4 col-md-6">
            <div class="card info-card sales-card">

              <div class="filter">
                <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                  <li class="dropdown-header text-start">
                    <h6>Go to</h6>
                  </li>

                  <li><a class="dropdown-item" href="<?php echo URLROOT; ?>/users/subjects">My Subjects</a></li>
                </ul>
              </div>

              <div class="card-body">
                <h5 class="card-title">Total <span>| Subjects</span></h5>

                <div class="d-flex align-items-center">
                  <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                    <i class="bi bi-journal-text"></i>
                  </div>
                  <div class="ps-3">
                    <h6><?php echo $data['subjects']; ?></h6>
                    <!-- <span class="ext-success small pt-1 fw-bold">12%</span> <span class="text-muted small pt-2 ps-1">increase</span> -->

                  </div>
                </div>
              </div>

            </div>
          </div><!-- End Subjects Card -->

          <!-- Classes Card -->
          <div class="col-xxl-4 col-md-6">
            <div class="card info-card sales-card">

              <div class="filter">
                <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                  <li class="dropdown-header text-start">
                    <h6>Go to</h6>
                  </li>

                  <li><a class="dropdown-item" href="<?php echo URLROOT; ?>/users/classes">My Classes</a></li>
                </ul>
              </div>

              <div class="card-body">
                <h5 class="card-title">Total <span>| Classes</span></h5>

                <div class="d-flex align-items-center">
                  <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                    <i class="bi bi-people"></i>
                  </div>
                  <div class="ps-3">
                    <h6><?php echo $data['classes']; ?></h6>
                    <!-- <span class="text-success small pt-1 fw-bold">12%</span> <span class="text-muted small pt-2 ps-1">increase</span> -->

                  </div>
                </div>
              </div>

            </div>
          </div><!-- End Classes Card -->


          <!-- All Activities Card -->
          <!-- <div class="col-xxl-4 col-md-6">
            <div class="card info-card sales-card">

              <div class="filter">
                <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                  <li class="dropdown-header text-start">
                    <h6>New Activity</h6>
                  </li>

                  <li><a class="dropdown-item" href="<?php echo URLROOT; ?>/users/set">Set Questions</a></li>
                </ul>
              </div>

              <div class="card-body">
                <h5 class="card-title">Total <span>| Activities</span></h5>

                <div class="d-flex align-items-center">
                  <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                    <i class="bi bi-pen"></i>
                  </div>
                  <div class="ps-3">
                    <h6><?php echo $data['all']; ?></h6>
                    <span class="text-success small pt-1 fw-bold">For current</span> <span class="text-muted small pt-2 ps-1">school session</span>

                  </div>
                </div>
              </div>

            </div>
          </div> --><!-- End All Activities Card -->



          <!-- Recent Activity -->
          <div class="col-12">
            <div class="card top-selling overflow-auto">

              <div class="filter">
                <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                  <li class="dropdown-header text-start">
                    <h6>New Activity</h6>
                  </li>

                  <li><a class="dropdown-item" href="<?php echo URLROOT; ?>/users/set/objectives_questions">Set Obj</a></li>
                  <li><a class="dropdown-item" href="<?php echo URLROOT; ?>/users/set/theory_questions">Set Theory</a></li>
                </ul>
              </div>

              <div class="card-body pb-0">
                <h5 class="card-title">All <span>| Activities</span></h5>

                <table class="table table-borderless">
                  <thead>
                    <tr>

                      <th scope="col">Subject</th>
                      <th scope="col">Section</th>
                      <th scope="col">Class</th>
                      <th scope="col">Term</th>
                      <th scope="col">Year</th>
                      <th scope="col">Status</th>
                      <th scope="col">Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($data['recent'] as $recent) :
                      $status = $this->postModel->checkSubjectNumRows($recent->class, $_SESSION['user_id'], $_COOKIE['sch_id']);
                      $obj_num_rows = $this->postModel->checkObjectivesNumRows($recent->paperID, $_COOKIE['sch_id']);
                      $theory_num_rows = $this->postModel->checkTheoryNumRows($recent->paperID, $_COOKIE['sch_id']);
                    ?>
                      <tr>
                        <td><?php echo $recent->subject; ?></td>
                        <td class="text-primary"><?php echo $recent->section; ?></td>
                        <td><?php echo $recent->class; ?></td>
                        <td><?php echo $recent->term; ?></td>
                        <td><?php echo $recent->year; ?></td>
                        <?php if ($recent->section == 'objectives_questions') : ?>
                          <?php if ($obj_num_rows != $status->num_rows) : ?>
                            <td><span class="badge bg-warning">Pending</span></td>
                          <?php else : ?>
                            <td><span class="badge bg-success">Completed</span></td>
                          <?php endif; ?>
                          <td scope="row">
                            <a href="<?php echo URLROOT; ?>/posts/show/<?php echo $recent->paperID; ?>?class=<?= $recent->class; ?>&subject=<?= $recent->subject; ?>">
                              <i class="bi bi-eye fs-3"></i>
                            </a>
                          </td>
                        <?php elseif ($recent->section == 'theory_questions') : ?>
                          <?php if ($theory_num_rows != $status->num_rows2) : ?>
                            <td><span class="badge bg-warning">Pending</span></td>
                          <?php else : ?>
                            <td><span class="badge bg-success">Completed</span></td>
                          <?php endif; ?>
                          <td scope="row">
                            <a href="<?php echo URLROOT; ?>/posts/show2/<?php echo $recent->paperID; ?>?class=<?= $recent->class; ?>&subject=<?= $recent->subject; ?>">
                              <i class="bi bi-eye fs-3"></i>
                            </a>
                          </td>
                        <?php endif; ?>
                      </tr>

                    <?php endforeach; ?>
                  </tbody>
                </table>

              </div>

            </div>
          </div><!-- End Recent Activity -->




        </div>
      </div>
    </div>
  </section>

</main><!-- End #main -->



<?php require APPROOT . '/views/inc/footer.php'; ?>































<!-- Delete Modal -->
<div class="modal fade" id="deleteSection<?php echo $recent->paperID; ?>" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">This action wil delete both <span class="fw-bold text-primary"><?php echo $recent->section; ?> and theory_questions for </span> <?php echo $recent->class; ?> <?php echo $recent->term; ?></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        This action is dangerous and cannot be reversed..
      </div>
      <div class="modal-footer">
        <div class="d-flex gap-4">
          <button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal" aria-label="Close">Cancel</button>
          <form action="<?php echo URLROOT; ?>/posts/delete_obj_all/<?= $recent->paperID; ?>" method="POST">
            <input class="btn btn-danger" type="submit" name="submit" value="Yes Continue">
          </form>
        </div>
      </div>

    </div>
  </div>
</div><!-- End Delete Modal-->