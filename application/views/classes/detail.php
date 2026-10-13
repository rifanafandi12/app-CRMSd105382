<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<?php
$this->load->view('components/page_header', [
    'title'      => 'Detail Kelas',
    'breadcrumb' => [
        ['label' => 'Kelas', 'url' => 'classes'],
        ['label' => $class->class_name ?? 'Detail'],
    ],
]);
?>

<div class="grid lg:grid-cols-3 gap-4">
    <div class="card lg:col-span-1">
        <div class="card-body text-center">
            <div class="w-20 h-20 rounded-2xl bg-gradient-to-br from-primary-500 to-primary-700 text-white flex items-center justify-center font-bold text-2xl mx-auto mb-4">
                <?= html_escape($class->class_name ?? '-') ?>
            </div>
            <h2 class="font-display font-bold text-xl text-neutral-900 mb-1">
                Kelas <?= html_escape($class->class_name ?? '-') ?>
            </h2>
            <p class="text-sm text-neutral-500 mb-5">Tingkat <?= (int)($class->grade_level ?? 0) ?></p>

            <dl class="space-y-3 text-sm text-left">
                <div class="flex justify-between">
                    <dt class="text-neutral-500">Wali Kelas</dt>
                    <dd class="font-medium text-neutral-800 text-right">
                        <?= html_escape($class->homeroom_teacher_name ?? 'Belum ada') ?>
                    </dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-neutral-500">Tahun Ajaran</dt>
                    <dd class="text-neutral-800"><?= html_escape($class->academic_year ?? '-') ?></dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-neutral-500">Jumlah Siswa</dt>
                    <dd class="text-neutral-800"><?= count($students ?? []) ?> siswa</dd>
                </div>
            </dl>
        </div>
    </div>

    <div class="card lg:col-span-2">
        <div class="card-header">
            <div>
                <h3 class="font-display font-semibold text-neutral-900">Daftar Siswa</h3>
                <p class="text-xs text-neutral-500 mt-0.5"><?= count($students ?? []) ?> siswa aktif</p>
            </div>
        </div>
        <div class="card-body p-0">
            <?php if (!empty($students)): ?>
                <ul class="divide-y divide-neutral-100">
                    <?php foreach ($students as $s): ?>
                        <li class="px-5 py-3 hover:bg-neutral-50 flex items-center gap-3">
                            <div class="w-9 h-9 rounded-full bg-gradient-to-br from-primary-500 to-primary-700 text-white flex items-center justify-center text-xs font-semibold">
                                <?= strtoupper(substr($s->student_name ?? 'S', 0, 1)) ?>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="font-medium text-neutral-900 text-sm truncate"><?= html_escape($s->student_name) ?></div>
                                <div class="text-xs text-neutral-500">NISN: <?= html_escape($s->nisn ?? '-') ?></div>
                            </div>
                            <a href="<?= base_url('students/detail/' . $s->id_student) ?>"
                               class="btn-ghost btn-sm">Detail</a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <?php $this->load->view('components/empty_state', [
                    'icon' => 'inbox', 'title' => 'Belum Ada Siswa',
                    'message' => 'Belum ada siswa di kelas ini.',
                ]); ?>
            <?php endif; ?>
        </div>
    </div>
</div>
