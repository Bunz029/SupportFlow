import './bootstrap';
import './notification-poller';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

// Add notification counter update functionality
document.addEventListener('DOMContentLoaded', function() {
    // Ensure Alpine.js is working properly
    if (typeof window.Alpine !== 'undefined') {
        console.log('Alpine.js is loaded from module');
    } else if (typeof Alpine !== 'undefined') {
        // Alpine.js might be loaded from CDN
        window.Alpine = Alpine;
        console.log('Alpine.js is loaded from CDN');
    } else {
        console.error('Alpine.js is not loaded properly. Loading from CDN as fallback...');
        const alpineScript = document.createElement('script');
        alpineScript.defer = true;
        alpineScript.src = 'https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js';
        document.head.appendChild(alpineScript);
    }
    
    // Only run if we have a notification counter on the page
    const notificationCounter = document.getElementById('notification-counter');
    if (notificationCounter) {
        // Function to update notification count
        const updateNotificationCount = async () => {
            try {
                const response = await fetch('/notifications/count');
                const data = await response.json();
                
                if (data.count > 0) {
                    notificationCounter.textContent = data.count;
                    notificationCounter.classList.remove('hidden');
                } else {
                    notificationCounter.textContent = '0';
                    notificationCounter.classList.add('hidden');
                }
            } catch (error) {
                console.error('Error updating notification count:', error);
            }
        };
        
        // Update initially and then every 30 seconds
        updateNotificationCount();
        setInterval(updateNotificationCount, 30000);
    }
});
