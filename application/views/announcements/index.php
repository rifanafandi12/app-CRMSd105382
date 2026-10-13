<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<?php
$this->load->view('components/page_header', [
    'title'      => 'Pengumuman Sekolah',
    'subtitle'   => $user_role === 'admin' ? 'Kelola pengumuman untuk wali murid & guru' : 'Informasi terbaru dari sekolah',
    'breadcrumb' => [['label' => 'Pengumuman']],
]);
?>

<!-- Stat untuk admin -->
<?php if ($user_role === 'admin' && !empty($announcements)): ?>
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <?php
        $total     = count($announcements);
        $published = 0;
        $draft     = 0;
        $expired   = 0;
        $today     = date('Y-m-d');

        foreach ($announcements as $a) {
            if ($a->is_published) $published++;
            else $draft++;
            if (!empty($a->expiry_date) && $a->expiry_date < $today) $expired++;
        }

        $this->load->view('components/stat_card', [
            'icon'  => 'announcement',
            'label' => 'Total Pengumuman',
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
            'label' => 'Kedaluwarsa',
            'value' => number_format($expired),
            'color' => 'danger',
        ]);
        ?>
    </div>
<?php endif; ?>

<!-- Main Card -->
<div class="card">
    <div class="card-header">
        <div>
            <h3 class="font-display font-semibold text-neutral-900">Daftar Pengumuman</h3>
            <p class="text-xs text-neutral-500 mt-0.5">
                <?= count($announcements) ?> pengumuman
            </p>
        </div>
        <?php if ($user_role === 'admin'): ?>
            <button type="button" onclick="openAddModal()" class="btn-primary">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Buat Pengumuman
            </button>
        <?php endif; ?>
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
                <input type="search" id="ann-search" class="form-input pl-9 text-sm" placeholder="Cari pengumuman...">
            </div>

            <select id="ann-category" class="form-select text-sm sm:max-w-[180px]">
                <option value="">Semua Kategori</option>
                <?php foreach (($categories ?? []) as $key => $label): ?>
                    <option value="<?= html_escape($key) ?>"><?= html_escape($label) ?></option>
                <?php endforeach; ?>
            </select>

            <?php if ($user_role === 'admin'): ?>
                <select id="ann-status" class="form-select text-sm sm:max-w-[160px]">
                    <option value="">Semua Status</option>
                    <option value="published">Dipublikasikan</option>
                    <option value="draft">Draft</option>
                </select>
            <?php endif; ?>
        </div>

        <!-- Table (admin) atau Cards (user) -->
        <?php if (!empty($announcements)): ?>
            <?php if ($user_role === 'admin'): ?>
                <!-- ADMIN: Table view -->
                <div class="table-wrapper">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Judul</th>
                                <th class="w-32">Kategori</th>
                                <th class="w-40">Tanggal</th>
                                <th class="w-32">Status</th>
                                <th class="w-24 text-center">Views</th>
                                <th class="w-32 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($announcements as $ann): ?>
                                <tr class="ann-row"
                                    data-title="<?= html_escape(strtolower($ann->title ?? '')) ?>"
                                    data-category="<?= html_escape($ann->category ?? '') ?>"
                                    data-status="<?= $ann->is_published ? 'published' : 'draft' ?>">
                                    <td>
                                        <div class="flex items-start gap-3">
                                            <div class="w-10 h-10 rounded-lg bg-<?= $this->get_category_color($ann->category) ?>-100 text-<?= $this->get_category_color($ann->category) ?>-600 flex items-center justify-center flex-shrink-0">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                          d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
                                                </svg>
                                            </div>
                                            <div class="min-w-0">
                                                <div class="font-medium text-neutral-900 line-clamp-1">
                                                    <?= html_escape($ann->title ?? '-') ?>
                                                </div>
                                                <div class="text-xs text-neutral-500 line-clamp-1 mt-0.5">
                                                    <?= html_escape(substr(strip_tags($ann->content ?? ''), 0, 80)) ?>...
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge-<?= get_category_color($ann->category) ?>">
                                            <?= html_escape(ucfirst($ann->category ?? 'umum')) ?>
                                        </span>
                                    </td>
                                    <td class="text-xs text-neutral-600">
                                        <?= format_date($ann->posted_date ?? '') ?>
                                    </td>
                                    <td>
                                        <?php if ($ann->is_published): ?>
                                            <button type="button"
                                                    onclick="togglePublish(<?= (int)$ann->id_announcement ?>)"
                                                    class="badge-success cursor-pointer hover:opacity-80">
                                                <span class="w-1.5 h-1.5 rounded-full bg-success-600"></span>
                                                Publish
                                            </button>
                                        <?php else: ?>
                                            <button type="button"
                                                    onclick="togglePublish(<?= (int)$ann->id_announcement ?>)"
                                                    class="badge-neutral cursor-pointer hover:opacity-80">
                                                <span class="w-1.5 h-1.5 rounded-full bg-neutral-400"></span>
                                                Draft
                                            </button>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center text-neutral-600 text-xs">
                                        <?= number_format($ann->view_count ?? 0) ?>
                                    </td>
                                    <td>
                                        <div class="flex items-center justify-center gap-1">
                                            <a href="<?= base_url('announcements/detail/' . $ann->id_announcement) ?>"
                                               class="p-2 rounded-lg text-neutral-500 hover:text-primary-600 hover:bg-primary-50 transition-colors"
                                               title="Lihat">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                          d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                          d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                </svg>
                                            </a>
                                            <button type="button"
                                                    onclick='openEditModal(<?= json_encode($ann) ?>)'
                                                    class="p-2 rounded-lg text-neutral-500 hover:text-primary-600 hover:bg-primary-50 transition-colors"
                                                    title="Edit">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                          d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                            </button>
                                            <button type="button"
                                                    onclick="confirmDelete(<?= (int)$ann->id_announcement ?>, '<?= html_escape($ann->title) ?>')"
                                                    class="p-2 rounded-lg text-neutral-500 hover:text-danger-600 hover:bg-danger-50 transition-colors"
                                                    title="Hapus">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <!-- USER (teacher/parent): Card grid -->
                <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    <?php foreach ($announcements as $ann): ?>
                        <a href="<?= base_url('announcements/detail/' . $ann->id_announcement) ?>"
                           class="card-hover p-5 block">
                            <div class="flex items-center gap-2 mb-3">
                                <span class="badge-<?= get_category_color($ann->category) ?>">
                                    <?= html_escape(ucfirst($ann->category ?? 'umum')) ?>
                                </span>
                                <span class="text-xs text-neutral-400">
                                    <?= format_time_ago($ann->posted_date ?? '') ?>
                                </span>
                            </div>
                            <h3 class="font-display font-semibold text-base text-neutral-900 mb-2 line-clamp-2">
                                <?= html_escape($ann->title ?? '-') ?>
                            </h3>
                            <p class="text-sm text-neutral-600 line-clamp-3">
                                <?= html_escape(substr(strip_tags($ann->content ?? ''), 0, 120)) ?>...
                            </p>
                            <?php if (!empty($ann->attachment)): ?>
                                <div class="flex items-center gap-1.5 mt-3 text-xs text-primary-600">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                                    </svg>
                                    Ada lampiran
                                </div>
                            <?php endif; ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <?php $this->load->view('components/empty_state', [
                'icon'       => 'announcement',
                'title'      => 'Belum Ada Pengumuman',
                'message'    => $user_role === 'admin' ? 'Mulai dengan membuat pengumuman pertama.' : 'Pengumuman dari sekolah akan muncul di sini.',
                'action_url' => $user_role === 'admin' ? 'javascript:openAddModal()' : null,
                'action_txt' => $user_role === 'admin' ? 'Buat Pengumuman' : null,
            ]); ?>
        <?php endif; ?>

        <!-- No result -->
        <div id="ann-no-result" class="hidden text-center py-12">
            <svg class="w-16 h-16 text-neutral-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                      d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <p class="text-sm text-neutral-500">Tidak ada pengumuman yang cocok</p>
        </div>
    </div>
