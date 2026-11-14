<?php

namespace App\Services;

use Cloudinary\Cloudinary;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Throwable;

// app/Services/MediaService.php
// uploads images to cloudinary and resolves public ids for deletion
class MediaService
{
    public function upload(UploadedFile $file, string $folder): string
    {
        $client = $this->client();

        try {
            $result = $client->uploadApi()->upload($file->getRealPath(), [
                'folder' => $folder,
                'resource_type' => 'image',
                'allowed_formats' => ['jpg', 'jpeg', 'png', 'webp'],
            ]);
        } catch (Throwable $exception) {
            Log::error('Cloudinary upload failed.', ['message' => $exception->getMessage()]);

            throw ValidationException::withMessages(['image' => ['Gagal mengunggah gambar ke Cloudinary.']]);
        }

        return $result['secure_url'];
    }

    public function delete(?string $url): void
    {
        $publicId = $this->publicIdFromUrl($url);
        if ($publicId === null) {
            return;
        }

        try {
            $this->client()->uploadApi()->destroy($publicId, ['resource_type' => 'image']);
        } catch (Throwable $exception) {
            Log::warning('Cloudinary asset deletion failed.', ['public_id' => $publicId, 'message' => $exception->getMessage()]);
        }
    }

    public function publicIdFromUrl(?string $url): ?string
    {
        if ($url === null || $url === '') {
            return null;
        }

        $path = parse_url($url, PHP_URL_PATH);
        if (! is_string($path) || ! preg_match('~/upload/(?:v[0-9]+/)?(.+?)(?:\.[a-zA-Z0-9]+)?$~', $path, $matches)) {
            return null;
        }

        return $matches[1];
    }

    private function client(): Cloudinary
    {
        $cloudName = config('services.cloudinary.cloud_name');
        if (blank($cloudName) || blank(config('services.cloudinary.api_key')) || blank(config('services.cloudinary.api_secret'))) {
            throw ValidationException::withMessages(['image' => ['Cloudinary belum dikonfigurasi. Isi CLOUDINARY_CLOUD_NAME, CLOUDINARY_API_KEY, dan CLOUDINARY_API_SECRET.']]);
        }

        return new Cloudinary([
            'cloud' => [
                'cloud_name' => $cloudName,
                'api_key' => config('services.cloudinary.api_key'),
                'api_secret' => config('services.cloudinary.api_secret'),
            ],
            'url' => ['secure' => true],
        ]);
    }
}
