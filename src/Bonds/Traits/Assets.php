<?php

namespace Newelement\Neutrino\Bonds\Traits;

trait Assets
{

    public $enqueueJS = [];
    public $enqueueCSS = [];
    public $enqueueAdminJS = [];
    public $enqueueAdminCSS = [];

    public function __construct() {
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
        //$js = config('neutrino.enqueue_js', []);
        //$all = array_merge($this->enqueueJS, $js);
        $all = $this->enqueueJS;
        return $all;
    }

    public function getAdminScripts()
    {
        //$adminJs = config('neutrino.enqueue_admin_js', []);
        //$all = array_merge($this->enqueueAdminJS, $adminJs);
        $all = $this->enqueueAdminJS;
        return $all;
    }

    public function getStyles()
    {
        //$css = config('neutrino.enqueue_css', []);
        //$all = array_merge($this->enqueueCSS, $css);
        $all = $this->enqueueCSS;
        return $all;
    }

    public function getAdminStyles()
    {
        //$adminCss = config('neutrino.enqueue_admin_css', []);
        //$all = array_merge($this->enqueueAdminCSS, $adminCss);
        $all = $this->enqueueAdminCSS;
        return $all;
    }

}
