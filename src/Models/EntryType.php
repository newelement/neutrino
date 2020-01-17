<?php

namespace Newelement\Neutrino\Models;

use Illuminate\Database\Eloquent\Model;

class EntryType extends Model
{
	protected $fillable = [
			'entry_type',
			'slug',
			'label_plural',
			'searchable',
			'created_at',
			'updated_at'
		];
}
