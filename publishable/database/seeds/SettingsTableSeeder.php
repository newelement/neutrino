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
         Setting::firstOrCreate(
			[
	            'key' => 'save_form'
            ],[
	            'value' => '',
				'value_bool' => 1,
				'type' => 'BOOL',
				'label' => 'Save Form Data',
				'details' => 'Save form fields when someone submits a form.',
	            'protected' => 1,
				'order' => 0
			]
		);
        Setting::firstOrCreate(
			[
	            'key' => 'moderate_comments'
            ],[
	            'value' => '',
				'value_bool' => 1,
				'type' => 'BOOL',
				'label' => 'Moderate Comments',
				'details' => 'Approve or delete comments.',
	            'protected' => 1,
				'order' => 0
			]
		);
        Setting::firstOrCreate(
			[
	            'key' => 'allow_comments'
            ],[
	            'value' => '',
				'value_bool' => 1,
				'type' => 'BOOL',
				'label' => 'Global Allow Comments',
				'details' => 'Turn off/on comments globally.',
	            'protected' => 1,
				'order' => 0
			]
		);
        Setting::firstOrCreate(
			[
	            'key' => 'cache'
            ],[
	            'value' => '',
				'value_bool' => 1,
				'type' => 'BOOL',
				'label' => 'Global Cache',
				'details' => 'Turn off/on cache globally. Excluding asset cache.',
	            'protected' => 1,
				'order' => 0
			]
		);
        Setting::firstOrCreate(
            [
                'key' => 'enable_asset_cache'
            ],[
                'value' => '',
                'value_bool' => 0,
                'type' => 'BOOL',
                'label' => 'Enable Asset Cache',
                'details' => 'Turn off/on asset cache globally.',
                'protected' => 1,
                'order' => 0
            ]
        );
    }
}
