<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<!-- ==============================================
     HERO SECTION
     ============================================== -->
<section class="bg-white border-b border-neutral-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 lg:py-24">
        <div class="grid lg:grid-cols-12 gap-12 items-center">

            <!-- Kiri: Text -->
            <div class="lg:col-span-7">
                <!-- Eyebrow -->
                <div class="inline-flex items-center gap-2 px-3 py-1 bg-primary-50 border border-primary-100 rounded-full text-primary-700 text-xs font-medium mb-6">
                    <span class="w-1.5 h-1.5 rounded-full bg-primary-600"></span>
                    Selamat Datang di Website Resmi
                </div>

                <!-- Heading -->
                <h1 class="font-display font-bold text-4xl sm:text-5xl lg:text-6xl text-neutral-900 leading-[1.1] mb-6 tracking-tight">
                    Mendidik Generasi<br>
                    <span class="text-primary-600">Cerdas &amp; Berkarakter</span>
                </h1>

                <!-- Deskripsi -->
                <p class="text-lg text-neutral-600 leading-relaxed mb-8 max-w-2xl">
                    SD Negeri 105382 Galang berkomitmen menyelenggarakan pendidikan dasar
                    yang berkualitas, membentuk peserta didik yang beriman, terampil,
                    dan berakhlak mulia sesuai profil Pelajar Pancasila.
                </p>

                <!-- CTA -->
                <div class="flex flex-wrap items-center gap-3 mb-10">
                    <a href="<?= base_url('login') ?>" class="btn-primary btn-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                        </svg>
                        Masuk ke Sistem
                    </a>
                    <a href="#profil" class="btn-outline btn-lg">
                        Tentang Sekolah
                    </a>
                </div>

                <!-- Info Kontak -->
                <div class="flex flex-wrap items-center gap-x-6 gap-y-2 text-sm text-neutral-500">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        Desa Galang Kota, Deli Serdang
                    </div>
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        sdn105382@gmail.com
                    </div>
                </div>
            </div>

            <!-- Kanan: Logo & Info -->
            <div class="lg:col-span-5 flex justify-center lg:justify-end">
                <div class="relative">
                    <!-- Logo card -->
                    <div class="bg-white border border-neutral-200 rounded-2xl shadow-lg p-8 w-72">
                        <div class="flex flex-col items-center text-center">
                            <img src="<?= base_url('assets/images/logo-tutwuri-handayani.webp') ?>"
                                 alt="Logo Tut Wuri Handayani"
                                 class="w-32 h-32 object-contain mb-4">
                            <div class="text-sm font-semibold text-neutral-900 mb-1">
                                SD Negeri 105382
                            </div>
                            <div class="text-xs text-neutral-500 mb-4">
                                Kecamatan Galang, Kab. Deli Serdang
                            </div>
                            <div class="w-full pt-4 border-t border-neutral-100">
                                <div class="text-[10px] uppercase tracking-wider text-neutral-400 mb-1">
                                    Terakreditasi
                                </div>
                                <div class="font-display font-bold text-2xl text-primary-600">A</div>
                            </div>
                        </div>
                    </div>

                    <!-- Accent badge -->
                    <div class="absolute -bottom-3 -right-3 bg-accent-500 text-white text-[10px] font-bold uppercase tracking-wider px-3 py-1.5 rounded-lg shadow-md">
                        Berkarakter
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ==============================================
     STATISTIK SEKOLAH
     ============================================== -->
<section class="bg-neutral-50 border-b border-neutral-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-6 lg:gap-10">
            <div class="text-center lg:text-left">
                <div class="font-display font-bold text-3xl lg:text-4xl text-neutral-900 mb-1">
                    <?= number_format($total_students ?? 0) ?>
                </div>
                <div class="text-sm text-neutral-500">Siswa Aktif</div>
            </div>
            <div class="text-center lg:text-left lg:border-l lg:border-neutral-200 lg:pl-10">
                <div class="font-display font-bold text-3xl lg:text-4xl text-neutral-900 mb-1">
                    <?= number_format($total_teachers ?? 0) ?>
                </div>
                <div class="text-sm text-neutral-500">Guru &amp; Staf</div>
            </div>
            <div class="text-center lg:text-left lg:border-l lg:border-neutral-200 lg:pl-10">
                <div class="font-display font-bold text-3xl lg:text-4xl text-neutral-900 mb-1">
                    12
                </div>
                <div class="text-sm text-neutral-500">Rombongan Belajar</div>
            </div>
            <div class="text-center lg:text-left lg:border-l lg:border-neutral-200 lg:pl-10">
                <div class="font-display font-bold text-3xl lg:text-4xl text-primary-600 mb-1">
                    A
                </div>
                <div class="text-sm text-neutral-500">Akreditasi</div>
            </div>
        </div>
    </div>
