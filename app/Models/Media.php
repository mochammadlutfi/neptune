<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Plank\Mediable\Media as BaseMedia;

class Media extends BaseMedia
{
    protected $appends = ['original_url', 'readable_size'];

    public function getOriginalUrlAttribute()
    {
        return $this->getUrl(); // relatif
    }

    public function getReadableSizeAttribute()
    {
        $bytes = $this->size;
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }
        return round($bytes, 1) . ' ' . $units[$i];
    }

}
