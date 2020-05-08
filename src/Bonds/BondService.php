<?php
namespace Newelement\Neutrino\Bonds;

use Illuminate\Http\Request;
use Newelement\Neutrino\Bonds\Traits\Package;
use Newelement\Neutrino\Bonds\Traits\MenuItems;
use Newelement\Neutrino\Bonds\Traits\Assets;
use Newelement\Neutrino\Bonds\Traits\SiteMap;

class BondService
{

    use Package, MenuItems, Assets, SiteMap;

    function __construct()
    {

    }

}
