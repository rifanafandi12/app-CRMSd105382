<?php
/**
 * Update semua path di database dari .jpg/.png/.jpeg → .webp
 * 
 * Cara pakai:
 *   php tools/update_db_paths.php
 * 
 * PENTING: Backup database dulu!
 */

require __DIR__ . '/../vendor/autoload.php';

// Load CodeIgniter database config
$envFile = __DIR__ . '/../.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        if (strpos($line, '=') === false) continue;
        list($key, $value) = explode('=', $line, 2);
        putenv(trim($key) . '=' . trim($value));
    }
}

$host = getenv('DB_HOST') ?: 'localhost';
$user = getenv('DB_USER') ?: 'root';
$pass = getenv('DB_PASS') ?: '';
$name = getenv('DB_NAME') ?: 'db_crm_sd_negeri_105382';

echo "🔌 Menghubungkan ke database...\n";

try {
    $pdo = new PDO(
        "mysql:host={$host};dbname={$name};charset=utf8mb4",
        $user,
        $pass,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch (PDOException $e) {
    echo "❌ Koneksi gagal: " . $e->getMessage() . "\n";
    exit(1);
}

echo "✅ Terhubung ke database\n\n";

// ==============================================
// Daftar kolom yang menyimpan path gambar
// ==============================================
$columns = [
    ['table' => 'users',         'column' => 'profile_picture'],
    ['table' => 'students',      'column' => 'photo'],
    ['table' => 'announcements', 'column' => 'attachment'],
    ['table' => 'aspirations',   'column' => 'attachment'],
    ['table' => 'gallery',       'column' => 'image_path'],
];

$totalUpdated = 0;

foreach ($columns as $col) {
    $table  = $col['table'];
    $column = $col['column'];

    echo "📋 Cek tabel `{$table}` kolom `{$column}`...\n";

    // Cari record dengan extension gambar
    $sql = "SELECT id_" . $table . " as id, {$column} as path
            FROM {$table}
            WHERE {$column} IS NOT NULL
              AND {$column} != ''
              AND {$column} REGEXP '\\\\.(jpg|jpeg|png)$'";
    // Catatan: id_column tidak selalu sama. Sesuaikan manual.
    
    // Skip query kompleks — pakai cara simpler:
    $stmt = $pdo->query("SELECT * FROM {$table} WHERE {$column} IS NOT NULL AND {$column} != ''");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $updated = 0;
    foreach ($rows as $row) {
        $oldPath = $row[$column];
        
        // Skip kalau bukan .jpg/.jpeg/.png
        if (!preg_match('/\.(jpg|jpeg|png)$/i', $oldPath)) {
            continue;
        }
        
        // Cek webp path
        $webpPath = preg_replace('/\.(jpg|jpeg|png)$/i', '.webp', $oldPath);
        
        // Cek file webp ada di disk
        $webpDisk = __DIR__ . '/../' . $webpPath;
        $oldDisk  = __DIR__ . '/../' . $oldPath;
        
        if (!file_exists($webpDisk)) {
            // WebP tidak ada — skip
            echo "   ⏭️  Skip (WebP tidak ada): {$oldPath}\n";
            continue;
        }
        
        // Update DB
        $pk = 'id_' . substr($table, 0, -1); // id_user, id_student, dll
        // Untuk galeri: id_gallery, bukan id_galleri
        $pkMap = [
            'users'         => 'id_user',
            'students'      => 'id_student',
            'announcements' => 'id_announcement',
            'aspirations'   => 'id_aspiration',
            'gallery'       => 'id_gallery',
        ];
        $pk = $pkMap[$table] ?? 'id';
        
        $updStmt = $pdo->prepare("UPDATE {$table} SET {$column} = ? WHERE {$pk} = ?");
        $updStmt->execute([$webpPath, $row[$pk]]);
        
        echo "   ✅ {$oldPath} → {$webpPath}\n";
        $updated++;
        $totalUpdated++;
    }
    
    if ($updated === 0) {
        echo "   ℹ️  Tidak ada yang perlu di-update\n";
    }
    echo "\n";
}

echo str_repeat('=', 60) . "\n";
echo "📊 Total path di-update: {$totalUpdated}\n";
echo str_repeat('=', 60) . "\n";
