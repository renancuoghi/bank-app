<?php

namespace App\Helper\File;

use App\Helper\File\Exception\InvalidImageException;
use Illuminate\Support\Facades\Storage;

use Illuminate\Support\Str;
use Exception;

class ImageHelper
{
    public const VALID_IMAGES_TYPES = [
        'image/jpeg',
        'image/png',
        'image/gif'
    ];

    public const MAX_SIZE_KB = 500;

    /**
     * Storage a image into a public directory
     *
     * @param $imageBase64 base64 encoded
     * @param $imageName
     * @param $prefix
     *
     * @return string
     */
    public static function saveImage($imageBase64, $imageName = null, $prefix=''): string
    {
        $imageSize = self::getBase64ImageSizeKb($imageBase64);
        if ($imageSize > 0 && $imageSize < self::MAX_SIZE_KB) {
            if (in_array(self::getMimeType($imageBase64), self::VALID_IMAGES_TYPES)) {
                if (isset($imageName) === false) {
                    $imageName = $prefix . Str::uuid() . '.' . self::getExtension($imageBase64);
                }

                if (Storage::disk('images')->put($imageName, file_get_contents($imageBase64))) {
                    return  Storage::disk('images')->url($imageName);
                }
            } else {
                throw new InvalidImageException('Only jpg, png and gif are supported');
            }
        }
        throw new InvalidImageException('Image cannot be greater than {self::MAX_SIZE_KB} kb');
    }

    /**
     * Remove a image if it exists
     *
     * @param string $imageName
     */
    public static function deleteImage(string $imageName): void
    {
        if (self::existeImage($imageName)) {
            Storage::disk('images')->delete(self::onlyFileName($imageName));
        }
    }

    /**
     * Check if a image exist
     *
     * @param string $imageName
     */
    public static function existeImage(string $imageName): bool
    {
        return Storage::disk('images')->exists(self::onlyFileName($imageName));
    }

    /**
     * Get only the name of a image removing all the path or url
     *
     * @param string $imageName
     *
     * @return string
     */
    public static function onlyFileName(string $fileName): string
    {
        $image = self::cleanUrl($fileName);
        $path = explode('/', $image);
        return end($path);
    }

    /**
     * Clean a url from a string image
     *
     * @param string $imageName
     *
     * @return string
     */
    public static function cleanUrl(string $imageName): string
    {
        return str_replace(env('APP_URL'), '', $imageName);
    }

    // fazer o remover arquivo
    /**
     * Get mimetype of a file
     *
     * @param string $imageBase64
     *
     * @return string
     */
    public static function getMimeType($imageBase64): string
    {
        if (empty($imageBase64) === false) {
            try {
                return mime_content_type($imageBase64);
            } catch(Exception) {
            }
        }
        return '';
    }
    /**
     * Get a file extension
     *
     * @param string $imageBase64
     *
     * @return string
     */
    public static function getExtension($imageBase64): string
    {
        $mime = self::getMimeType($imageBase64);
        $arr = explode('/', $mime);
        return end($arr);
    }

    /**
     * Get a image size in kb
     *
     * @param string $imageBase64
     *
     * @return float
     */
    public static function getBase64ImageSizeKb($base64Image): float
    {
        try {
            $size_in_bytes = (int) (strlen(rtrim($base64Image, '=')) * 3 / 4);
            return $size_in_bytes / 1024;
        } catch(Exception) {
            return 0;
        }
    }
}
