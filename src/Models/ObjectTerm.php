<?php

namespace Newelement\Neutrino\Models;

use Illuminate\Database\Eloquent\Model;

class ObjectTerm extends Model
{

	protected $fillable = [
			'object_id',
			'taxonomy_type_id',
			'taxonomy_id',
			'object_type',
		];

	/*public function parentId()
    {
		return $this->belongsTo(self::class, 'parent_id', 'id');
    }*/

	/*public function entries()
	{
		return $this->hasMany('\Newelement\Neutrino\Models\Entry', 'id', 'object_id');
	}*/
}
