<?php require APPROOT . '/views/inc/header.php';
require APPROOT . '/views/inc/navbar.php';
require APPROOT . '/views/inc/sidebar.php';
?>

<main id="main" class="main">
  <?php if ($_SESSION['role'] != 'Admin') : ?>
    <div class="pagetitle">
      <h1>Dashboard</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="<?= URLROOT; ?>/users/dashboard">Home</a></li>
          <li class="breadcrumb-item">Dashboard</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->
  <?php else : ?>
    <div class="pagetitle">
      <h1>Dashboard</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="<?= URLROOT; ?>/users/dashboard">Home</a></li>
          <li class="breadcrumb-item">Admin</li>
          <li class="breadcrumb-item">Dashboard</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->
  <?php endif; ?>

  <section class="section dashboard">
    <div class="row">

      <!-- Left side columns -->
      <div class="col-lg-10">
        <div class="row">
          <?php if ($_SESSION['role'] != 'Admin') : ?>
            <!-- Subjects Card -->
            <div class="col-xxl-4 col-md-6">
              <div class="card info-card sales-card">

                <div class="filter">
                  <label class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></label>
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
                      <a href="<?php echo URLROOT; ?>/users/subjects">
                        <i class="bi bi-journal-text"></i>
                      </a>
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
                  <label class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></label>
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
                      <a href="<?php echo URLROOT; ?>/users/classes"> <i class="bi bi-people"></i></a>
                    </div>
                    <div class="ps-3">
                      <h6><?php echo $data['classes']; ?></h6>
                      <!-- <span class="text-success small pt-1 fw-bold">12%</span> <span class="text-muted small pt-2 ps-1">increase</span> -->

                    </div>
                  </div>
                </div>

              </div>
            </div><!-- End Classes Card -->
            <!-- Recent Activity -->
            <div class="col-12">
              <div class="card top-selling overflow-auto">

                <div class="filter">
                  <label class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></label>
                  <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                    <li class="dropdown-header text-start">
                      <h6>New Activity</h6>
                    </li>

                    <li><a class="dropdown-item" href="<?php echo URLROOT; ?>/users/set/objectives_questions">Set Obj</a></li>
                    <li><a class="dropdown-item" href="<?php echo URLROOT; ?>/users/set/theory_questions">Set Theory</a></li>
                  </ul>
                </div>

                <div class="card-body pb-0">
                  <h5 class="card-title">Recent | Activities</h5>

                  <table class="table table-borderless">
                    <thead>
                      <tr>

                        <th scope="col">Subject</th>
                        <th scope="col">Section</th>
                        <th scope="col">Class</th>
                        <th scope="col">Term</th>
                        <th scope="col">View</th>
                      </tr>
                    </thead>
                    <tbody>

                      <?php
                      if (!empty($data['recent'])) :
                        foreach ($data['recent'] as $recent) : ?>
                          <tr>
                            <td><?php echo $recent->subject; ?></td>
                            <td class="text-primary">
                              <?php echo $recent->section; ?>
                            </td>
                            <td><?php echo $recent->class; ?></td>
                            <td><?php echo $recent->term; ?></td>
                            <td scope="row" class="d-flex gap-2">
                              <?php if ($recent->section == 'objectives_questions') : ?>
                                <a class="btn btn-sm btn-outline-primary" href="<?php echo URLROOT; ?>/posts/add/<?php echo $recent->paperID; ?>">
                                  <i class="bi bi-chevron-right"></i>
                                </a>
                                <a class="btn btn-sm btn-outline-primary" href="<?php echo URLROOT; ?>/users/review_params?paperID=<?= $recent->paperID; ?>&section=<?= $recent->section; ?>">
                                  <i class="bi bi-pen"></i>
                                </a>
                              <?php elseif ($recent->section == 'theory_questions') : ?>
                                <a class="btn btn-sm btn-outline-primary" href="<?php echo URLROOT; ?>/posts/add2/<?php echo $recent->paperID; ?>">
                                  <i class="bi bi-chevron-right"></i>
                                </a>
                                <a class="btn btn-sm btn-outline-primary" href="<?php echo URLROOT; ?>/users/review_params?paperID=<?= $recent->paperID; ?>&section=<?= $recent->section; ?>">
                                  <i class="bi bi-pen"></i>
                                </a>
                              <?php elseif ($recent->section == 'custom') : ?>

                                <a class="btn btn-sm btn-outline-primary" href="<?php echo URLROOT; ?>/posts/custom/<?php echo $recent->paperID; ?>">
                                  <i class="bi bi-chevron-right"></i>
                                </a>
                                <a class="btn btn-sm btn-outline-primary" href="<?php echo URLROOT; ?>/users/review_params?paperID=<?= $recent->paperID; ?>&section=<?= $recent->section; ?>">
                                  <i class="bi bi-pen"></i>
                                </a>
                              <?php endif; ?>
                            </td>
                          </tr>

                        <?php endforeach;
                      else : ?>
                        <tr>
                          <td class="text-danger">No data set</td>
                        </tr>
                      <?php endif; ?>
                    </tbody>
                  </table>

                </div>

              </div>
            </div><!-- End Recent Activity -->
          <?php endif; ?>
          <?php if ($_SESSION['role'] == 'Admin') : ?>

            <!-- Archive Table -->
            <div class="col-12">
              <div class="card top-selling overflow-auto">
                <div class="card-body pb-0">
                  <h5 class="card-title">Archives | Output</h5>
                  <ul class="nav nav-tabs nav-tabs-bordered">

                    <?php foreach ($data['class'] as $teachers) : ?>
                      <li class="nav-item">
                        <button type="button" class="nav-link" data-bs-toggle="tab" data-bs-target="#clasz<?= $teachers->id ?>"><?= $teachers->username ?></button>
                      </li>
                    <?php endforeach; ?>
                  </ul><!-- Tab Links ul Ends -->
                  <div class="tab-content pt-2">
                    <?php $n = 1;
                    foreach ($data['class'] as $teachers) : ?>

                      <div class="tab-pane fade" id="clasz<?= $teachers->id ?>">
                        <table class="table table-borderless">
                          <thead>
                            <tr>
                              <th>S|N</th>
                              <th scope="col">Subject</th>
                              <th scope="col">Section</th>
                              <th scope="col">Class</th>
                              <th scope="col">Term</th>
                              <th scope="col">Status</th>
                              <th scope="col">Action</th>
                            </tr>
                          </thead>
                          <tbody>

                            <?php
                            if (!empty($archive = $this->postModel->getArchive($teachers->id))) :
                              foreach ($archive as $recent) :
                                $obj_num_rows = $this->postModel->checkObjectivesNumRows($recent->paperID, $_COOKIE['sch_id']);
                                $theory_num_rows = $this->postModel->checkTheoryNumRows($recent->paperID, $_COOKIE['sch_id']);
                            ?>
                                <tr>
                                  <td><?= $n; ?></td>
                                  <td><?php echo $recent->subject; ?></td>
                                  <td class="text-primary">
                                    <?php echo $recent->section; ?>
                                  </td>
                                  <td><?php echo $recent->class; ?></td>
                                  <td><?php echo $recent->term; ?></td>
                                  <!-- Document status cell -->
                                  <?php if ($recent->section == 'objectives_questions') : ?>
                                    <?php if ($recent->num_rows > $obj_num_rows) : ?>

                                      <td><span class="badge bg-warning">Pending</span></td>
                                      <td scope="row" class="d-flex gap-2">
                                        <!-- <a href="<?php echo URLROOT; ?>/posts/show/<?= $recent->paperID; ?>?class=<?= $recent->class; ?>&subject=<?= $recent->subject; ?>" class="btn btn-sm btn-outline-dark" data-bs-toggle="tooltip" data-bs-title="Preview">
                                          <i class="bi bi-eye"></i>
                                        </a> -->
                                        <a class="btn btn-sm btn-outline-primary" href="<?php echo URLROOT; ?>/output/print/<?php echo $recent->paperID; ?>">
                                          <i class="bi bi-printer"></i>
                                        </a>
                                      </td>
                                    <?php else : ?>
                                      <td><span class="badge bg-success">Completed</span></td>
                                      <td scope="row" class="d-flex gap-2">
                                        <!-- <a href="<?php echo URLROOT; ?>/posts/show/<?= $recent->paperID; ?>?class=<?= $recent->class; ?>&subject=<?= $recent->subject; ?>" class="btn btn-sm btn-outline-dark" data-bs-toggle="tooltip" data-bs-title="Preview">
                                          <i class="bi bi-eye"></i>
                                        </a> -->
                                        <a class="btn btn-sm btn-outline-success" href="<?php echo URLROOT; ?>/output/print/<?php echo $recent->paperID; ?>">
                                          <i class="bi bi-printer"></i>
                                        </a>
                                      </td>
                                    <?php endif; ?>
                                  <?php elseif ($recent->section == 'theory_questions') : ?>
                                    <?php if ($recent->num_rows > $theory_num_rows) : ?>
                                      <td><span class="badge bg-warning">Pending</span></td>
                                      <td scope="row" class="d-flex gap-2">
                                        <!-- <a href="<?php echo URLROOT; ?>/posts/show2/<?= $recent->paperID; ?>?class=<?= $recent->class; ?>&subject=<?= $recent->subject; ?>" class="btn btn-sm btn-outline-dark" data-bs-toggle="tooltip" data-bs-title="Preview">
                                          <i class="bi bi-eye"></i>
                                        </a> -->
                                        <a class="btn btn-sm btn-outline-primary" href="<?php echo URLROOT; ?>/output/print/<?php echo $recent->paperID; ?>">
                                          <i class="bi bi-printer"></i>
                                        </a>
                                      </td>
                                    <?php else : ?>
                                      <td><span class="badge bg-success">Completed</span></td>
                                      <td scope="row" class="d-flex gap-2">
                                        <!-- <a href="<?php echo URLROOT; ?>/posts/show2/<?= $recent->paperID; ?>?class=<?= $recent->class; ?>&subject=<?= $recent->subject; ?>" class="btn btn-sm btn-outline-dark" data-bs-toggle="tooltip" data-bs-title="Preview">
                                          <i class="bi bi-eye"></i>
                                        </a> -->
                                        <a class="btn btn-sm btn-outline-success" href="<?php echo URLROOT; ?>/output/print/<?php echo $recent->paperID; ?>">
                                          <i class="bi bi-printer"></i>
                                        </a>
                                      </td>
                                    <?php endif; ?>
                                  <?php endif; ?>
                                  <!-- Document status cell ends -->
                                </tr>

                              <?php $n++;
                              endforeach; ?>
                            <?php else : ?>
                              <tr>
                                <td class="text-danger">No data set</td>
                              </tr>
                            <?php endif; ?>
                          </tbody>
                        </table>
                      </div>
                    <?php endforeach; ?>
                  </div>
                </div>

              </div>
            </div><!-- End Archive -->
          <?php endif; ?>
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