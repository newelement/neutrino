<?php

namespace Newelement\Neutrino\Models;

use Illuminate\Database\Eloquent\Model;

class ObjectMedia extends Model
{

	protected $fillable = [
			'object_id',
			'object_type',
			'file_path',
			'featured',
			'media_group_type',
		];

}
