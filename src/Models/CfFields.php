<?php

namespace Newelement\Neutrino\Models;

use Illuminate\Database\Eloquent\Model;

class CfFields extends Model
{

	protected $fillable = [
			'field_id',
			'group_id',
			'field_type',
			'field_config',
			'description',
			'sort',
			'field_label',
			'field_name',
			'field_required',
			'multiple_files',
			'allowed_filetypes',
			'empty_first_option',
			'repeater_id',
		];


}
