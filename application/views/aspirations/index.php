<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<?php
$is_admin_view = $is_admin_view ?? false;
$is_parent = ($user_role ?? '') === 'parent';

$this->load->view('components/page_header', [
    'title'      => $is_parent ? 'Aspirasi Saya' : 'Manajemen Aspirasi',
    'subtitle'   => $is_parent ? 'Sampaikan saran, kritik, atau pertanyaan Anda' : 'Kelola aspirasi dari orang tua siswa',
    'breadcrumb' => [['label' => 'Aspirasi']],
]);
?>

<?php if ($is_parent): ?>
    <!-- Parent: tombol kirim -->
    <div class="mb-6">
        <button type="button" onclick="openAddModal()" class="btn-primary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Kirim Aspirasi Baru
        </button>
    </div>
<?php endif; ?>

<!-- Stat -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <?php
    $total = count($aspirations ?? []);
    $pending = $dibaca = $diproses = $selesai = 0;
    foreach (($aspirations ?? []) as $a) {
        if ($a->status === 'pending')  $pending++;
        if ($a->status === 'dibaca')   $dibaca++;
        if ($a->status === 'diproses') $diproses++;
        if ($a->status === 'selesai')  $selesai++;
    }
    $this->load->view('components/stat_card', ['icon' => 'aspiration', 'label' => 'Total',     'value' => $total,     'color' => 'primary']);
    $this->load->view('components/stat_card', ['icon' => 'chart',      'label' => 'Pending',   'value' => $pending,   'color' => 'warning']);
    $this->load->view('components/stat_card', ['icon' => 'chat',       'label' => 'Diproses',  'value' => $diproses,  'color' => 'accent']);
    $this->load->view('components/stat_card', ['icon' => 'online',     'label' => 'Selesai',   'value' => $selesai,   'color' => 'success']);
    ?>
</div>