</section>

<!-- ==============================================
     VISI & MISI
     ============================================== -->
<section id="profil" class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Section header -->
        <div class="max-w-2xl mb-12">
            <div class="text-primary-600 font-semibold text-xs uppercase tracking-wider mb-3">
                Tentang Sekolah
            </div>
            <h2 class="font-display font-bold text-3xl lg:text-4xl text-neutral-900 leading-tight mb-4">
                Arah dan Tujuan Pendidikan Kami
            </h2>
            <p class="text-neutral-600 leading-relaxed">
                Kami berkomitmen mencetak generasi penerus yang unggul dalam ilmu
                pengetahuan dan berakhlak mulia.
            </p>
        </div>

        <!-- Visi & Misi -->
        <div class="grid md:grid-cols-2 gap-6">

            <!-- Visi -->
            <div class="border border-neutral-200 rounded-2xl p-8">
                <div class="flex items-center gap-3 mb-5">
                    <div class="w-10 h-10 rounded-lg bg-primary-50 text-primary-600 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </div>
                    <h3 class="font-display font-bold text-xl text-neutral-900">Visi</h3>
                </div>
                <p class="text-neutral-700 leading-relaxed text-[15px]">
                    <?= html_escape($vision ?? '') ?>
                </p>
            </div>

            <!-- Misi -->
            <div class="border border-neutral-200 rounded-2xl p-8">
                <div class="flex items-center gap-3 mb-5">
                    <div class="w-10 h-10 rounded-lg bg-accent-50 text-accent-600 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                        </svg>
                    </div>
                    <h3 class="font-display font-bold text-xl text-neutral-900">Misi</h3>
                </div>
                <ul class="space-y-3">
                    <?php foreach (($missions ?? []) as $i => $mission): ?>
                        <li class="flex items-start gap-3 text-neutral-700">
                            <span class="w-5 h-5 rounded-full bg-accent-100 text-accent-700 flex items-center justify-center text-[10px] font-bold flex-shrink-0 mt-0.5">
                                <?= $i + 1 ?>
                            </span>
                            <span class="text-[15px] leading-relaxed"><?= html_escape($mission) ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- ==============================================
     PENGUMUMAN TERBARU
     ============================================== -->
