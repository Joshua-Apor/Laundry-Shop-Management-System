document.addEventListener('click', (event) => {
    const closeButton = event.target.closest('[data-close-record-modal]');

    if (closeButton) {
        closeButton.closest('dialog')?.close();

        return;
    }

    if (event.target instanceof HTMLDialogElement && event.target.matches('[data-record-details-modal]')) {
        event.target.close();

        return;
    }

    const button = event.target.closest('[data-record-details-button]');

    if (!button || button.disabled) {
        return;
    }

    const modal = document.getElementById(button.dataset.modalTarget);
    const spinner = button.querySelector('[data-loading-spinner]');
    const text = button.querySelector('[data-loading-text]');

    if (!modal || !(modal instanceof HTMLDialogElement)) {
        return;
    }

    button.disabled = true;
    spinner?.classList.remove('hidden');

    if (text) {
        text.textContent = 'Loading details...';
    }

    window.setTimeout(() => {
        button.disabled = false;
        spinner?.classList.add('hidden');

        if (text) {
            text.textContent = 'View Details';
        }

        modal.showModal();
        modal.querySelector('[data-close-record-modal]')?.focus();
    }, 350);
});
