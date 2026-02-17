/**
 * contact-form.js — AJAX contact form with client-side validation
 */
export function initContactForm() {
    const form   = document.getElementById('contact-form');
    const status = document.getElementById('form-status');
    const submit = document.getElementById('contact-submit');

    if (!form) return;

    form.addEventListener('submit', async (e) => {
        e.preventDefault();

        // Clear previous errors
        clearErrors();
        hideStatus();

        const name    = form.querySelector('#contact-name');
        const email   = form.querySelector('#contact-email');
        const message = form.querySelector('#contact-message');

        // Validate
        let valid = true;

        if (!name.value.trim()) {
            showError('fg-name', 'err-name');
            valid = false;
        }

        if (!email.value.trim() || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value.trim())) {
            showError('fg-email', 'err-email');
            valid = false;
        }

        if (!message.value.trim() || message.value.trim().length < 20) {
            showError('fg-message', 'err-message');
            valid = false;
        }

        if (!valid) return;

        // Submit
        submit.disabled = true;
        submit.classList.add('btn--loading');
        submit.textContent = 'Sending…';

        try {
            const resp = await fetch('api/contact.php', {
                method: 'POST',
                body: new FormData(form),
            });

            const data = await resp.json();

            if (resp.ok && data.success) {
                showStatus('success', data.message ?? 'Message sent! I\'ll be in touch soon.');
                form.reset();
            } else if (resp.status === 429) {
                showStatus('error', 'Too many messages. Please wait a few minutes before trying again.');
            } else {
                showStatus('error', data.error ?? 'Something went wrong. Please try again.');
            }
        } catch {
            showStatus('error', 'Network error. Please check your connection and try again.');
        } finally {
            submit.disabled = false;
            submit.classList.remove('btn--loading');
            submit.innerHTML = 'Send Message <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>';
        }
    });

    function showError(groupId, errorId) {
        document.getElementById(groupId)?.classList.add('has-error');
        document.getElementById(errorId)?.style && (document.getElementById(errorId).style.display = 'block');
    }

    function clearErrors() {
        form.querySelectorAll('.form-group').forEach(g => g.classList.remove('has-error'));
    }

    function showStatus(type, msg) {
        if (!status) return;
        status.className = `form-status form-status--${type}`;
        status.textContent = msg;
        status.style.display = 'block';
        status.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    function hideStatus() {
        if (!status) return;
        status.style.display = 'none';
        status.className = 'form-status';
    }
}
