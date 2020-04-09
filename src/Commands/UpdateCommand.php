<?php
namespace Newelement\Neutrino\Commands;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Intervention\Image\ImageServiceProviderLaravel5;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Process\Process;
use Newelement\Neutrino\Providers\NeutrinoDummyServiceProvider;
use Newelement\Neutrino\Traits\Seedable;
use Newelement\Neutrino\NeutrinoServiceProvider;

class UpdateCommand extends Command
{
	use Seedable;

	protected $seedersPath = __DIR__.'/../../publishable/database/seeds/';

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'neutrino:update';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update the Neutrino Admin package';

    protected function getOptions()
    {
        return [
            ['force', null, InputOption::VALUE_NONE, 'Force the operation to run when in production', null],
            ['with-data', null, InputOption::VALUE_NONE, 'Install with default data', null],
        ];
    }

    protected function findComposer()
    {
        if (file_exists(getcwd().'/composer.phar')) {
            return '"'.PHP_BINARY.'" '.getcwd().'/composer.phar';
        }
        return 'composer';
    }
    public function fire(Filesystem $filesystem)
    {
        return $this->handle($filesystem);
    }

    public function handle(Filesystem $filesystem)
    {
        $this->info('Updating Neutrino assets, database, views and config files...');

        $this->call('vendor:publish', ['--provider' => 'JamesMills\LaravelTimezone\LaravelTimezoneServiceProvider', '--tag' => 'migrations']);

		$this->info('Migrating any database changes...');
        $this->call('migrate', ['--force' => $this->option('force')]);

        $this->info('Dumping the autoloaded files and reloading all new files...');
        $composer = $this->findComposer();
        $process = new Process([$composer.' dump-autoload']);
        $process->setTimeout(null);
        $process->setWorkingDirectory(base_path())->run();

        //$this->info('Merging config...');
        //$this->call('vendor:publish', ['--provider' => 'Newelement\Neutrino\NeutrinoServiceProvider', '--tag' => 'config']);

        $initData = $this->ask('Do you want to update the Neutrino application views? CAUTION this will overwrite any Neutrino views you may have altered. If you do not update the views you may need to update them manually. See documentation for more info. [Y/N]');

        if( $initData === 'y' || $initData === 'Y' ){
            $this->call('vendor:publish', ['--provider' => 'Newelement\Neutrino\NeutrinoServiceProvider', '--tag' => 'views', '--force' => true ]);
        }
        $this->call('vendor:publish', ['--provider' => 'Newelement\Neutrino\NeutrinoServiceProvider', '--tag' => 'adminviews', '--force' => true ]);

        $this->info('Updating assets...');
        $this->call('vendor:publish', ['--provider' => 'Newelement\Neutrino\NeutrinoServiceProvider', '--tag' => 'public', '--force' => true ]);

        $this->info('Clearing application cache...');
        \Storage::disk('public')->delete('assets/css/all.css');
        \Storage::disk('public')->delete('assets/js/all.js');
        $this->call('cache:clear');

        $this->info('Successfully updated Neutrino.');

    }
}
