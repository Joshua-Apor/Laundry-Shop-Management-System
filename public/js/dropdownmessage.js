function initializeDropdownMessages(root = document) {
    root.querySelectorAll('[data-dropdown-message]').forEach((message) => {
        if (message.dataset.dropdownMessageInitialized === 'true') {
            return;
        }

        message.dataset.dropdownMessageInitialized = 'true';

        const duration = Number.parseInt(message.dataset.dropdownMessageDuration, 10);
        const dismiss = () => {
            message.classList.add('pointer-events-none', '-translate-y-2', 'opacity-0');

            window.setTimeout(() => {
                message.classList.add('invisible');
            }, 300);
        };

        message.classList.remove('invisible', 'pointer-events-none', '-translate-y-2', 'opacity-0');

        const timeout = window.setTimeout(dismiss, Number.isFinite(duration) && duration > 0 ? duration : 5000);

        message.querySelector('[data-dropdown-message-dismiss]')?.addEventListener('click', () => {
            window.clearTimeout(timeout);
            dismiss();
        });
    });
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => initializeDropdownMessages(), { once: true });
} else {
    initializeDropdownMessages();
}

window.initializeDropdownMessages = initializeDropdownMessages;