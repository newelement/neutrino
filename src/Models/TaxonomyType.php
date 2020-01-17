<?php

namespace Newelement\Neutrino\Models;

use Illuminate\Database\Eloquent\Model;

class TaxonomyType extends Model
{
	protected $fillable = [
			'title',
			'slug',
			'description',
			'status',
			'show_on',
			'short',
			'created_at',
			'updated_at'
		];
}
