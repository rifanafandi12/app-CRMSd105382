<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<?php
$this->load->view('components/page_header', [
    'title'      => 'Detail Pengumuman',
    'breadcrumb' => [
        ['label' => 'Pengumuman', 'url' => 'announcements'],
        ['label' => 'Detail'],
    ],
]);
?>

<div class="grid lg:grid-cols-3 gap-4">

    <!-- Konten Utama -->
    <div class="lg:col-span-2">
        <div class="card">
            <div class="card-body">
                <!-- Meta -->
                <div class="flex flex-wrap items-center gap-3 mb-4">
                    <span class="badge-<?= get_category_color($announcement->category) ?>">
                        <?= html_escape(ucfirst($announcement->category ?? 'umum')) ?>
                    </span>
                    <div class="flex items-center gap-1 text-xs text-neutral-500">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <?= format_date($announcement->posted_date ?? '') ?>
                    </div>
                    <div class="flex items-center gap-1 text-xs text-neutral-500">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        <?= number_format($announcement->view_count ?? 0) ?> views
                    </div>
                    <?php if (!$announcement->is_published): ?>
                        <span class="badge-neutral">Draft</span>
                    <?php endif; ?>
                </div>

                <!-- Judul -->
                <h1 class="font-display font-bold text-2xl lg:text-3xl text-neutral-900 mb-4">
                    <?= html_escape($announcement->title) ?>
                </h1>

                <!-- Author -->
                <div class="flex items-center gap-2 pb-5 mb-5 border-b border-neutral-100">
                    <div class="w-8 h-8 rounded-full bg-primary-100 text-primary-600 flex items-center justify-center text-xs font-semibold">
                        <?= strtoupper(substr($announcement->author_name ?? 'A', 0, 1)) ?>
                    </div>
                    <div class="text-xs">
                        <div class="font-medium text-neutral-800"><?= html_escape($announcement->author_name ?? 'Admin') ?></div>
                        <div class="text-neutral-500"><?= format_time_ago($announcement->posted_date ?? '') ?></div>
                    </div>
                </div>

                <!-- Content -->
                <div class="prose prose-sm max-w-none text-neutral-700 leading-relaxed">
                    <?= nl2br(html_escape($announcement->content ?? '')) ?>
                </div>

                <!-- Attachment -->
                <?php if (!empty($announcement->attachment)): ?>
                    <div class="mt-6 p-4 rounded-lg bg-primary-50 border border-primary-100">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-primary-600 text-white flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <div class="font-medium text-primary-900 text-sm">Lampiran Tersedia</div>
                                <div class="text-xs text-primary-700">Klik untuk mengunduh</div>
                            </div>
                            <a href="<?= base_url($announcement->attachment) ?>"
                               target="_blank"
                               download
                               class="btn-primary btn-sm">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                </svg>
                                Unduh
                            </a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex flex-wrap gap-2 mt-4">
            <a href="<?= base_url('announcements') ?>" class="btn-outline">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Kembali
            </a>
            <?php if ($user_role === 'admin'): ?>
                <a href="<?= base_url('announcements/edit/' . $announcement->id_announcement) ?>" class="btn-outline">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Edit
                </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Sidebar Info -->
    <div class="space-y-4">
        <div class="card">
            <div class="card-header">
                <h3 class="font-display font-semibold text-neutral-900 text-sm">Informasi</h3>
            </div>
            <div class="card-body space-y-3 text-sm">
                <div class="flex justify-between">
                    <span class="text-neutral-500">Kategori</span>
                    <span class="font-medium text-neutral-800">
                        <?= html_escape(ucfirst($announcement->category ?? 'umum')) ?>
                    </span>
                </div>
                <div class="flex justify-between">
                    <span class="text-neutral-500">Diposting</span>
                    <span class="font-medium text-neutral-800">
                        <?= format_date($announcement->posted_date ?? '') ?>
                    </span>
                </div>
                <?php if (!empty($announcement->expiry_date)): ?>
                    <div class="flex justify-between">
                        <span class="text-neutral-500">Berlaku Sampai</span>
                        <span class="font-medium text-neutral-800">
                            <?= format_date($announcement->expiry_date) ?>
                        </span>
                    </div>
                <?php endif; ?>
                <div class="flex justify-between">
                    <span class="text-neutral-500">Views</span>
                    <span class="font-medium text-neutral-800">
                        <?= number_format($announcement->view_count ?? 0) ?>
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>
