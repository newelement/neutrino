<?php

namespace Newelement\Neutrino\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ObjectTaxonomy extends Model
{
	use SoftDeletes;

	/*public function parentId()
    {
		return $this->belongsTo(self::class, 'parent_id', 'id');
    }*/

}
