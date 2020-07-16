<?php
namespace Newelement\Neutrino\Http\Controllers;

use Illuminate\Http\Request;
use Newelement\Neutrino\Traits\Blocks;
use Newelement\Neutrino\Models\Gallery;
use Newelement\Neutrino\Models\Form;

class BlocksController extends Controller
{
    use Blocks;

    public function getBlocks()
    {
        $blocks = $this->blocks;
        $configBlocks = config('neutrino.blocks', []);
        $i = 0;
        foreach( $blocks as $bblock ){
            $blocks[$i]['id'] = uniqid();
            $i++;
        }
        foreach( $configBlocks as $block ){
            $newBlock = [];
            $newBlock['id'] = uniqid();
            $newBlock['value'] = '';
            $newBlock['group'] = isset($block['group'])? $block['group'] : false;
            $newBlock['name'] = isset($block['name'])? $block['name'] : false;
            $newBlock['title'] = isset($block['title'])? $block['title'] : false;
            $newBlock['icon'] = isset($block['icon'])? $block['icon'] : false;
            $newBlock['tag'] = isset($block['tag'])? $block['tag'] : false;
            $newBlock['contentEditable'] = isset($block['contentEditable'])? $block['contentEditable'] : false;
            $newBlock['blocks'] = isset($block['block'])? $block['blocks'] : [];
            $newBlock['fields'] = isset($block['fields'])? $block['fields'] : [];
            $newBlock['options'] = isset($block['options'])? $block['options'] : [];
            $newBlock['showBlockItemOptions'] = false;
            $newBlock['group_options'] = isset($block['group_options'])? $block['group_options'] : [];

            $template = '';

            if( isset($block['template']) && function_exists( $block['template'] ) ){
                $template = $block['template'];
            }

            if( isset($block['template']) && strlen($block['template']) && strpos($block['template'], '@' )  ){
                $classMethod = explode('@', $block['template'] );
                $class = $classMethod[0];
                $method = isset($classMethod[1])? $classMethod[1] : false;
                if( $method ){
                    $Class = new $class;
                    $template = $Class->$method($newBlock);
                }
            }

            $newBlock['template'] = $template;

            $blocks[] = $newBlock;
        }

        return json_encode( $blocks );
    }

    public function testimonial($blockData)
    {
        $html = view('neutrino::blocks.testimonials.testimonials', [ 'data' => $blockData ])->render();
        return  $html;
    }

    public function testimonialCompiler($json)
    {
        $html = view('neutrino::blocks.testimonials.testimonials-compiled', [ 'data' => $json ])->render();
        return  $html;
    }

    public function carouselTemplate($blockData)
    {
        $html = view('neutrino::blocks.carousel.template', [ 'data' => $blockData ])->render();
        return  $html;
    }

    public function carouselCompiler($json)
    {
        $json->options_assoc = $this->convertToAssoc($json->options);

        $html = view('neutrino::blocks.carousel.compiled', [ 'data' => $json ])->render();
        return  $html;
    }

    public function accordionTemplate($blockData)
    {
        $html = view('neutrino::blocks.accordion.template', [ 'data' => $blockData ])->render();
        return  $html;
    }

    public function accordionCompiler($json)
    {
        $html = view('neutrino::blocks.accordion.compiled', [ 'data' => $json ])->render();
        return  $html;
    }

    public function galleryTemplate($blockData)
    {
        $galleries = Gallery::orderBy('title', 'asc')->get();
        $html = view('neutrino::blocks.gallery.template', [ 'data' => $blockData, 'galleries' => $galleries ])->render();
        return  $html;
    }

    public function galleryCompiler($json)
    {
        $html = view('neutrino::blocks.gallery.compiled', [ 'data' => $json ])->render();
        return  $html;
    }

    public function formTemplate($blockData)
    {
        $forms = Form::orderBy('title', 'asc')->get();
        $html = view('neutrino::blocks.form.template', [ 'data' => $blockData, 'forms' => $forms ])->render();
        return  $html;
    }

    public function formCompiler($json)
    {
        $json->options_assoc = $this->convertToAssoc($json->options);
        $html = view('neutrino::blocks.form.compiled', [ 'data' => $json ])->render();
        return  $html;
    }

    public function mapTemplate($blockData)
    {
        $html = view('neutrino::blocks.map.template', [ 'data' => $blockData ])->render();
        return  $html;
    }

    public function mapCompiler($json)
    {
        $json->options_assoc = $this->convertToAssoc($json->options);

        $html = view('neutrino::blocks.map.compiled', [ 'data' => $json ])->render();
        return  $html;
    }

    public function heroTemplate($blockData)
    {
        $html = view('neutrino::blocks.hero.template', [ 'data' => $blockData ])->render();
        return  $html;
    }

    public function heroCompiler($json)
    {
        $json->options_assoc = $this->convertToAssoc($json->options);
        $html = view('neutrino::blocks.hero.compiled', [ 'data' => $json ])->render();
        return  $html;
    }

    public function dividerTemplate($blockData)
    {
        $html = view('neutrino::blocks.dividers.template', [ 'data' => $blockData ])->render();
        return  $html;
    }

    public function dividerCompiler($json)
    {
        $json->options_assoc = $this->convertToAssoc($json->options);
        $html = view('neutrino::blocks.dividers.compiled', [ 'data' => $json ])->render();
        return  $html;
    }

    private function convertToAssoc($options)
    {
        $optionsAssoc = [];
        foreach( $options as $option ){
            $optionsAssoc[$option->name] = $option;
        }
        return $optionsAssoc;
    }
}