</div>

<?php if ($user_role === 'admin'): ?>

    <!-- MODAL: TAMBAH -->
    <?php ob_start(); ?>
    <?php $this->load->view('announcements/form', ['is_edit' => false, 'classes' => $classes, 'categories' => $categories]); ?>
    <?php $modal_add = ob_get_clean(); ?>
    <?php $this->load->view('components/modal', [
        'id'      => 'modal-add-ann',
        'title'   => 'Buat Pengumuman Baru',
        'size'    => 'lg',
        'content' => $modal_add,
    ]); ?>

    <!-- MODAL: EDIT -->
    <?php ob_start(); ?>
    <?php $this->load->view('announcements/form', ['is_edit' => true, 'classes' => $classes, 'categories' => $categories]); ?>
    <?php $modal_edit = ob_get_clean(); ?>
    <?php $this->load->view('components/modal', [
        'id'      => 'modal-edit-ann',
        'title'   => 'Edit Pengumuman',
        'size'    => 'lg',
        'content' => $modal_edit,
    ]); ?>

    <!-- MODAL: DELETE -->
    <?php ob_start(); ?>
    <form id="form-delete-ann" action="<?= base_url('announcements/delete') ?>" method="post">
        <input type="hidden" name="<?= $this->security->get_csrf_token_name() ?>" value="<?= $this->security->get_csrf_hash() ?>">
        <input type="hidden" name="id_announcement" id="delete-ann-id">

        <div class="flex items-start gap-4">
            <div class="w-12 h-12 rounded-full bg-danger-100 text-danger-600 flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <div class="flex-1">
                <h4 class="font-display font-semibold text-neutral-900 mb-1">Hapus Pengumuman?</h4>
                <p class="text-sm text-neutral-600">Pengumuman <span class="font-medium text-neutral-900" id="delete-ann-title">-</span> akan dihapus permanen.</p>
            </div>
        </div>

        <div class="flex items-center justify-end gap-2 mt-6 pt-5 border-t border-neutral-100">
            <button type="button" onclick="closeModal('modal-delete-ann')" class="btn-outline">Batal</button>
            <button type="submit" class="btn-danger">Ya, Hapus</button>
        </div>
    </form>
    <?php $modal_del = ob_get_clean(); ?>
    <?php $this->load->view('components/modal', [
        'id'      => 'modal-delete-ann',
        'title'   => 'Konfirmasi Hapus',
        'size'    => 'sm',
        'content' => $modal_del,
    ]); ?>

<?php endif; ?>

<script>
    window.BASE_URL = "<?= base_url() ?>";
    window.USER_ROLE = "<?= $user_role ?>";
</script>
<script src="<?= base_url('assets/js/announcements.js') ?>"></script>
