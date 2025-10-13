<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\ImageManagerStatic;

class ImageConverterService
{
    /**
     * Convert uploaded image to WebP format and save to storage
     *
     * @param UploadedFile $file
     * @param string $directory
     * @param int $quality
     * @param int|null $width
     * @param int|null $height
     * @return string|null
     */
    public static function convertToWebP(UploadedFile $file, string $directory = 'warungku', int $quality = 85, ?int $width = null, ?int $height = null): ?string
    {
        try {
            // Generate unique filename with .webp extension
            $filename = uniqid() . '_' . time() . '.webp';
            $path = $directory . '/' . $filename;

            // Create image instance using ImageManagerStatic
            $image = ImageManagerStatic::make($file);

            // Resize if dimensions are provided
            if ($width || $height) {
                $image->resize($width, $height, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
            }

            // Convert to WebP and save
            $image->encode('webp', $quality);
            
            // Save to storage
            Storage::disk('public')->put($path, $image->stream());

            return $path;

        } catch (\Exception $e) {
            \Log::error('Image conversion failed: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Convert existing image file to WebP format
     *
     * @param string $filePath
     * @param string $directory
     * @param int $quality
     * @param int|null $width
     * @param int|null $height
     * @return string|null
     */
    public static function convertExistingToWebP(string $filePath, string $directory = 'warungku', int $quality = 85, ?int $width = null, ?int $height = null): ?string
    {
        try {
            // Check if file exists
            if (!Storage::disk('public')->exists($filePath)) {
                return null;
            }

            // Generate unique filename with .webp extension
            $filename = uniqid() . '_' . time() . '.webp';
            $newPath = $directory . '/' . $filename;

            // Create image instance from existing file using ImageManagerStatic
            $image = ImageManagerStatic::make(Storage::disk('public')->path($filePath));

            // Resize if dimensions are provided
            if ($width || $height) {
                $image->resize($width, $height, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
            }

            // Convert to WebP and save
            $image->encode('webp', $quality);
            
            // Save to storage
            Storage::disk('public')->put($newPath, $image->stream());

            return $newPath;

        } catch (\Exception $e) {
            \Log::error('Image conversion failed: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Delete old image file if it exists
     *
     * @param string|null $filePath
     * @return bool
     */
    public static function deleteImage(?string $filePath): bool
    {
        if (!$filePath || !Storage::disk('public')->exists($filePath)) {
            return false;
        }

        try {
            Storage::disk('public')->delete($filePath);
            return true;
        } catch (\Exception $e) {
            \Log::error('Image deletion failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get optimized image URL
     *
     * @param string|null $filePath
     * @return string|null
     */
    public static function getImageUrl(?string $filePath): ?string
    {
        if (!$filePath) {
            return null;
        }

        if (preg_match('#^https?://#', $filePath)) {
            return $filePath;
        }

        return asset('storage/' . ltrim($filePath, '/'));
    }
}
