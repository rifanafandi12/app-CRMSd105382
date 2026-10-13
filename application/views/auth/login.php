<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="mb-8">
    <h2 class="font-display font-bold text-2xl text-neutral-900">Selamat Datang Kembali</h2>
    <p class="text-neutral-500 text-sm mt-1.5">Masuk menggunakan akun yang telah terdaftar</p>
</div>

<form action="<?= base_url('auth/login') ?>" method="post" class="space-y-5" autocomplete="on">
    <input type="hidden" name="<?= $this->security->get_csrf_token_name() ?>" value="<?= $this->security->get_csrf_hash() ?>">

    <!-- Username -->
    <div>
        <label for="username" class="form-label">Username</label>
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                <svg class="w-5 h-5 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
            </div>
            <input type="text"
                   id="username"
                   name="username"
                   class="form-input pl-11"
                   placeholder="Masukkan username"
                   value="<?= set_value('username') ?>"
                   autocomplete="username"
                   autofocus
                   required>
        </div>
        <?= form_error('username', '<p class="form-error">', '</p>') ?>
    </div>

    <!-- Password -->
    <div>
        <label for="password" class="form-label">Password</label>
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                <svg class="w-5 h-5 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
            </div>
            <input type="password"
                   id="password"
                   name="password"
                   class="form-input pl-11 pr-11"
                   placeholder="Masukkan password"
                   autocomplete="current-password"
                   required>
            <button type="button"
                    onclick="togglePassword()"
                    class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-neutral-400 hover:text-neutral-600 transition-colors"
                    aria-label="Toggle password visibility">
                <svg id="eye-icon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
            </button>
        </div>
        <?= form_error('password', '<p class="form-error">', '</p>') ?>
    </div>

    <!-- Remember me -->
    <div class="flex items-center justify-between">
        <label class="flex items-center gap-2 cursor-pointer select-none">
            <input type="checkbox" name="remember" class="form-checkbox">
            <span class="text-sm text-neutral-600">Ingat saya</span>
        </label>
    </div>

    <!-- Submit button -->
    <button type="submit" class="btn-primary w-full btn-lg">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
        </svg>
        Masuk ke Sistem
    </button>

</form>

<!-- Info -->
<div class="mt-8 pt-6 border-t border-neutral-200">
    <p class="text-xs text-neutral-500 text-center leading-relaxed">
        Belum memiliki akun? Hubungi administrator sekolah<br>
        atau datang langsung ke kantor tata usaha.
    </p>
</div>

<script>
function togglePassword() {
    const input = document.getElementById('password');
    const icon = document.getElementById('eye-icon');
    if (input.type === 'password') {
        input.type = 'text';
        icon.innerHTML = `
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
        `;
    } else {
        input.type = 'password';
        icon.innerHTML = `
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
        `;
    }
}
</script>
