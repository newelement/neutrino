<?php

namespace Newelement\Neutrino\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $fillable = [
            'activity_group',
            'activity_package',
            'object_id',
            'object_type',
            'content',
            'log_level',
            'created_by_string',
            'created_by',
            'updated_by'
        ];

    public function createdUser()
    {
        return $this->belongsTo('\Newelement\Neutrino\Models\User', 'created_by');
    }

    public function getObjectFormattedAttribute()
    {
        //return $this->statuses[ $this->object_type ];
    }
}
