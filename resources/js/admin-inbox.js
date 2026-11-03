const bulkForm = document.querySelector('#inbox-bulk-form');
const deleteDialog = document.querySelector('[data-delete-dialog]');

if (bulkForm instanceof HTMLFormElement && deleteDialog instanceof HTMLDialogElement) {
    const checkboxes = Array.from(document.querySelectorAll('.inbox-row-checkbox'));
    const selectAll = document.querySelector('[data-select-all]');
    const selectionCount = document.querySelector('[data-selection-count]');
    const bulkButtons = Array.from(document.querySelectorAll('[data-bulk-submit], [data-bulk-delete]'));
    const deleteCopy = deleteDialog.querySelector('[data-delete-copy]');
    const confirmDelete = deleteDialog.querySelector('[data-confirm-delete]');
    const bulkMethod = bulkForm.querySelector('[data-bulk-method]');
    const baseBulkAction = bulkForm.action;
    let pendingDeleteForm = null;
    let pendingBulkDelete = false;

    const selectedCheckboxes = () => checkboxes.filter((checkbox) => checkbox.checked);

    const updateSelection = () => {
        const selected = selectedCheckboxes();
        const count = selected.length;

        selectionCount.textContent = `${count} pesan dipilih`;
        bulkButtons.forEach((button) => {
            button.disabled = count === 0;
        });

        if (selectAll) {
            selectAll.checked = checkboxes.length > 0 && count === checkboxes.length;
            selectAll.indeterminate = count > 0 && count < checkboxes.length;
        }
    };

    checkboxes.forEach((checkbox) => checkbox.addEventListener('change', updateSelection));
    selectAll?.addEventListener('change', () => {
        checkboxes.forEach((checkbox) => {
            checkbox.checked = selectAll.checked;
        });
        updateSelection();
    });

    document.querySelectorAll('[data-bulk-submit]').forEach((button) => {
        button.addEventListener('click', (event) => {
            if (selectedCheckboxes().length === 0) {
                event.preventDefault();
                return;
            }
        });
    });

    document.querySelector('[data-bulk-delete]')?.addEventListener('click', () => {
        const count = selectedCheckboxes().length;
        if (count === 0) return;

        pendingDeleteForm = bulkForm;
        pendingBulkDelete = true;
        deleteCopy.textContent = `${count} pesan terpilih akan dihapus permanen.`;
        deleteDialog.showModal();
    });

    document.querySelectorAll('[data-delete-trigger]').forEach((button) => {
        button.addEventListener('click', () => {
            pendingDeleteForm = document.getElementById(button.dataset.deleteForm);
            pendingBulkDelete = false;
            deleteCopy.textContent = `Pesan dari ${button.dataset.deleteName} akan dihapus permanen.`;
            deleteDialog.showModal();
        });
    });

    confirmDelete.addEventListener('click', () => {
        if (!(pendingDeleteForm instanceof HTMLFormElement)) return;

        if (pendingBulkDelete) {
            pendingDeleteForm.action = pendingDeleteForm.dataset.deleteAction;
            bulkMethod.disabled = false;
        }

        pendingDeleteForm.requestSubmit();
        deleteDialog.close();
    });

    deleteDialog.addEventListener('close', () => {
        if (pendingBulkDelete) {
            bulkForm.action = baseBulkAction;
            bulkMethod.disabled = true;
        }
        pendingDeleteForm = null;
        pendingBulkDelete = false;
    });

    updateSelection();
}

const messageDialog = document.querySelector('[data-message-dialog]');
if (messageDialog instanceof HTMLDialogElement && messageDialog.dataset.open === 'true') {
    messageDialog.showModal();
}
