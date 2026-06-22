

<!-- Bootstrap 5 Bundle with Popper JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Select all alert notification banners on the page
    const alertElements = document.querySelectorAll('.alert');
    
    alertElements.forEach(function(alert) {
        // Automatically trigger dismissal after 5 seconds
        setTimeout(function() {
            if (typeof bootstrap !== 'undefined' && bootstrap.Alert) {
                // Use Bootstrap's native transition effects to safely close the alert
                const bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
                if (bsAlert) {
                    bsAlert.close();
                }
            } else {
                // Minimalist visual fallback if Bootstrap is not completely loaded
                alert.style.transition = 'opacity 0.5s ease-out';
                alert.style.opacity = '0';
                setTimeout(function() {
                    alert.remove();
                }, 500);
            }
        }, 2000); // 5000ms = 5 seconds
    });
});
</script>
</body>
</html>