<?php
/**
 * Admin Footer Template
 * Regashi Printing Website
 */
?>

<!-- Bootstrap Bundle with Popper -->
<script src="<?php echo SITE_URL; ?>/assets/js/bootstrap.bundle.min.js"></script>

<!-- Custom Scripts -->
<script src="<?php echo SITE_URL; ?>/assets/js/admin.js"></script>

<?php if (isset($extra_js)): ?>
    <?php echo $extra_js; ?>
<?php endif; ?>

</body>
</html>
