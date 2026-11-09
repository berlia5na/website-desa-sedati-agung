const sidebar = document.querySelector('#admin-sidebar');
const sidebarToggle = document.querySelector('[data-sidebar-toggle]');
const menuSearch = document.querySelector('[data-menu-search]');
const notificationToggle = document.querySelector('[data-notification-toggle]');
const notificationPopover = document.querySelector('[data-notification-popover]');
const themeToggle = document.querySelector('[data-theme-toggle]');
const deleteSambutanButton = document.querySelector('[data-delete-sambutan]');

const syncMediaDeleteControl = (button) => {
    const fileInput = document.getElementById(button.dataset.clearUploadFor);
    const deleteInput = button.parentElement.querySelector('[data-saved-media-delete]');
    const hasSelectedFile = Boolean(fileInput?.files?.length);
    const hasSavedFile = Boolean(deleteInput);
    const isMarkedForDeletion = Boolean(deleteInput?.checked);
    const label = hasSelectedFile
        ? `Hapus ${button.dataset.mediaLabel} yang dipilih`
        : isMarkedForDeletion
            ? `Batalkan hapus ${button.dataset.mediaLabel}`
            : `Hapus ${button.dataset.mediaLabel}`;

    button.hidden = !hasSelectedFile && !hasSavedFile;
    button.setAttribute('aria-pressed', String(isMarkedForDeletion && !hasSelectedFile));
    button.setAttribute('aria-label', label);
    button.title = label;
    button.querySelector('[data-media-delete-label]').textContent = label;

    let warning = button.nextElementSibling;
    if (!warning?.matches('.admin-photo-delete-warning')) {
        warning = document.createElement('small');
        warning.className = 'admin-photo-delete-warning';
        warning.setAttribute('role', 'status');
        warning.textContent = 'Foto/gambar yang sudah dipublikasikan tetap tampil sampai Anda menekan Simpan atau Publikasikan.';
        button.insertAdjacentElement('afterend', warning);
    }

    warning.hidden = !isMarkedForDeletion || hasSelectedFile;
};

document.querySelectorAll('[data-media-delete-control]').forEach(syncMediaDeleteControl);

document.addEventListener('change', (event) => {
    const input = event.target;
    if (input.matches('[data-saved-media-delete]')) {
        const button = input.parentElement.querySelector('[data-media-delete-control]');
        if (button) {
            syncMediaDeleteControl(button);
        }
    } else if (input.matches('input[type="file"]') && input.id) {
        const button = document.querySelector(`[data-clear-upload-for="${CSS.escape(input.id)}"]`);
        if (button) {
            const deleteInput = button.parentElement.querySelector('[data-saved-media-delete]');
            if (input.files?.length && deleteInput) {
                deleteInput.checked = false;
            }
            syncMediaDeleteControl(button);
        }
    }
});

document.addEventListener('click', (event) => {
    const button = event.target.closest('[data-media-delete-control]');
    if (!button) {
        return;
    }

    const fileInput = document.getElementById(button.dataset.clearUploadFor);
    if (fileInput?.files?.length) {
        fileInput.value = '';
        fileInput.dispatchEvent(new Event('change', { bubbles: true }));
    } else {
        const deleteInput = button.parentElement.querySelector('[data-saved-media-delete]');
        if (deleteInput) {
            deleteInput.checked = !deleteInput.checked;
            deleteInput.dispatchEvent(new Event('change', { bubbles: true }));
        }
    }

    syncMediaDeleteControl(button);
});

document.addEventListener('reset', (event) => {
    window.setTimeout(() => {
        event.target.querySelectorAll('[data-media-delete-control]').forEach(syncMediaDeleteControl);
    }, 0);
}, true);

deleteSambutanButton?.addEventListener('click', (event) => {
    if (!window.confirm('Hapus sambutan kepala desa beserta foto dari halaman beranda?')) {
        event.preventDefault();
    }
});

const syncThemeToggle = () => {
    const isDark = document.documentElement.dataset.adminTheme === 'dark';
    if (!themeToggle) {
        return;
    }

    themeToggle.setAttribute('aria-pressed', String(isDark));
    themeToggle.setAttribute('aria-label', isDark ? 'Aktifkan mode terang' : 'Aktifkan mode gelap');
    themeToggle.querySelector('.admin-theme-toggle__icon').textContent = isDark ? '☀' : '☾';
    themeToggle.querySelector('.admin-theme-toggle__label').textContent = isDark ? 'Mode terang' : 'Mode gelap';
};

themeToggle?.addEventListener('click', () => {
    const nextTheme = document.documentElement.dataset.adminTheme === 'dark' ? 'light' : 'dark';
    document.documentElement.dataset.adminTheme = nextTheme;

    try {
        localStorage.setItem('desa-admin-theme', nextTheme);
    } catch {
        // Keep the selected theme active for this page when storage is unavailable.
    }

    syncThemeToggle();
});

syncThemeToggle();

sidebarToggle?.addEventListener('click', () => {
    const isOpen = sidebar?.classList.toggle('is-open') ?? false;
    sidebarToggle.setAttribute('aria-expanded', String(isOpen));
});

menuSearch?.addEventListener('input', () => {
    const query = menuSearch.value.trim().toLocaleLowerCase();

    document.querySelectorAll('[data-search-label]').forEach((link) => {
        link.hidden = !link.dataset.searchLabel.includes(query);
    });
});

