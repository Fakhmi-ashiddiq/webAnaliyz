<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class StorageController extends Controller
{
    public function show(string $path): Response
    {
        $disk = Storage::disk('public');

        abort_if(
            ! $disk->exists($path),
            404
        );

        return $disk->response($path);
    }
}
