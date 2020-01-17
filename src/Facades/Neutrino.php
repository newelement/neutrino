<?php
namespace Newelement\Neutrino\Facades;

use Illuminate\Support\Facades\Facade;

class Neutrino extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'neutrino';
    }
}
