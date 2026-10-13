<?php
/**
 * Script untuk convert semua gambar lama ke WebP
 * 
 * Cara pakai (dari root project):
 *   php tools/convert_existing.php
 * 
 * Atau convert folder spesifik:
 *   php tools/convert_existing.php assets/uploads/gallery
 */

// Load composer autoload
require __DIR__ . '/../vendor/autoload.php';

use WebPConvert\WebPConvert;

// Setup
$basePath = __DIR__ . '/..';
$folders = $argv[1] ?? 'assets/uploads';

$fullPath = $basePath . '/' . $folders;

if (!is_dir($fullPath)) {
    echo "❌ Folder tidak ditemukan: {$fullPath}\n";
    exit(1);
}

echo "🚀 Memulai konversi WebP...\n";
echo "📁 Folder: {$fullPath}\n\n";

$stats = [
    'total'      => 0,
    'converted'  => 0,
    'skipped'    => 0,
    'failed'     => 0,
    'saved'      => 0, // bytes
];

// Recursive scan
$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($fullPath, RecursiveDirectoryIterator::SKIP_DOTS)
);

foreach ($iterator as $file) {
    if (!$file->isFile()) continue;

    $ext = strtolower($file->getExtension());
    if (!in_array($ext, ['jpg', 'jpeg', 'png'])) continue;

    $stats['total']++;
    $source = $file->getPathname();
    $destination = preg_replace('/\.(jpg|jpeg|png)$/i', '.webp', $source);

    // Skip kalau webp sudah ada & lebih baru
    if (file_exists($destination) && filemtime($destination) >= filemtime($source)) {
        echo "⏭️  SKIP: " . basename($source) . " (WebP sudah ada)\n";
        $stats['skipped']++;
        continue;
    }

    $origSize = filesize($source);

    try {
        WebPConvert::convert($source, $destination, [
            'quality'    => 80,
            'max-width'  => 1920,
            'max-height' => 1920,
            'auto-limit' => true,
            'sharp-yuv'  => true,
            'converters' => ['cwebp', 'vips', 'imagick', 'gmagick', 'imagemagick', 'graphicsmagick', 'ffmpeg', 'gd'],
            'metadata'   => 'none',
        ]);

        if (!file_exists($destination)) {
            throw new Exception('WebP tidak dihasilkan');
        }

        $webpSize = filesize($destination);
        $saved = $origSize - $webpSize;

        if ($saved > 0) {
            $stats['converted']++;
            $stats['saved'] += $saved;
            $percent = round(($saved / $origSize) * 100);
            
            echo "✅ CONVERT: " . basename($source) 
               . " (" . formatBytes($origSize) . " → " . formatBytes($webpSize) 
               . ", hemat {$percent}%)\n";

            // Hapus original
            @unlink($source);
        } else {
            echo "⚠️  SKIP: " . basename($source) . " (WebP lebih besar)\n";
            @unlink($destination);
            $stats['skipped']++;
        }
    } catch (Exception $e) {
        echo "❌ FAIL: " . basename($source) . " — " . $e->getMessage() . "\n";
        $stats['failed']++;
    }
}

// Summary
echo "\n" . str_repeat('=', 60) . "\n";
echo "📊 RINGKASAN\n";
echo str_repeat('=', 60) . "\n";
echo "Total file ditemukan : {$stats['total']}\n";
echo "Berhasil dikonversi  : {$stats['converted']}\n";
echo "Di-skip             : {$stats['skipped']}\n";
echo "Gagal               : {$stats['failed']}\n";
echo "Total hemat         : " . formatBytes($stats['saved']) . "\n";
echo str_repeat('=', 60) . "\n";

function formatBytes($bytes) {
    $units = ['B', 'KB', 'MB', 'GB'];
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    $bytes /= pow(1024, $pow);
    return round($bytes, 2) . ' ' . $units[$pow];
}
