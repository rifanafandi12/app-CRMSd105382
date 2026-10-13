<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<?php
$this->load->view('components/page_header', [
    'title'      => 'Kelola Galeri',
    'subtitle'   => 'Kelola foto kegiatan, prestasi, dan fasilitas sekolah',
    'breadcrumb' => [['label' => 'Galeri']],
]);
?>

<!-- Stat Cards -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <?php
    $total     = count($gallery ?? []);
    $published = 0;
    $draft     = 0;
    $categories_count = [];

    foreach (($gallery ?? []) as $g) {
        if ($g->is_published) $published++;
        else $draft++;

        $cat = $g->category ?? 'lainnya';
        $categories_count[$cat] = ($categories_count[$cat] ?? 0) + 1;
    }

    $this->load->view('components/stat_card', [
        'icon'  => 'gallery',
        'label' => 'Total Foto',
        'value' => number_format($total),
        'color' => 'primary',
    ]);
    $this->load->view('components/stat_card', [
        'icon'  => 'online',
        'label' => 'Dipublikasikan',
        'value' => number_format($published),
        'color' => 'success',
    ]);
    $this->load->view('components/stat_card', [
        'icon'  => 'chart',
        'label' => 'Draft',
        'value' => number_format($draft),
        'color' => 'warning',
    ]);
    $this->load->view('components/stat_card', [
        'icon'  => 'inbox',
        'label' => 'Kategori Aktif',
        'value' => count($categories_count),
        'color' => 'accent',
    ]);
    ?>
</div>

<!-- Main Card -->
<div class="card">
    <div class="card-header">
        <div>
            <h3 class="font-display font-semibold text-neutral-900">Galeri Foto</h3>
            <p class="text-xs text-neutral-500 mt-0.5"><?= count($gallery) ?> foto</p>
        </div>
        <button type="button" onclick="openAddModal()" class="btn-primary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Foto
        </button>
    </div>

    <div class="card-body">
        <!-- Toolbar -->
        <div class="flex flex-col sm:flex-row gap-3 mb-4">
            <div class="relative flex-1 sm:max-w-xs">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="w-4 h-4 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <input type="search" id="gallery-search" class="form-input pl-9 text-sm" placeholder="Cari foto...">
            </div>

            <select id="gallery-category" class="form-select text-sm sm:max-w-[200px]">
                <option value="">Semua Kategori</option>
                <?php foreach (($categories ?? []) as $key => $label): ?>
                    <option value="<?= html_escape($key) ?>"><?= html_escape($label) ?></option>
                <?php endforeach; ?>
            </select>

            <select id="gallery-status" class="form-select text-sm sm:max-w-[160px]">
                <option value="">Semua Status</option>
                <option value="published">Dipublikasikan</option>
                <option value="draft">Draft</option>
            </select>
        </div>

        <!-- Grid -->
        <?php if (!empty($gallery)): ?>
            <div id="gallery-grid" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
                <?php foreach ($gallery as $g): ?>
                    <div class="gallery-item group relative aspect-square rounded-xl overflow-hidden border border-neutral-200 bg-neutral-100 hover:shadow-elevated transition-all"
                         data-title="<?= html_escape(strtolower($g->title ?? '')) ?>"
                         data-category="<?= html_escape($g->category ?? '') ?>"
                         data-status="<?= $g->is_published ? 'published' : 'draft' ?>">

                        <img src="<?= base_url($g->image_path) ?>"
                             alt="<?= html_escape($g->title ?? '') ?>"
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300 cursor-pointer"
                             onclick="openLightbox(<?= (int)$g->id_gallery ?>, '<?= html_escape($g->title) ?>', '<?= base_url($g->image_path) ?>')">

                        <!-- Overlay actions -->
                        <div class="absolute inset-0 bg-gradient-to-t from-neutral-900/80 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity">
                            <div class="absolute top-2 right-2 flex items-center gap-1">
                                <button type="button"
                                        onclick='openEditModal(<?= json_encode($g) ?>)'
                                        class="w-8 h-8 rounded-lg bg-white/95 text-neutral-700 hover:text-primary-600 flex items-center justify-center transition-colors"
                                        title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </button>
                                <button type="button"
                                        onclick="confirmDelete(<?= (int)$g->id_gallery ?>, '<?= html_escape($g->title) ?>')"
                                        class="w-8 h-8 rounded-lg bg-white/95 text-neutral-700 hover:text-danger-600 flex items-center justify-center transition-colors"
                                        title="Hapus">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </div>

                            <div class="absolute bottom-0 left-0 right-0 p-3">
                                <div class="text-white text-xs font-medium line-clamp-2 mb-1">
                                    <?= html_escape($g->title ?? '') ?>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="text-[10px] text-white/70 uppercase tracking-wide">
                                        <?= html_escape(ucfirst($g->category ?? '')) ?>
                                    </span>
                                    <?php if ($g->is_published): ?>
                                        <button type="button"
                                                onclick="togglePublish(<?= (int)$g->id_gallery ?>)"
                                                class="badge-success text-[10px] cursor-pointer">
                                            Publish
                                        </button>
                                    <?php else: ?>
                                        <button type="button"
                                                onclick="togglePublish(<?= (int)$g->id_gallery ?>)"
                                                class="badge-neutral text-[10px] cursor-pointer">
                                            Draft
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- No result -->
            <div id="gallery-no-result" class="hidden text-center py-12">
                <svg class="w-16 h-16 text-neutral-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <p class="text-sm text-neutral-500">Tidak ada foto yang cocok</p>
            </div>
        <?php else: ?>
            <?php $this->load->view('components/empty_state', [
                'icon'       => 'gallery',
                'title'      => 'Belum Ada Foto',
                'message'    => 'Mulai dengan menambahkan foto kegiatan sekolah.',
                'action_url' => 'javascript:openAddModal()',
                'action_txt' => 'Tambah Foto',
            ]); ?>
        <?php endif; ?>
    </div>
