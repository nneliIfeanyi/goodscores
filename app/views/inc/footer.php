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
<!-- Page loader fadeout -->
<script>
    $(document).ready(function() {
        $('#loader').delay(300).fadeOut('slow');
        $('#loader-container').delay(300).fadeOut('slow');
    });
</script>

<!-- Page loader fade in on form submit -->
<!-- <script>
    $(':submit').each(function() {
        $(this).click(function() {
            $('#loader').fadeIn();
        });
    });
</script> -->
<script>
    function goTop() {
        window.location.assign('#');
    }
</script>

<script>
    tinymce.init({
        selector: 'textarea',
        // extended_valid_elements: 'mycustominline',
        // custom_elements: '~mycustominline',
        height: 180,
        plugins: 'charmap',
        menubar: '',
        toolbar: 'customInsertButton charmap',
        setup: (editor) => {

            editor.ui.registry.addButton('customInsertButton', {
                text: '__________',
                onAction: (_) => editor.insertContent(`&nbsp;__________&nbsp;`)
            });
        },
        charmap: [
            [0xb0, 'degree sign'],
            [0xb9, 'superscript one'],
            [0xb2, 'superscript two'],
            [0xb3, 'superscript three'],
            [0x221A, 'square root'],
            [0x3C0, 'pi'],
            [0xBD, 'one half'],
            [0xBC, 'one quarter'],
            [0xBE, 'three quarter'],
            [0x2153, 'two third'],
            [0x2154, 'one third'],
            [0x2208, 'element of'],
            [0x220B, 'member'],
            [0x2209, 'not element of'],
            [0x2203, 'there exist'],
            [0x2205, 'empty set'],
            [0x2207, 'nabla'],
            [0x221D, 'proportional to'],
            [0x221E, 'infinity'],
            [0x2220, 'angle'],
            [0x2229, 'intersection'],
            [0x222A, 'union'],
            [0x2264, 'less or equal to'],
            [0x2265, 'greater or equal to'],
            [0x2282, 'subset of'],
            [0x2283, 'superset of'],
            [0x2284, 'not a subset of'],
            [0x2286, 'subset of or equal to'],
            [0x2287, 'superset of or equal to'],
            [0x2260, 'not equal to'],
            [0x222B, 'integral'],
            [0x2211, 'summation'],
            [0x2044, 'fraction slash'],
            [0x83, 'function'],
        ]
    });
</script>
</body>

</html>