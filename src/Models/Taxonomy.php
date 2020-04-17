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

    public function getProductCountAttribute()
    {
        $order = 'products.title';
        $dir = 'asc';

        $filters = $this->getFilters();

        $where = [
            'ot.taxonomy_id' => $this->id,
            'products.status' => 'P',
            'ot.object_type' => 'product'
        ];

        $count = \Newelement\Shoppe\Models\Product::query();
        $count = $count->join('object_terms AS ot', 'ot.object_id', '=', 'products.id');
        $i = 0;
        foreach( $filters as $slug => $value ){
            $i++;
            $count = $count->join('object_terms AS ot'.$i, 'ot'.$i.'.object_id', '=', 'products.id');
        }
        $count = $count->join('taxonomies AS t', 't.id', '=', 'ot.taxonomy_id');
        $count = $count->where($where);

        $c = 0;
        foreach( $filters as $slug => $value ){
            $c++;
            $taxonomy = \Newelement\Neutrino\Models\TaxonomyType::where('slug', $slug)->first();
            $term = self::where('slug', $value)->first();
            $count = $count->where([
                'ot'.$c.'.taxonomy_type_id' => $taxonomy->id,
                'ot'.$c.'.taxonomy_id' => $term->id,
                'ot'.$c.'.object_type' => 'product'
            ]);
        }
        $count = $count->orderBy($order, $dir);
        $results = $count->count();

        return $results;
    }

    private function getFilters()
    {
        $filters = [];
        $currentQueries = request()->query();
        if( !isset($currentQueries['filters']) ){
            return $filters;
        }
        $filtersArr = $currentQueries['filters'];
        foreach( $filtersArr as $name => $value ){
            if( is_array($value)  ){
                foreach( $value as $v ){
                    $filters[$name][] = $v;
                }
            } else {
                $filters[$name][] = $value;
            }
        }
        return $filters;
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

        $fullUrl = '/'.$url;

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
