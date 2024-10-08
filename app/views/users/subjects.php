<?php require APPROOT . '/views/inc/header.php'; ?>
<?php require APPROOT . '/views/inc/navbar.php'; ?>
<?php require APPROOT . '/views/inc/sidebar.php'; ?>



<main id="main" class="main">

  <div class="pagetitle">
    <h1>My Subjects</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= URLROOT; ?>/users/dashboard">Home</a></li>
        <li class="breadcrumb-item">Users</li>
        <li class="breadcrumb-item active">Subjects</li>
      </ol>
    </nav>
  </div><!-- End Page Title -->

  <section class="section">
    <div class="row">
      <div class="col-lg-6">
        <div class="card">
          <div class="card-body">

            <div class="alert alert-primary bg-primary text-light border-0 alert-dismissible fade show" role="alert">
              <strong>Add Subjects You Teach</strong>
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <form id="add-subject">

              <div class="my-4">
                <label for="subject">Enter Subject Name</label>
                <input type="text" id="subject" name="subject" required class="form-control form-control-lg" placeholder="" data-parsley-trigger="keyup" />
              </div>

              <div class="d-grid">
                <input type="submit" id="submit" value="Continue" class="btn btn-outline-primary">
              </div>
            </form>

          </div>
        </div>

      </div>

      <div class="col-lg-6">

        <div class="card">
          <div class="card-body">
            <h5 class="card-title">Added Subjects</h5>

            <!-- Dark Table -->
            <table class="table table-striped">
              <thead>
                <tr>
                  <th scope="col">#</th>
                  <th scope="col">Subject</th>
                  <th scope="col">Action</th>
                </tr>
              </thead>
              <tbody>
                <?php if (!empty($data['subjects'])) : ?>
                  <?php $n = 1;
                  foreach ($data['subjects'] as $subject) : ?>
                    <tr>
                      <th scope="row"><?php echo $n; ?></th>
                      <td><?php echo $subject->subject; ?></td>
                      <td>
                        <a class="btn btn-outline-primary" href="<?php echo URLROOT; ?>/users/edit_subject/<?php echo $subject->id; ?>"><i class="bi bi-pen"></i></a>
                        <span class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#subject<?php echo $subject->id; ?>"><i class="bi bi-trash"></i></span>
                      </td>
                    </tr>
                    <!-- Delete Modal -->
                    <div class="modal fade" id="subject<?php echo $subject->id; ?>" tabindex="-1">
                      <div class="modal-dialog">
                        <div class="modal-content">
                          <div class="modal-header">
                            <h5 class="modal-title">Are you sure to delete added subject:<strong class="text-primary"> <?php echo $subject->subject; ?></strong>?</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                          </div>
                          <div class="modal-body">
                            This action cannot be reversed..
                          </div>
                          <div class="modal-footer">
                            <div class="d-flex gap-4">
                              <button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal" aria-label="Close">Cancel</button>
                              <form action="<?php echo URLROOT; ?>/submissions/delete_subject/<?php echo $subject->id; ?>" method="POST">
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
                    <td colspan="3" class="text-danger text-center">No Subject Added!</td>
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
<script>
        $('#add-subject').on('submit', function(event) {
            event.preventDefault();
            $.ajax({
                url: "<?php echo URLROOT; ?>/submissions/add_subject",
                method: "POST",
                data: new FormData(this),
                contentType: false,
                processData: false,
                beforeSend: function() {
                    $('#submit').attr('disabled', 'disabled');
                    $('#submit').val('Processing, Pls Wait ....');

                },
                success: function(data) {
                   $('#submit').attr('disabled', false);
                    $('#submit').val('Continue');
                    $('#ajax-msg').html(data);
                }
            });

        });
    </script>