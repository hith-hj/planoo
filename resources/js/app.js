import './bootstrap';

const themeToggle = document.getElementById('theme-toggle');
const systemTheme = window.matchMedia('(prefers-color-scheme: dark)');
let themeTransitionTimer;
let savedTheme;
try { savedTheme = localStorage.getItem('planoo-theme'); } catch {}
function applyTheme(theme) {
    const dark = theme === 'dark';
    document.documentElement.dataset.theme = theme;
    themeToggle?.setAttribute('aria-pressed', String(dark));
    const icon = themeToggle?.querySelector('i');
    if (icon) icon.className = dark ? 'bi bi-sun' : 'bi bi-moon';
    document.querySelector('meta[name="theme-color"]')?.setAttribute('content', dark ? '#171420' : '#462e8e');
}
applyTheme(document.documentElement.dataset.theme);
themeToggle?.addEventListener('click', () => {
    document.documentElement.classList.add('theme-transition');
    window.clearTimeout(themeTransitionTimer);
    savedTheme = document.documentElement.dataset.theme === 'dark' ? 'light' : 'dark';
    applyTheme(savedTheme);
    try { localStorage.setItem('planoo-theme', savedTheme); } catch {}
    themeTransitionTimer = window.setTimeout(() => document.documentElement.classList.remove('theme-transition'), 350);
});
systemTheme.addEventListener('change', event => {
    if (savedTheme !== 'light' && savedTheme !== 'dark') applyTheme(event.matches ? 'dark' : 'light');
});

const menuButton = document.getElementById('mobile-menu-btn');
const mobileMenu = document.getElementById('mobile-menu');
function closeMenu() {
    if (!mobileMenu || !menuButton) return;
    mobileMenu.hidden = true;
    menuButton.setAttribute('aria-expanded', 'false');
}
menuButton?.addEventListener('click', () => {
    const open = menuButton.getAttribute('aria-expanded') !== 'true';
    mobileMenu.hidden = !open;
    menuButton.setAttribute('aria-expanded', String(open));
});
mobileMenu?.querySelectorAll('a').forEach(link => link.addEventListener('click', closeMenu));
document.addEventListener('keydown', event => {
    if (event.key === 'Escape' && mobileMenu && !mobileMenu.hidden) {
        closeMenu();
        menuButton?.focus();
    }
});
document.addEventListener('click', event => {
    if (!event.target.closest('[data-site-header]')) closeMenu();
});
window.matchMedia('(min-width: 901px)').addEventListener('change', closeMenu);

const filters = document.querySelectorAll('[data-filter]');
const cards = document.querySelectorAll('[data-category]');
filters.forEach(button => button.addEventListener('click', () => {
    filters.forEach(filter => filter.setAttribute('aria-pressed', String(filter === button)));
    let count = 0;
    cards.forEach(card => {
        card.hidden = button.dataset.filter !== 'all' && card.dataset.category !== button.dataset.filter;
        if (!card.hidden) count++;
    });
    const status = document.getElementById('filter-status');
    if (status) status.textContent = status.dataset.template.replace(':count', count);
}));

const navigationLinks = document.querySelectorAll('[data-desktop-nav] [data-section]');
if ('IntersectionObserver' in window) {
    const observer = new IntersectionObserver(entries => {
        entries.forEach(entry => {
            if (!entry.isIntersecting) return;
            navigationLinks.forEach(link => {
                if (link.dataset.section === entry.target.id) link.setAttribute('aria-current', 'location');
                else link.removeAttribute('aria-current');
            });
        });
    }, { rootMargin: '-15% 0px -55% 0px', threshold: 0 });
    navigationLinks.forEach(link => {
        const section = document.getElementById(link.dataset.section);
        if (section) observer.observe(section);
    });
}

// Reveal content automatically as it enters the viewport.
const revealTargets = document.querySelectorAll('[data-reveal]');
if ('IntersectionObserver' in window && document.documentElement.dataset.motion === 'full') {
    const revealObserver = new IntersectionObserver(entries => {
        entries.forEach(entry => {
            if (!entry.isIntersecting) return;
            entry.target.dataset.reveal = 'visible';
            revealObserver.unobserve(entry.target);
        });
    }, { threshold: 0.08 });
    revealTargets.forEach(element => {
        // Never hide content already on screen or currently focused.
        if (element.getBoundingClientRect().top < window.innerHeight || element.contains(document.activeElement)) return;
        element.dataset.reveal = 'pending';
        revealObserver.observe(element);
    });
    document.addEventListener('focusin', event => {
        const target = event.target.closest('[data-reveal]');
        if (target) target.dataset.reveal = 'visible';
    });
}
