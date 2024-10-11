<?php require APPROOT . '/views/inc/header.php'; ?>
<?php require APPROOT . '/views/inc/navbar.php'; ?>
<?php require APPROOT . '/views/inc/sidebar.php'; ?>



<main id="main" class="main">

    <div class="pagetitle">
        <h1><?= $data['subject']->subject; ?> </h1>
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
                            <strong>Edit Subject</strong>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        <form>

                            <div class="my-4">
                                <label for="className">Subject</label>
                                <input type="text" id="className" name="subject" required class="form-control form-control-lg" value="<?= $data['subject']->subject; ?>" data-parsley-trigger=" keyup" />
                            </div>
                            <div class="d-flex gap-4">
                                <input type="submit" id="submit" value="Save changes" class="btn btn-primary">
                                <button type="button" onclick="history.back()" class="btn btn-outline-primary"><i class="bi bi-chevron-left"></i> Go back</button>
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
        $('form').on('submit', function(event) {
            event.preventDefault();
            $.ajax({
                url: "<?php echo URLROOT; ?>/submissions/edit_subject/<?= $data['subject']->id; ?>",
                method: "POST",
                data: new FormData(this),
                contentType: false,
                processData: false,
                beforeSend: function() {
                    $('#submit').attr('disabled', 'disabled');
                    $('#submit').val('Processing, Pls Wait ....');

                },
                success: function(data) {
                    $('#ajax-msg').html(data);
                }
            });

        });
    </script>