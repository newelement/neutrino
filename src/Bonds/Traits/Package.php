<?php

namespace Newelement\Neutrino\Bonds\Traits;

trait Package
{

    public $packages = [];

    public function registerPackage($arr)
    {
        $this->packages[] = $arr;
    }

    public function getPackages()
    {
        return $this->packages;
    }
}
