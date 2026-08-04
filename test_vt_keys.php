<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$service = new \App\Services\WebAnalyzerService();
$res = $service->analyzeVirusTotal('https://www.facebook.com/');
if ($res) {
    print_r($res['raw_data']['data']['attributes']['trackers'] ?? 'No trackers');
}
