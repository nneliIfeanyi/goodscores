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
            <?php if (empty($data['content']->content)) : ?>
              <form action="<?php echo URLROOT; ?>/processing/custom/<?= $data['paperID']; ?>" method="POST">
                <textarea class="form-control" name="content">
                  <?php if ($data['subject'] == 'English' || $data['subject'] == 'English Language' || $data['subject'] == 'English language' || $data['subject'] == 'english language' || $data['subject'] == 'english') : ?>
                    <div style="text-align: center;"><span style="font-size: large;font-weight:700;">Section A Comprehension</span><br/>
                    <span style="font-weight: bold;">Instruction: </span><span>Read the following passage carefully and ...</span><br/>
                    <span><b>--Comprehension Title--</b></span><br/>
                    <p>Continue here..</p>
                  </div><hr/>
                    <?php else : ?>
                      <div style="text-align: center;"><span style="font-size: large;font-weight:700;">Section A</span><br/>
                      <span style="font-weight: bold;">Instruction: </span><span>Answer (X) questions in this section</span></div><hr/>
                    <?php endif; ?>
                  <div>
                    <b>1)</b>&nbsp;<br/>
                    <b>2)</b>&nbsp;<br/>
                    <b>3)</b>&nbsp;<br/>
                    <b>4)</b>&nbsp;<br/>
                    <b>5)</b>&nbsp;<br/>
                  </div><br/>
                  <div style="text-align: center;"><span style="font-size: large;font-weight:700;">Section B</span><br/>
                  <span style="font-weight: bold;">Instruction: </span><span>Answer (X) questions in this section</span></div><hr/>
                   <div>
                    <b>1)</b>&nbsp;<br/>
                    <b>2)</b>&nbsp;<br/>
                    <b>3)</b>&nbsp;<br/>
                    <b>4)</b>&nbsp;<br/>
                    <b>5)</b>&nbsp;<br/>
                  </div><br/>
                  <div style="text-align: center;"><span style="font-size: large;font-weight:700;">Section C</span><br/>
                  <span style="font-weight: bold;">Instruction: </span><span>Answer (X) questions in this section</span></div><hr/>
                  <div>
                    <b>1)</b>&nbsp;<br/>
                    <b>2)</b>&nbsp;<br/>
                    <b>3)</b>&nbsp;<br/>
                    <b>4)</b>&nbsp;<br/>
                    <b>5)</b>&nbsp;<br/>
                  </div><br/>
              </textarea>
                <div class="row">
                  <div class="col-8 mx-auto">
                    <div class="d-grid">
                      <input type="submit" name="submit" value="Save" class="btn btn-outline-primary my-3">
                    </div>
                  </div>
                </div>
              </form>
            <?php else : ?>
              <form action="<?php echo URLROOT; ?>/processing/custom/<?= $data['paperID']; ?>" method="POST">
                <textarea class="form-control" name="content"><?= $data['content']->content; ?></textarea>
                <div class="row">
                  <div class="col-8 mx-auto">
                    <div class="d-grid">
                      <input type="submit" name="submit" value="Save" class="btn btn-outline-primary my-3">
                    </div>
                  </div>
                </div>
              </form>
            <?php endif; ?>
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