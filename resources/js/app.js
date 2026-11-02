import './bootstrap';

const navigationToggle = document.querySelector('[data-site-nav-toggle]');
const siteHeader = navigationToggle?.closest('.site-header');

navigationToggle?.addEventListener('click', () => {
    const isExpanded = navigationToggle.getAttribute('aria-expanded') === 'true';
    navigationToggle.setAttribute('aria-expanded', String(!isExpanded));
    navigationToggle.setAttribute('aria-label', isExpanded ? 'Buka menu navigasi' : 'Tutup menu navigasi');
    siteHeader?.classList.toggle('is-menu-open', !isExpanded);
});
