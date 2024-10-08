<?php require APPROOT . '/views/inc/header.php'; ?>
<?php require APPROOT . '/views/inc/navbar.php';
?>
<?php require APPROOT . '/views/inc/sidebar.php';
?>

<main id="main" class="main">

  <div class="pagetitle">
    <h1>Set Questions | <?php echo $data['subject']; ?></h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= URLROOT; ?>/users/classes"><?php echo $data['class']; ?></a></li>
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
           
              <!-- <div class="alert alert-primary bg-primary text-light border-0 alert-dismissible fade show" role="alert">
                <strong>__</strong>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div> -->
              <form action="<?php echo URLROOT; ?>/processing/custom/<?= $data['paperID']; ?>" method="POST">
                <textarea class="form-control" name="content"><?= $data['content']->content;?></textarea>
                <div class="row">
                  <div class="col-8 mx-auto">
                    <div class="d-grid">
                      <input type="submit" name="submit" value="SET" class="btn btn-outline-primary">
                    </div>
                  </div>
                </div>
              </form>
          </div>
        </div>

      </div>
    </div>
  </section>

</main><!-- End #main -->

<?php require APPROOT . '/views/inc/footer.php'; ?>
<script>
    tinymce.init({
        selector: 'textarea',
        height: 580,
        //plugins: 'charmap',
       // menubar: '',
        //toolbar: 'dash charmap equa',
    
    });
</script>