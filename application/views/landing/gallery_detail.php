<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<article class="py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Breadcrumb -->
        <nav class="flex items-center gap-2 text-xs text-neutral-500 mb-6">
            <a href="<?= base_url() ?>" class="hover:text-primary-600">Beranda</a>
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <a href="<?= base_url('landing/gallery') ?>" class="hover:text-primary-600">Galeri</a>
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <span class="text-neutral-700 font-medium truncate"><?= html_escape($gallery->title) ?></span>
        </nav>

        <!-- Image -->
        <div class="rounded-2xl overflow-hidden shadow-xl bg-neutral-100">
            <img src="<?= base_url($gallery->image_path) ?>"
                 alt="<?= html_escape($gallery->title) ?>"
                 class="w-full h-auto object-contain max-h-[70vh]">
        </div>

        <!-- Info -->
        <div class="mt-6">
            <div class="flex flex-wrap items-center gap-3 mb-4">
                <span class="badge-<?= get_category_color($gallery->category) ?>">
                    <?= html_escape(ucfirst($gallery->category ?? '')) ?>
                </span>
                <span class="text-xs text-neutral-500">
                    <svg class="w-3.5 h-3.5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <?= format_date($gallery->created_at ?? '') ?>
                </span>
                <span class="text-xs text-neutral-500">
                    <svg class="w-3.5 h-3.5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    <?= number_format($gallery->view_count ?? 0) ?> views
                </span>
            </div>

            <h1 class="font-display font-bold text-2xl lg:text-3xl text-neutral-900 mb-3">
                <?= html_escape($gallery->title) ?>
            </h1>

            <?php if (!empty($gallery->description)): ?>
                <p class="text-neutral-600 leading-relaxed">
                    <?= nl2br(html_escape($gallery->description)) ?>
                </p>
            <?php endif; ?>
        </div>

        <!-- Related Gallery -->
        <?php if (!empty($related)): ?>
            <div class="mt-12 pt-8 border-t border-neutral-200">
                <h3 class="font-display font-bold text-lg text-neutral-900 mb-4">Foto Terkait</h3>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    <?php foreach ($related as $r): ?>
                        <a href="<?= base_url('landing/gallery_detail/' . $r->id_gallery) ?>"
                           class="group aspect-square rounded-lg overflow-hidden border border-neutral-200">
                            <img src="<?= base_url($r->image_path) ?>"
                                 alt="<?= html_escape($r->title) ?>"
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Back -->
        <div class="mt-10 pt-6 border-t border-neutral-200">
            <a href="<?= base_url('landing/gallery') ?>" class="btn-outline">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Kembali ke Galeri
            </a>
        </div>
    </article>
