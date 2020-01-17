<?php

namespace Newelement\Neutrino\Models;

use Illuminate\Database\Eloquent\Model;
use Newelement\Neutrino\Facades\Neutrino;

class Menu extends Model
{

	protected $fillable = [
			'name',
			'created_at',
			'updated_at',
		];

	public function menuItems()
    {
        return $this->hasMany('\Newelement\Neutrino\Models\MenuItem');
    }
}
