<?php
// app/Views/layouts/auth_footer.php
?>
<!-- Bootstrap 5 Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Auto-inject CSRF into forms on auth screens if missing
(function() {
    var csrfToken = "<?php echo $_SESSION['csrf_token'] ?? ''; ?>";
    if (!csrfToken) return;
    document.querySelectorAll('form[method="post"], form[method="POST"]').forEach(function(form) {
        if (!form.querySelector('input[name="csrf_token"]')) {
            var input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'csrf_token';
            input.value = csrfToken;
            form.appendChild(input);
        }
    });
})();
</script>
</body>
</html>
