document.addEventListener('DOMContentLoaded', () => {
    const passwordToggles = document.querySelectorAll(
        '[data-password-toggle]'
    );

    passwordToggles.forEach((toggle) => {
        const container = toggle.closest('.relative');

        if (!container) {
            return;
        }

        const passwordInput = container.querySelector(
            '[data-password-input]'
        );

        if (!passwordInput) {
            return;
        }

        toggle.addEventListener('click', () => {
            const icon = toggle.querySelector('i');

            const isPassword =
                passwordInput.type === 'password';

            passwordInput.type = isPassword
                ? 'text'
                : 'password';

            toggle.setAttribute(
                'aria-label',
                isPassword
                    ? 'Hide password'
                    : 'Show password'
            );

            if (icon) {
                icon.classList.toggle(
                    'fa-eye',
                    !isPassword
                );

                icon.classList.toggle(
                    'fa-eye-slash',
                    isPassword
                );
            }
        });
    });
});