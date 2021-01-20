<?php

namespace Newelement\Neutrino\Models;

use Illuminate\Database\Eloquent\Model;

class FormField extends Model
{
	protected $fillable = [
		'form_id',
		'field_id',
		'field_type',
		'field_label',
		'field_name',
		'required',
		'placeholder',
		'max_length',
		'min_length',
		'settings',
		'select_multiple',
        'descriptive_text',
		'sort'
	];
}
