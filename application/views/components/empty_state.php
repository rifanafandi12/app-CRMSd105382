<?php
/**
 * Empty State Component
 * 
 * @var string $icon       Nama icon
 * @var string $title      Judul
 * @var string $message    Deskripsi
 * @var string $action_url Opsional — URL tombol aksi
 * @var string $action_txt Opsional — Teks tombol aksi
 */
defined('BASEPATH') OR exit('No direct script access allowed');

$icon       = $icon       ?? 'inbox';
$title      = $title      ?? 'Belum Ada Data';
$message    = $message    ?? '';
$action_url = $action_url ?? null;
$action_txt = $action_txt ?? null;

$icon_map = [
    'inbox'   => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>',
    'search'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>',
    'chart'   => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>',
];

$icon_path = $icon_map[$icon] ?? $icon_map['inbox'];
?>

<div class="empty-state py-16">
    <svg class="w-20 h-20 text-neutral-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <?= $icon_path ?>
    </svg>
    <h3 class="font-display font-semibold text-lg text-neutral-900 mb-1"><?= html_escape($title) ?></h3>
    <?php if ($message): ?>
        <p class="text-sm text-neutral-500 max-w-md mb-5"><?= html_escape($message) ?></p>
    <?php endif; ?>
    <?php if ($action_url && $action_txt): ?>
        <a href="<?= html_escape($action_url) ?>" class="btn-primary btn-sm"><?= html_escape($action_txt) ?></a>
    <?php endif; ?>
</div>
