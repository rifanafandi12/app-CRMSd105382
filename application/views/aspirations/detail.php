<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<?php
$is_admin_view = in_array($user_role, ['admin', 'teacher']);

$this->load->view('components/page_header', [
    'title'      => 'Detail Aspirasi',
    'breadcrumb' => [
        ['label' => 'Aspirasi', 'url' => 'aspirations'],
        ['label' => 'Detail'],
    ],
]);
?>

<div class="grid lg:grid-cols-3 gap-4">
    <div class="lg:col-span-2 space-y-4">
        <!-- Aspirasi -->
        <div class="card">
            <div class="card-body">
                <?php
                $statusColors = ['pending' => 'warning', 'dibaca' => 'primary', 'diproses' => 'accent', 'selesai' => 'success'];
                $statusLabels = ['pending' => 'Pending', 'dibaca' => 'Dibaca', 'diproses' => 'Diproses', 'selesai' => 'Selesai'];
                $color = $statusColors[$aspiration->status] ?? 'neutral';
                ?>
                <div class="flex flex-wrap items-center gap-3 mb-4">
                    <span class="badge-<?= $color ?>"><?= $statusLabels[$aspiration->status] ?? $aspiration->status ?></span>
                    <span class="badge-neutral"><?= html_escape(ucfirst($aspiration->category ?? 'umum')) ?></span>
                    <span class="text-xs text-neutral-500">
                        <?= format_datetime($aspiration->created_at ?? '', true) ?>
                    </span>
                </div>

                <h1 class="font-display font-bold text-2xl text-neutral-900 mb-4">
                    <?= html_escape($aspiration->subject) ?>
                </h1>

                <div class="prose prose-sm max-w-none text-neutral-700 leading-relaxed">
                    <?= nl2br(html_escape($aspiration->content)) ?>
                </div>

                <?php if (!empty($aspiration->attachment)): ?>
                    <div class="mt-5 p-4 rounded-lg bg-primary-50 border border-primary-100">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-primary-600 text-white flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <div class="font-medium text-primary-900 text-sm">Lampiran</div>
                                <div class="text-xs text-primary-700">Klik untuk mengunduh</div>
                            </div>
                            <a href="<?= base_url($aspiration->attachment) ?>" target="_blank" download class="btn-primary btn-sm">
                                Unduh
                            </a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Respon (kalau sudah ada) -->
        <?php if (!empty($aspiration->response)): ?>
            <div class="card border-l-4 border-success-500">
                <div class="card-body">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-9 h-9 rounded-full bg-success-100 text-success-600 flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <div>
                            <div class="font-display font-semibold text-neutral-900 text-sm">Respon Sekolah</div>
                            <div class="text-xs text-neutral-500">
                                oleh <?= html_escape($aspiration->responder_name ?? 'Admin') ?>
                                • <?= format_time_ago($aspiration->responded_at ?? '') ?>
                            </div>
                        </div>
                    </div>
                    <div class="text-sm text-neutral-700 leading-relaxed bg-success-50 rounded-lg p-4">
                        <?= nl2br(html_escape($aspiration->response)) ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Form Respon (kalau admin & belum direspon) -->
        <?php if ($is_admin_view && empty($aspiration->response)): ?>
            <div class="card">
                <div class="card-header">
                    <h3 class="font-display font-semibold text-neutral-900">Berikan Respon</h3>
                </div>
                <div class="card-body">
                    <form action="<?= base_url('aspirations/respond') ?>" method="post">
                        <input type="hidden" name="<?= $this->security->get_csrf_token_name() ?>" value="<?= $this->security->get_csrf_hash() ?>">
                        <input type="hidden" name="id_aspiration" value="<?= (int)$aspiration->id_aspiration ?>">

                        <div>
                            <label class="form-label">Respon Anda <span class="text-danger-500">*</span></label>
                            <textarea name="response" class="form-textarea" rows="5" required maxlength="5000"
                                      placeholder="Tulis respon untuk orang tua..."></textarea>
                        </div>

                        <div class="flex items-center justify-end gap-2 mt-4">
                            <button type="submit" class="btn-primary">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                                </svg>
                                Kirim Respon
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        <?php endif; ?>

        <!-- Back -->
        <div>
            <a href="<?= base_url('aspirations') ?>" class="btn-outline">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Kembali ke Daftar
            </a>
        </div>
    </div>

    <!-- Sidebar Info -->
    <div class="lg:col-span-1">
        <div class="card">
            <div class="card-header">
                <h3 class="font-display font-semibold text-neutral-900 text-sm">Informasi</h3>
            </div>
            <div class="card-body space-y-3 text-sm">
                <div class="flex justify-between">
                    <span class="text-neutral-500">Pengirim</span>
                    <span class="font-medium text-neutral-800 text-right"><?= html_escape($aspiration->parent_name ?? '-') ?></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-neutral-500">Kategori</span>
                    <span class="font-medium text-neutral-800"><?= html_escape(ucfirst($aspiration->category)) ?></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-neutral-500">Status</span>
                    <span class="badge-<?= $color ?>"><?= $statusLabels[$aspiration->status] ?? $aspiration->status ?></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-neutral-500">Dikirim</span>
                    <span class="font-medium text-neutral-800 text-right"><?= format_date($aspiration->created_at ?? '') ?></span>
                </div>
                <?php if (!empty($aspiration->responded_at)): ?>
                    <div class="flex justify-between">
                        <span class="text-neutral-500">Direspon</span>
                        <span class="font-medium text-neutral-800 text-right"><?= format_date($aspiration->responded_at) ?></span>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
