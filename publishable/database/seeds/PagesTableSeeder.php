<?php

use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Newelement\Neutrino\Models\Page;

class PagesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $content = '<p>Zombies reversus ab inferno, nam malum cerebro. De carne animata corpora quaeritis.
                    Summus sit​​, morbo vel maleficia? De Apocalypsi undead dictum mauris. Hi mortuis soulless creaturas, imo monstra adventus vultus comedat cerebella viventium. Qui offenderit rapto, terribilem incessu.
                    The voodoo sacerdos suscitat mortuos comedere carnem. Search for solum oculi eorum defunctis cerebro.
                    Nescio an Undead zombies. Sicut malus movie horror.</p>
                    <p>Tremor est vivos magna. Expansis ulnis video missing carnem armis caeruleum in locis. A morbo amarus in auras. Nihil horum sagittis tincidunt, gelida portenta. The unleashed virus est, et iam mortui ambulabunt super terram. Souless mortuum oculos attonitos back zombies. An hoc incipere Clairvius Narcisse, an ante? Is bello mundi z?</p>
                    ';

        $blockContent = '[
	{
		"id": "qui3fbmtp",
		"tag": "p",
		"icon": "paragraph",
		"name": "paragraph",
		"group": false,
		"title": "Paragraph",
		"value": "Zombies reversus ab inferno, nam malum cerebro. De carne animata corpora quaeritis. Summus sit​​, morbo vel maleficia? De Apocalypsi undead dictum mauris. Hi mortuis soulless creaturas, imo monstra adventus vultus comedat cerebella viventium. Qui offenderit rapto, terribilem incessu. The voodoo sacerdos suscitat mortuos comedere carnem. Search for solum oculi eorum defunctis cerebro.&nbsp;Nescio an Undead zombies. Sicut malus movie horror.",
		"options": [],
		"template": false,
		"field_groups": [
			{
				"fields": [],
				"options": [],
				"showBlockItemOptions": false
			}
		],
		"contentEditable": true
	},
	{
		"id": "hatp1lagf",
		"tag": "p",
		"icon": "paragraph",
		"name": "paragraph",
		"group": false,
		"title": "Paragraph",
		"value": "Tremor est vivos magna. Expansis ulnis video missing carnem armis caeruleum in locis. A morbo amarus in auras. Nihil horum sagittis tincidunt, gelida portenta. The unleashed virus est, et iam mortui ambulabunt super terram. Souless mortuum oculos attonitos back zombies. An hoc incipere Clairvius Narcisse, an ante? Is bello mundi z?",
		"options": [],
		"template": false,
		"field_groups": [
			{
				"fields": [],
				"options": [],
				"showBlockItemOptions": false
			}
		],
		"contentEditable": true
	}
]';

        $pageExists = Page::where('slug', 'home')->first();
            if( !$pageExists ){
            Page::insert([
                'title' => 'Home',
                'slug' => 'home',
                'content' => htmlentities($content),
                'block_content' => $blockContent,
                'status' => 'P',
                'parent_id' => 0
            ]);
            }
    }
}
