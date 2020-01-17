<?php

use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Newelement\Neutrino\Models\TaxonomyType;

class TaxonomyTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        TaxonomyType::updateOrCreate([
            'title' => 'Category',
            'slug' => 'category',
			'show_on' => 'post'
        ],['sort' => 0]);
    }
}
