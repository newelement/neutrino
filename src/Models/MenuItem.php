<?php

namespace Newelement\Neutrino\Models;

use Illuminate\Database\Eloquent\Model;
use Newelement\Neutrino\Facades\Neutrino;

class MenuItem extends Model
{
	protected $fillable = [
			'menu_id',
			'title',
			'url',
			'target',
			'parameters',
			'parent_id',
			'order',
			'created_at',
			'updated_at',
		];
}
