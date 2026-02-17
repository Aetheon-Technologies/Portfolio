/**
 * project-filter.js — Client-side project filtering by category
 * Cards are rendered by PHP with data-category attributes.
 * No AJAX — all cards are in the DOM already.
 */
export function initProjectFilter() {
    const filterBar = document.querySelector('.filter-bar');
    const grid      = document.getElementById('projects-grid');

    if (!filterBar || !grid) return;

    const cards   = Array.from(grid.querySelectorAll('.project-card'));
    const buttons = Array.from(filterBar.querySelectorAll('.filter-btn'));

    filterBar.addEventListener('click', (e) => {
        const btn = e.target.closest('.filter-btn');
        if (!btn) return;

        const filter = btn.dataset.filter ?? 'all';

        // Update active button
        buttons.forEach(b => b.classList.toggle('is-active', b === btn));

        // Show/hide cards with a smooth opacity transition
        cards.forEach(card => {
            const category = card.dataset.category ?? '';
            const show     = filter === 'all' || category === filter;

            if (show) {
                card.style.display  = '';
                // Short timeout so display:'' triggers transition
                requestAnimationFrame(() => {
                    card.style.opacity   = '1';
                    card.style.transform = '';
                });
            } else {
                card.style.opacity   = '0';
                card.style.transform = 'translateY(8px)';
                // Hide after transition
                card.addEventListener('transitionend', () => {
                    if (card.style.opacity === '0') {
                        card.style.display = 'none';
                    }
                }, { once: true });
            }
        });
    });
}
