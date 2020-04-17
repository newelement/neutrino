<?php

namespace Newelement\Neutrino\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Newelement\Searchable\SearchableTrait;
use Kyslik\ColumnSortable\Sortable;
use DB, Auth;

class Page extends Model
{
	use SoftDeletes, SearchableTrait, Sortable;

	protected $searchable = [
        'columns' => [
            'title' => 7,
            'content' => 5
        ],
    ];

    public $sortable = [
        'title',
        'status',
        'sort',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at'
    ];

	protected $fillable = [
			'title',
			'slug',
			'content',
            'block_content',
            'short_content',
			'status',
            'editor_type',
			'parent_id',
			'created_at',
			'updated_at',
			'deleted_at',
			'keywords',
			'meta_description',
			'social_image',
            'sort',
            'template',
            'sitemap_change',
            'sitemap_priority',
			'protected',
			'created_by',
			'updated_by'
		];

	public static function boot()
    {
	    parent::boot();
	    static::creating(function($model){
			if (!app()->runningInConsole()) {
	           $user = Auth::user();
	           $model->created_by = $user->id;
	           $model->updated_by = $user->id;
		   } else{
			   $model->created_by = 1;
   				$model->updated_by = 1;
		   }
	    });

		static::updating(function($model){
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
		return $this->hasOne('\Newelement\Neutrino\Models\ObjectMedia', 'object_id', 'id')->where(['object_type' => 'page', 'featured' => 1]);
    }

    public function getBlockContentAttribute($value)
    {
        return $value? $value : json_encode([]);
    }

	public function url()
	{
		$url = $this->generateUrl();
		return '/'.$url;
	}

	public function parent()
	{
		return $this->belongsTo(self::class, 'parent_id', 'id' );
	}

    public function children()
    {
        return $this->hasMany(self::class, 'parent_id', 'id');
    }

	private function generateUrl($parent_id = 0)
	{
		if( $parent_id === 0 ){
			$parent_id = $this->parent_id;
			$paths[] = $this->slug;
		} else {
			$parent = DB::table('pages')->where('id', $parent_id)->first();
			$parent_id = $parent->parent_id;
			$paths[] = $parent->slug;
		}

		if ($parent_id > 0){
			$paths[] = $this->generateUrl($parent_id);
		}

		$paths = array_reverse($paths);
		$path = implode('/',$paths);

		return $path;
	}

    public function editing()
    {
        return $this->hasOne('Newelement\Neutrino\Models\ObjectEditing', 'object_id', 'id')->where('object_type', 'page');
    }

}
