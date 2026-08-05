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

            if (Storage::disk('public')->exists($relativePath)) {
                Storage::disk('public')->delete($relativePath);
            }

            $canBrowsershot = config('services.browsershot.enabled')
                && $this->nodeAvailable();

            /*
            |--------------------------------------------------------------------------
            | Provider: browsershot (Node.js + Chromium lokal)
            |--------------------------------------------------------------------------
            | Jika diminta, aktif, dan Node tersedia, coba tangkap lewat Chrome.
            | Apabila gagal (Chrome diblokir situs, timeout, dll.) otomatis
            | dilanjutkan ke layanan external agar screenshot tetap muncul.
            |
            */
            if ($provider === 'browsershot' && $canBrowsershot) {
                try {
                    if ($this->captureFromBrowsershot($url, $relativePath)) {
                        return $relativePath;
                    }
                } catch (Throwable $exception) {
                    Log::warning('Browsershot gagal, dicoba layanan screenshot external.', [
                        'url' => $url,
                        'message' => $exception->getMessage(),
                        'exception' => get_class($exception),
                    ]);
                }

                $this->forgetPartialScreenshot($relativePath);

                return $this->captureFromExternalService($url, $relativePath)
                    ? $relativePath
                    : null;
            }

            /*
            |--------------------------------------------------------------------------
            | Provider: external (tanpa Node, cocok untuk shared hosting)
            |--------------------------------------------------------------------------
            */
            if ($provider === 'external') {
                return $this->captureFromExternalService($url, $relativePath)
                    ? $relativePath
                    : null;
            }

            /*
            |--------------------------------------------------------------------------
            | Provider browsershot tetapi Node tidak tersedia / nonaktif
            |--------------------------------------------------------------------------
            | Fallback otomatis ke layanan external agar screenshot tetap muncul
            | di lingkungan yang tidak bisa menjalankan Node.js (shared hosting).
            |
            */
            if ($provider === 'browsershot') {
                Log::info('Node.js tidak tersedia atau browsershot nonaktif, fallback ke layanan external.', [
                    'url' => $url,
                ]);

                return $this->captureFromExternalService($url, $relativePath)
                    ? $relativePath
                    : null;
            }

            return null;
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

    private function captureFromBrowsershot(
        string $url,
        string $relativePath
    ): bool {
        $absolutePath = Storage::disk('public')->path($relativePath);

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

            return false;
        }

        return true;
    }

    private function forgetPartialScreenshot(string $relativePath): void
    {
        if (Storage::disk('public')->exists($relativePath)) {
            Storage::disk('public')->delete($relativePath);
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