<div class="card">
    <div class="card-header">
        <div>
            <h3 class="font-display font-semibold text-neutral-900">Daftar Aspirasi</h3>
            <p class="text-xs text-neutral-500 mt-0.5"><?= $total ?> aspirasi</p>
        </div>
    </div>

    <div class="card-body">
        <!-- Filter -->
        <div class="flex flex-col sm:flex-row gap-3 mb-4">
            <div class="relative flex-1 sm:max-w-xs">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="w-4 h-4 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <input type="search" id="asp-search" class="form-input pl-9 text-sm" placeholder="Cari aspirasi...">
            </div>
            <select id="asp-status" class="form-select text-sm sm:max-w-[180px]">
                <option value="">Semua Status</option>
                <option value="pending">Pending</option>
                <option value="dibaca">Dibaca</option>
                <option value="diproses">Diproses</option>
                <option value="selesai">Selesai</option>
            </select>
            <select id="asp-category" class="form-select text-sm sm:max-w-[180px]">
                <option value="">Semua Kategori</option>
                <option value="saran">Saran</option>
                <option value="kritik">Kritik</option>
                <option value="keluhan">Keluhan</option>
                <option value="pertanyaan">Pertanyaan</option>
                <option value="lainnya">Lainnya</option>
            </select>
        </div>

        <!-- List -->
        <?php if (!empty($aspirations)): ?>
            <div class="space-y-3">
                <?php foreach ($aspirations as $asp): ?>
                    <?php
                    $statusColors = ['pending' => 'warning', 'dibaca' => 'primary', 'diproses' => 'accent', 'selesai' => 'success'];
                    $statusLabels = ['pending' => 'Pending', 'dibaca' => 'Dibaca', 'diproses' => 'Diproses', 'selesai' => 'Selesai'];
                    $color = $statusColors[$asp->status] ?? 'neutral';
                    ?>
                    <div class="asp-item border border-neutral-200 rounded-xl p-5 hover:shadow-card transition-shadow"
                         data-title="<?= html_escape(strtolower($asp->subject ?? '')) ?>"
                         data-content="<?= html_escape(strtolower(substr($asp->content ?? '', 0, 200))) ?>"
                         data-status="<?= html_escape($asp->status) ?>"
                         data-category="<?= html_escape($asp->category) ?>">
                        <div class="flex items-start justify-between gap-3 mb-3">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="badge-<?= $color ?>"><?= $statusLabels[$asp->status] ?? $asp->status ?></span>
                                <span class="badge-neutral"><?= html_escape(ucfirst($asp->category ?? 'umum')) ?></span>
                                <?php if (!empty($asp->attachment)): ?>
                                    <span class="text-xs text-primary-600 flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                                        </svg>
                                        Lampiran
                                    </span>
                                <?php endif; ?>
                            </div>
                            <span class="text-xs text-neutral-400 whitespace-nowrap">
                                <?= format_time_ago($asp->created_at ?? '') ?>
                            </span>
                        </div>

                        <a href="<?= base_url('aspirations/detail/' . $asp->id_aspiration) ?>" class="block group">
                            <h4 class="font-display font-semibold text-neutral-900 mb-1.5 group-hover:text-primary-600 transition-colors">
                                <?= html_escape($asp->subject ?? '-') ?>
                            </h4>
                            <p class="text-sm text-neutral-600 line-clamp-2">
                                <?= html_escape(substr(strip_tags($asp->content ?? ''), 0, 200)) ?>
                            </p>
                        </a>

                        <?php if ($is_admin_view): ?>
                            <div class="flex items-center justify-between mt-4 pt-4 border-t border-neutral-100">
                                <div class="flex items-center gap-2 text-xs text-neutral-500">
                                    <div class="w-6 h-6 rounded-full bg-primary-100 text-primary-600 flex items-center justify-center text-[10px] font-semibold">
                                        <?= strtoupper(substr($asp->parent_name ?? 'O', 0, 1)) ?>
                                    </div>
                                    <span><?= html_escape($asp->parent_name ?? '-') ?></span>
                                </div>
                                <div class="flex items-center gap-1">
                                    <a href="<?= base_url('aspirations/detail/' . $asp->id_aspiration) ?>"
                                       class="btn-ghost btn-sm">
                                        <?= $asp->status === 'selesai' ? 'Lihat' : 'Respon' ?>
                                    </a>
                                    <button type="button"
                                            onclick="confirmDelete(<?= (int)$asp->id_aspiration ?>, '<?= html_escape($asp->subject) ?>')"
                                            class="p-2 rounded-lg text-neutral-400 hover:text-danger-600 hover:bg-danger-50 transition-colors"
                                            title="Hapus">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>

            <div id="asp-no-result" class="hidden text-center py-12">
                <p class="text-sm text-neutral-500">Tidak ada aspirasi yang cocok</p>
            </div>
        <?php else: ?>
            <?php $this->load->view('components/empty_state', [
                'icon'       => 'aspiration',
                'title'      => $is_parent ? 'Belum Ada Aspirasi' : 'Belum Ada Aspirasi Masuk',
                'message'    => $is_parent ? 'Kirim aspirasi pertama Anda ke pihak sekolah.' : 'Aspirasi dari orang tua akan muncul di sini.',
                'action_url' => $is_parent ? 'javascript:openAddModal()' : null,
                'action_txt' => $is_parent ? 'Kirim Aspirasi' : null,
            ]); ?>
        <?php endif; ?>
    </div>
</div>

<?php if ($is_parent): ?>
<!-- MODAL: ADD -->
<?php ob_start(); ?>
<form id="form-add-aspiration" action="<?= base_url('aspirations/add') ?>" method="post" enctype="multipart/form-data">
    <input type="hidden" name="<?= $this->security->get_csrf_token_name() ?>" value="<?= $this->security->get_csrf_hash() ?>">
    <div class="space-y-4">
        <div>
            <label class="form-label">Subjek <span class="text-danger-500">*</span></label>
            <input type="text" name="subject" class="form-input" required maxlength="200" placeholder="Contoh: Saran untuk kegiatan ekstrakurikuler">
        </div>
        <div>
            <label class="form-label">Kategori <span class="text-danger-500">*</span></label>
            <select name="category" class="form-select" required>
                <option value="saran">Saran</option>
                <option value="kritik">Kritik</option>
                <option value="keluhan">Keluhan</option>
                <option value="pertanyaan">Pertanyaan</option>
                <option value="lainnya">Lainnya</option>
            </select>
        </div>
        <div>
            <label class="form-label">Isi Aspirasi <span class="text-danger-500">*</span></label>
            <textarea name="content" class="form-textarea" rows="5" required maxlength="5000" placeholder="Tulis aspirasi Anda secara detail..."></textarea>
        </div>
        <div>
            <label class="form-label">Lampiran (Opsional)</label>
            <input type="file" name="attachment" class="form-input text-sm" accept=".jpg,.jpeg,.png,.gif,.webp,.pdf,.doc,.docx">
            <p class="form-help">Maks 2MB. Gambar akan otomatis dikonversi ke WebP.</p>
        </div>
    </div>
    <div class="flex items-center justify-end gap-2 mt-6 pt-5 border-t border-neutral-100">
        <button type="button" onclick="closeModal('modal-add-aspiration')" class="btn-outline">Batal</button>
        <button type="submit" class="btn-primary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
            </svg>
            Kirim Aspirasi
        </button>
    </div>
