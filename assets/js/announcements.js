(function () {
    'use strict';

    // ==============================================
    // OPEN MODALS
    // ==============================================
    window.openAddModal = function () {
        const form = document.getElementById('form-add-ann');
        if (form) form.reset();
        openModal('modal-add-ann');
    };

    window.openEditModal = function (ann) {
        if (!ann) return;

        const id = ann.id_announcement || '';
        setValue('edit-ann-id', id);
        setValue('edit-ann-title', ann.title || '');
        setValue('edit-ann-category', ann.category || 'umum');
        setValue('edit-ann-target', ann.target_class_id || '');
        setValue('edit-ann-content', ann.content || '');
        setValue('edit-ann-expiry', ann.expiry_date || '');

        // Checkbox publish
        const pubEl = document.getElementById('edit-ann-published');
        if (pubEl) pubEl.checked = !!parseInt(ann.is_published);

        // Attachment link
        const attEl = document.getElementById('edit-ann-current-attachment');
        if (attEl) {
            if (ann.attachment) {
                const link = attEl.querySelector('a');
                if (link) link.href = window.BASE_URL + ann.attachment;
                attEl.classList.remove('hidden');
            } else {
                attEl.classList.add('hidden');
            }
        }

        // Reset file input
        const fileEl = document.getElementById('edit-ann-attachment');
        if (fileEl) fileEl.value = '';

        openModal('modal-edit-ann');
    };

    window.confirmDelete = function (id, title) {
        setValue('delete-ann-id', id);
        setText('delete-ann-title', title || '-');
        openModal('modal-delete-ann');
    };

    function setValue(id, val) {
        const el = document.getElementById(id);
        if (el) el.value = val;
    }

    function setText(id, val) {
        const el = document.getElementById(id);
        if (el) el.textContent = val;
    }

    // ==============================================
    // TOGGLE PUBLISH (AJAX)
    // ==============================================
    window.togglePublish = function (id) {
        if (!confirm('Ubah status publikasi pengumuman ini?')) return;

        const csrfMeta = document.querySelector('meta[name="csrf-token"]');
        const csrfVal = csrfMeta ? csrfMeta.getAttribute('content') : '';

        fetch(window.BASE_URL + 'announcements/toggle_publish', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: `csrf_token=${encodeURIComponent(csrfVal)}&id_announcement=${id}`,
        })
            .then(r => r.json())
            .then(data => {
                if (data.status === 'success') {
                    location.reload();
                } else {
                    alert(data.message || 'Gagal mengubah status');
                }
            })
            .catch(() => alert('Terjadi kesalahan'));
    };

    // ==============================================
    // SEARCH & FILTER
    // ==============================================
    document.addEventListener('DOMContentLoaded', function () {
        const searchEl  = document.getElementById('ann-search');
        const categoryEl = document.getElementById('ann-category');
        const statusEl  = document.getElementById('ann-status');
        const noResult  = document.getElementById('ann-no-result');

        function filter() {
            const q    = (searchEl?.value || '').toLowerCase().trim();
            const cat  = categoryEl?.value || '';
            const stat = statusEl?.value || '';

            const rows = document.querySelectorAll('.ann-row');
            let visible = 0;

            rows.forEach(row => {
                const title    = row.dataset.title || '';
                const category = row.dataset.category || '';
                const status   = row.dataset.status || '';

                const matchSearch = !q    || title.includes(q);
                const matchCat    = !cat  || category === cat;
                const matchStatus = !stat || status === stat;

                if (matchSearch && matchCat && matchStatus) {
                    row.style.display = '';
                    visible++;
                } else {
                    row.style.display = 'none';
                }
            });

            // Untuk non-admin (card view)
            if (rows.length === 0) {
                document.querySelectorAll('.card-hover').forEach(card => {
                    const title = (card.querySelector('h3')?.textContent || '').toLowerCase();
                    card.parentElement.style.display = (!q || title.includes(q)) ? '' : 'none';
                });
            }

            if (noResult) {
                noResult.classList.toggle('hidden', visible > 0 || rows.length === 0);
            }
        }

        searchEl?.addEventListener('input', filter);
        categoryEl?.addEventListener('change', filter);
        statusEl?.addEventListener('change', filter);
    });
})();
