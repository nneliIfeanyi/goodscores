<?php require APPROOT . '/views/inc/header.php'; ?>
<?php require APPROOT . '/views/inc/navbar.php'; ?>
<?php require APPROOT . '/views/inc/sidebar.php'; ?>



<main id="main" class="main">

  <div class="pagetitle">
    <h1>My Classes</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="index.html">Home</a></li>
        <li class="breadcrumb-item">Users</li>
        <li class="breadcrumb-item active">Classes</li>
      </ol>
    </nav>
  </div><!-- End Page Title -->

  <section class="section">
    <div class="row">
      <div class="col-lg-6">

        <div class="card">
          <div class="card-body">

            <div class="alert alert-primary bg-primary text-light border-0 alert-dismissible fade show" role="alert">
              <strong>Add New Class</strong>
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <form action="<?php echo URLROOT; ?>/submissions/add_class" method="POST">

              <div class="my-4">
                <label for="className">Class Name</label>
                <input type="text" id="className" name="classname" required class="form-control form-control-lg" placeholder="" data-parsley-trigger="keyup" />
              </div>
              <div class="my-4">
                <label for="className">Number of objective questions</label>
                <input type="number" id="className" name="obj_num_rows" required class="form-control form-control-lg" placeholder="" data-parsley-trigger="keyup" />
              </div>

              <div class="my-4">
                <label for="className">Number of theory questions</label>
                <input type="number" id="className" name="theory_num_rows" required class="form-control form-control-lg" placeholder="" data-parsley-trigger="keyup" />
              </div>

              <div class="d-grid">
                <input type="submit" name="set" value="Continue" class="btn btn-outline-primary">
              </div>
            </form>

          </div>
        </div>

      </div>

      <div class="col-lg-6">

        <div class="card">
          <div class="card-body">
            <h5 class="card-title">Added Classes</h5>

            <!-- Dark Table -->
            <table class="table table-striped">
              <thead>
                <tr>
                  <th scope="col">#</th>
                  <th scope="col">Class</th>
                  <th scope="col">Action</th>
                </tr>
              </thead>
              <tbody>
                <?php if (!empty($data['classes'])) : ?>
                  <?php $n = 1;
                  foreach ($data['classes'] as $class) : ?>
                    <tr>
                      <th scope="row"><?php echo $n; ?></th>
                      <td><?php echo $class->classname; ?></td>
                      <td>
                        <a href="javascript:void" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#class<?php echo $class->id; ?>"><i class="bi bi-trash"></i></a>
                      </td>
                    </tr>
                    <!-- Delete Modal -->
                    <div class="modal fade" id="class<?php echo $class->id; ?>" tabindex="-1">
                      <div class="modal-dialog">
                        <div class="modal-content">
                          <div class="modal-header">
                            <h5 class="modal-title">Are you sure to delete added class:<strong class="text-primary"> <?php echo $class->classname; ?></strong>?</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                          </div>
                          <div class="modal-body">
                            This action cannot be reversed..
                          </div>
                          <div class="modal-footer">
                            <div class="d-flex gap-4">
                              <button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal" aria-label="Close">Cancel</button>
                              <form action="<?php echo URLROOT; ?>/submissions/delete_class/<?php echo $class->id; ?>" method="POST">
                              <input class="btn btn-danger" type="submit" name="submit" value="Yes Continue">
                              </form>
                            </div>
                          </div>

                        </div>
                      </div>
                    </div><!-- End Delete Modal-->
                  <?php $n++;
                  endforeach; ?>
                <?php else : ?>
                  <tr>
                    <td colspan="3" class="text-danger text-center">No Class Added!</td>
                  </tr>
                <?php endif; ?>

              </tbody>
            </table>
            <!-- End Table -->

          </div>
        </div>

      </div>
    </div>
  </section>

</main><!-- End #main -->


<?php require APPROOT . '/views/inc/footer.php'; ?>