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
class InstallCommand extends Command
{
	use Seedable;

	protected $seedersPath = __DIR__.'/../../publishable/database/seeds/';

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'neutrino:install';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install the Neutrino Admin package';

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
        $this->info('Publishing the Neutrino assets, database, and config files');
        // Publish only relevant resources on install

        $this->call('vendor:publish', ['--provider' => NeutrinoServiceProvider::class]); // , '--tag' => $tags
		$this->call('vendor:publish', ['--provider' => 'Newelement\LaravelCalendarEvent\ServiceProvider']);
        $this->call('vendor:publish', ['--provider' => 'Kyslik\ColumnSortable\ColumnSortableServiceProvider', '--tag' => 'config']);

		$this->info('Migrating the database tables into your application');
        $this->call('migrate', ['--force' => $this->option('force')]);

        $this->info('Attempting to set Neutrino User model as parent to App\User');
        if (file_exists(app_path('User.php'))) {
            $str = file_get_contents(app_path('User.php'));
            if ($str !== false) {
                $str = str_replace('extends Authenticatable', "extends \Newelement\Neutrino\Models\User", $str);
                file_put_contents(app_path('User.php'), $str);
            }
        } else {
            $this->warn('Unable to locate "app/User.php".  Did you move this file?');
            $this->warn('You will need to update this manually.  Change "extends Authenticatable" to "extends \Newelement\Neutrino\Models\User" in your User model');
        }

        $this->info('Dumping the autoloaded files and reloading all new files');

        $composer = $this->findComposer();
        $process = new Process([$composer.' dump-autoload']);
        $process->setTimeout(null); // Setting timeout to null to prevent installation from stopping at a certain point in time
        $process->setWorkingDirectory(base_path())->run();

		$this->info('Adding Neutrino routes to routes/web.php');

        $routes_contents = $filesystem->get(base_path('routes/web.php'));
        if (false === strpos($routes_contents, 'Neutrino::routes()')) {
            $filesystem->append(
                base_path('routes/web.php'),
                "\nNeutrino::routes();\n"
            );
        }

		if (false === strpos($routes_contents, 'Neutrino::routesPublic()')) {
            $filesystem->append(
                base_path('routes/web.php'),
                "\nNeutrino::routesPublic();\n"
            );
        }

        $initData = $this->ask('Do you want the initial installation data? HIGHLY recommended for fresh install. [Y/N]');

        if( $initData === 'y' || $initData === 'Y' ){
            $this->info('Seeding data into the database');
            $this->seed('NeutrinoDatabaseSeeder');
        }

        $this->info('Adding the storage symlink to your public folder');
        $this->call('storage:link');

        $initUser = $this->ask('Do you want to create an admin user? HIGHLY recommended for fresh install. [Y/N]');

        if( $initUser === 'y' || $initUser === 'Y' ){
            $this->call('neutrino:admin');
            $this->info('Successfully installed Neutrino. Enjoy!');
        } else {
            $this->info('Successfully installed Neutrino. Enjoy!');
            $this->info('-> NEXT please run `php artisan neutrino:admin` ONLY IF this is a fresh install.');
        }

    }
}
