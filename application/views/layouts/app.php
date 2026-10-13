<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?= csrf_meta() ?>
    
    <title><?= html_escape($title ?? 'Dashboard') ?> &mdash; CRM SDN 105382 Galang</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="<?= base_url('assets/css/app.css') ?>">
    <link rel="icon" type="image/svg+xml" href="<?= base_url('assets/images/favicon.svg') ?>">
    
    <?php if (isset($extra_css)): ?>
        <?= $extra_css ?>
    <?php endif; ?>
</head>
<body class="h-full bg-secondary-50 font-sans antialiased">

<div class="min-h-full">
    <!-- Sidebar -->
    <?php $this->load->view('components/sidebar', $this->data ?? []); ?>

    <!-- Main content wrapper -->
    <div class="lg:pl-64 flex flex-col min-h-screen">
        <!-- Top navbar -->
        <?php $this->load->view('components/navbar', $this->data ?? []); ?>

        <!-- Page content -->
        <main class="flex-1 p-4 sm:p-6 lg:p-8">
            <!-- Flash messages -->
            <?php if ($this->session->flashdata('success')): ?>
                <div class="alert-success mb-6" id="flash-message">
                    <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span><?= $this->session->flashdata('success') ?></span>
                </div>
            <?php endif; ?>

            <?php if ($this->session->flashdata('error')): ?>
                <div class="alert-danger mb-6" id="flash-message">
                    <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span><?= $this->session->flashdata('error') ?></span>
                </div>
            <?php endif; ?>

            <!-- Content view -->
            <?php $this->load->view($content_view); ?>
        </main>

        <!-- Footer -->
        <footer class="px-4 sm:px-6 lg:px-8 py-4 border-t border-secondary-200 bg-white">
            <div class="flex flex-col sm:flex-row justify-between items-center gap-2 text-xs text-secondary-500">
                <div>&copy; <?= date('Y') ?> SD Negeri 105382 Galang. Seluruh hak cipta dilindungi.</div>
                <div class="flex items-center gap-4">
                    <span class="flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-success-500 animate-pulse-soft"></span>
                        Sistem Aktif
                    </span>
                    <span>v1.0.0</span>
                </div>
            </div>
        </footer>
    </div>
</div>

<script src="<?= base_url('assets/js/app.js') ?>"></script>

<?php if (isset($extra_js)): ?>
    <?= $extra_js ?>
<?php endif; ?>

</body>
</html>
