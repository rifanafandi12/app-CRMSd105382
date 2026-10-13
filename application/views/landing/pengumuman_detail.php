<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<article class="py-12">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Breadcrumb -->
        <nav class="flex items-center gap-2 text-xs text-neutral-500 mb-6">
            <a href="<?= base_url() ?>" class="hover:text-primary-600">Beranda</a>
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <a href="<?= base_url('landing/pengumuman') ?>" class="hover:text-primary-600">Pengumuman</a>
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <span class="text-neutral-700 font-medium">Detail</span>
        </nav>

        <!-- Meta -->
        <div class="flex flex-wrap items-center gap-3 mb-4">
            <span class="badge-primary"><?= html_escape(ucfirst($announcement->category ?? 'umum')) ?></span>
            <span class="text-xs text-neutral-500"><?= format_date($announcement->posted_date ?? '') ?></span>
        </div>

        <!-- Judul -->
        <h1 class="font-display font-bold text-3xl lg:text-4xl text-neutral-900 mb-6 leading-tight">
            <?= html_escape($announcement->title) ?>
        </h1>

        <!-- Content -->
        <div class="prose prose-lg max-w-none text-neutral-700 leading-relaxed">
            <?= nl2br(html_escape($announcement->content ?? '')) ?>
        </div>

        <!-- Attachment -->
        <?php if (!empty($announcement->attachment)): ?>
            <div class="mt-8 p-5 rounded-xl bg-primary-50 border border-primary-100">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-lg bg-primary-600 text-white flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <div class="font-medium text-primary-900">Lampiran Tersedia</div>
                        <div class="text-xs text-primary-700">Klik tombol di samping untuk mengunduh</div>
                    </div>
                    <a href="<?= base_url($announcement->attachment) ?>" target="_blank" download class="btn-primary">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        Unduh
                    </a>
                </div>
            </div>
        <?php endif; ?>

        <!-- Back -->
        <div class="mt-10 pt-6 border-t border-neutral-200">
            <a href="<?= base_url('landing/pengumuman') ?>" class="btn-outline">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Kembali ke Daftar
            </a>
        </div>
    </div>
</article>
