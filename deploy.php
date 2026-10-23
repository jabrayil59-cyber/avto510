<?php
// GitHub Webhook - Hostinger avtomatik yeniləmə
// Bu faylı public_html/ qovluğuna yükləyin

$SECRET = 'avto510webhook'; // GitHub webhook secret

// İmzanı yoxla
$payload   = file_get_contents('php://input');
$signature = 'sha256=' . hash_hmac('sha256', $payload, $SECRET);

if (!hash_equals($signature, $_SERVER['HTTP_X_HUB_SIGNATURE_256'] ?? '')) {
    http_response_code(403);
    die('Forbidden');
}

$data   = json_decode($payload, true);
$branch = $data['ref'] ?? '';

// Yalnız main branch-dən
if ($branch !== 'refs/heads/main') {
    die('Not main branch');
}

// GitHub-dan faylları çək
$files = [
    'config.php',
    'categories.php',
    'products.php',
    'admin.html',
    'create_tables.sql',
];

$raw_base = 'https://raw.githubusercontent.com/jabrayil59-cyber/avto510/main/';
$dest_dir = __DIR__ . '/';

$log = [];
foreach ($files as $file) {
    $url     = $raw_base . $file;
    $content = @file_get_contents($url);
    if ($content !== false) {
        file_put_contents($dest_dir . $file, $content);
        $log[] = "✓ $file yeniləndi";
    } else {
        $log[] = "✗ $file yüklənmədi";
    }
}

// Log faylına yaz
file_put_contents($dest_dir . 'deploy.log', date('Y-m-d H:i:s') . "\n" . implode("\n", $log) . "\n\n", FILE_APPEND);

echo implode("\n", $log);
?>
