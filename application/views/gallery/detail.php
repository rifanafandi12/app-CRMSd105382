<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<?php
$this->load->view('components/page_header', [
    'title'      => 'Detail Galeri',
    'breadcrumb' => [
        ['label' => 'Galeri', 'url' => 'gallery'],
        ['label' => 'Detail'],
    ],
]);
?>

<div class="grid lg:grid-cols-3 gap-4">

    <!-- Image -->
    <div class="lg:col-span-2">
        <div class="card overflow-hidden">
            <img src="<?= base_url($item->image_path) ?>"
                 alt="<?= html_escape($item->title) ?>"
                 class="w-full h-auto object-contain bg-neutral-100 max-h-[600px]">
        </div>

        <div class="flex flex-wrap gap-2 mt-4">
            <a href="<?= base_url('gallery') ?>" class="btn-outline">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Kembali
            </a>
        </div>
    </div>

    <!-- Info -->
    <div class="space-y-4">
        <div class="card">
            <div class="card-body">
                <div class="flex items-center gap-2 mb-4">
                    <span class="badge-<?= get_category_color($item->category) ?>">
                        <?= html_escape(ucfirst($item->category ?? '')) ?>
                    </span>
                    <?php if ($item->is_published): ?>
                        <span class="badge-success">Published</span>
                    <?php else: ?>
                        <span class="badge-neutral">Draft</span>
                    <?php endif; ?>
                </div>

                <h1 class="font-display font-bold text-xl text-neutral-900 mb-2">
                    <?= html_escape($item->title) ?>
                </h1>

                <?php if (!empty($item->description)): ?>
                    <p class="text-sm text-neutral-600 leading-relaxed mb-4">
                        <?= nl2br(html_escape($item->description)) ?>
                    </p>
                <?php endif; ?>

                <div class="pt-4 border-t border-neutral-100 space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-neutral-500">Uploader</span>
                        <span class="font-medium text-neutral-800">
                            <?= html_escape($item->uploader_name ?? 'Unknown') ?>
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-neutral-500">Tanggal</span>
                        <span class="font-medium text-neutral-800">
                            <?= format_date($item->created_at ?? '') ?>
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-neutral-500">Views</span>
                        <span class="font-medium text-neutral-800">
                            <?= number_format($item->view_count ?? 0) ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
