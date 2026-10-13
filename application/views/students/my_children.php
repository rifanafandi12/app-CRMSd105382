<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<?php
$this->load->view('components/page_header', [
    'title'      => 'Data Anak Saya',
    'subtitle'   => 'Informasi anak Anda yang terdaftar di sekolah',
    'breadcrumb' => [['label' => 'Data Anak']],
]);
?>

<?php if (!empty($children)): ?>
    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
        <?php foreach ($children as $child): ?>
            <div class="card overflow-hidden">
                <!-- Header gradient -->
                <div class="h-20 bg-gradient-to-br from-primary-500 via-primary-600 to-primary-700"></div>

                <!-- Avatar -->
                <div class="px-5 pb-5">
                    <div class="-mt-10 mb-3">
                        <div class="w-20 h-20 rounded-full bg-white p-1 shadow-lg">
                            <div class="w-full h-full rounded-full bg-gradient-to-br from-primary-500 to-primary-700 text-white flex items-center justify-center text-2xl font-bold">
                                <?= strtoupper(substr($child->student_name ?? 'S', 0, 1)) ?>
                            </div>
                        </div>
                    </div>

                    <h3 class="font-display font-bold text-lg text-neutral-900 mb-1">
                        <?= html_escape($child->student_name ?? '-') ?>
                    </h3>
                    <div class="flex items-center gap-2 mb-4">
                        <span class="badge-primary"><?= html_escape($child->class_name ?? 'Belum ada kelas') ?></span>
                        <span class="text-xs text-neutral-500"><?= html_escape($child->gender ?? '-') ?></span>
                    </div>

                    <dl class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <dt class="text-neutral-500">NISN</dt>
                            <dd class="font-medium text-neutral-800 font-mono text-xs"><?= html_escape($child->nisn ?? '-') ?></dd>
                        </div>
                        <?php if (!empty($child->birth_place) || !empty($child->birth_date)): ?>
                            <div class="flex justify-between">
                                <dt class="text-neutral-500">Lahir</dt>
                                <dd class="text-neutral-800 text-right">
                                    <?= html_escape(($child->birth_place ?? '') . ', ' . format_date($child->birth_date ?? '')) ?>
                                </dd>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($child->enrollment_year)): ?>
                            <div class="flex justify-between">
                                <dt class="text-neutral-500">Tahun Masuk</dt>
                                <dd class="text-neutral-800"><?= html_escape($child->enrollment_year) ?></dd>
                            </div>
                        <?php endif; ?>
                    </dl>

                    <div class="flex gap-2 mt-5 pt-5 border-t border-neutral-100">
                        <a href="<?= base_url('scores/raport/' . ($child->id_student ?? 0)) ?>"
                           class="btn-primary btn-sm flex-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Rapor
                        </a>
                        <a href="<?= base_url('students/detail/' . ($child->id_student ?? 0)) ?>"
                           class="btn-outline btn-sm flex-1">
                            Detail
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <div class="card">
        <?php $this->load->view('components/empty_state', [
            'icon'    => 'inbox',
            'title'   => 'Belum Ada Data Anak',
            'message' => 'Data anak Anda belum terdaftar di sistem. Silakan hubungi pihak sekolah untuk informasi lebih lanjut.',
        ]); ?>
    </div>
<?php endif; ?>
