const unitList = document.querySelector('[data-bumdes-units]');
const addUnitButton = document.querySelector('[data-add-bumdes-unit]');
const unitTemplate = document.querySelector('#bumdes-unit-template');
const managerList = document.querySelector('[data-bumdes-managers]');
const addManagerButton = document.querySelector('[data-add-bumdes-manager]');
const managerTemplate = document.querySelector('#bumdes-manager-template');
const photoInput = document.querySelector('#foto');
const photoPreview = document.querySelector('[data-bumdes-photo-preview]');

if (photoInput && photoPreview) {
    let previewUrl = null;

    photoInput.addEventListener('change', () => {
        const photo = photoInput.files?.[0];

        if (!photo) {
            return;
        }

        if (previewUrl) {
            URL.revokeObjectURL(previewUrl);
        }

        previewUrl = URL.createObjectURL(photo);
        photoPreview.replaceChildren();

        const image = document.createElement('img');
        image.src = previewUrl;
        image.alt = `Pratinjau foto ${photo.name}`;
        photoPreview.append(image);
    });
}

if (managerList && addManagerButton && managerTemplate) {
    const managerPreviewUrls = new WeakMap();
    const syncManagerNumbers = () => {
        managerList.querySelectorAll('[data-bumdes-manager]').forEach((card, index) => {
            card.querySelector('.bumdes-manager-card__number').textContent = String(index + 1).padStart(2, '0');
        });
    };

    addManagerButton.addEventListener('click', () => {
        const index = Number(managerList.dataset.nextIndex);

        if (managerList.querySelectorAll('[data-bumdes-manager]').length >= 20) {
            addManagerButton.disabled = true;
            return;
        }

        const html = managerTemplate.innerHTML.replaceAll('__INDEX__', String(index));
        managerList.insertAdjacentHTML('beforeend', html);
        managerList.dataset.nextIndex = String(index + 1);
        syncManagerNumbers();
        managerList.querySelector(`[name="pengurus[${index}][nama]"]`)?.focus();

        if (managerList.querySelectorAll('[data-bumdes-manager]').length >= 20) {
            addManagerButton.disabled = true;
        }
    });

    managerList.addEventListener('click', (event) => {
        const removeButton = event.target.closest('[data-remove-bumdes-manager]');

        if (!removeButton) {
            return;
        }

        const card = removeButton.closest('[data-bumdes-manager]');
        const remainingCards = managerList.querySelectorAll('[data-bumdes-manager]').length;

        if (card && remainingCards > 1) {
            const previewUrl = managerPreviewUrls.get(card);
            if (previewUrl) {
                URL.revokeObjectURL(previewUrl);
            }
            card.remove();
            syncManagerNumbers();
            addManagerButton.disabled = false;
        } else if (card) {
            const previewUrl = managerPreviewUrls.get(card);
            if (previewUrl) {
                URL.revokeObjectURL(previewUrl);
                managerPreviewUrls.delete(card);
            }
            card.querySelectorAll('input, textarea').forEach((field) => {
                field.value = '';
            });
            const preview = card.querySelector('[data-manager-photo-preview]');
            const placeholder = document.createElement('span');
            placeholder.textContent = 'Belum ada foto';
            preview.replaceChildren(placeholder);
        }
    });

    managerList.addEventListener('change', (event) => {
        if (!event.target.matches('.bumdes-manager-card__photo-input')) {
            return;
        }

        const card = event.target.closest('[data-bumdes-manager]');
        const preview = card.querySelector('[data-manager-photo-preview]');
        const photo = event.target.files?.[0];

        if (!photo || !preview) {
            return;
        }

        const oldPreviewUrl = managerPreviewUrls.get(card);
        if (oldPreviewUrl) {
            URL.revokeObjectURL(oldPreviewUrl);
        }

        const previewUrl = URL.createObjectURL(photo);
        managerPreviewUrls.set(card, previewUrl);
        const image = document.createElement('img');
        image.src = previewUrl;
        image.alt = `Pratinjau foto ${photo.name}`;
        preview.replaceChildren(image);
    });
}

if (unitList && addUnitButton && unitTemplate) {
    addUnitButton.addEventListener('click', () => {
        const index = Number(unitList.dataset.nextIndex);

        if (unitList.querySelectorAll('[data-bumdes-unit]').length >= 20) {
            addUnitButton.disabled = true;
            return;
        }

        const html = unitTemplate.innerHTML.replaceAll('__INDEX__', String(index));
        unitList.insertAdjacentHTML('beforeend', html);
        unitList.dataset.nextIndex = String(index + 1);

        if (unitList.querySelectorAll('[data-bumdes-unit]').length >= 20) {
            addUnitButton.disabled = true;
        }
    });

    unitList.addEventListener('change', (event) => {
        if (event.target.matches('.bumdes-unit-editor__remove input')) {
            event.target.closest('[data-bumdes-unit]').classList.toggle('is-marked-for-removal', event.target.checked);
        }
    });
}