</div>

<!-- MODAL: LIGHTBOX -->
<div id="lightbox" class="fixed inset-0 z-[60] hidden bg-neutral-900/95 backdrop-blur-sm" onclick="closeLightbox()">
    <div class="absolute top-4 right-4 flex items-center gap-2">
        <button onclick="closeLightbox()" class="w-10 h-10 rounded-full bg-white/10 text-white hover:bg-white/20 flex items-center justify-center transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>
    <div class="h-full flex items-center justify-center p-4">
        <div onclick="event.stopPropagation()">
            <img id="lightbox-img" src="" alt="" class="max-w-full max-h-[85vh] object-contain rounded-lg shadow-2xl">
            <div class="text-center mt-4 text-white">
                <p id="lightbox-title" class="font-display font-semibold"></p>
            </div>
        </div>
    </div>
</div>

<!-- MODAL: TAMBAH -->
<?php ob_start(); ?>
<?php $this->load->view('gallery/form', ['is_edit' => false, 'categories' => $categories]); ?>
<?php $modal_add = ob_get_clean(); ?>
<?php $this->load->view('components/modal', [
    'id'      => 'modal-add-gallery',
    'title'   => 'Tambah Foto ke Galeri',
    'size'    => 'lg',
    'content' => $modal_add,
]); ?>

<!-- MODAL: EDIT -->
<?php ob_start(); ?>
<?php $this->load->view('gallery/form', ['is_edit' => true, 'categories' => $categories]); ?>
<?php $modal_edit = ob_get_clean(); ?>
<?php $this->load->view('components/modal', [
    'id'      => 'modal-edit-gallery',
    'title'   => 'Edit Foto Galeri',
    'size'    => 'lg',
    'content' => $modal_edit,
]); ?>

<!-- MODAL: DELETE -->
<?php ob_start(); ?>
<form id="form-delete-gallery" action="<?= base_url('gallery/delete') ?>" method="post">
    <input type="hidden" name="<?= $this->security->get_csrf_token_name() ?>" value="<?= $this->security->get_csrf_hash() ?>">
    <input type="hidden" name="id_gallery" id="delete-gallery-id">

    <div class="flex items-start gap-4">
        <div class="w-12 h-12 rounded-full bg-danger-100 text-danger-600 flex items-center justify-center flex-shrink-0">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
        </div>
        <div class="flex-1">
            <h4 class="font-display font-semibold text-neutral-900 mb-1">Hapus Foto?</h4>
            <p class="text-sm text-neutral-600">
                Foto <span class="font-medium text-neutral-900" id="delete-gallery-title">-</span> akan dihapus permanen.
            </p>
        </div>
    </div>

    <div class="flex items-center justify-end gap-2 mt-6 pt-5 border-t border-neutral-100">
        <button type="button" onclick="closeModal('modal-delete-gallery')" class="btn-outline">Batal</button>
        <button type="submit" class="btn-danger">Ya, Hapus</button>
    </div>
</form>
<?php $modal_del = ob_get_clean(); ?>
<?php $this->load->view('components/modal', [
    'id'      => 'modal-delete-gallery',
    'title'   => 'Konfirmasi Hapus',
    'size'    => 'sm',
    'content' => $modal_del,
]); ?>

<script>
    window.BASE_URL = "<?= base_url() ?>";
</script>
<script src="<?= base_url('assets/js/gallery.js') ?>"></script>