<?php if (!empty($announcements)): ?>
<section class="py-20 bg-neutral-50 border-t border-neutral-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 mb-10">
            <div>
                <div class="text-primary-600 font-semibold text-xs uppercase tracking-wider mb-3">
                    Informasi
                </div>
                <h2 class="font-display font-bold text-3xl lg:text-4xl text-neutral-900 leading-tight">
                    Pengumuman Terbaru
                </h2>
            </div>
            <a href="<?= base_url('pengumuman') ?>" class="btn-outline self-start sm:self-auto">
                Lihat Semua
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        </div>

        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
            <?php foreach (array_slice($announcements, 0, 3) as $ann):
                $catColors = [
                    'akademik'  => 'primary',
                    'kesiswaan' => 'success',
                    'umum'      => 'neutral',
                    'event'     => 'accent',
                    'libur'     => 'warning',
                    'darurat'   => 'danger',
                ];
                $catColor = $catColors[$ann->category ?? 'umum'] ?? 'neutral';
            ?>
                <a href="<?= base_url('pengumuman/' . $ann->id_announcement) ?>"
                   class="group bg-white border border-neutral-200 rounded-xl p-6 hover:border-primary-300 hover:shadow-md transition-all duration-200 flex flex-col">

                    <!-- Meta -->
                    <div class="flex items-center gap-2 mb-4">
                        <span class="badge-<?= $catColor ?> text-[10px]">
                            <?= html_escape(ucfirst($ann->category ?? 'umum')) ?>
                        </span>
                        <span class="text-xs text-neutral-400">
                            <?= format_date($ann->posted_date ?? '') ?>
                        </span>
                    </div>

                    <!-- Title -->
                    <h3 class="font-display font-semibold text-lg text-neutral-900 leading-snug mb-3 line-clamp-2 group-hover:text-primary-600 transition-colors">
                        <?= html_escape($ann->title ?? '') ?>
                    </h3>

                    <!-- Excerpt -->
                    <p class="text-sm text-neutral-600 leading-relaxed line-clamp-3 mb-4 flex-1">
                        <?= html_escape(substr(strip_tags($ann->content ?? ''), 0, 130)) ?>...
                    </p>

                    <!-- Footer -->
                    <div class="flex items-center justify-between pt-4 border-t border-neutral-100">
                        <span class="text-xs text-neutral-400 flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <?= number_format($ann->view_count ?? 0) ?>
                        </span>
                        <span class="text-xs font-medium text-primary-600 flex items-center gap-1 group-hover:gap-2 transition-all">
                            Baca
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </span>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ==============================================
     INFORMASI KONTAK
     ============================================== -->
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-2 gap-12 items-start">

            <!-- Kiri: Info -->
            <div>
                <div class="text-primary-600 font-semibold text-xs uppercase tracking-wider mb-3">
                    Hubungi Kami
                </div>
                <h2 class="font-display font-bold text-3xl lg:text-4xl text-neutral-900 leading-tight mb-4">
                    Kunjungi Sekolah Kami
                </h2>
                <p class="text-neutral-600 leading-relaxed mb-8 max-w-lg">
                    Untuk informasi pendaftaran, kunjungan, atau pertanyaan lainnya,
                    silakan hubungi kami melalui kontak di bawah ini.
                </p>

                <!-- Kontak List -->
                <div class="space-y-4">
                    <div class="flex items-start gap-4 p-5 border border-neutral-200 rounded-xl">
                        <div class="w-10 h-10 rounded-lg bg-primary-50 text-primary-600 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                        <div>
                            <div class="font-semibold text-neutral-900 text-sm mb-0.5">Alamat</div>
                            <div class="text-sm text-neutral-600 leading-relaxed">
                                <?= html_escape($school_address ?? 'Jalan Kesatuan Gang Inpres, Desa Galang Kota, Kec. Galang, Kab. Deli Serdang, Sumatera Utara') ?>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-start gap-4 p-5 border border-neutral-200 rounded-xl">
                        <div class="w-10 h-10 rounded-lg bg-accent-50 text-accent-600 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div>
                            <div class="font-semibold text-neutral-900 text-sm mb-0.5">Email</div>
                            <a href="mailto:<?= html_escape($school_email ?? 'sdn105382@gmail.com') ?>"
                               class="text-sm text-primary-600 hover:underline">
                                <?= html_escape($school_email ?? 'sdn105382@gmail.com') ?>
                            </a>
                        </div>
                    </div>

                    <div class="flex items-start gap-4 p-5 border border-neutral-200 rounded-xl">
                        <div class="w-10 h-10 rounded-lg bg-success-50 text-success-600 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <div class="font-semibold text-neutral-900 text-sm mb-0.5">Jam Operasional</div>
                            <div class="text-sm text-neutral-600">
                                Senin - Jumat: 07.00 - 14.00 WIB<br>
                                Sabtu - Minggu &amp; Libur Nasional: Tutup
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Kanan: CTA Card -->
            <div class="lg:sticky lg:top-24">
                <div class="border border-neutral-200 rounded-2xl p-8 bg-neutral-50">
                    <div class="w-14 h-14 rounded-xl bg-primary-600 text-white flex items-center justify-center mb-6">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    </div>

                    <h3 class="font-display font-bold text-xl text-neutral-900 mb-2">
                        Akses Sistem Sekolah
                    </h3>
                    <p class="text-sm text-neutral-600 leading-relaxed mb-6">
                        Portal khusus untuk guru dan orang tua murid. Pantau
                        perkembangan akademik dan komunikasi dengan pihak sekolah.
                    </p>

                    <a href="<?= base_url('login') ?>" class="btn-primary w-full justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                        </svg>
                        Masuk Sekarang
                    </a>

                    <div class="mt-5 pt-5 border-t border-neutral-200">
                        <div class="text-xs text-neutral-500 leading-relaxed">
                            Belum punya akun? Hubungi kantor tata usaha
                            sekolah untuk pendaftaran akun orang tua.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
