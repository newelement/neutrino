<?php

namespace Newelement\Neutrino\Models;

use Illuminate\Database\Eloquent\Model;

class TaxonomyType extends Model
{
	protected $fillable = [
			'title',
			'slug',
			'description',
            'meta_description',
            'keywords',
            'social_image_1',
            'social_image_2',
            'social_description',
			'status',
			'show_on',
			'sort',
			'created_at',
			'updated_at'
		];

    public function featuredImage()
    {
        return $this->hasOne('\Newelement\Neutrino\Models\ObjectMedia', 'object_id', 'id')->where(['object_type' => 'taxonomy_type', 'featured' => 1]);
    }
}
