<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class UploadController extends Controller
{
    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'bucket' => 'required|string'
        ]);

        $file = $request->file('file');
        $bucket = $request->bucket;
        $fileName = Str::uuid() . '.' . $file->getClientOriginalExtension();
        
        $supabaseUrl = env('SUPABASE_URL');
        $supabaseKey = env('SUPABASE_SERVICE_ROLE_KEY') ?? env('SUPABASE_KEY');

        if (!$supabaseUrl || !$supabaseKey) {
            return response()->json(['message' => 'Supabase credentials not set in .env'], 500);
        }

        $url = "{$supabaseUrl}/storage/v1/object/{$bucket}/{$fileName}";

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $supabaseKey,
            'Content-Type' => $file->getMimeType(),
        ])->withBody(file_get_contents($file->getRealPath()), $file->getMimeType())
          ->post($url);

        if ($response->successful()) {
            $publicUrl = "{$supabaseUrl}/storage/v1/object/public/{$bucket}/{$fileName}";
            return response()->json([
                'message' => 'Upload success',
                'url' => $publicUrl
            ]);
        }

        return response()->json([
            'message' => 'Upload failed',
            'error' => $response->json()
        ], 400);
    }
}
