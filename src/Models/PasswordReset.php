<?php

namespace Newelement\Neutrino\Models;

use Illuminate\Database\Eloquent\Model;

class PasswordReset extends Model
{

	protected $fillable = [
			'email',
			'token',
			'created_at',
		];

}
