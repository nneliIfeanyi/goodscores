<?php require APPROOT . '/views/inc/header.php'; ?>
<?php require APPROOT . '/views/inc/navbar.php'; ?>
<?php require APPROOT . '/views/inc/sidebar.php';
?>

<main id="main" class="main">

  <div class="pagetitle">
    <h1>Set Questions | <?php echo $data['subject']; ?></h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="index.html"><?php echo $data['class']; ?></a></li>
        <li class="breadcrumb-item"><?php echo $data['term']; ?></li>
        <li class="breadcrumb-item active"><?php echo $data['year']; ?></li>
      </ol>
    </nav>
  </div><!-- End Page Title -->

  <section class="section">
    <div class="row">
      <div class="col-md-10 col-lg-8">

        <div class="card">
          <div class="card-body">
            <?php
              if ($_SESSION['num_rows'] == $_SESSION['total_subject_num_rows']) {
                ?>
            <div class="alert alert-primary bg-primary text-light border-0 alert-dismissible fade show" role="alert">
              <strong><?php echo $data['section']; ?></strong>
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
              <textarea class="form-control" disabled name="question" required placeholder="COMPLETED"></textarea>
              <div class="row my-3">
                <div class="col-6 col-md-3">
                  <input type="text" disabled class="form-control" placeholder="Opt-A" required name="opt1">
                </div>
                <div class="col-6 col-md-3">
                  <input type="text" disabled class="form-control" placeholder="Opt-B" required name="opt2">
                </div>
                <div class="col-6 col-md-3">
                  <input type="text" disabled class="form-control" placeholder="Opt-C" name="opt3">
                </div>
                <div class="col-6 col-md-3">
                  <input type="text" disabled class="form-control" placeholder="Opt-D" name="opt4">
                </div>
              </div>
              <div class="d-grid">
                <a href="<?php echo URLROOT; ?>/submissions/set/theory_questions" class="btn btn-outline-primary">Goto Theory Questions</a>
              </div>
            </form>
            <?php 
              }else{

            ?>
            <div class="alert alert-primary bg-primary text-light border-0 alert-dismissible fade show" role="alert">
              <strong><?php echo $data['section']; ?></strong>
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <form action="<?php echo URLROOT; ?>/posts/add2" method="POST">
              <input type="hidden" name="class" value="<?php echo $_SESSION['class']; ?>">
              <input type="hidden" name="subject" value="<?php echo $_SESSION['subject']; ?>">
              <input type="hidden" name="term" value="<?php echo $_SESSION['term']; ?>">
              <input type="hidden" name="section" value="<?php echo $_SESSION['section']; ?>">
              <input type="hidden" name="paperID" value="<?php echo $_SESSION['paperID']; ?>">
              <textarea class="tinymce-editor" name="question" required></textarea>
              
              <div class="d-grid">
                <input type="submit" name="submit" value="SET" class="btn btn-outline-primary">
              </div>
            </form>
             <?php 
              }

            ?>
          </div>
        </div>

      </div>
    </div>
  </section>

</main><!-- End #main -->

<?php require APPROOT . '/views/inc/footer.php'; ?>
<!-- <script>
  tinymce.init({
    selector: 'textarea', // change this value according to your HTML
    plugins: 'a_tinymce_plugin',
    a_plugin_option: true,
    a_configuration_option: 400
  });
</script> -->

<script>
  tinymce.init({
    selector: 'textarea.tinymce-editor', // change this value according to your HTML
    plugins: 'charmap table',
    menubar: 'insert edit format table',
    toolbar: "undo redo",
    // a_plugin_option: true,
    // a_configuration_option: 400
    //inline: true,
    hidden_input: true
  });
</script>