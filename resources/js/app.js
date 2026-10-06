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

    if (button.matches('[data-modal-loading-trigger]')) {
        const spinner = button.querySelector('[data-loading-spinner]');

        button.disabled = true;
        button.setAttribute('aria-busy', 'true');
        spinner?.classList.remove('hidden');

        window.setTimeout(() => {
            button.disabled = false;
            button.removeAttribute('aria-busy');
            spinner?.classList.add('hidden');
        }, 400);
    }

    modal.showModal();
    (modal.querySelector('[data-pin-code-digit]') ?? modal.querySelector('[data-close-record-modal], [data-modal-close]'))?.focus();
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

function getPinCodeInputs(container) {
    return Array.from(container.querySelectorAll('[data-pin-code-digit]'));
}

function syncPinCodeValue(container) {
    const value = getPinCodeInputs(container)
        .map((input) => input.value.replace(/\D/g, '').slice(-1))
        .join('');

    container.querySelector('[data-pin-code-value]').value = value;
}

document.addEventListener('input', (event) => {
    const input = event.target.closest('[data-pin-code-digit]');

    if (!input) {
        return;
    }

    const container = input.closest('[data-pin-code-input]');
    const inputs = getPinCodeInputs(container);
    const inputIndex = inputs.indexOf(input);
    const characters = input.value.replace(/\D/g, '');

    if (characters.length > 1) {
        characters.slice(0, inputs.length - inputIndex).split('').forEach((character, characterIndex) => {
            inputs[inputIndex + characterIndex].value = character;
        });

        syncPinCodeValue(container);
        inputs[Math.min(inputIndex + characters.length, inputs.length) - 1]?.focus();

        return;
    }

    input.value = characters.slice(-1);
    syncPinCodeValue(container);

    if (input.value !== '') {
        inputs[inputIndex + 1]?.focus();
    }
});

document.addEventListener('keydown', (event) => {
    const input = event.target.closest('[data-pin-code-digit]');

    if (!input) {
        return;
    }

    const inputs = getPinCodeInputs(input.closest('[data-pin-code-input]'));
    const inputIndex = inputs.indexOf(input);

    if (event.key === 'Backspace' && input.value === '') {
        const previousInput = inputs[inputIndex - 1];

        if (previousInput) {
            event.preventDefault();
            previousInput.value = '';
            syncPinCodeValue(input.closest('[data-pin-code-input]'));
            previousInput.focus();
        }
    } else if (event.key === 'ArrowLeft') {
        event.preventDefault();
        inputs[inputIndex - 1]?.focus();
    } else if (event.key === 'ArrowRight') {
        event.preventDefault();
        inputs[inputIndex + 1]?.focus();
    }
});

document.addEventListener('focusin', (event) => {
    const input = event.target.closest('[data-pin-code-digit]');

    if (input) {
        input.select();
    }
});
document.addEventListener('paste', (event) => {
    const input = event.target.closest('[data-pin-code-digit]');

    if (!input) {
        return;
    }

    const container = input.closest('[data-pin-code-input]');
    const inputs = getPinCodeInputs(container);
    const inputIndex = inputs.indexOf(input);
    const characters = (event.clipboardData?.getData('text') ?? '').replace(/\D/g, '').slice(0, inputs.length - inputIndex);

    if (characters === '') {
        return;
    }

    event.preventDefault();
    characters.split('').forEach((character, characterIndex) => {
        inputs[inputIndex + characterIndex].value = character;
    });

    syncPinCodeValue(container);
    inputs[Math.min(inputIndex + characters.length, inputs.length) - 1]?.focus();
});

document.querySelectorAll('[data-modal-open-on-load]').forEach((dialog) => {
    if (dialog instanceof HTMLDialogElement && !dialog.open) {
        dialog.showModal();
        dialog.querySelector('[data-pin-code-digit]')?.focus();
    }
});

const profilePictureInput = document.querySelector('[data-profile-picture-input]');

