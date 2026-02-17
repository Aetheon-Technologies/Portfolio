/**
 * typewriter.js — Hero role text cycling effect
 * Reads role strings from data-roles JSON attribute on the target element.
 * Respects prefers-reduced-motion.
 */
export function initTypewriter() {
    const el = document.getElementById('hero-role');
    if (!el) return;

    // Skip animation if user prefers reduced motion
    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        const roles = JSON.parse(el.dataset.roles ?? '[]');
        el.textContent = roles[0] ?? '';
        return;
    }

    let roles;
    try {
        roles = JSON.parse(el.dataset.roles ?? '[]');
    } catch {
        roles = [];
    }

    if (roles.length === 0) return;

    let roleIndex  = 0;
    let charIndex  = 0;
    let isDeleting = false;

    const TYPING_SPEED  = 65;   // ms per character typed
    const DELETING_SPEED = 40;  // ms per character deleted
    const PAUSE_AFTER   = 2200; // ms to display full string
    const PAUSE_BEFORE  = 400;  // ms before typing next string

    function tick() {
        const current = roles[roleIndex];

        if (!isDeleting) {
            // Type forward
            el.textContent = current.slice(0, charIndex + 1);
            charIndex++;

            if (charIndex === current.length) {
                // Finished typing — pause, then start deleting
                isDeleting = true;
                setTimeout(tick, PAUSE_AFTER);
                return;
            }
        } else {
            // Delete backward
            el.textContent = current.slice(0, charIndex - 1);
            charIndex--;

            if (charIndex === 0) {
                // Finished deleting — move to next role
                isDeleting = false;
                roleIndex  = (roleIndex + 1) % roles.length;
                setTimeout(tick, PAUSE_BEFORE);
                return;
            }
        }

        const speed = isDeleting ? DELETING_SPEED : TYPING_SPEED;
        setTimeout(tick, speed);
    }

    // Start after a short delay so the page settles
    setTimeout(tick, 600);
}
