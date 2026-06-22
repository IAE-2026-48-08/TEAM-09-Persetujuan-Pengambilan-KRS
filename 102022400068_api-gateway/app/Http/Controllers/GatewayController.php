<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GatewayController extends Controller
{
    /**
     * Catch-all proxy method to forward requests to target microservices.
     */
    public function proxy(Request $request, $any = '')
    {
        $path = $request->path(); // e.g., "api/v1/courses"
        
        // Determine backend service based on path prefix
        $targetUrl = null;
        if (str_starts_with($path, 'api/v1/students')) {
            $targetUrl = config('services.student.url');
        } elseif (str_starts_with($path, 'api/v1/courses') || str_starts_with($path, 'api/v1/krs')) {
            $targetUrl = config('services.krs.url');
        } elseif (str_starts_with($path, 'api/v1/curriculums') || str_starts_with($path, 'api/v1/grades')) {
            $targetUrl = config('services.grades.url');
        }

        if (!$targetUrl) {
            Log::warning("Gateway routing failed: Service not found for path /$path");
            return response()->json([
                'status' => 'error',
                'message' => 'Service not found or routing not configured for: /' . $path
            ], 404);
        }

        // Build target destination URL preserving path and query string
        $requestUri = ltrim($request->getRequestUri(), '/');
        $destination = rtrim($targetUrl, '/') . '/' . $requestUri;

        Log::info("Gateway routing request: [{$request->method()}] /$path -> $destination");

        // Forward matching request headers (anti-header stripping & JSON support)
        $headers = [];
        $allowedHeaders = [
            'Authorization',
            'X-IAE-KEY',
            'X-API-KEY',       // Diperlukan oleh student-service
            'Accept',          // Sangat penting agar downstream service merespon dengan format JSON, bukan HTML
            'Content-Type',
            'User-Agent',
        ];

        foreach ($allowedHeaders as $headerName) {
            if ($request->hasHeader($headerName)) {
                $headers[$headerName] = $request->header($headerName);
            }
        }

        try {
            // Forward HTTP request dengan Timeout untuk mencegah synchronous blocking
            $response = Http::withHeaders($headers)
                ->connectTimeout(3) // Timeout saat menghubungkan (dalam detik)
                ->timeout(10)       // Timeout maksimum eksekusi request (dalam detik)
                ->send($request->method(), $destination, [
                    'body' => $request->getContent(),
                    'query' => $request->query()
                ]);

            // Forward response back to the client
            return response($response->body(), $response->status())
                ->withHeaders($response->headers());
        } catch (\Exception $e) {
            Log::error("Gateway forwarding exception: " . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Gateway forwarding error (Timeout/Connection Refused): ' . $e->getMessage()
            ], 504);
        }
    }
}
