<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<section class="bg-gradient-to-br from-primary-700 via-primary-600 to-primary-800 py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-white/10 backdrop-blur-sm rounded-full text-white text-xs font-medium mb-4">
            <span class="w-2 h-2 rounded-full bg-accent-400"></span>
            Informasi Resmi Sekolah
        </div>
        <h1 class="font-display font-bold text-3xl lg:text-4xl text-white mb-4">Pengumuman</h1>
        <p class="text-primary-100 max-w-2xl mx-auto">
            Informasi terbaru dari SD Negeri 105382 Galang untuk siswa, orang tua, dan masyarakat umum.
        </p>
    </div>
</section>

<section class="py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <?php if (!empty($announcements)): ?>
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($announcements as $ann): ?>
                    <a href="<?= base_url('landing/pengumuman/' . $ann->id_announcement) ?>"
                       class="card-hover overflow-hidden block group">
                        <?php if (!empty($ann->attachment) && preg_match('/\.(jpg|jpeg|png|gif|webp)$/i', $ann->attachment)): ?>
                            <div class="aspect-video overflow-hidden bg-neutral-100">
                                <img src="<?= base_url($ann->attachment) ?>"
                                     alt="<?= html_escape($ann->title) ?>"
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                            </div>
                        <?php else: ?>
                            <div class="aspect-video bg-gradient-to-br from-primary-500 to-primary-700 flex items-center justify-center">
                                <svg class="w-16 h-16 text-white/40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                          d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
                                </svg>
                            </div>
                        <?php endif; ?>

                        <div class="p-5">
                            <div class="flex items-center gap-2 mb-3">
                                <span class="badge-primary text-[10px]">
                                    <?= html_escape(ucfirst($ann->category ?? 'umum')) ?>
                                </span>
                                <span class="text-[10px] text-neutral-400">
                                    <?= format_date($ann->posted_date ?? '') ?>
                                </span>
                            </div>
                            <h3 class="font-display font-semibold text-base text-neutral-900 mb-2 line-clamp-2 group-hover:text-primary-600 transition-colors">
                                <?= html_escape($ann->title ?? '-') ?>
                            </h3>
                            <p class="text-sm text-neutral-600 line-clamp-3">
                                <?= html_escape(substr(strip_tags($ann->content ?? ''), 0, 120)) ?>...
                            </p>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="text-center py-16">
                <svg class="w-20 h-20 text-neutral-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
                </svg>
                <h3 class="font-display font-semibold text-neutral-900 mb-2">Belum Ada Pengumuman</h3>
                <p class="text-sm text-neutral-500">Pengumuman akan muncul di sini.</p>
            </div>
        <?php endif; ?>
    </div>
</section>
