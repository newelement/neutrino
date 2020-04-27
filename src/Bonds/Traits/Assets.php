<?php

namespace Newelement\Neutrino\Bonds\Traits;

trait Assets
{

    public $enqueueJS = [];
    public $enqueueCSS = [];
    public $enqueueAdminJS = [];
    public $enqueueAdminCSS = [];

    public function __construct() {
        $js = config('neutrino.enqueue_js', []);
        $css = config('neutrino.enqueue_css', []);
        $adminJs = config('neutrino.enqueue_admin_js', []);
        $adminCss = config('neutrino.enqueue_admin_css', []);
        $this->enqueueJS = $js;
        $this->enqueueCSS = $css;
        $this->enqueueAdminJS = $adminJs;
        $this->enqueueAdminCSS = $adminCss;
    }

    public function enqueueScripts($scripts)
    {
        foreach( (array) $scripts as $script ){
            $this->enqueueJS[] = $script;
        }
    }

    public function enqueueStyles($styles)
    {
        foreach( (array) $styles as $style ){
            $this->enqueueCSS[] = $style;
        }
    }

    public function enqueueAdminScripts($scripts)
    {
        foreach( (array) $scripts as $script ){
            $this->enqueueAdminJS[] = $script;
        }
    }

    public function enqueueAdminStyles($styles)
    {
        foreach( (array) $styles as $style ){
            $this->enqueueAdminCSS[] = $style;
        }
    }

    public function getScripts()
    {
        return $this->enqueueJS;
    }

    public function getAdminScripts()
    {
        return $this->enqueueAdminJS;
    }

    public function getStyles()
    {
        return $this->enqueueCSS;
    }

    public function getAdminStyles()
    {
        return $this->enqueueAdminCSS;
    }

}
