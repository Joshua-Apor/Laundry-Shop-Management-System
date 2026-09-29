function initializeLoadingButtons() {
    const buttons = document.querySelectorAll('[data-loading-button]');

    buttons.forEach((button) => {

        if (button.dataset.loadingInitialized === 'true') {
            return;
        }

        button.dataset.loadingInitialized = 'true';

        const form = button.closest('form');

        if (!form) {
            return;
        }

        form.addEventListener('submit', () => {

            if (button.disabled) {
                return;
            }

            button.disabled = true;

            let spinner = button.querySelector('[data-loading-spinner]');

            if (!spinner) {
                spinner = document.createElement('span');

                spinner.setAttribute('data-loading-spinner', '');

                spinner.className =
                    'h-4 w-4 animate-spin rounded-full border-2 border-white/40 border-t-white';

                button.prepend(spinner);
            }

            spinner.classList.remove('hidden');

            const text = button.querySelector('[data-loading-text]');

            if (text) {
                if (!text.dataset.originalText) {
                    text.dataset.originalText = text.textContent;
                }

                // Use custom loading message if provided
                text.textContent =
                    button.dataset.loadingMessage || 'Loading...';
            }
        });
    });
}

document.addEventListener('DOMContentLoaded', initializeLoadingButtons);
window.initializeLoadingButtons = initializeLoadingButtons;