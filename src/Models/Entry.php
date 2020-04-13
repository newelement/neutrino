<?php

namespace Newelement\Neutrino\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Newelement\Searchable\SearchableTrait;
use Kyslik\ColumnSortable\Sortable;
use Auth;

class Entry extends Model
{
	use SoftDeletes, SearchableTrait, Sortable;

    protected $dates = [
        'publish_date',
    ];

	protected $searchable = [
        'columns' => [
            'title' => 7,
            'content' => 5,
            'short_content' => 5
        ],
    ];

    public $sortable = [
        'title',
        'status',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at'
    ];

	public static function boot()
    {
       parent::boot();
       static::creating(function($model)
       {
           $user = Auth::user();
           $model->created_by = $user->id;
           $model->updated_by = $user->id;
       });
       static::updating(function($model)
       {
           $user = Auth::user();
           $model->updated_by = $user->id;
       });
   	}

	public function createdUser()
	{
		return $this->belongsTo('\Newelement\Neutrino\Models\User', 'created_by');
	}

	public function updatedUser()
	{
		return $this->belongsTo('\Newelement\Neutrino\Models\User', 'updated_by');
	}

	public function featuredImage()
    {
		return $this->hasOne('\Newelement\Neutrino\Models\ObjectMedia', 'object_id', 'id')->where(['object_type' => 'entry', 'featured' => 1]);
    }

    public function getBlockContentAttribute($value)
    {
        return $value? $value : json_encode([]);
    }

	public function comments()
	{
		return $this->hasMany('\Newelement\Neutrino\Models\Comment');
	}

	public function url()
	{
		return '/'.$this->entry_type.'/'.$this->slug;
	}

    public function editing()
    {
        return $this->hasOne('Newelement\Neutrino\Models\ObjectEditing', 'object_id', 'id')->where('object_type', 'entry');
    }

}
