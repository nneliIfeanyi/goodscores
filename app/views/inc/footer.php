<label onclick="goTop()" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></label>
<!-- JQUERY JS File -->
<script src="<?php echo URLROOT; ?>/assets/js/jquery.js"></script>

<!-- Parsley JS File -->
<script src="<?php echo URLROOT; ?>/assets/js/parsley.min.js"></script>
<!-- Template Main JS File -->
<script src="<?php echo URLROOT; ?>/assets/js/main.js"></script>

<script src="<?php echo URLROOT; ?>/app.js"></script>

<!-- Vendor JS Files -->
<script src="<?php echo URLROOT; ?>/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="<?php echo URLROOT; ?>/assets/vendor/simple-datatables/simple-datatables.js"></script>
<script src="<?php echo URLROOT; ?>/assets/tinymce/tinymce.min.js"></script>
<script>
    $('form').parsley();
</script>
<!-- Page loader fadeout -->
<script>
    $(document).ready(function() {
        $('#loader').fadeOut();
    });
</script>
<!-- Page loader fade in on link click -->
<script>
    $(document).ready(function() {
        $('a').each(function() {
            $(this).click(function() {
                $('#loader').fadeIn();
            });
        });
    });
</script>
<!-- Page loader fade in on form submit -->
<script>
    $(document).ready(function() {
        $(':submit').each(function() {
            if ($('form').parsley().isValid()) {
                $(this).click(function() {
                    $('#loader').fadeIn();
                });
            }
        });
    });
</script>
<script>
    function goTop() {
        window.location.assign('#');
    }
</script>
</body>

</html>