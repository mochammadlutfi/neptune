<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Plank\Mediable\Facades\MediaUploader;
use App\Models\Media;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class MediaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Media::query();
        
        // Search by filename
        if ($request->filled('q')) {
            $query->where('filename', 'like', '%' . $request->q . '%');
        }
        
        // Filter by media type
        if ($request->filled('filter_type')) {
            $filterType = $request->filter_type;
            switch ($filterType) {
                case 'image':
                    $query->where('mime_type', 'like', 'image/%');
                    break;
                case 'video':
                    $query->where('mime_type', 'like', 'video/%');
                    break;
                case 'audio':
                    $query->where('mime_type', 'like', 'audio/%');
                    break;
                case 'document':
                    $query->where(function($q) {
                        $q->where('mime_type', 'like', 'application/%')
                          ->orWhere('mime_type', 'like', 'text/%')
                          ->where('mime_type', 'not like', 'image/%')
                          ->where('mime_type', 'not like', 'video/%')
                          ->where('mime_type', 'not like', 'audio/%');
                    });
                    break;
            }
        }
        
        // Sort by latest
        $query->latest();
        
        // Pagination
        $limit = $request->input('limit', 24);
        $data = $query->paginate($limit);
        
        // Transform data to include additional info
        $data->getCollection()->transform(function ($media) {
            return [
                'id' => $media->id,
                'filename' => $media->filename,
                'mime_type' => $media->mime_type,
                'size' => $media->size,
                'readable_size' => $this->formatFileSize($media->size),
                'original_url' => $media->getUrl(),
                'created_at' => $media->created_at,
                'dimensions' => $this->getImageDimensions($media)
            ];
        });
        
        return response()->json($data);
    }
    
    /**
     * Format file size to human readable format
     */
    private function formatFileSize($bytes)
    {
        if ($bytes == 0) return '0 Bytes';
        
        $k = 1024;
        $sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
        $i = floor(log($bytes) / log($k));
        
        return round($bytes / pow($k, $i), 2) . ' ' . $sizes[$i];
    }
    
    /**
     * Get image dimensions if it's an image
     */
    private function getImageDimensions($media)
    {
        if (strpos($media->mime_type, 'image/') === 0) {
            try {
                $path = Storage::disk($media->disk)->path($media->getDiskPath());
                if (file_exists($path)) {
                    $imageSize = getimagesize($path);
                    if ($imageSize) {
                        return $imageSize[0] . ' × ' . $imageSize[1] . ' px';
                    }
                }
            } catch (\Exception $e) {
                // Ignore errors
            }
        }
        return null;
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:10240', // 10MB
        ]);

        try {
            $media = MediaUploader::fromSource($request->file('file'))
                ->toDisk('public')
                ->toDirectory('uploads')
                ->upload();

            return response()->json([
                'success' => true,
                'media' => [
                    'id' => $media->id,
                    'filename' => $media->filename,
                    'mime_type' => $media->mime_type,
                    'size' => $media->size,
                    'readable_size' => $this->formatFileSize($media->size),
                    'original_url' => $media->getUrl(),
                    'created_at' => $media->created_at,
                    'dimensions' => $this->getImageDimensions($media)
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload file: ' . $e->getMessage()
            ], 422);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
