<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$service = new \App\Services\WebAnalyzerService();
$res = $service->analyzeVirusTotal('https://www.oploverz.am/');
file_put_contents('scratch/vt_sample.json', json_encode($res, JSON_PRETTY_PRINT));
echo "Done.";
