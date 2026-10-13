<?php
/**
 * Modal Component — reusable
 * 
 * @var string $id         ID unik untuk modal
 * @var string $title      Judul modal
 * @var string $size       sm | md | lg | xl (default: md)
 * @var string $content    Isi modal (HTML) — bisa juga pakai ob_start()
 * 
 * Usage:
 *   $data['modal_content'] = $this->load->view('students/form', [], true);
 *   $this->load->view('components/modal', [
 *       'id'      => 'modal-student',
 *       'title'   => 'Tambah Siswa',
 *       'size'    => 'lg',
 *       'content' => $modal_content,
 *   ]);
 */
defined('BASEPATH') OR exit('No direct script access allowed');

$id      = $id      ?? 'modal-' . uniqid();
$title   = $title   ?? 'Modal';
$size    = $size    ?? 'md';
$content = $content ?? '';

$size_map = [
    'sm' => 'max-w-md',
    'md' => 'max-w-xl',
    'lg' => 'max-w-3xl',
    'xl' => 'max-w-5xl',
];
$size_class = $size_map[$size] ?? $size_map['md'];
?>

<div id="<?= html_escape($id) ?>"
     class="fixed inset-0 z-50 hidden overflow-y-auto"
     aria-labelledby="<?= html_escape($id) ?>-title"
     role="dialog"
     aria-modal="true">

    <!-- Backdrop -->
    <div class="fixed inset-0 bg-neutral-900/60 backdrop-blur-sm transition-opacity"
         onclick="closeModal('<?= html_escape($id) ?>')"></div>

    <!-- Modal Panel -->
    <div class="relative min-h-full flex items-center justify-center p-4">
        <div class="relative bg-white rounded-2xl shadow-2xl w-full <?= $size_class ?> animate-slide-up">

            <!-- Header -->
            <div class="flex items-center justify-between px-6 py-4 border-b border-neutral-100">
                <h3 id="<?= html_escape($id) ?>-title"
                    class="font-display font-bold text-lg text-neutral-900">
                    <?= html_escape($title) ?>
                </h3>
                <button type="button"
                        onclick="closeModal('<?= html_escape($id) ?>')"
                        class="p-1.5 rounded-lg text-neutral-400 hover:text-neutral-700 hover:bg-neutral-100 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Body -->
            <div class="px-6 py-5">
                <?= $content ?>
            </div>
        </div>
    </div>
</div>

<script>
    function openModal(id) {
        const modal = document.getElementById(id);
        if (modal) {
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
    }

    function closeModal(id) {
        const modal = document.getElementById(id);
        if (modal) {
            modal.classList.add('hidden');
            document.body.style.overflow = '';
        }
    }

    // ESC untuk close modal
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            document.querySelectorAll('[role="dialog"]:not(.hidden)').forEach(m => {
                closeModal(m.id);
            });
        }
    });
</script>
