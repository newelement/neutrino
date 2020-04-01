<?php

namespace Newelement\Neutrino\Models;

use Illuminate\Database\Eloquent\Model;
use Newelement\Neutrino\Facades\Neutrino;

class GalleryImage extends Model
{
    protected $fillable = [
        'gallery_id',
        'title',
        'image_path',
        'featured',
        'description',
        'caption',
        'sort'
    ];

}
