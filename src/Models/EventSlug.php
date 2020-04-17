<?php

namespace Newelement\Neutrino\Models;

use Illuminate\Database\Eloquent\Model;
use Newelement\Neutrino\Facades\Neutrino;

class EventSlug extends Model
{
    protected $fillable = [
        'event_id',
        'slug'
        'sitemap_change',
        'sitemap_priority'
    ];
}
