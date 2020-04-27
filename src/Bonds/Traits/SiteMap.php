<?php

namespace Newelement\Neutrino\Bonds\Traits;

trait SiteMap
{

    public $models = [];

    // [ 'model' => '', 'key' => '' ]
    public function registerSiteMap($model)
    {
        $this->models[] = $model;
    }

    public function getSiteMapModels()
    {
        return $this->models;
    }
}
