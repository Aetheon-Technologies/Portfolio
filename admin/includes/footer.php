</main><!-- /.admin-main -->

<script>
// Auto-dismiss flash messages
document.querySelectorAll('.flash').forEach(flash => {
    const close = flash.querySelector('.flash__close');
    if (close) close.addEventListener('click', () => flash.remove());
    setTimeout(() => flash && flash.remove(), 5000);
});

// Confirm deletes
document.querySelectorAll('.btn-delete').forEach(btn => {
    btn.closest('form')?.addEventListener('submit', (e) => {
        if (!confirm('Are you sure you want to delete this? This cannot be undone.')) {
            e.preventDefault();
        }
    });
});

// Slug auto-generation from title
const titleInput = document.getElementById('field-title');
const slugInput  = document.getElementById('field-slug');
let slugEdited   = false;

if (slugInput) {
    slugInput.addEventListener('input', () => { slugEdited = true; });
}
if (titleInput && slugInput) {
    titleInput.addEventListener('input', () => {
        if (!slugEdited) {
            slugInput.value = titleInput.value
                .toLowerCase()
                .replace(/[^a-z0-9\s-]/g, '')
                .replace(/[\s-]+/g, '-')
                .replace(/^-|-$/g, '');
        }
    });
}

// Image file preview
document.querySelectorAll('[data-preview-target]').forEach(input => {
    input.addEventListener('change', () => {
        const targetId = input.dataset.previewTarget;
        const img      = document.getElementById(targetId);
        if (!img || !input.files[0]) return;
        const reader   = new FileReader();
        reader.onload  = e => img.src = e.target.result;
        reader.readAsDataURL(input.files[0]);
    });
});
</script>
</body>
</html>
