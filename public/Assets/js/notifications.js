/**
 * Beautiful Success Notification System
 * Creates an animated, modern success alert with icon and auto-dismiss
 */

// Wait for DOM to be fully loaded
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initNotifications);
} else {
    initNotifications();
}

function initNotifications() {
    'use strict';

    // Create notification container if it doesn't exist
    function createNotificationContainer() {
        if (!document.getElementById('customNotificationContainer')) {
            const container = document.createElement('div');
            container.id = 'customNotificationContainer';
            container.style.cssText = 'position: fixed; top: 20px; right: 20px; z-index: 9999; display: flex; flex-direction: column; gap: 10px; pointer-events: none;';
            document.body.appendChild(container);
        }
    }

    // Show beautiful success notification
    window.showSuccessPopup = function (message) {
        createNotificationContainer();

        const notification = document.createElement('div');
        notification.className = 'custom-success-notification';
        notification.style.cssText = 'background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; padding: 20px 25px; border-radius: 12px; box-shadow: 0 10px 40px rgba(16, 185, 129, 0.3), 0 0 0 1px rgba(255, 255, 255, 0.1); display: flex; align-items: center; gap: 15px; min-width: 320px; max-width: 450px; pointer-events: auto; animation: slideInRight 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55); position: relative; overflow: hidden;';

        notification.innerHTML = '<div style="width: 48px; height: 48px; background: rgba(255, 255, 255, 0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0; animation: scaleIn 0.5s cubic-bezier(0.68, -0.55, 0.265, 1.55) 0.2s both;"><i class="bi bi-check-circle-fill" style="font-size: 28px; color: white;"></i></div><div style="flex: 1;"><div style="font-weight: 600; font-size: 16px; margin-bottom: 4px;">Berhasil!</div><div style="font-size: 14px; opacity: 0.95;">' + message + '</div></div><button onclick="this.parentElement.remove()" style="background: rgba(255, 255, 255, 0.2); border: none; color: white; width: 28px; height: 28px; border-radius: 50%; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.2s; flex-shrink: 0;" onmouseover="this.style.background=\'rgba(255,255,255,0.3)\'" onmouseout="this.style.background=\'rgba(255,255,255,0.2)\'"><i class="bi bi-x" style="font-size: 18px;"></i></button><div style="position: absolute; bottom: 0; left: 0; height: 4px; background: rgba(255, 255, 255, 0.3); width: 100%; animation: progressBar 4s linear;"></div>';

        document.getElementById('customNotificationContainer').appendChild(notification);

        // Auto remove after 4 seconds
        setTimeout(function () {
            notification.style.animation = 'slideOutRight 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55)';
            setTimeout(function () {
                if (notification.parentElement) {
                    notification.remove();
                }
            }, 400);
        }, 4000);
    };

    // Show error notification
    window.showErrorPopup = function (message) {
        createNotificationContainer();

        const notification = document.createElement('div');
        notification.className = 'custom-error-notification';
        notification.style.cssText = 'background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); color: white; padding: 20px 25px; border-radius: 12px; box-shadow: 0 10px 40px rgba(239, 68, 68, 0.3), 0 0 0 1px rgba(255, 255, 255, 0.1); display: flex; align-items: center; gap: 15px; min-width: 320px; max-width: 450px; pointer-events: auto; animation: slideInRight 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55); position: relative; overflow: hidden;';

        notification.innerHTML = '<div style="width: 48px; height: 48px; background: rgba(255, 255, 255, 0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0; animation: scaleIn 0.5s cubic-bezier(0.68, -0.55, 0.265, 1.55) 0.2s both;"><i class="bi bi-exclamation-circle-fill" style="font-size: 28px; color: white;"></i></div><div style="flex: 1;"><div style="font-weight: 600; font-size: 16px; margin-bottom: 4px;">Error!</div><div style="font-size: 14px; opacity: 0.95;">' + message + '</div></div><button onclick="this.parentElement.remove()" style="background: rgba(255, 255, 255, 0.2); border: none; color: white; width: 28px; height: 28px; border-radius: 50%; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.2s; flex-shrink: 0;" onmouseover="this.style.background=\'rgba(255,255,255,0.3)\'" onmouseout="this.style.background=\'rgba(255,255,255,0.2)\'"><i class="bi bi-x" style="font-size: 18px;"></i></button><div style="position: absolute; bottom: 0; left: 0; height: 4px; background: rgba(255, 255, 255, 0.3); width: 100%; animation: progressBar 4s linear;"></div>';

        document.getElementById('customNotificationContainer').appendChild(notification);

        // Auto remove after 4 seconds
        setTimeout(function () {
            notification.style.animation = 'slideOutRight 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55)';
            setTimeout(function () {
                if (notification.parentElement) {
                    notification.remove();
                }
            }, 400);
        }, 4000);
    };

    // Add CSS animations
    if (!document.getElementById('customNotificationStyles')) {
        const style = document.createElement('style');
        style.id = 'customNotificationStyles';
        style.textContent = '@keyframes slideInRight { from { transform: translateX(400px); opacity: 0; } to { transform: translateX(0); opacity: 1; } } @keyframes slideOutRight { from { transform: translateX(0); opacity: 1; } to { transform: translateX(400px); opacity: 0; } } @keyframes scaleIn { from { transform: scale(0); } to { transform: scale(1); } } @keyframes progressBar { from { width: 100%; } to { width: 0%; } } .custom-success-notification:hover, .custom-error-notification:hover { transform: translateY(-2px); box-shadow: 0 15px 50px rgba(16, 185, 129, 0.4), 0 0 0 1px rgba(255, 255, 255, 0.1); } .custom-error-notification:hover { box-shadow: 0 15px 50px rgba(239, 68, 68, 0.4), 0 0 0 1px rgba(255, 255, 255, 0.1); }';
        document.head.appendChild(style);
    }

    console.log('Custom notification system loaded successfully');
}
