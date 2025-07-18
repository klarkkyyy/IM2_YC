document.addEventListener('DOMContentLoaded', function() {
    // Notification dropdown toggle
    const notificationToggle = document.getElementById('notification-toggle');
    if (notificationToggle) {
        notificationToggle.addEventListener('click', function(e) {
            e.preventDefault();
            document.getElementById('notification-dropdown').classList.toggle('show');
        });
    }
    
    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.notification-badge')) {
            const dropdown = document.getElementById('notification-dropdown');
            if (dropdown) dropdown.classList.remove('show');
        }
    });
    
    // Mark notifications as read when clicked
    document.querySelectorAll('.notification-item').forEach(item => {
        item.addEventListener('click', function() {
            if (this.classList.contains('unread')) {
                const updateId = this.dataset.id;
                
                // Send AJAX request to mark as read
                fetch('mark_read.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'update_id=' + updateId
                });
                
                this.classList.remove('unread');
                
                // Update badge count
                const badge = document.querySelector('.notification-count');
                if (badge) {
                    const currentCount = parseInt(badge.textContent);
                    if (currentCount > 1) {
                        badge.textContent = currentCount - 1;
                    } else {
                        badge.remove();
                    }
                }
            }
        });
    });
    
    // Mark all as read
    const markAllRead = document.querySelector('.mark-all-read');
    if (markAllRead) {
        markAllRead.addEventListener('click', function(e) {
            e.stopPropagation();
            fetch('mark_all_read.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'client_id=<?= isset($_SESSION['User_id']) ? $_SESSION['User_id'] : '' ?>'
            }).then(() => {
                document.querySelectorAll('.notification-item.unread').forEach(item => {
                    item.classList.remove('unread');
                });
                document.querySelector('.notification-count')?.remove();
            });
        });
    }
});