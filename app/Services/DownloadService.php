<?php

namespace App\Services;

use App\Models\Download;
use App\Models\ProductFile;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class DownloadService
{
    public function getDownload(Download $download): ?BinaryFileResponse
    {
        if (!$download->isValid()) {
            return null;
        }

        $file = $download->productFile;
        $path = $file->getStoragePath();

        if (!Storage::disk($file->disk)->exists($file->path)) {
            return null;
        }

        $download->incrementDownload(request()->ip());

        $response = new BinaryFileResponse($path);
        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $file->name . '.' . $this->getExtension($file->mime),
            $file->name . '.' . $this->getExtension($file->mime)
        );
        $response->headers->set('X-Accel-Redirect', '/protected/' . $file->path); // For nginx X-Accel
        $response->headers->set('Cache-Control', 'private, must-revalidate');
        $response->headers->set('Pragma', 'private');

        return $response;
    }

    public function getProductFileStream(ProductFile $file): ?BinaryFileResponse
    {
        $path = $file->getStoragePath();
        if (!Storage::disk($file->disk)->exists($file->path)) {
            return null;
        }

        $response = new BinaryFileResponse($path);
        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_INLINE,
            $file->name . '.' . $this->getExtension($file->mime),
            $file->name . '.' . $this->getExtension($file->mime)
        );
        return $response;
    }

    protected function getExtension(string $mime): string
    {
        $map = [
            'application/pdf' => 'pdf',
            'video/mp4' => 'mp4',
            'audio/mpeg' => 'mp3',
            'application/zip' => 'zip',
            'application/x-rar-compressed' => 'rar',
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'text/plain' => 'txt',
            'application/json' => 'json',
        ];
        return $map[$mime] ?? 'bin';
    }
}