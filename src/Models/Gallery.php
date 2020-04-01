<?php

namespace Newelement\Neutrino\Models;

use Illuminate\Database\Eloquent\Model;
use Newelement\Neutrino\Facades\Neutrino;
use Illuminate\Database\Eloquent\SoftDeletes;

class Gallery extends Model
{
    use SoftDeletes;

    public function images()
    {
        return $this->hasMany('Newelement\Neutrino\Models\GalleryImage')->orderBy('sort', 'asc');
    }

    public function featuredImages()
    {
        return $this->hasMany('Newelement\Neutrino\Models\GalleryImage')->where('featured', 1);
    }
}