if (profilePictureInput) {
    const profilePictureError = document.querySelector('[data-profile-picture-error]');
    const profilePicturePreview = document.querySelector('[data-profile-picture-preview]');
    const profilePictureInitials = document.querySelector('[data-profile-picture-initials]');
    const cropper = document.querySelector('[data-profile-picture-cropper]');
    const cropFrame = document.querySelector('[data-profile-picture-crop-frame]');
    const cropImage = document.querySelector('[data-profile-picture-crop-image]');
    const cropCircle = document.querySelector('[data-profile-picture-crop-circle]');
    const allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
    let selectedImageUrl = null;
    let croppedPreviewUrl = null;
    let originalPreviewUrl = profilePicturePreview?.getAttribute('src') ?? null;
    let cropScale = 1;
    let cropLeft = 0;
    let cropTop = 0;
    let cropDiameter = 0;
    let circleLeft = 0;
    let circleTop = 0;
    let dragStart = null;

    const showProfilePictureError = (message) => {
        profilePictureError.textContent = message;
        profilePictureError.classList.remove('hidden');
    };

    const clearProfilePictureError = () => {
        profilePictureError.textContent = '';
        profilePictureError.classList.add('hidden');
    };

    const resetProfilePicturePreview = () => {
        if (selectedImageUrl) {
            URL.revokeObjectURL(selectedImageUrl);
            selectedImageUrl = null;
        }

        if (croppedPreviewUrl) {
            URL.revokeObjectURL(croppedPreviewUrl);
            croppedPreviewUrl = null;
        }

        if (originalPreviewUrl) {
            profilePicturePreview.src = originalPreviewUrl;
            profilePicturePreview.classList.remove('hidden');
            profilePictureInitials?.classList.add('hidden');
        } else {
            profilePicturePreview.removeAttribute('src');
            profilePicturePreview.classList.add('hidden');
            profilePictureInitials?.classList.remove('hidden');
        }
    };

    const discardPendingCrop = () => {
        profilePictureInput.value = '';

        if (cropper.open) {
            cropper.close();
        }

        resetProfilePicturePreview();
        clearProfilePictureError();
    };

    const positionCropImage = (center = false) => {
        const frameWidth = cropFrame.clientWidth;
        const frameHeight = cropFrame.clientHeight;
        const imageWidth = cropImage.naturalWidth * cropScale;
        const imageHeight = cropImage.naturalHeight * cropScale;
        const minimumLeft = frameWidth - imageWidth;
        const minimumTop = frameHeight - imageHeight;

        if (center) {
            cropLeft = (frameWidth - imageWidth) / 2;
            cropTop = (frameHeight - imageHeight) / 2;
        }

        cropLeft = Math.min(0, Math.max(minimumLeft, cropLeft));
        cropTop = Math.min(0, Math.max(minimumTop, cropTop));
        cropImage.style.width = imageWidth + 'px';
        cropImage.style.height = imageHeight + 'px';
        cropImage.style.left = cropLeft + 'px';
        cropImage.style.top = cropTop + 'px';
    };

    const positionCropCircle = (center = false) => {
        const frameWidth = cropFrame.clientWidth;
        const frameHeight = cropFrame.clientHeight;
        const minimumFrameSide = Math.min(frameWidth, frameHeight);

        cropDiameter = Math.min(minimumFrameSide * 0.98, Math.max(minimumFrameSide * 0.35, cropDiameter));

        if (center) {
            circleLeft = (frameWidth - cropDiameter) / 2;
            circleTop = (frameHeight - cropDiameter) / 2;
        }

        circleLeft = Math.min(frameWidth - cropDiameter, Math.max(0, circleLeft));
        circleTop = Math.min(frameHeight - cropDiameter, Math.max(0, circleTop));
        cropCircle.style.width = cropDiameter + 'px';
        cropCircle.style.height = cropDiameter + 'px';
        cropCircle.style.left = circleLeft + 'px';
        cropCircle.style.top = circleTop + 'px';
    };

    const updateCropScale = (center = false) => {
        const frameWidth = cropFrame.clientWidth;
        const frameHeight = cropFrame.clientHeight;

        cropDiameter = Math.min(frameWidth, frameHeight) * 0.92;
        positionCropCircle(true);
        const coverScale = Math.max(frameWidth / cropImage.naturalWidth, frameHeight / cropImage.naturalHeight);

        cropScale = coverScale;
        positionCropImage(center);
    };

    profilePictureInput.addEventListener('change', () => {
        const file = profilePictureInput.files?.[0];

        clearProfilePictureError();

        if (!file) {
            return;
        }

        const extension = file.name.split('.').pop()?.toLowerCase();

        if (!allowedExtensions.includes(extension) || !['image/jpeg', 'image/png', 'image/webp'].includes(file.type)) {
            profilePictureInput.value = '';
            if (cropper.open) {
                cropper.close();
            }
            resetProfilePicturePreview();
            showProfilePictureError('That file type is not allowed. Choose a JPG, PNG, or WebP image.');

            return;
        }

        if (file.size > 2 * 1024 * 1024) {
            profilePictureInput.value = '';
            if (cropper.open) {
                cropper.close();
            }
            resetProfilePicturePreview();
            showProfilePictureError('The picture is larger than 2 MB. Choose a smaller image.');

            return;
        }

        if (selectedImageUrl) {
            URL.revokeObjectURL(selectedImageUrl);
        }

        selectedImageUrl = URL.createObjectURL(file);
        cropImage.onload = () => {
            cropper.showModal();
            updateCropScale(true);
            cropFrame.focus();
        };
        cropImage.src = selectedImageUrl;
    });

    cropFrame.addEventListener('pointerdown', (event) => {
        if (event.target.closest('[data-profile-picture-crop-circle]')) {
            return;
        }

        dragStart = {
            mode: 'image',
            pointerId: event.pointerId,
            x: event.clientX,
            y: event.clientY,
            left: cropLeft,
            top: cropTop,
        };

        cropFrame.setPointerCapture(event.pointerId);
    });

    cropFrame.addEventListener('pointermove', (event) => {
        if (!dragStart || dragStart.pointerId !== event.pointerId) {
            return;
        }

        const horizontalMovement = event.clientX - dragStart.x;
        const verticalMovement = event.clientY - dragStart.y;

        if (dragStart.mode === 'image') {
            cropLeft = dragStart.left + horizontalMovement;
            cropTop = dragStart.top + verticalMovement;
            positionCropImage();
        } else if (dragStart.mode === 'circle') {
            circleLeft = dragStart.left + horizontalMovement;
            circleTop = dragStart.top + verticalMovement;
            positionCropCircle();
        } else {
            const minimumFrameSide = Math.min(cropFrame.clientWidth, cropFrame.clientHeight);
            const corner = dragStart.corner.split('-');
            const horizontalDirection = corner[1] === 'right' ? 1 : -1;
            const verticalDirection = corner[0] === 'bottom' ? 1 : -1;
            const diameterChange = (horizontalDirection * horizontalMovement + verticalDirection * verticalMovement) / 2;
            const centerX = dragStart.left + dragStart.diameter / 2;
            const centerY = dragStart.top + dragStart.diameter / 2;

            cropDiameter = dragStart.diameter + diameterChange;
            positionCropCircle();
            circleLeft = centerX - cropDiameter / 2;
            circleTop = centerY - cropDiameter / 2;
            positionCropCircle();
        }
    });

    cropFrame.addEventListener('pointerup', () => {
        dragStart = null;
    });

    cropFrame.addEventListener('pointercancel', () => {
        dragStart = null;
    });

    cropCircle.addEventListener('pointerdown', (event) => {
        event.stopPropagation();

        const resizeHandle = event.target.closest('[data-profile-picture-crop-resize]');

        dragStart = {
            mode: resizeHandle ? 'resize' : 'circle',
            corner: resizeHandle?.dataset.profilePictureCropResize,
            pointerId: event.pointerId,
            x: event.clientX,
            y: event.clientY,
            left: circleLeft,
            top: circleTop,
            diameter: cropDiameter,
        };

        cropFrame.setPointerCapture(event.pointerId);
    });

    cropCircle.addEventListener('keydown', (event) => {
        const movement = event.shiftKey ? 8 : 12;

        if (event.shiftKey && ['ArrowLeft', 'ArrowDown'].includes(event.key)) {
            cropDiameter -= movement;
        } else if (event.shiftKey && ['ArrowRight', 'ArrowUp'].includes(event.key)) {
            cropDiameter += movement;
        } else if (event.key === 'ArrowLeft') {
            circleLeft -= movement;
        } else if (event.key === 'ArrowRight') {
            circleLeft += movement;
        } else if (event.key === 'ArrowUp') {
            circleTop -= movement;
        } else if (event.key === 'ArrowDown') {
            circleTop += movement;
        } else {
            return;
        }

        event.preventDefault();
        event.stopPropagation();
        positionCropCircle();
    });

    cropFrame.addEventListener('keydown', (event) => {
        const movement = event.shiftKey ? 25 : 8;

        if (event.key === 'ArrowLeft') {
            cropLeft -= movement;
        } else if (event.key === 'ArrowRight') {
            cropLeft += movement;
        } else if (event.key === 'ArrowUp') {
            cropTop -= movement;
        } else if (event.key === 'ArrowDown') {
            cropTop += movement;
        } else {
            return;
        }

        event.preventDefault();
        positionCropImage();
    });

    document.querySelector('[data-profile-picture-crop]')?.addEventListener('click', () => {
        const sourceX = (circleLeft - cropLeft) / cropScale;
        const sourceY = (circleTop - cropTop) / cropScale;
        const sourceSize = cropDiameter / cropScale;
        const canvas = document.createElement('canvas');
        const context = canvas.getContext('2d');
        const sourceFile = profilePictureInput.files?.[0];

        if (!context || !sourceFile) {
            showProfilePictureError('The crop could not be created. Please choose the picture again.');

            return;
        }

        canvas.width = 512;
        canvas.height = 512;

        if (sourceFile.type === 'image/jpeg') {
            context.fillStyle = '#ffffff';
            context.fillRect(0, 0, canvas.width, canvas.height);
        }

        context.drawImage(cropImage, sourceX, sourceY, sourceSize, sourceSize, 0, 0, canvas.width, canvas.height);
        canvas.toBlob((blob) => {
            if (!blob) {
                showProfilePictureError('The crop could not be created. Please try again.');

                return;
            }

            const extension = sourceFile.name.split('.').pop().toLowerCase();
            const croppedFile = new File([blob], 'profile-picture.' + extension, {
                type: sourceFile.type,
                lastModified: Date.now(),
            });
            const transfer = new DataTransfer();

            transfer.items.add(croppedFile);
            profilePictureInput.files = transfer.files;
            if (croppedPreviewUrl) {
                URL.revokeObjectURL(croppedPreviewUrl);
            }

            croppedPreviewUrl = URL.createObjectURL(blob);
            profilePicturePreview.src = croppedPreviewUrl;
            profilePicturePreview.classList.remove('hidden');
            profilePictureInitials?.classList.add('hidden');
            cropper.close();
            clearProfilePictureError();
        }, sourceFile.type, 0.92);
    });

    document.querySelector('[data-profile-picture-cancel]')?.addEventListener('click', discardPendingCrop);

    cropper.addEventListener('cancel', (event) => {
        event.preventDefault();
        discardPendingCrop();
    });

    cropper.addEventListener('click', (event) => {
        if (event.target === cropper) {
            discardPendingCrop();
        }
    });

    window.addEventListener('resize', () => {
        if (cropper.open && cropImage.complete) {
            cropDiameter = Math.min(cropFrame.clientWidth, cropFrame.clientHeight) * 0.92;
            updateCropScale(true);
        }
    });
}
