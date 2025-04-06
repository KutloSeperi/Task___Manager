// Toast notifications with auto-dismiss
const Notifications = (() => {
    const show = (message, type = 'success') => {
        const notification = document.createElement('div');
        notification.className = `notification ${type}`;
        notification.textContent = message;
        document.body.appendChild(notification);

        setTimeout(() => notification.remove(), 3000);
    };

    return { show };
})();