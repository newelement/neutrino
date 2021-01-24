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

        // NPM Packages...
        $this->updateNodePackages(function ($packages) {
            return [
                "axios" => "^0.21",
                "bootstrap" => "^4.0.0",
                "cross-env" => "^7.0",
                "jquery" => "^3.2",
                "laravel-mix" => "^5.0.9",
                "lodash" => "^4.17.13",
                "popper.js" => "^1.12",
                "resolve-url-loader" => "^3.1.0",
                "sass" => "^1.15.2",
                "sass-loader" => "^8.0.0",
                "vue-template-compiler" => "^2.6.11",
                "@fortawesome/fontawesome-free" => "^5.13.0",
                "slick-carousel" => "^1.8.1"
            ] + $packages;
        });

        $this->flushNodeModules();

        copy(__DIR__.'/../../stubs/webpack.mix.js', base_path('webpack.mix.js'));
        (new Filesystem)->copyDirectory(__DIR__.'/../../stubs/resources/sass', resource_path('sass'));
        (new Filesystem)->copyDirectory(__DIR__.'/../../stubs/resources/js', resource_path('js'));

        $this->call('vendor:publish', ['--provider' => NeutrinoServiceProvider::class]); // , '--tag' => $tags
		$this->call('vendor:publish', ['--provider' => 'Newelement\LaravelCalendarEvent\ServiceProvider']);
        $this->call('vendor:publish', ['--provider' => 'Intervention\Image\ImageServiceProviderLaravelRecent']);
        $this->call('vendor:publish', ['--provider' => 'Kyslik\ColumnSortable\ColumnSortableServiceProvider', '--tag' => 'config']);

		$this->info('Migrating the database tables into your application');
        $this->call('migrate', ['--force' => $this->option('force')]);

        $this->info('Attempting to set Neutrino User model as parent to App\User');
        if (file_exists(app_path('User.php')) || file_exists(app_path('Models/User.php'))) {
            $userPath = file_exists(app_path('User.php')) ? app_path('User.php') : app_path('Models/User.php');

            $str = file_get_contents($userPath);

            if ($str !== false) {
                $str = str_replace('extends Authenticatable', "extends \Newelement\Neutrino\Models\User", $str);

                file_put_contents($userPath, $str);
            }
        } else {
            $this->warn('Unable to locate "User.php" in app or app/Models. Did you move this file?');
            $this->warn('You will need to update this manually. Change "extends Authenticatable" to "extends \Newelement\Neutrino\Models\User" in your User model');
        }

        $this->info('Dumping the autoloaded files and reloading all new files');

        $composer = $this->findComposer();
        $process = new Process([$composer.' dump-autoload']);
        $process->setTimeout(null);
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

        if( strtoupper($initData) === 'Y' ){
            $this->info('Seeding data into the database');
            $this->seed('NeutrinoDatabaseSeeder');
        }

        $this->info('Adding the storage symlink to your public folder');
        $this->call('storage:link');

        $initUser = $this->ask('Do you want to create an admin user? HIGHLY recommended for fresh install. [Y/N]');

        if( strtoupper($initUser) === 'Y' ){
            $this->call('neutrino:admin');
            $this->info('Successfully installed Neutrino. Enjoy!');
            $this->comment('Please execute the "npm install && npm run dev" command to build your assets.');
            $this->comment('You will also need to update your package.json file and use these scripts: ');
            $this->comment('"scripts": {
                "dev": "npm run development",
                "development": "cross-env NODE_ENV=development node_modules/webpack/bin/webpack.js --progress --hide-modules --config=node_modules/laravel-mix/setup/webpack.config.js",
                "watch": "npm run development -- --watch",
                "watch-poll": "npm run watch -- --watch-poll",
                "hot": "cross-env NODE_ENV=development node_modules/webpack-dev-server/bin/webpack-dev-server.js --inline --hot --disable-host-check --config=node_modules/laravel-mix/setup/webpack.config.js",
                "prod": "npm run production",
                "production": "cross-env NODE_ENV=production node_modules/webpack/bin/webpack.js --no-progress --hide-modules --config=node_modules/laravel-mix/setup/webpack.config.js"
            }');
        } else {
            $this->info('Successfully installed Neutrino. Enjoy!');
            $this->info('-> To setup an admin run: `php artisan neutrino:admin` ');
            $this->comment('Please execute the "npm install && npm run dev" command to build your assets.');
            $this->comment('You will also need to update your package.json file and use these scripts: ');
            $this->comment('"scripts": {
                "dev": "npm run development",
                "development": "cross-env NODE_ENV=development node_modules/webpack/bin/webpack.js --progress --hide-modules --config=node_modules/laravel-mix/setup/webpack.config.js",
                "watch": "npm run development -- --watch",
                "watch-poll": "npm run watch -- --watch-poll",
                "hot": "cross-env NODE_ENV=development node_modules/webpack-dev-server/bin/webpack-dev-server.js --inline --hot --disable-host-check --config=node_modules/laravel-mix/setup/webpack.config.js",
                "prod": "npm run production",
                "production": "cross-env NODE_ENV=production node_modules/webpack/bin/webpack.js --no-progress --hide-modules --config=node_modules/laravel-mix/setup/webpack.config.js"
            }');
        }

    }

    protected static function updateNodePackages(callable $callback, $dev = true)
    {
        if (! file_exists(base_path('package.json'))) {
            return;
        }

        $configurationKey = $dev ? 'devDependencies' : 'dependencies';

        $packages = json_decode(file_get_contents(base_path('package.json')), true);

        $packages[$configurationKey] = $callback(
            array_key_exists($configurationKey, $packages) ? $packages[$configurationKey] : [],
            $configurationKey
        );

        ksort($packages[$configurationKey]);

        file_put_contents(
            base_path('package.json'),
            json_encode($packages, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT).PHP_EOL
        );
    }

    protected function flushNodeModules()
    {
        tap(new Filesystem, function ($files) {
            $files->deleteDirectory(base_path('node_modules'));

            $files->delete(base_path('yarn.lock'));
            $files->delete(base_path('package-lock.json'));
        });
    }

}
