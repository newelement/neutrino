<?php
namespace Newelement\Neutrino;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Arr;
use TorMorten\Eventy\Facades\Events as Eventy;
use Newelement\Neutrino\Contracts\RedirectModelContract;
use Newelement\Neutrino\Middleware\RedirectRequests;
use Newelement\Neutrino\Models\Redirect;

use Newelement\Neutrino\Facades\Neutrino as NeutrinoFacade;
use Newelement\Neutrino\Bonds\BondService;
use Newelement\Neutrino\Http\Middleware\NeutrinoAdminMiddleware;

class NeutrinoServiceProvider extends ServiceProvider
{
    protected $router;

	public function register()
    {

		$loader = AliasLoader::getInstance();
        $loader->alias('Neutrino', NeutrinoFacade::class);

        $this->app->singleton('neutrino', function () {
            return new Neutrino();
        });

        $this->app->singleton('NeutrinoAuth', function () {
            return auth();
        });

        $this->app->singleton('NeutrinoGuard', function () {
            return config('auth.defaults.guard', 'web');
        });

        $this->app->singleton('NeutrinoBond', function () {
            return new BondService;
        });

        $this->app->bind(RedirectModelContract::class, Redirect::class);
        $this->app->alias(RedirectModelContract::class, 'redirect.model');

		$this->loadHelpers();
		$this->registerConfigs();

		if ($this->app->runningInConsole()) {
            $this->registerPublishableResources();
            $this->registerConsoleCommands();
        }
	}

	public function boot(Router $router, Dispatcher $event)
	{
        $this->router = $router;

		$viewsDirectory = __DIR__.'/../resources/views/theme';
        $adminViewsDirectory = __DIR__.'/../resources/views/cms';
		$publishAssetsDirectory = __DIR__.'/../publishable/assets';

        $this->loadViewsFrom($viewsDirectory, 'neutrino');
        $this->loadViewsFrom($adminViewsDirectory, 'neutrino');

		$this->publishes([$viewsDirectory => base_path('resources/views/vendor/neutrino')], 'views');
		$this->publishes([ $publishAssetsDirectory => public_path('vendor/newelement/neutrino') ], 'public');
        $router->aliasMiddleware('admin.user', NeutrinoAdminMiddleware::class);
		$this->loadTranslationsFrom(realpath(__DIR__.'/../publishable/lang'), 'neutrino');
		$this->loadMigrationsFrom(realpath(__DIR__.'/../migrations'));

		app('arrilot.widget-namespaces')->registerNamespace('neutrino', '\Newelement\Neutrino\Widgets');

        // Redirects
        $this->app->alias(RedirectModelContract::class, 'redirect.model');
        $this->router->pushMiddlewareToGroup('web', RedirectRequests::class);

		$this->initActions();
	}

	/**
     * Register the publishable files.
     */
    private function registerPublishableResources()
    {
        $publishablePath = dirname(__DIR__).'/publishable';

        $publishable = [
            'config' => [
                "{$publishablePath}/config/neutrino.php" => config_path('neutrino.php'),
            ],
			'seeds' => [
                "{$publishablePath}/database/seeds/" => database_path('seeds'),
            ],
        ];
        foreach ($publishable as $group => $paths) {
            $this->publishes($paths, $group);
        }
    }

    public function registerConfigs()
    {
        $this->mergeConfigFrom(
            dirname(__DIR__).'/publishable/config/neutrino.php', 'neutrino'
        );
    }


	protected function loadHelpers()
    {
        foreach (glob(__DIR__.'/Helpers/*.php') as $filename) {
            require_once $filename;
        }
    }

	/**
     * Register the commands accessible from the Console.
     */
    private function registerConsoleCommands()
    {
        $this->commands(Commands\InstallCommand::class);
        $this->commands(Commands\AdminCommand::class);
        $this->commands(Commands\UpdateCommand::class);
        $this->commands(Commands\DomainMigrationCommand::class);
    }

    private function initActions()
    {
        /*Eventy::addAction('neutrino.admin.menu', function($json) {

            $menuItems = config('neutrino.admin_menu_items', [] );
            $newMenuItems = json_decode($json, true);
            $items = array_push($menuItems, $newMenuItems);
            config(['neutrino.admin_menu_items' => $items]);

        }, 5, 1);*/
    }

}
