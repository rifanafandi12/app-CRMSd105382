/**
 * App.js — Utility umum untuk seluruh aplikasi
 */

// ==============================================
// SIDEBAR TOGGLE
// ==============================================
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const backdrop = document.getElementById('sidebar-backdrop');

    if (!sidebar) return;

    const isHidden = sidebar.classList.contains('-translate-x-full');

    if (isHidden) {
        sidebar.classList.remove('-translate-x-full');
        backdrop.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    } else {
        sidebar.classList.add('-translate-x-full');
        backdrop.classList.add('hidden');
        document.body.style.overflow = '';
    }
}

// ==============================================
// AJAX CSRF TOKEN
// ==============================================
(function() {
    const meta = document.querySelector('meta[name="csrf-token"]');
    if (meta) {
        const token = meta.getAttribute('content');
        if (typeof $ !== 'undefined') {
            $.ajaxSetup({
                headers: { 'X-CSRF-Token': token }
            });
        }
    }
})();

// ==============================================
// FLASH MESSAGE AUTO-DISMISS
// ==============================================
document.addEventListener('DOMContentLoaded', function() {
    const flash = document.getElementById('flash-message');
    if (flash) {
        setTimeout(() => {
            flash.style.transition = 'opacity 0.5s ease';
            flash.style.opacity = '0';
            setTimeout(() => flash.remove(), 500);
        }, 5000);
    }
});

// ==============================================
// CONFIRM DELETE
// ==============================================
document.addEventListener('click', function(e) {
    const link = e.target.closest('[data-confirm]');
    if (link) {
        const message = link.getAttribute('data-confirm') || 'Apakah Anda yakin?';
        if (!confirm(message)) {
            e.preventDefault();
            return false;
        }
    }
});

// ==============================================
// FORMAT DATETIME
// ==============================================
function formatDateTime(dateString) {
    if (!dateString) return '-';
    const date = new Date(dateString);
    return date.toLocaleString('id-ID', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
}

// ==============================================
// EXPORT GLOBAL
// ==============================================
window.App = {
    toggleSidebar,
    formatDateTime,
};
