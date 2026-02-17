/**
 * nav.js — Sticky nav, mobile toggle, active section tracking
 */
export function initNav() {
    const nav     = document.getElementById('nav');
    const toggle  = document.getElementById('nav-toggle');
    const links   = document.getElementById('nav-links');
    const navLinks = links ? links.querySelectorAll('a[href^="#"]') : [];

    if (!nav) return;

    // Sticky background on scroll
    const updateScrolled = () => {
        nav.classList.toggle('nav--scrolled', window.scrollY > 40);
    };
    window.addEventListener('scroll', updateScrolled, { passive: true });
    updateScrolled();

    // Mobile toggle
    if (toggle && links) {
        toggle.addEventListener('click', () => {
            const isOpen = links.classList.toggle('is-open');
            toggle.setAttribute('aria-expanded', String(isOpen));
            // Animate hamburger lines
            const spans = toggle.querySelectorAll('span');
            if (isOpen) {
                spans[0].style.transform = 'translateY(7px) rotate(45deg)';
                spans[1].style.opacity   = '0';
                spans[2].style.transform = 'translateY(-7px) rotate(-45deg)';
            } else {
                spans[0].style.transform = '';
                spans[1].style.opacity   = '';
                spans[2].style.transform = '';
            }
        });

        // Close on nav link click (mobile)
        navLinks.forEach(link => {
            link.addEventListener('click', () => {
                links.classList.remove('is-open');
                toggle.setAttribute('aria-expanded', 'false');
                const spans = toggle.querySelectorAll('span');
                spans[0].style.transform = '';
                spans[1].style.opacity   = '';
                spans[2].style.transform = '';
            });
        });

        // Close on outside click
        document.addEventListener('click', (e) => {
            if (!nav.contains(e.target)) {
                links.classList.remove('is-open');
                toggle.setAttribute('aria-expanded', 'false');
            }
        });
    }

    // Active section tracking
    const sections = document.querySelectorAll('section[id]');
    if (sections.length === 0 || navLinks.length === 0) return;

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                navLinks.forEach(link => {
                    link.classList.toggle(
                        'is-active',
                        link.getAttribute('href') === `#${entry.target.id}`
                    );
                });
            }
        });
    }, {
        rootMargin: '-30% 0px -60% 0px',
        threshold: 0,
    });

    sections.forEach(sec => observer.observe(sec));
}
