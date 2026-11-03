import './bootstrap';

const navigationToggle = document.querySelector('[data-site-nav-toggle]');
const siteHeader = navigationToggle?.closest('.site-header');

navigationToggle?.addEventListener('click', () => {
    const isExpanded = navigationToggle.getAttribute('aria-expanded') === 'true';
    navigationToggle.setAttribute('aria-expanded', String(!isExpanded));
    navigationToggle.setAttribute('aria-label', isExpanded ? 'Buka menu navigasi' : 'Tutup menu navigasi');
    siteHeader?.classList.toggle('is-menu-open', !isExpanded);
});

const contactForm = document.querySelector('[data-contact-form]');

if (contactForm instanceof HTMLFormElement) {
    const contactStatus = contactForm.querySelector('[data-contact-status]');
    const submitButton = contactForm.querySelector('[type="submit"]');
    const submitLabel = contactForm.querySelector('[data-contact-submit-label]');
    const submitArrow = contactForm.querySelector('[data-contact-submit-arrow]');
    const fields = [
        { input: contactForm.elements.namedItem('nama'), error: document.querySelector('#contact-name-error'), label: 'Nama lengkap' },
        { input: contactForm.elements.namedItem('email'), error: document.querySelector('#contact-email-error'), label: 'Email' },
        { input: contactForm.elements.namedItem('telepon'), error: document.querySelector('#contact-phone-error'), label: 'Nomor telepon', optional: true },
        { input: contactForm.elements.namedItem('subjek'), error: document.querySelector('#contact-subject-error'), label: 'Subjek' },
        { input: contactForm.elements.namedItem('pesan'), error: document.querySelector('#contact-message-error'), label: 'Pesan' },
    ];

    const validateField = (field) => {
        const value = field.input.value.trim();
        let message = '';

        if (!value && !field.optional) {
            message = `${field.label} wajib diisi.`;
        } else if (field.input.type === 'email' && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)) {
            message = 'Masukkan alamat email dengan format yang benar.';
        } else if (field.input.name === 'pesan' && value.length < 10) {
            message = 'Pesan minimal terdiri dari 10 karakter.';
        }

        field.error.textContent = message;
        field.input.setAttribute('aria-invalid', String(Boolean(message)));

        return message === '';
    };

    fields.forEach((field) => {
        field.input.addEventListener('blur', () => validateField(field));
        field.input.addEventListener('input', () => {
            if (field.input.getAttribute('aria-invalid') === 'true') {
                validateField(field);
            }
        });
        field.input.addEventListener('change', () => {
            if (field.input.getAttribute('aria-invalid') === 'true') {
                validateField(field);
            }
        });
    });

    contactForm.addEventListener('submit', (event) => {
        event.preventDefault();
        contactStatus.textContent = '';
        contactStatus.removeAttribute('data-state');

        const invalidFields = fields.filter((field) => !validateField(field));

        if (invalidFields.length > 0) {
            contactStatus.dataset.state = 'error';
            contactStatus.textContent = 'Mohon periksa kembali kolom yang ditandai.';
            invalidFields[0].input.focus();

            return;
        }

        submitButton.disabled = true;
        submitButton.classList.add('is-loading');
        submitLabel.textContent = 'Mengirim...';
        submitArrow.hidden = true;
        window.setTimeout(() => HTMLFormElement.prototype.submit.call(contactForm), 150);
    });
}
