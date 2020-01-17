<?php

namespace Newelement\Neutrino\Models;

use Illuminate\Database\Eloquent\Model;

class CfObjectData extends Model
{

	protected $fillable = [
			'object_id',
			'field_id',
			'field_type',
			'field_text',
			'field_number',
			'field_decimal',
			'field_editor',
			'field_file',
			'field_image',
			'field_config',
			'field_date',
			'parent_field_id',
			'field_name',
			'object_type',
			'batch_id',
			'content_id',
			'batch_sort'
		];

	public function getField()
	{
		return $this->belongsTo('\Newelement\Neutrino\Models\CfField', 'field_id', 'field_id');
	}
}
