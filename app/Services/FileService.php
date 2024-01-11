<?php

namespace App\Services;

use Intervention\Image\Image;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Imagick\Driver;

class FileService
{
    public function updateFile($model, $request, $type)
    {
        if (!empty($model->file)) {
            $currentFile = public_path() . $model->file;

            if (file_exists($currentFile) && $currentFile != public_path() . '/user-placeholder.png') {
                unlink($currentFile);
            }
        }

        $file = null;

        $manager = new ImageManager(new Driver());

        if ($type === 'user') {
            $file = $manager->read($request->file('file'))->resize(400, 400);
        } else {
            $file = $manager->read($request->file('file'));
        }

        $ext = $request->file('file');
        $extension = $ext->getClientOriginalExtension();
        $name = time() . '.' . $extension;
        $file->toPng()->save(base_path('public/file/' . $name));
        $model->file = 'public/file/' . $name;

        return $model;
    }
}
