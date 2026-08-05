<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\Browsershot\Browsershot;
use Throwable;

class ScreenshotService
{
    public function capture(string $url): ?string
    {
        try {
            $provider = config('services.screenshots.provider', 'browsershot');

            if ($provider === 'none') {
                return null;
            }

            $host = parse_url($url, PHP_URL_HOST);

            if (! $host) {
                Log::warning('Domain screenshot tidak dapat dibaca.', [
                    'url' => $url,
                ]);

                return null;
            }

            $cleanHost = preg_replace('/^www\./i', '', $host);
            $fileName = Str::slug($cleanHost).'.png';
            $relativePath = 'screenshots/'.$fileName;

            Storage::disk('public')->makeDirectory('screenshots', 0755, true);

            if (! Storage::disk('public')->directoryExists('screenshots')) {
                Log::warning('Direktori screenshots tidak dapat dibuat.', [
                    'url' => $url,
                    'path' => Storage::disk('public')->path('screenshots'),
                ]);

                return null;
            }

            $absolutePath = Storage::disk('public')->path($relativePath);

            if (Storage::disk('public')->exists($relativePath)) {
                Storage::disk('public')->delete($relativePath);
            }

            if ($provider === 'external') {
                return $this->captureFromExternalService(
                    $url,
                    $relativePath
                )
                    ? $relativePath
                    : null;
            }

            if (! config('services.browsershot.enabled')) {
                Log::info('Screenshot dimatikan lewat BROWSERSHOT_ENABLED.', [
                    'url' => $url,
                ]);

                return null;
            }

            if (! $this->nodeAvailable()) {
                Log::info('Node.js tidak tersedia, screenshot dilewati.', [
                    'url' => $url,
                ]);

                return null;
            }

            $browsershot = Browsershot::url($url)
                ->windowSize(1366, 768)
                ->setOption('waitUntil', 'load')
                ->setDelay(2000)
                ->timeout((int) config('services.browsershot.timeout'))
                ->noSandbox();

            if ($nodePath = config('services.browsershot.node_path')) {
                $browsershot->setNodeBinary($nodePath);
            }

            if ($npmPath = config('services.browsershot.npm_path')) {
                $browsershot->setNpmBinary($npmPath);
            }

            if ($chromePath = config('services.browsershot.chrome_path')) {
                $browsershot->setChromePath($chromePath);
            }

            $browsershot->save($absolutePath);

            if (! Storage::disk('public')->exists($relativePath)) {
                Log::error('Screenshot selesai diproses tetapi file tidak ditemukan.', [
                    'url' => $url,
                    'path' => $absolutePath,
                ]);

                return null;
            }

            return $relativePath;
        } catch (Throwable $exception) {
            Log::error('Screenshot gagal dibuat.', [
                'url' => $url,
                'message' => $exception->getMessage(),
                'exception' => get_class($exception),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
            ]);

            return null;
        }
    }

    private function captureFromExternalService(
        string $url,
        string $relativePath
    ): bool {
        $templates = config(
            'services.screenshots.external_templates',
            []
        );

        if (empty($templates)) {
            Log::info('Template screenshot external tidak diisi.', [
                'url' => $url,
            ]);

            return false;
        }

        $timeout = (int) config(
            'services.screenshots.external_timeout',
            15
        );
        $verifySsl = (bool) config(
            'services.screenshots.verify_ssl',
            false
        );

        foreach ($templates as $template) {
            $serviceUrl = str_replace(
                '{url}',
                rawurlencode($url),
                $template
            );

            $request = Http::timeout($timeout)
                ->connectTimeout(5)
                ->withHeaders([
                    'User-Agent' => 'WebAnalyzerBot/1.0',
                ]);

            if (! $verifySsl) {
                $request->withoutVerifying();
            }

            try {
                $response = $request->get($serviceUrl);
            } catch (Throwable $exception) {
                Log::warning('Permintaan screenshot external gagal.', [
                    'url' => $url,
                    'service' => $serviceUrl,
                    'message' => $exception->getMessage(),
                ]);

                continue;
            }

            if (! $response->successful()) {
                Log::warning('Screenshot external mengembalikan status error.', [
                    'url' => $url,
                    'service' => $serviceUrl,
                    'status' => $response->status(),
                ]);

                continue;
            }

            $body = $response->body();

            if (empty($body) || strlen($body) < 100) {
                Log::warning('Screenshot external mengembalikan body kosong.', [
                    'url' => $url,
                    'service' => $serviceUrl,
                    'status' => $response->status(),
                    'content_type' => $response->header('Content-Type'),
                    'body_size' => strlen($body),
                ]);

                continue;
            }

            $contentType = strtolower((string) $response->header('Content-Type'));

            if (! str_contains($contentType, 'image') && ! $response->isBinary()) {
                Log::warning('Screenshot external bukan gambar.', [
                    'url' => $url,
                    'service' => $serviceUrl,
                    'content_type' => $contentType,
                ]);

                continue;
            }

            if (Storage::disk('public')->exists($relativePath)) {
                Storage::disk('public')->delete($relativePath);
            }

            Storage::disk('public')->put(
                $relativePath,
                $response->body()
            );

            if (Storage::disk('public')->exists($relativePath)) {
                Log::info('Screenshot external berhasil disimpan.', [
                    'url' => $url,
                    'service' => $serviceUrl,
                    'bytes' => $response->body()
                        ? strlen($response->body())
                        : 0,
                ]);

                return true;
            }
        }

        Log::error('Semua layanan screenshot external gagal.', [
            'url' => $url,
            'templates' => $templates,
        ]);

        return false;
    }

    private function nodeAvailable(): bool
    {
        if (! function_exists('exec')) {
            return false;
        }

        $output = [];
        $exitCode = 1;

        @exec('node --version 2>&1', $output, $exitCode);

        return $exitCode === 0 && ! empty($output);
    }
}
