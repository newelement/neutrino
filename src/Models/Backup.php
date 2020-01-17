<?php

namespace Newelement\Neutrino\Models;

use Illuminate\Database\Eloquent\Model;

class Backup extends Model
{
	protected $fillable = [
			'object_id',
			'object_type',
			'content'
		];

}
