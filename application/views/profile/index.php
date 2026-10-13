<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<?php
$this->load->view('components/page_header', [
    'title'      => 'Profil Saya',
    'subtitle'   => 'Kelola informasi akun Anda',
    'breadcrumb' => [['label' => 'Profil']],
]);
?>

<?php
// Ambil inisial untuk avatar
$initial = strtoupper(substr($user->name ?? 'U', 0, 1));
$avatarUrl = !empty($user->profile_picture) ? base_url($user->profile_picture) : null;

$roleLabels = ['admin' => 'Administrator', 'teacher' => 'Guru', 'parent' => 'Orang Tua'];
$roleColors = ['admin' => 'danger', 'teacher' => 'success', 'parent' => 'accent'];
?>

<div class="grid lg:grid-cols-3 gap-4">

    <!-- KARTU PROFIL -->
    <div class="lg:col-span-1 space-y-4">
        <!-- Avatar Card -->
        <div class="card">
            <div class="card-body text-center">
                <!-- Avatar -->
                <div class="relative inline-block mb-4">
                    <?php if ($avatarUrl): ?>
                        <img src="<?= html_escape($avatarUrl) ?>"
                             alt="Avatar"
                             class="w-28 h-28 rounded-full object-cover border-4 border-white shadow-lg">
                    <?php else: ?>
                        <div class="w-28 h-28 rounded-full bg-gradient-to-br from-primary-500 to-primary-700 text-white flex items-center justify-center font-bold text-4xl border-4 border-white shadow-lg">
                            <?= html_escape($initial) ?>
                        </div>
                    <?php endif; ?>

                    <!-- Online indicator -->
                    <?php if (!empty($user->is_online)): ?>
                        <span class="absolute bottom-1 right-1 w-6 h-6 rounded-full bg-success-500 border-4 border-white"></span>
                    <?php endif; ?>
                </div>

                <!-- Nama & Role -->
                <h2 class="font-display font-bold text-xl text-neutral-900 mb-1">
                    <?= html_escape($user->name) ?>
                </h2>
                <div class="mb-4">
                    <span class="badge-<?= $roleColors[$user->role] ?? 'neutral' ?>">
                        <?= $roleLabels[$user->role] ?? $user->role ?>
                    </span>
                </div>

                <!-- Username -->
                <p class="text-xs text-neutral-500 mb-5">
                    @<?= html_escape($user->username) ?>
                </p>

                <!-- Tombol Avatar -->
                <div class="flex flex-col gap-2">
                    <label for="avatar-input" class="btn-primary btn-sm cursor-pointer justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <?= $avatarUrl ? 'Ganti Foto' : 'Upload Foto' ?>
                    </label>

                    <form id="avatar-form" action="<?= base_url('profile/upload_avatar') ?>" method="post" enctype="multipart/form-data" class="hidden">
                        <input type="hidden" name="<?= $this->security->get_csrf_token_name() ?>" value="<?= $this->security->get_csrf_hash() ?>">
                        <input type="file" id="avatar-input" name="avatar" accept="image/*" onchange="submitAvatar()">
                    </form>

                    <?php if ($avatarUrl): ?>
                        <form action="<?= base_url('profile/remove_avatar') ?>" method="post" onsubmit="return confirm('Hapus foto profil?')">
                            <input type="hidden" name="<?= $this->security->get_csrf_token_name() ?>" value="<?= $this->security->get_csrf_hash() ?>">
                            <button type="submit" class="btn-outline btn-sm w-full justify-center text-danger-600 border-danger-300 hover:bg-danger-50">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                                Hapus Foto
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Info Akun -->
        <div class="card">
            <div class="card-header">
                <h3 class="font-display font-semibold text-neutral-900 text-sm">Informasi Akun</h3>
            </div>
            <div class="card-body space-y-3 text-sm">
                <div class="flex justify-between">
                    <span class="text-neutral-500">Email</span>
                    <span class="font-medium text-neutral-800 text-right truncate ml-2">
                        <?= html_escape($user->email ?? '-') ?>
                    </span>
                </div>
                <div class="flex justify-between">
                    <span class="text-neutral-500">Telepon</span>
                    <span class="font-medium text-neutral-800"><?= html_escape($user->phone ?? '-') ?></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-neutral-500">Bergabung</span>
                    <span class="font-medium text-neutral-800"><?= format_date($member_since ?? '') ?></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-neutral-500">Login Terakhir</span>
                    <span class="font-medium text-neutral-800">
                        <?= $last_login ? format_time_ago($last_login) : 'Belum pernah' ?>
                    </span>
                </div>
            </div>
        </div>

        <!-- Statistik -->
        <div class="card">
            <div class="card-header">
                <h3 class="font-display font-semibold text-neutral-900 text-sm">Statistik</h3>
            </div>
            <div class="card-body grid grid-cols-2 gap-3">
                <div class="text-center p-3 rounded-lg bg-primary-50">
                    <div class="font-display font-bold text-xl text-primary-600"><?= number_format($total_messages_sent ?? 0) ?></div>
                    <div class="text-[10px] text-primary-700 uppercase tracking-wide mt-1">Pesan Terkirim</div>
                </div>
                <div class="text-center p-3 rounded-lg bg-accent-50">
                    <div class="font-display font-bold text-xl text-accent-600"><?= number_format($total_messages_received ?? 0) ?></div>
                    <div class="text-[10px] text-accent-700 uppercase tracking-wide mt-1">Pesan Diterima</div>
                </div>
                <?php if ($user->role === 'parent'): ?>
                    <div class="text-center p-3 rounded-lg bg-warning-50 col-span-2">
                        <div class="font-display font-bold text-xl text-warning-600"><?= number_format($total_aspirations ?? 0) ?></div>
                        <div class="text-[10px] text-warning-700 uppercase tracking-wide mt-1">Aspirasi Terkirim</div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- KOLOM KANAN -->
    <div class="lg:col-span-2 space-y-4">

        <!-- EDIT PROFIL -->
        <div class="card">
            <div class="card-header">
                <div>
                    <h3 class="font-display font-semibold text-neutral-900">Edit Profil</h3>
                    <p class="text-xs text-neutral-500 mt-0.5">Perbarui informasi akun Anda</p>
                </div>
            </div>
            <div class="card-body">
                <form action="<?= base_url('profile/update') ?>" method="post">
                    <input type="hidden" name="<?= $this->security->get_csrf_token_name() ?>" value="<?= $this->security->get_csrf_hash() ?>">

                    <div class="grid sm:grid-cols-2 gap-4">
                        <div class="sm:col-span-2">
                            <label class="form-label">Nama Lengkap <span class="text-danger-500">*</span></label>
                            <input type="text" name="name" class="form-input" required maxlength="100"
                                   value="<?= html_escape($user->name ?? '') ?>">
                        </div>

                        <div>
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-input" maxlength="100"
                                   value="<?= html_escape($user->email ?? '') ?>">
                        </div>

                        <div>
                            <label class="form-label">No. Telepon</label>
                            <input type="text" name="phone" class="form-input" maxlength="20"
                                   value="<?= html_escape($user->phone ?? '') ?>">
                        </div>

                        <div class="sm:col-span-2 pt-4 mt-2 border-t border-neutral-100">
                            <div class="text-xs font-semibold text-neutral-500 uppercase tracking-wide mb-3">
                                Ganti Password (Opsional)
                            </div>
                            <p class="text-xs text-neutral-500 mb-3">Kosongkan jika tidak ingin ganti password.</p>
                        </div>

                        <div>
                            <label class="form-label">Password Baru</label>
                            <input type="password" name="password" class="form-input" minlength="6">
                        </div>

                        <div>
                            <label class="form-label">Konfirmasi Password</label>
                            <input type="password" name="passconf" class="form-input">
                        </div>
                    </div>

                    <div class="flex justify-end mt-5 pt-5 border-t border-neutral-100">
                        <button type="submit" class="btn-primary">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- DATA TAMBAHAN: ANAK (Kalau Parent) -->
        <?php if ($user->role === 'parent' && !empty($children)): ?>
            <div class="card">
                <div class="card-header">
                    <div>
                        <h3 class="font-display font-semibold text-neutral-900">Anak Saya</h3>
                        <p class="text-xs text-neutral-500 mt-0.5"><?= count($children) ?> anak terdaftar</p>
                    </div>
                    <a href="<?= base_url('students/my_children') ?>" class="text-sm text-primary-600 hover:text-primary-700">
                        Lihat Semua
                    </a>
                </div>
                <div class="card-body p-0">
                    <ul class="divide-y divide-neutral-100">
                        <?php foreach ($children as $child): ?>
                            <li class="px-5 py-3 hover:bg-neutral-50 flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-primary-500 to-primary-700 text-white flex items-center justify-center font-semibold text-sm flex-shrink-0">
                                    <?= strtoupper(substr($child->student_name, 0, 1)) ?>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="font-medium text-neutral-900 text-sm truncate">
                                        <?= html_escape($child->student_name) ?>
                                    </div>
                                    <div class="text-xs text-neutral-500">
                                        Kelas <?= html_escape($child->class_name ?? '-') ?> • NISN <?= html_escape($child->nisn ?? '-') ?>
                                    </div>
                                </div>
                                <a href="<?= base_url('students/detail/' . $child->id_student) ?>" class="btn-ghost btn-sm">
                                    Detail
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        <?php endif; ?>

        <!-- DATA TAMBAHAN: KELAS (Kalau Teacher) -->
        <?php if ($user->role === 'teacher' && !empty($my_classes)): ?>
            <div class="card">
                <div class="card-header">
                    <h3 class="font-display font-semibold text-neutral-900">Kelas yang Diwalikan</h3>
                </div>
                <div class="card-body p-0">
                    <ul class="divide-y divide-neutral-100">
                        <?php foreach ($my_classes as $cls): ?>
                            <li class="px-5 py-3 hover:bg-neutral-50 flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg bg-primary-100 text-primary-600 flex items-center justify-center font-bold text-sm flex-shrink-0">
                                    <?= html_escape($cls->class_name) ?>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="font-medium text-neutral-900 text-sm">
                                        Kelas <?= html_escape($cls->class_name) ?>
                                    </div>
                                    <div class="text-xs text-neutral-500">
                                        Tingkat <?= (int)$cls->grade_level ?> • <?= html_escape($cls->academic_year ?? '-') ?>
                                    </div>
                                </div>
                                <a href="<?= base_url('classes/detail/' . $cls->id_class) ?>" class="btn-ghost btn-sm">
                                    Detail
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        <?php endif; ?>

        <!-- AKTIVITAS TERBARU -->
        <?php if (!empty($recent_activities)): ?>
            <div class="card">
                <div class="card-header">
                    <h3 class="font-display font-semibold text-neutral-900">Aktivitas Saya</h3>
                </div>
                <div class="card-body p-0">
                    <ul class="divide-y divide-neutral-100 max-h-96 overflow-y-auto">
                        <?php foreach ($recent_activities as $log): ?>
                            <li class="px-5 py-3 hover:bg-neutral-50 flex items-start gap-3">
                                <div class="w-8 h-8 rounded-full bg-primary-100 text-primary-600 flex items-center justify-center flex-shrink-0 mt-0.5">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="text-xs font-medium text-neutral-800 line-clamp-2">
                                        <?= html_escape($log->description ?? '-') ?>
                                    </div>
                                    <div class="text-[10px] text-neutral-400 mt-0.5">
                                        <?= format_time_ago($log->created_at ?? '') ?>
                                    </div>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        <?php endif; ?>

    </div>
</div>

<script>
function submitAvatar() {
    const form = document.getElementById('avatar-form');
    if (form) form.submit();
}
</script>
