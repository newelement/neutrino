<?php

use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Newelement\Neutrino\Models\Menu;
use Newelement\Neutrino\Models\MenuItem;

class MenuTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Menu::updateOrCreate([
            'name' => 'Main Menu'
        ]);

		MenuItem::updateOrCreate([
			'menu_id' => 1,
            'title' => 'Home',
            'url' => '/',
			'target' => 'self',
			'parent_id' => 0,
			'order' => 0,
			'parameters' => 'url',
        ]);
        
        MenuItem::updateOrCreate([
			'menu_id' => 1,
            'title' => 'Blog',
            'url' => '/post',
			'target' => 'self',
			'parent_id' => 0,
			'order' => 1,
			'parameters' => 'entry_type',
        ]);
    }
}
