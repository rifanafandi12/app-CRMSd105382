<?php
/**
 * Stat Card Component
 * 
 * Variabel yang dibutuhkan:
 * @var string $icon       users|teacher|classroom|chat|online|chart|aspiration|announcement|gallery
 * @var string $label      
 * @var string $value      
 * @var string $color      primary|success|warning|danger|accent
 * @var string $trend      Opsional
 * @var bool   $trend_up   Opsional (default: true)
 */
defined('BASEPATH') OR exit('No direct script access allowed');

$icon     = $icon     ?? 'users';
$label    = $label    ?? 'Label';
$value    = $value    ?? '0';
$color    = $color    ?? 'primary';
$trend    = $trend    ?? null;
$trend_up = $trend_up ?? true;

$icon_map = [
    'users'        => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>',
    'teacher'      => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>',
    'classroom'    => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>',
    'chat'         => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>',
    'online'       => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.636 18.364a9 9 0 010-12.728m12.728 0a9 9 0 010 12.728m-9.9-2.829a5 5 0 010-7.07m7.072 0a5 5 0 010 7.07M13 12a1 1 0 11-2 0 1 1 0 012 0z"/>',
    'chart'        => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>',
    'aspiration'   => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>',
    'announcement' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>',
    'gallery'      => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>',
    'student'      => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>',
];

$color_map = [
    'primary' => ['bg-primary-100', 'text-primary-600'],
    'success' => ['bg-success-100', 'text-success-600'],
    'warning' => ['bg-warning-100', 'text-warning-600'],
    'danger'  => ['bg-danger-100',  'text-danger-600'],
    'accent'  => ['bg-accent-100',  'text-accent-600'],
];

$icon_path = $icon_map[$icon] ?? $icon_map['users'];
$colors    = $color_map[$color] ?? $color_map['primary'];
?>

<div class="card p-5 flex items-start justify-between hover:shadow-elevated transition-shadow duration-200">
    <div class="min-w-0 flex-1">
        <div class="text-xs uppercase tracking-wide text-neutral-500 font-semibold mb-1.5"><?= html_escape($label) ?></div>
        <div class="font-display font-bold text-3xl text-neutral-900 mb-1 leading-none">
            <?= html_escape($value) ?>
        </div>
        <?php if ($trend): ?>
            <div class="flex items-center gap-1 text-xs font-medium mt-2 <?= $trend_up ? 'text-success-600' : 'text-danger-600' ?>">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <?php if ($trend_up): ?>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    <?php else: ?>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"/>
                    <?php endif; ?>
                </svg>
                <span><?= html_escape($trend) ?></span>
            </div>
        <?php endif; ?>
    </div>

    <div class="w-11 h-11 rounded-lg flex items-center justify-center flex-shrink-0 <?= $colors[0] ?>">
        <svg class="w-5 h-5 <?= $colors[1] ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <?= $icon_path ?>
        </svg>
    </div>
</div>
