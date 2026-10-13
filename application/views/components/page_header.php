<?php
/**
 * Page Header dengan breadcrumb + action button
 * 
 * @var string $title
 * @var string $subtitle
 * @var array  $breadcrumb  [['label' => 'X', 'url' => '/x'], ...]
 * @var string $action_url
 * @var string $action_label
 * @var string $action_icon
 */
defined('BASEPATH') OR exit('No direct script access allowed');

$title        = $title        ?? 'Halaman';
$subtitle     = $subtitle     ?? null;
$breadcrumb   = $breadcrumb   ?? [];
$action_url   = $action_url   ?? null;
$action_label = $action_label ?? null;
$action_icon  = $action_icon  ?? 'plus';
?>

<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
    <div>
        <?php if (!empty($breadcrumb)): ?>
            <nav class="flex items-center gap-1.5 text-xs text-neutral-500 mb-2">
                <a href="<?= base_url('dashboard') ?>" class="hover:text-primary-600 transition-colors">Beranda</a>
                <?php foreach ($breadcrumb as $crumb): ?>
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                    <?php if (!empty($crumb['url'])): ?>
                        <a href="<?= base_url($crumb['url']) ?>" class="hover:text-primary-600 transition-colors">
                            <?= html_escape($crumb['label']) ?>
                        </a>
                    <?php else: ?>
                        <span class="text-neutral-700 font-medium"><?= html_escape($crumb['label']) ?></span>
                    <?php endif; ?>
                <?php endforeach; ?>
            </nav>
        <?php endif; ?>

        <h1 class="font-display font-bold text-2xl text-neutral-900"><?= html_escape($title) ?></h1>
        <?php if ($subtitle): ?>
            <p class="text-sm text-neutral-500 mt-1"><?= html_escape($subtitle) ?></p>
        <?php endif; ?>
    </div>

    <?php if ($action_url && $action_label): ?>
        <a href="<?= base_url($action_url) ?>" class="btn-primary self-start sm:self-auto">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <?php if ($action_icon === 'plus'): ?>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                <?php elseif ($action_icon === 'download'): ?>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                <?php endif; ?>
            </svg>
            <?= html_escape($action_label) ?>
        </a>
    <?php endif; ?>
</div>