document.addEventListener('keydown', (event) => {
    if ((event.metaKey || event.ctrlKey) && event.key.toLocaleLowerCase() === 'k') {
        event.preventDefault();
        menuSearch?.focus();
    }

    if (event.key === 'Escape') {
        sidebar?.classList.remove('is-open');
        sidebarToggle?.setAttribute('aria-expanded', 'false');
        if (notificationPopover) {
            notificationPopover.hidden = true;
            notificationToggle?.setAttribute('aria-expanded', 'false');
        }
    }
});

notificationToggle?.addEventListener('click', () => {
    if (!notificationPopover) {
        return;
    }

    notificationPopover.hidden = !notificationPopover.hidden;
    notificationToggle.setAttribute('aria-expanded', String(!notificationPopover.hidden));
});

const tabButtons = [...document.querySelectorAll('[data-admin-tab]')];
const tabPanels = [...document.querySelectorAll('[data-admin-panel]')];

tabButtons.forEach((button) => {
    button.addEventListener('click', () => {
        const activeTab = button.dataset.adminTab;

        tabButtons.forEach((tab) => {
            const active = tab === button;
            tab.classList.toggle('is-active', active);
            tab.setAttribute('aria-selected', String(active));
            tab.tabIndex = active ? 0 : -1;
        });

        tabPanels.forEach((panel) => {
            panel.hidden = panel.dataset.adminPanel !== activeTab;
        });
    });
});

const editorForm = document.querySelector('#home-editor-form');

if (editorForm) {
    const previewText = (key, value, fallback) => {
        const element = document.querySelector(`[data-preview="${key}"]`);
        if (element) {
            element.textContent = value.trim() || fallback;
        }
    };

    const syncPreview = (refreshImages = false) => {
        editorForm.querySelectorAll('[data-preview-source]').forEach((input) => {
            const previewKey = input.dataset.previewSource;
            const defaultValue = input.dataset.previewDefault ?? '';
            previewText(previewKey, input.value, defaultValue);
        });

        editorForm.querySelectorAll('[data-preview-toggle]').forEach((input) => {
            const target = document.querySelector(`[data-preview-visibility="${input.dataset.previewToggle}"]`);
            if (target) {
                target.hidden = !input.checked;
            }
        });

        const numberFormat = new Intl.NumberFormat('id-ID');
        ['jumlah_penduduk', 'jumlah_usaha_bumdes', 'jumlah_umkm'].forEach((name) => {
            const input = editorForm.elements.namedItem(name);
            const preview = document.querySelector(`[data-preview-stat="${name}"]`);
            if (input && preview) {
                preview.textContent = input.value === '' ? '—' : numberFormat.format(Number(input.value));
            }
        });

        const previewImage = document.querySelector('[data-preview-image]');
        const heroImageInput = editorForm.querySelector('[name="hero_image"]');
        const welcomePhotoInput = editorForm.querySelector('[name="sambutan_foto"]');
        const updateImage = (input, target, fallback) => {
            if (!input || !target) {
                return;
            }

            const image = input.files?.[0];
            if (!image) {
                target.src = fallback;
                target.hidden = !fallback;
                const placeholder = target.parentElement?.querySelector('b');
                if (placeholder) {
                    placeholder.hidden = Boolean(fallback);
                }
                return;
            }

            const reader = new FileReader();
            reader.addEventListener('load', () => {
                target.src = reader.result;
                target.hidden = false;
                const placeholder = target.parentElement?.querySelector('b');
                if (placeholder) {
                    placeholder.hidden = true;
                }
            });
            reader.readAsDataURL(image);
        };

        if (refreshImages) {
            updateImage(heroImageInput, previewImage, previewImage?.dataset.savedImage ?? '');
            updateImage(welcomePhotoInput, document.querySelector('[data-preview-welcome-photo]'), document.querySelector('[data-preview-welcome-photo]')?.dataset.savedImage ?? '');
        }
        previewText('widget-title', editorForm.elements.namedItem('widget_judul')?.value ?? '', 'Banner layanan desa');
        previewText('widget-text', editorForm.elements.namedItem('widget_teks')?.value ?? '', 'Deskripsi widget akan tampil di sini.');

        const spotlightSelect = editorForm.querySelector('[name="sorotan[]"]');
        const spotlightPreview = document.querySelector('[data-preview-spotlights]');
        if (spotlightSelect && spotlightPreview) {
            spotlightPreview.replaceChildren();
            const selected = [...spotlightSelect.selectedOptions];
            if (selected.length === 0) {
                const empty = document.createElement('span');
                empty.className = 'preview-empty';
                empty.textContent = 'Belum ada sorotan dipilih';
                spotlightPreview.append(empty);
            } else {
                selected.forEach((option) => {
                    const card = document.createElement('span');
                    card.className = 'preview-spotlight';
                    card.textContent = option.textContent.trim();
                    spotlightPreview.append(card);
                });
            }
        }
    };

    editorForm.addEventListener('input', () => syncPreview());
    editorForm.addEventListener('change', (event) => syncPreview(event.target.matches('[type="file"]')));
    editorForm.addEventListener('reset', () => window.setTimeout(() => {
        syncPreview(true);
    }, 0));

    const spotlightSelect = editorForm.querySelector('[name="sorotan[]"]');
    const spotlightError = editorForm.querySelector('[data-spotlight-error]');
    spotlightSelect?.addEventListener('change', () => {
        const selected = [...spotlightSelect.selectedOptions];
        if (selected.length > 3) {
            selected[selected.length - 1].selected = false;
            if (spotlightError) {
                spotlightError.hidden = false;
                window.setTimeout(() => {
                    spotlightError.hidden = true;
                }, 2600);
            }
        }
    });

    syncPreview(true);
}
