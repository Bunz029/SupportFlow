/**
 * Notification poller - Periodically checks for new notifications
 */
document.addEventListener('DOMContentLoaded', function() {
    // Only run on pages with the notification bell
    const notificationCounter = document.getElementById('notification-counter');
    if (!notificationCounter) return;

    // Function to update notification count
    const updateNotificationCount = () => {
        fetch('/notifications/count')
            .then(response => response.json())
            .then(data => {
                // Update notification counter
                notificationCounter.textContent = data.count;
                
                // Show/hide counter based on count
                if (data.count > 0) {
                    notificationCounter.classList.remove('hidden');
                } else {
                    notificationCounter.classList.add('hidden');
                }
            })
            .catch(error => console.error('Error fetching notifications:', error));
    };

    // Check for new notifications every 60 seconds
    setInterval(updateNotificationCount, 60000);
    
    // Also check on page load
    updateNotificationCount();
}); 