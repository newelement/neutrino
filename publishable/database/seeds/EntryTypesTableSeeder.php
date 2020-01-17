<?php

use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Newelement\Neutrino\Models\EntryType;

class EntryTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        EntryType::updateOrCreate([
            'entry_type' => 'Post',
            'slug' => 'post',
            'searchable' => 1,
        ]);
    }
}
