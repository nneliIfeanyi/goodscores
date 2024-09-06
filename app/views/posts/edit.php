<?php require APPROOT . '/views/inc/header.php'; ?>
<?php require APPROOT . '/views/inc/navbar.php'; ?>
<?php require APPROOT . '/views/inc/sidebar.php';?>

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
              <strong><?php echo $data['params']->section; ?></strong>
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <form action="<?php echo URLROOT; ?>/posts/edit/<?php echo $data['post']->id; ?>" method="POST">
              <input type="hidden" name="paperID" value="<?php echo $data['post']->paperID; ?>">
              <textarea class="form-control" name="question" required placeholder="Reset Question"><?php echo $data['post']->question; ?></textarea>
              <div class="row my-3">
                <div class="col-6 col-md-3">
                  <input type="text" class="form-control" value="<?php echo $data['post']->opt1; ?>" placeholder="Opt-A" required name="opt1">
                </div>
                <div class="col-6 col-md-3">
                  <input type="text" class="form-control" value="<?php echo $data['post']->opt2; ?>" placeholder="Opt-B" required name="opt2">
                </div>
                <div class="col-6 col-md-3">
                  <input type="text" class="form-control" value="<?php echo $data['post']->opt3; ?>" placeholder="Opt-C" name="opt3">
                </div>
                <div class="col-6 col-md-3">
                  <input type="text" class="form-control" value="<?php echo $data['post']->opt4; ?>" placeholder="Opt-D" name="opt4">
                </div>
              </div>
              <div class="d-grid">
                <input type="submit" name="submit" value="Save Changes" class="btn btn-outline-primary">
              </div>
            </form>

          </div>
        </div>

      </div>
    </div>
  </section>

</main><!-- End #main -->

<?php require APPROOT . '/views/inc/footer.php'; ?>



