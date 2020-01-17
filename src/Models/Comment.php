<?php

namespace Newelement\Neutrino\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comment extends Model
{
	use SoftDeletes;

	public function entry()
	{
		return $this->belongsTo('\Newelement\Neutrino\Models\Entry');
	}

	public function createdUser()
	{
		return $this->belongsTo('\Newelement\Neutrino\Models\User', 'created_by');
	}
}
