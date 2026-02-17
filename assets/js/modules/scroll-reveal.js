/**
 * scroll-reveal.js — IntersectionObserver fade-in animations
 * Respects prefers-reduced-motion
 */
export function initScrollReveal() {
    // Skip animations for users who prefer reduced motion
    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        document.querySelectorAll('[data-reveal]').forEach(el => {
            el.style.opacity  = '1';
            el.style.transform = 'none';
        });
        return;
    }

    const elements = document.querySelectorAll('[data-reveal]');
    if (elements.length === 0) return;

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const el    = entry.target;
                const delay = el.dataset.revealDelay ?? '0';
                el.style.transitionDelay = delay + 'ms';
                el.classList.add('is-visible');
                observer.unobserve(el); // animate once
            }
        });
    }, {
        rootMargin: '0px 0px -64px 0px',
        threshold: 0.1,
    });

    elements.forEach((el, i) => {
        // Stagger siblings in the same parent grid
        if (!el.dataset.revealDelay) {
            const siblings = el.parentElement.querySelectorAll('[data-reveal]');
            siblings.forEach((sib, j) => {
                if (!sib.dataset.revealDelay) {
                    sib.dataset.revealDelay = j * 80;
                }
            });
        }
        observer.observe(el);
    });
}
