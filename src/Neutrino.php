<?php
namespace Newelement\Neutrino;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Newelement\Neutrino\Models\Category;
use Newelement\Neutrino\Models\Page;
use Newelement\Neutrino\Models\Permission;
use Newelement\Neutrino\Models\Entry;
use Newelement\Neutrino\Models\Role;
use Newelement\Neutrino\Models\Setting;
use Newelement\Neutrino\Models\User;
use Newelement\Neutrino\Models\CfGroups;
use Newelement\Neutrino\Models\CfFields;
use Newelement\Neutrino\Models\CfRule;
use Newelement\Neutrino\Models\EntryType;
use DB;

class Neutrino
{

	protected $viewLoadingEvents = [];

	protected $models = [
        'Category'    => Category::class,
        'Menu'        => Menu::class,
        'MenuItem'    => MenuItem::class,
        'Page'        => Page::class,
        'Permission'  => Permission::class,
        'Entry'       => Entry::class,
        'Role'        => Role::class,
        'Setting'     => Setting::class,
        'User'        => User::class,
		'CfGroups'    => CfGroups::class,
		'CfFields'    => CfFields::class,
		'CfRule'      => CfRule::class,
		'EntryType'   => EntryType::class,
		'Translation' => Translation::class,
    ];

	public function routes()
	{
	    require __DIR__.'/../routes/neutrino.php';
	}

	public function routesPublic()
	{
	    require __DIR__.'/../routes/neutrinoPublic.php';
	}

	public function model($name)
    {
        return app($this->models[Str::studly($name)]);
    }

    public function modelClass($name)
    {
        return $this->models[$name];
    }

    public function useModel($name, $object)
    {
        if (is_string($object)) {
            $object = app($object);
        }
        $class = get_class($object);
        if (isset($this->models[Str::studly($name)]) && !$object instanceof $this->models[Str::studly($name)]) {
            throw new \Exception("[{$class}] must be instance of [{$this->models[Str::studly($name)]}].");
        }
        $this->models[Str::studly($name)] = $class;
        return $this;
    }

	public function view($name, array $parameters = [])
    {
        foreach (Arr::get($this->viewLoadingEvents, $name, []) as $event) {
            $event($name, $parameters);
        }
        return view($name, $parameters);
    }

    public function onLoadingView($name, \Closure $closure)
    {
        if (!isset($this->viewLoadingEvents[$name])) {
            $this->viewLoadingEvents[$name] = [];
        }

        $this->viewLoadingEvents[$name][] = $closure;
    }

	public function getLocales()
    {
        return array_diff(scandir(realpath(__DIR__.'/../publishable/lang')), ['..', '.']);
    }

}
