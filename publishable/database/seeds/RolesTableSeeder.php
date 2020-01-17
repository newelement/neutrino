<?php

use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Newelement\Neutrino\Models\Role;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Role::updateOrCreate([
            'name' => 'editor',
            'display_name' => 'Editor'
        ]);
        
        Role::updateOrCreate([
            'name' => 'guest',
            'display_name' => 'Guest'
        ]);
    }
}
