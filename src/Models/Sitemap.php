<?php

namespace Newelement\Neutrino\Models;

use Illuminate\Database\Eloquent\Model;

class Sitemap extends Model
{
    protected $table = 'sitemap';

    protected $guarded = [];

    public $timestamps = false;

    protected $primaryKey = null;

    public $incrementing = false;

}
