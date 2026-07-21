<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$session = App\Models\KtpVerifikasiSession::where('session_key', 'cm32PedhvpHbjdw8JFInrnsfCcQpzBomr88qgyojdOg4l6Jv')->first();

if (!$session) {
    echo "SESSION NOT FOUND\n";
    exit;
}

echo "Status:    " . $session->status . "\n";
echo "Total:     " . $session->total_photos . "\n";
echo "Processed: " . $session->processed . "\n";
echo "KTP URL:   " . $session->ktp_url . "\n";
echo "KTP Nama:  " . $session->ktp_nama . "\n";
echo "KTP NIK:   " . $session->ktp_nik . "\n";

$results = $session->results ?? [];
echo "Results count: " . count($results) . "\n";

if (!empty($results)) {
    $first = $results[0];
    echo "\n--- First result keys: " . implode(', ', array_keys($first)) . " ---\n";

    if (isset($first['data'])) {
        echo "  data keys: " . implode(', ', array_keys($first['data'])) . "\n";
        echo "  foto_base64 in data: " . (isset($first['data']['foto_base64']) ? 'YES len='.strlen($first['data']['foto_base64']) : 'NO') . "\n";
    }

    echo "  foto_base64 top-level: " . (isset($first['foto_base64']) ? 'YES len='.strlen($first['foto_base64']) : 'NO') . "\n";
    echo "  nama_file top-level:   " . ($first['nama_file'] ?? 'NOT SET') . "\n";
    echo "  confidence: "            . ($first['confidence'] ?? 'NOT SET') . "\n";
    echo "  status: "                . ($first['status'] ?? 'NOT SET') . "\n";

    echo "\n--- All results (summary) ---\n";
    foreach ($results as $i => $r) {
        echo "  [{$i}] confidence=" . ($r['confidence'] ?? '?') . " nama_file=" . ($r['nama_file'] ?? ($r['data']['nama_file'] ?? '?')) . "\n";
    }
}
