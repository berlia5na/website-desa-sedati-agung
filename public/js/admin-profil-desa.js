const officialList = document.querySelector('[data-official-list]');
const addOfficialButton = document.querySelector('[data-add-official]');
const officialTemplate = document.querySelector('#official-member-template');

if (officialList && addOfficialButton && officialTemplate) {
    let nextIndex = Number(officialList.dataset.nextIndex);

    const bindRemoveButton = (card) => {
        const button = card.querySelector('[data-remove-official]');
        const deleteInput = card.querySelector('[data-official-delete-value]');
        const deletedNotice = card.querySelector('[data-official-deleted-notice]');
        const idInput = card.querySelector('input[name$="[id]"]');

        button?.addEventListener('click', () => {
            if (!idInput?.value) {
                card.remove();
                return;
            }

            const markedForDeletion = deleteInput.value !== '1';
            deleteInput.value = markedForDeletion ? '1' : '0';
            card.classList.toggle('is-marked-for-deletion', markedForDeletion);
            button.textContent = markedForDeletion ? 'Batalkan' : 'Hapus';
            deletedNotice.hidden = !markedForDeletion;
        });

        if (deleteInput?.value === '1') {
            card.classList.add('is-marked-for-deletion');
            button.textContent = 'Batalkan';
            deletedNotice.hidden = false;
        }
    };

    officialList.querySelectorAll('[data-official-card]').forEach(bindRemoveButton);

    addOfficialButton.addEventListener('click', () => {
        const markup = officialTemplate.innerHTML.replaceAll('__INDEX__', String(nextIndex));
        officialList.insertAdjacentHTML('beforeend', markup);
        bindRemoveButton(officialList.lastElementChild);
        officialList.lastElementChild.querySelector('input[name$="[nama]"]')?.focus();
        nextIndex += 1;
        officialList.dataset.nextIndex = String(nextIndex);
    });
}

const mapLocationInput = document.querySelector('[data-map-location]');
const mapPreview = document.querySelector('[data-map-preview]');
const mapLink = document.querySelector('[data-map-link]');

mapLocationInput?.addEventListener('input', () => {
    const location = mapLocationInput.value.trim();
    const query = encodeURIComponent(location || mapLocationInput.dataset.mapDefault);

    if (mapPreview) {
        mapPreview.src = `https://maps.google.com/maps?q=${query}&output=embed`;
    }

    if (mapLink) {
        mapLink.href = `https://www.google.com/maps/search/?api=1&query=${query}`;
    }
});