</form>
<?php $modal_add = ob_get_clean(); ?>
<?php $this->load->view('components/modal', ['id' => 'modal-add-aspiration', 'title' => 'Kirim Aspirasi Baru', 'size' => 'lg', 'content' => $modal_add]); ?>
<?php endif; ?>

<?php if ($is_admin_view): ?>
<!-- MODAL: DELETE -->
<?php ob_start(); ?>
<form id="form-delete-aspiration" action="<?= base_url('aspirations/delete') ?>" method="post">
    <input type="hidden" name="<?= $this->security->get_csrf_token_name() ?>" value="<?= $this->security->get_csrf_hash() ?>">
    <input type="hidden" name="id_aspiration" id="delete-asp-id">
    <div class="flex items-start gap-4">
        <div class="w-12 h-12 rounded-full bg-danger-100 text-danger-600 flex items-center justify-center flex-shrink-0">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
        </div>
        <div class="flex-1">
            <h4 class="font-display font-semibold text-neutral-900 mb-1">Hapus Aspirasi?</h4>
            <p class="text-sm text-neutral-600">
                Aspirasi <span class="font-medium text-neutral-900" id="delete-asp-title">-</span> akan dihapus permanen.
            </p>
        </div>
    </div>
    <div class="flex items-center justify-end gap-2 mt-6 pt-5 border-t border-neutral-100">
        <button type="button" onclick="closeModal('modal-delete-aspiration')" class="btn-outline">Batal</button>
        <button type="submit" class="btn-danger">Ya, Hapus</button>
    </div>
</form>
<?php $modal_del = ob_get_clean(); ?>
<?php $this->load->view('components/modal', ['id' => 'modal-delete-aspiration', 'title' => 'Konfirmasi Hapus', 'size' => 'sm', 'content' => $modal_del]); ?>
<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchEl   = document.getElementById('asp-search');
    const statusEl   = document.getElementById('asp-status');
    const categoryEl = document.getElementById('asp-category');

    function filter() {
        const q = (searchEl?.value || '').toLowerCase().trim();
        const s = statusEl?.value || '';
        const c = categoryEl?.value || '';
        const items = document.querySelectorAll('.asp-item');
        let visible = 0;

        items.forEach(item => {
            const title   = item.dataset.title || '';
            const content = item.dataset.content || '';
            const status  = item.dataset.status || '';
            const cat     = item.dataset.category || '';
            const match = (!q || title.includes(q) || content.includes(q))
                       && (!s || status === s)
                       && (!c || cat === c);
            item.style.display = match ? '' : 'none';
            if (match) visible++;
        });

        document.getElementById('asp-no-result')?.classList.toggle('hidden', visible > 0 || items.length === 0);
    }

    searchEl?.addEventListener('input', filter);
    statusEl?.addEventListener('change', filter);
    categoryEl?.addEventListener('change', filter);
});

function openAddModal() {
    document.getElementById('form-add-aspiration')?.reset();
    openModal('modal-add-aspiration');
}

function confirmDelete(id, title) {
    document.getElementById('delete-asp-id').value = id;
    document.getElementById('delete-asp-title').textContent = title;
    openModal('modal-delete-aspiration');
}
</script>
