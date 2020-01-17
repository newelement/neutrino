<?php
use Illuminate\Database\Seeder;
use Newelement\Neutrino\Traits\Seedable;
class NeutrinoDatabaseSeeder extends Seeder
{
    use Seedable;
    protected $seedersPath = __DIR__.'/';
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->seed('EntryTypesTableSeeder');
		$this->seed('TaxonomyTableSeeder');
		$this->seed('RolesTableSeeder');
		$this->seed('SettingsTableSeeder');
		$this->seed('PagesTableSeeder');
		$this->seed('MenuTableSeeder');
    }
}
