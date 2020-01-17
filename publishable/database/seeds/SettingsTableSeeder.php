<?php

use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Newelement\Neutrino\Models\Setting;

class SettingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
         Setting::updateOrCreate(
			[
	            'key' => 'save_form',
	            'value' => '',
				'value_bool' => 1,
				'type' => 'BOOL',
				'label' => 'Save Form Data',
				'details' => 'Save form fields when someone submits a form.',
	            'protected' => 1,
				'order' => 0
			]
		);
        Setting::updateOrCreate(
			[
	            'key' => 'moderate_comments',
	            'value' => '',
				'value_bool' => 1,
				'type' => 'BOOL',
				'label' => 'Moderate Comments',
				'details' => 'Approve or delete comments.',
	            'protected' => 1,
				'order' => 0
			]
		);
        Setting::updateOrCreate(
			[
	            'key' => 'allow_comments',
	            'value' => '',
				'value_bool' => 1,
				'type' => 'BOOL',
				'label' => 'Global Allow Comments',
				'details' => 'Turn off/on comments globally.',
	            'protected' => 1,
				'order' => 0
			]
		);
        Setting::updateOrCreate(
            [
                'key' => 'enable_block_editor',
                'value' => '',
                'value_bool' => 1,
                'type' => 'BOOL',
                'label' => 'Enable Block Edtior',
                'details' => 'Use the block editor for pages and entries.',
                'protected' => 1,
                'order' => 0
            ]
        );
        Setting::updateOrCreate(
			[
	            'key' => 'cache',
	            'value' => '',
				'value_bool' => 1,
				'type' => 'BOOL',
				'label' => 'Global cache',
				'details' => 'Turn off/on cache globally.',
	            'protected' => 1,
				'order' => 0
			]
		);
    }
}
