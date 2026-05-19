</div>
        </div>
    </div>
    <script src="<?php echo SITE_URL; ?>/assets/js/main.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarToggle = document.querySelector('.sidebar-toggle');
            const adminWrapper = document.querySelector('.admin-wrapper');
            
            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', function() {
                    adminWrapper.classList.toggle('sidebar-collapsed');
                });
            }
        });
    </script>
</body>
</html>