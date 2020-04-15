<?php

namespace Newelement\Neutrino\Bonds\Traits;

trait MenuItems
{

    public $menuItems = [];

    public function __construct() {
        $menuItems = config('neutrino.admin_menu_items', []);
        $this->menuItems = $menuItems;
    }

    public function registerMenuItems($menuItems)
    {
        foreach( (array) $menuItems as $menuItem ){
            $this->menuItems[] = $menuItem;
        }
    }

    public function getMenuItems()
    {
        return $this->menuItems;
    }

    public function getMenuSlot($slot)
    {
        $arrs = [];
        foreach( $this->menuItems as $key => $menu ){
            if( $menu['slot'] === $slot ){
                $arrs[] = $this->menuItems[$key];
            }
        }
        return $arrs;
    }

}
