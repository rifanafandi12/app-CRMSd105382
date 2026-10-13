<?php
/**
 * Pagination Component
 * 
 * @var int    $current_page
 * @var int    $total_pages
 * @var string $base_url_func  Nama fungsi JS untuk navigasi, atau URL dasar
 * @var int    $max_visible    Jumlah halaman maksimal yang ditampilkan
 */
defined('BASEPATH') OR exit('No direct script access allowed');

$current_page = $current_page ?? 1;
$total_pages  = $total_pages  ?? 1;
$max_visible  = $max_visible  ?? 5;
$on_click     = $on_click     ?? 'goToPage'; // nama fungsi JS

if ($total_pages <= 1) return;

// Hitung range halaman yang ditampilkan
$half = floor($max_visible / 2);
$start = max(1, $current_page - $half);
$end   = min($total_pages, $start + $max_visible - 1);
$start = max(1, $end - $max_visible + 1);
?>

<nav class="flex items-center justify-between gap-3 mt-5 flex-wrap" aria-label="Pagination">
    <!-- Info -->
    <div class="text-xs text-neutral-500">
        Halaman <span class="font-semibold text-neutral-700"><?= $current_page ?></span>
        dari <span class="font-semibold text-neutral-700"><?= $total_pages ?></span>
    </div>

    <!-- Page buttons -->
    <div class="flex items-center gap-1">
        <!-- Prev -->
        <button type="button"
                onclick="<?= $on_click ?>(<?= max(1, $current_page - 1) ?>)"
                <?= $current_page <= 1 ? 'disabled' : '' ?>
                class="w-9 h-9 flex items-center justify-center rounded-lg text-sm font-medium text-neutral-600 hover:bg-neutral-100 disabled:opacity-40 disabled:cursor-not-allowed transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </button>

        <!-- First page + ellipsis -->
        <?php if ($start > 1): ?>
            <button type="button" onclick="<?= $on_click ?>(1)"
                    class="w-9 h-9 flex items-center justify-center rounded-lg text-sm font-medium text-neutral-600 hover:bg-neutral-100 transition-colors">
                1
            </button>
            <?php if ($start > 2): ?>
                <span class="w-9 h-9 flex items-center justify-center text-neutral-400">...</span>
            <?php endif; ?>
        <?php endif; ?>

        <!-- Numbered pages -->
        <?php for ($i = $start; $i <= $end; $i++): ?>
            <button type="button"
                    onclick="<?= $on_click ?>(<?= $i ?>)"
                    class="w-9 h-9 flex items-center justify-center rounded-lg text-sm font-medium transition-colors
                        <?= $i == $current_page
                            ? 'bg-primary-600 text-white shadow-sm'
                            : 'text-neutral-600 hover:bg-neutral-100' ?>">
                <?= $i ?>
            </button>
        <?php endfor; ?>

        <!-- Last page + ellipsis -->
        <?php if ($end < $total_pages): ?>
            <?php if ($end < $total_pages - 1): ?>
                <span class="w-9 h-9 flex items-center justify-center text-neutral-400">...</span>
            <?php endif; ?>
            <button type="button" onclick="<?= $on_click ?>(<?= $total_pages ?>)"
                    class="w-9 h-9 flex items-center justify-center rounded-lg text-sm font-medium text-neutral-600 hover:bg-neutral-100 transition-colors">
                <?= $total_pages ?>
            </button>
        <?php endif; ?>

        <!-- Next -->
        <button type="button"
                onclick="<?= $on_click ?>(<?= min($total_pages, $current_page + 1) ?>)"
                <?= $current_page >= $total_pages ? 'disabled' : '' ?>
                class="w-9 h-9 flex items-center justify-center rounded-lg text-sm font-medium text-neutral-600 hover:bg-neutral-100 disabled:opacity-40 disabled:cursor-not-allowed transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </button>
    </div>
</nav>
