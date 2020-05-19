<?php

namespace Newelement\Neutrino\Models;

use Illuminate\Database\Eloquent\Model;

class EntryType extends Model
{
	protected $fillable = [
			'entry_type',
			'slug',
			'label_plural',
            'meta_description',
            'keywords',
            'social_image_1',
            'social_image_2',
            'social_description',
			'searchable',
            'sitemap_change',
            'sitemap_piority',
			'created_at',
			'updated_at'
		];

    public function featuredImage()
    {
        return $this->hasOne('\Newelement\Neutrino\Models\ObjectMedia', 'object_id', 'id')->where(['object_type' => 'entry_type', 'featured' => 1]);
    }
}
