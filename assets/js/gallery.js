(function () {
    'use strict';

    // ==============================================
    // OPEN MODALS
    // ==============================================
    window.openAddModal = function () {
        const form = document.getElementById('form-add-gallery');
        if (form) form.reset();

        resetPreview('add');
        openModal('modal-add-gallery');
    };

    window.openEditModal = function (item) {
        if (!item) return;

        setValue('edit-gallery-id', item.id_gallery || '');
        setValue('edit-gallery-title', item.title || '');
        setValue('edit-gallery-category', item.category || 'kegiatan');
        setValue('edit-gallery-description', item.description || '');

        const pubEl = document.getElementById('edit-gallery-published');
        if (pubEl) pubEl.checked = !!parseInt(item.is_published);

        // Preview: tampilkan gambar lama
        const preview = document.getElementById('edit-preview');
        const placeholder = document.getElementById('edit-preview-placeholder');
        const fileInput = document.getElementById('edit-gallery-image');
        if (fileInput) fileInput.value = '';

        if (item.image_path) {
            preview.src = window.BASE_URL + item.image_path;
            preview.classList.remove('hidden');
            placeholder.classList.add('hidden');
        } else {
            preview.classList.add('hidden');
            placeholder.classList.remove('hidden');
        }

        openModal('modal-edit-gallery');
    };

    window.confirmDelete = function (id, title) {
        setValue('delete-gallery-id', id);
        setText('delete-gallery-title', title || '-');
        openModal('modal-delete-gallery');
    };

    function setValue(id, val) {
        const el = document.getElementById(id);
        if (el) el.value = val;
    }

    function setText(id, val) {
        const el = document.getElementById(id);
        if (el) el.textContent = val;
    }

    function resetPreview(type) {
        const preview = document.getElementById(type + '-preview');
        const placeholder = document.getElementById(type + '-preview-placeholder');
        if (preview) {
            preview.src = '';
            preview.classList.add('hidden');
        }
        if (placeholder) placeholder.classList.remove('hidden');
    }

    // ==============================================
    // IMAGE PREVIEW
    // ==============================================
    window.previewImage = function (input, previewId) {
        const file = input.files && input.files[0];
        if (!file) return;

        // Validasi tipe
        if (!file.type.startsWith('image/')) {
            alert('File harus berupa gambar');
            input.value = '';
            return;
        }

        // Validasi ukuran (5MB)
        if (file.size > 5 * 1024 * 1024) {
            alert('Ukuran file maksimal 5MB');
            input.value = '';
            return;
        }

        const reader = new FileReader();
        reader.onload = function (e) {
            const preview = document.getElementById(previewId);
            const placeholder = document.getElementById(previewId + '-placeholder');
            if (preview) {
                preview.src = e.target.result;
                preview.classList.remove('hidden');
            }
            if (placeholder) placeholder.classList.add('hidden');
        };
        reader.readAsDataURL(file);
    };

    // ==============================================
    // LIGHTBOX
    // ==============================================
    window.openLightbox = function (id, title, imgSrc) {
        const lightbox = document.getElementById('lightbox');
        const img = document.getElementById('lightbox-img');
        const titleEl = document.getElementById('lightbox-title');

        if (!lightbox || !img) return;

        img.src = imgSrc;
        titleEl.textContent = title || '';
        lightbox.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    };

    window.closeLightbox = function () {
        const lightbox = document.getElementById('lightbox');
        if (lightbox) {
            lightbox.classList.add('hidden');
            document.body.style.overflow = '';
        }
    };

    // ESC key untuk close lightbox
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            closeLightbox();
        }
    });

    // ==============================================
    // TOGGLE PUBLISH (AJAX)
    // ==============================================
    window.togglePublish = function (id) {
        event.stopPropagation();
        if (!confirm('Ubah status publikasi foto ini?')) return;

        const csrfMeta = document.querySelector('meta[name="csrf-token"]');
        const csrfVal = csrfMeta ? csrfMeta.getAttribute('content') : '';

        fetch(window.BASE_URL + 'gallery/toggle_publish', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: `csrf_token=${encodeURIComponent(csrfVal)}&id_gallery=${id}`,
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
        const searchEl   = document.getElementById('gallery-search');
        const categoryEl = document.getElementById('gallery-category');
        const statusEl   = document.getElementById('gallery-status');
        const noResult   = document.getElementById('gallery-no-result');

        function filter() {
            const q      = (searchEl?.value || '').toLowerCase().trim();
            const cat    = categoryEl?.value || '';
            const status = statusEl?.value || '';

            const items = document.querySelectorAll('.gallery-item');
            let visible = 0;

            items.forEach(item => {
                const title    = item.dataset.title || '';
                const category = item.dataset.category || '';
                const stat     = item.dataset.status || '';

                const matchSearch = !q      || title.includes(q);
                const matchCat    = !cat    || category === cat;
                const matchStatus = !status || stat === status;

                if (matchSearch && matchCat && matchStatus) {
                    item.style.display = '';
                    visible++;
                } else {
                    item.style.display = 'none';
                }
            });

            if (noResult) {
                noResult.classList.toggle('hidden', visible > 0 || items.length === 0);
            }
        }

        searchEl?.addEventListener('input', filter);
        categoryEl?.addEventListener('change', filter);
        statusEl?.addEventListener('change', filter);
    });

})();
