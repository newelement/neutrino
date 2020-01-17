<?php

namespace Newelement\Neutrino\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Form extends Model
{
	use SoftDeletes;

	protected $with = ['fields'];

	public function fields()
	{
		return $this->hasMany('\Newelement\Neutrino\Models\FormField')->orderBy('sort', 'asc');
	}

}
