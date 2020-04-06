<?php

namespace Newelement\Neutrino\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class Taxonomy extends Model
{

	public function parentId()
    {
		return $this->belongsTo(self::class, 'parent_id', 'id');
    }

    public function taxonomyType()
    {
        return $this->belongsTo('\Newelement\Neutrino\Models\TaxonomyType', 'taxonomy_type_id', 'id');
    }

	public function featuredImage()
    {
		return $this->hasOne('\Newelement\Neutrino\Models\ObjectMedia', 'object_id', 'id')->where(['object_type' => 'taxonomy', 'featured' => 1]);
    }

	public function children()
    {
		return $this->hasMany(self::class, 'parent_id', 'id')->orderBy('sort', 'asc')->orderBy('title', 'asc');
    }

    public function products()
    {
        return $this->hasMany('\Newelement\Neutrino\Models\ObjectTerm', 'taxonomy_id', 'id')
                ->where('object_type', 'product')
                ->with('products');
            /*
        return $this->hasManyThrough('\Newelement\Shoppe\Models\Product', '\Newelement\Neutrino\Models\ObjectTerm', 'taxonomy_id', 'id', 'id', 'object_id')
                ->where('object_type', 'product')
                ->with('products');
                */
    }

	public function url()
	{
		$url = $this->generateUrl();
		$tax = DB::table('taxonomy_types')->where('id', $this->taxonomy_type_id)->first();
        if( count(\Request::segments()) > 1 && $tax->slug !== 'product-category' ){
            $fullUrl = '/'.$tax->slug.'/'.$url;
        }
        if( $tax->slug === 'product-category' ){
            $fullUrl = strpos( $url, config('shoppe.slugs.store_landing') )? '/'.config('shoppe.slugs.store_landing').'/'.str_replace( config('shoppe.slugs.store_landing'), '', $url) : '/'.config('shoppe.slugs.store_landing').'/'.$url;
        }
		return $fullUrl;
	}

	private function generateUrl($parent_id = 0)
	{
		if( $parent_id === 0 ){
			$parent_id = $this->parent_id;
			$paths[] = $this->slug;
		} else {
			$parent = DB::table('taxonomies')->where('id', $parent_id)->first();
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

}
