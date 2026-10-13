<?php
/**
 * Table Toolbar — search + filter + action button
 * 
 * @var string $search_placeholder
 * @var string $search_id
 * @var array  $filters    [['id' => 'filter-class', 'label' => 'Semua Kelas', 'options' => [...]]]
 * @var string $action_url
 * @var string $action_label
 * @var string $action_icon
 */
defined('BASEPATH') OR exit('No direct script access allowed');

$search_placeholder = $search_placeholder ?? 'Cari...';
$search_id          = $search_id          ?? 'table-search';
$filters            = $filters            ?? [];
$action_url         = $action_url         ?? null;
$action_label       = $action_label       ?? null;
$action_icon        = $action_icon        ?? 'plus';
?>

<div class="flex flex-col sm:flex-row sm:items-center gap-3 mb-4">
    <div class="flex-1 flex flex-col sm:flex-row gap-2">
        <!-- Search -->
        <div class="relative flex-1 sm:max-w-xs">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="w-4 h-4 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
            <input type="search"
                   id="<?= html_escape($search_id) ?>"
                   class="form-input pl-9 text-sm"
                   placeholder="<?= html_escape($search_placeholder) ?>">
        </div>

        <!-- Filters -->
        <?php foreach ($filters as $filter): ?>
            <select id="<?= html_escape($filter['id'] ?? '') ?>"
                    class="form-select text-sm sm:max-w-[180px]">
                <?php foreach ($filter['options'] as $value => $label): ?>
                    <option value="<?= html_escape($value) ?>"><?= html_escape($label) ?></option>
                <?php endforeach; ?>
            </select>
        <?php endforeach; ?>
    </div>

    <!-- Action Button -->
    <?php if ($action_url && $action_label): ?>
        <button type="button"
                onclick="<?= $action_url ?>"
                class="btn-primary self-start sm:self-auto">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <?php if ($action_icon === 'plus'): ?>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                <?php elseif ($action_icon === 'download'): ?>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                <?php endif; ?>
            </svg>
            <?= html_escape($action_label) ?>
        </button>
    <?php endif; ?>
</div>
