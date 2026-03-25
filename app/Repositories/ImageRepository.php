<?php

namespace App\Repositories;

use Illuminate\Support\Facades\Storage;

class ImageRepository
{
    public function upload($file, $folder = 'uploads')
    {
        return $file->store($folder, 'public');
    }

    public function delete($path)
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }

    public function replace($file, $oldPath = null, $folder = 'uploads')
    {
        if ($oldPath) {
            $this->delete($oldPath);
        }

        return $this->upload($file, $folder);
    }
}