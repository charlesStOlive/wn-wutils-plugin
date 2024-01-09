<?php

namespace Waka\Wutils\Classes;
use File;
use Winter\Storm\Database\Attach\Resizer;
use \Illuminate\Http\UploadedFile;
use Input;

use Carbon\Carbon;

class ImageUploadOptimiser
{
    public static function reduceUploadedSize($file, $maxSize)
    {
            $uploadedFile = Input::file('file_data');
            $originalName = $uploadedFile->getClientOriginalName();
            $originalExtension = $uploadedFile->getClientOriginalExtension();
            $tempFilePath = tempnam(sys_get_temp_dir(), 'resize') . '.' . $originalExtension;
            copy($uploadedFile->getRealPath(), $tempFilePath);

            list($width, $height) = getimagesize($tempFilePath);
            $largest = max($width, $height);
            $ratioReduction = $maxSize / $largest;
            trace_log($ratioReduction);

            if ($ratioReduction < 1) {
                $newWidth = $width * $ratioReduction;
                $newHeight = $height * $ratioReduction;
                Resizer::open($tempFilePath)
                    ->resize($newWidth, $newHeight, ['mode' => 'auto'])
                    ->save($tempFilePath);
            }

            // Créer une nouvelle instance de UploadedFile avec le fichier redimensionné
            $resizedFile = new UploadedFile($tempFilePath, $originalName);
            $fileSize = filesize($tempFilePath); // Obtenez la taille du fichier redimensionné
            $file->file_size = $fileSize;
            unlink($uploadedFile);
            return $resizedFile;
    }
}
