async function loadDashboardResults(container) {
    container.setAttribute('aria-busy', 'true');

    try {
        const response = await fetch(container.dataset.dashboardEndpoint, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                Accept: 'text/html',
            },
            credentials: 'same-origin',
        });

        if (!response.ok || response.redirected) {
            throw new Error('Dashboard request failed.');
        }

        const documentContent = new DOMParser().parseFromString(await response.text(), 'text/html');
        const fragment = documentContent.querySelector('[data-dashboard-fragment]');

        if (!fragment) {
            throw new Error('Dashboard response was incomplete.');
        }

        container.innerHTML = fragment.innerHTML;
        container.removeAttribute('aria-busy');
    } catch {
        container.removeAttribute('aria-busy');
        container.innerHTML = `
            <div class="rounded-xl border border-red-200 bg-red-50 p-5 text-center" role="alert">
                <p class="text-sm font-semibold text-red-700">Dashboard data could not be loaded.</p>
                <button type="button" data-dashboard-retry class="mt-3 rounded-lg bg-[#168cff] px-4 py-2 text-sm font-semibold text-white hover:bg-[#0878df]">
                    Try again
                </button>
            </div>
        `;
    }
}

document.addEventListener('click', (event) => {
    const retryButton = event.target.closest('[data-dashboard-retry]');

    if (retryButton) {
        const container = retryButton.closest('[data-dashboard-results]');

        if (container) {
            loadDashboardResults(container);
        }

        return;
    }

    const suggestionList = document.querySelector('[data-customer-suggestions]');
    const customerOption = event.target.closest('[data-customer-option]');

    if (customerOption) {
        document.getElementById('customer-name').value = customerOption.dataset.name;
        document.getElementById('customer-phone').value = customerOption.dataset.phone;
        document.getElementById('customer-address').value = customerOption.dataset.address;
        document.getElementById('customer-id').value = customerOption.dataset.customerId;
        customerOption.closest('dialog')?.close();
        suggestionList?.classList.add('hidden');
        document.getElementById('customer-name').setAttribute('aria-expanded', 'false');

        return;
    }

    if (!event.target.closest('#customer-name, [data-customer-suggestions]')) {
        suggestionList?.classList.add('hidden');
        document.getElementById('customer-name')?.setAttribute('aria-expanded', 'false');
    }

    const closeButton = event.target.closest('[data-close-record-modal], [data-modal-close]');

    if (closeButton) {
        closeButton.closest('dialog')?.close();

        return;
    }

    if (event.target instanceof HTMLDialogElement && event.target.matches('[data-record-details-modal], [data-modal-dialog]')) {
        event.target.close();

        return;
    }

    const button = event.target.closest('[data-record-details-button], [data-modal-trigger]');

    if (!button || button.disabled) {
        return;
    }

    const modal = document.getElementById(button.dataset.modalTarget);

    if (!modal || !(modal instanceof HTMLDialogElement)) {
        return;
    }

    modal.showModal();
    modal.querySelector('[data-close-record-modal], [data-modal-close]')?.focus();
});

document.querySelectorAll('[data-dashboard-results]').forEach((container) => {
    loadDashboardResults(container);
});

document.addEventListener('input', (event) => {
    if (event.target.matches('#customer-name, #customer-phone')) {
        document.getElementById('customer-id').value = '';
    }

    if (event.target.matches('#customer-name')) {
        const search = event.target.value.trim().toLocaleLowerCase();
        const suggestionList = document.querySelector('[data-customer-suggestions]');
        const customers = document.querySelectorAll('#customer-picker [data-customer-option]');

        suggestionList.replaceChildren();

        if (search.length === 0) {
            suggestionList.classList.add('hidden');
            event.target.setAttribute('aria-expanded', 'false');

            return;
        }

        let matchCount = 0;

        customers.forEach((customer) => {
            if (!customer.dataset.name.toLocaleLowerCase().includes(search) || matchCount >= 8) {
                return;
            }

            const option = document.createElement('button');
            option.type = 'button';
            option.setAttribute('role', 'option');
            option.dataset.customerOption = '';
            option.dataset.customerId = customer.dataset.customerId;
            option.dataset.name = customer.dataset.name;
            option.dataset.phone = customer.dataset.phone;
            option.dataset.address = customer.dataset.address;
            option.className = 'block w-full rounded-md px-3 py-2 text-left transition-colors hover:bg-sky-50 focus-visible:bg-sky-50 focus-visible:outline-none';

            const name = document.createElement('span');
            name.className = 'block truncate text-sm font-semibold text-slate-800';
            name.textContent = customer.dataset.name;

            const phone = document.createElement('span');
            phone.className = 'mt-0.5 block text-xs text-slate-500';
            phone.textContent = customer.dataset.phone;

            option.append(name, phone);
            suggestionList.append(option);
            matchCount += 1;
        });

        suggestionList.classList.toggle('hidden', matchCount === 0);
        event.target.setAttribute('aria-expanded', matchCount > 0 ? 'true' : 'false');

        return;
    }

    if (!event.target.matches('[data-customer-search]')) {
        return;
    }

    const search = event.target.value.trim().toLocaleLowerCase();
    const options = event.target.closest('dialog').querySelectorAll('[data-customer-option]');
    let visibleOptions = 0;

    options.forEach((option) => {
        const matches = `${option.dataset.name} ${option.dataset.phone}`.toLocaleLowerCase().includes(search);
        option.classList.toggle('hidden', !matches);

        if (matches) {
            visibleOptions += 1;
        }
    });

    event.target.closest('dialog').querySelector('[data-customer-empty]')?.classList.toggle('hidden', visibleOptions > 0);
});
