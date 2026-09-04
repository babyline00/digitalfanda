<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Download;
use App\Services\DownloadService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DownloadController extends Controller
{
    public function __construct(protected DownloadService $downloadService) {}

    public function download(string $token)
    {
        $download = Download::where('token', $token)
            ->with(['productFile', 'orderItem.order'])
            ->firstOrFail();

        abort_unless($download->isValid(), 403, 'Download link has expired or reached its limit.');

        $response = $this->downloadService->getDownload($download);

        if (!$response) {
            abort(404, 'File not found on server.');
        }

        return $response;
    }

    public function stream(string $token)
    {
        $download = Download::where('token', $token)
            ->with(['productFile', 'orderItem.order'])
            ->firstOrFail();

        abort_unless($download->isValid(), 403, 'Stream link has expired or reached its limit.');

        $file = $download->productFile;

        // For streaming, just return the file inline
        $response = $this->downloadService->getProductFileStream($file);

        if (!$response) {
            abort(404);
        }

        $download->incrementDownload(request()->ip());

        return $response;
    }
}