<?php require APPROOT . '/views/inc/header.php'; ?>
<?php require APPROOT . '/views/inc/navbar.php'; ?>
<?php require APPROOT . '/views/inc/sidebar.php'; ?>
<main id="main" class="main">

  <div class="pagetitle">
    <h1><?php echo $data['params']->subject;?></h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><?php echo $data['params']->term;?></li>
        <li class="breadcrumb-item"><?php echo $data['params']->class;?></li>
        <li class="breadcrumb-item active"><?php echo $data['params']->year;?></li>
      </ol>
    </nav>
  </div><!-- End Page Title -->
 
              <h5 class="fw-bold text-primary"><?php echo $data['params']->section;?></h5>
              <?php $n=1;foreach($data['obj'] as $obj):?>

              <!-- Accordion without outline borders -->
              <div class="accordion accordion-flush" id="accordionFlushExample">
                <div class="accordion-item bg-light px-1">
                  <h2 class="accordion-header" id="flush-headingOne">
                    <button class="lead accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#<?php echo $obj->id;?>" aria-expanded="false" aria-controls="flush-collapseOne">
                      <i>(<?= $n;?>)</i>&nbsp;&nbsp;<?php echo $obj->question;?>
                    </button>
                  </h2>
                  <div id="<?php echo $obj->id;?>" class="accordion-collapse collapse" aria-labelledby="flush-headingOne" data-bs-parent="#accordionFlushExample">
                    <div class="accordion-body row">
                      <p class="col-2 d-lg-none">(A)</p>
                      <div class="col-10 col-lg-3">
                        <p data-bs-toggle="tooltip" data-bs-title="Option A"><?php echo $obj->opt1;?></p>
                      </div>

                      <p class="col-2 d-lg-none">(B)</p>
                      <div class="col-10 col-lg-3">
                        <p data-bs-toggle="tooltip" data-bs-title="Option B"><?php echo $obj->opt2;?></p>
                      </div>

                    <div class="d-flex justify-content-center">
                      <a href="<?php echo URLROOT;?>/posts/edit/<?php echo $obj->id;?>" class="btn btn-success btn-sm mt-3 rounded-0"><i class="bi bi-pen"></i> Edit</a>
                      <a href="<?php echo URLROOT;?>/posts/delete/<?php echo $obj->id;?>" class="btn btn-danger btn-sm mt-3 rounded-0"><i class="bi bi-trash"></i> Delete</a>
                    </div>
                  </div>
                </div>
              </div><!-- End Accordion without outline borders -->

            <?php $n++; endforeach;?>
          
<?php require APPROOT . '/views/inc/footer.php'; ?>