<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<section class="bg-gradient-to-br from-primary-700 via-primary-600 to-primary-800 py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-white/10 backdrop-blur-sm rounded-full text-white text-xs font-medium mb-4">
            <span class="w-2 h-2 rounded-full bg-accent-400"></span>
            Dokumentasi Kegiatan
        </div>
        <h1 class="font-display font-bold text-3xl lg:text-4xl text-white mb-4">Galeri Sekolah</h1>
        <p class="text-primary-100 max-w-2xl mx-auto">
            Kumpulan momen berharga, prestasi, dan fasilitas SD Negeri 105382 Galang.
        </p>
    </div>
</section>

<section class="py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Filter Kategori -->
        <div class="flex flex-wrap justify-center gap-2 mb-8">
            <a href="<?= base_url('landing/gallery') ?>"
               class="px-4 py-2 rounded-full text-sm font-medium transition-colors <?= ($category ?? 'all') === 'all' ? 'bg-primary-600 text-white' : 'bg-white text-neutral-600 hover:bg-neutral-100 border border-neutral-200' ?>">
                Semua
            </a>
            <?php foreach (($categories ?? []) as $key => $label): ?>
                <a href="<?= base_url('landing/gallery?category=' . urlencode($key)) ?>"
                   class="px-4 py-2 rounded-full text-sm font-medium transition-colors <?= ($category ?? '') === $key ? 'bg-primary-600 text-white' : 'bg-white text-neutral-600 hover:bg-neutral-100 border border-neutral-200' ?>">
                    <?= html_escape($label) ?>
                </a>
            <?php endforeach; ?>
        </div>

        <!-- Gallery Grid -->
        <?php if (!empty($gallery)): ?>
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
                <?php foreach ($gallery as $g): ?>
                    <a href="<?= base_url('landing/gallery_detail/' . $g->id_gallery) ?>"
                       class="group relative aspect-square rounded-xl overflow-hidden border border-neutral-200 bg-neutral-100">
                        <img src="<?= base_url($g->image_path) ?>"
                             alt="<?= html_escape($g->title) ?>"
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                        <div class="absolute inset-0 bg-gradient-to-t from-neutral-900/80 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex items-end p-4">
                            <div class="text-white">
                                <p class="text-sm font-medium line-clamp-2"><?= html_escape($g->title) ?></p>
                                <p class="text-[10px] text-white/70 mt-1 uppercase tracking-wide">
                                    <?= html_escape(ucfirst($g->category ?? '')) ?>
                                </p>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>

            <!-- Pagination -->
            <?php if (($total_pages ?? 1) > 1): ?>
                <div class="flex items-center justify-center gap-2 mt-10">
                    <?php if (($current_page ?? 1) > 1): ?>
                        <a href="<?= base_url('landing/gallery?page=' . ($current_page - 1) . '&category=' . urlencode($category ?? '')) ?>"
                           class="btn-outline btn-sm">
                            Sebelumnya
                        </a>
                    <?php endif; ?>
                    <span class="text-sm text-neutral-500 px-4">
                        Halaman <?= $current_page ?> dari <?= $total_pages ?>
                    </span>
                    <?php if (($current_page ?? 1) < ($total_pages ?? 1)): ?>
                        <a href="<?= base_url('landing/gallery?page=' . ($current_page + 1) . '&category=' . urlencode($category ?? '')) ?>"
                           class="btn-outline btn-sm">
                            Selanjutnya
                        </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="text-center py-16">
                <svg class="w-20 h-20 text-neutral-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <h3 class="font-display font-semibold text-neutral-900 mb-2">Belum Ada Foto</h3>
                <p class="text-sm text-neutral-500">Galeri akan muncul di sini.</p>
            </div>
        <?php endif; ?>
    </div>
</section>
