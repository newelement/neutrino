<?php
namespace Newelement\Neutrino\Models;

use Illuminate\Database\Eloquent\Model;

class ObjectEditing extends Model
{
	protected $fillable = [
			'object_id',
			'object_type',
			'user_id',
            'updated_at'
		];

    protected $table = 'object_editing';

    public function user()
    {
        return $this->belongsTo('Newelement\Neutrino\Models\User');
    }
}
