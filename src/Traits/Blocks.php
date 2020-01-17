<?php

namespace Newelement\Neutrino\Traits;

use Newelement\Neutrino\Models\Page;
use Newelement\Neutrino\Models\Taxonomy;
use Newelement\Neutrino\Models\TaxonomyType;
use Newelement\Neutrino\Models\User;
use Newelement\Neutrino\Models\Entry;
use Newelement\Neutrino\Models\CfGroups;
use Newelement\Neutrino\Models\CfFields;
use Newelement\Neutrino\Models\CfRule;
use Newelement\Neutrino\Models\CfObjectData;

trait Blocks
{

    public $blocks = [];

    public function __construct() {
        $this->defaultBlocks();
    }

    private function defaultBlocks()
    {
        $this->blocks = [
            [
                'name' => 'heading1',
                'title' => 'Heading 1',
                'icon' => 'h1',
                'tag' => 'h1',
                'contentEditable' => true,
                'template' => false,
                'value' => '',
                'blocks' => [],
                'group' => false
            ],
            [
                'name' => 'heading2',
                'title' => 'Heading 2',
                'icon' => 'h2',
                'tag' => 'h2',
                'contentEditable' => true,
                'template' => false,
                'value' => '',
                'blocks' => [],
                'group' => false
            ],
            [
                'name' => 'heading3',
                'title' => 'Heading 3',
                'icon' => 'h3',
                'tag' => 'h3',
                'contentEditable' => true,
                'template' => false,
                'value' => '',
                'blocks' => [],
                'group' => false
            ],
            [
                'name' => 'heading4',
                'title' => 'Heading 4',
                'icon' => 'h4',
                'tag' => 'h4',
                'contentEditable' => true,
                'template' => false,
                'value' => '',
                'blocks' => [],
                'group' => false
            ],
            [
                'name' => 'freetext',
                'title' => 'Text',
                'icon' => 'align-left',
                'tag' => false,
                'contentEditable' => true,
                'template' => false,
                'value' => '',
                'blocks' => [],
                'group' => false
            ],
            [
                'name' => 'paragraph',
                'title' => 'Paragraph',
                'icon' => 'paragraph',
                'tag' => 'p',
                'placeholder' => 'Enter your text ...',
                'contentEditable' => true,
                'template' => false,
                'value' => '',
                'blocks' => [],
                'group' => false
            ],
            [
                'name' => 'unorderedlist',
                'title' => 'Unordered List',
                'icon' => 'list-ul',
                'tag' => 'ul',
                'contentEditable' => true,
                'template' => false,
                'value' => '',
                'blocks' => [],
                'group' => false
            ],
            [
                'name' => 'orderedlist',
                'title' => 'Ordered List',
                'icon' => 'list-ol',
                'tag' => 'ol',
                'contentEditable' => true,
                'template' => false,
                'value' => '',
                'blocks' => [],
                'group' => false
            ],
        ];
    }

    public function registerBlock($template)
    {
        $this->blocks[] = $template;
    }

    public function compileBlocks($blocks)
    {
        $htmls = [];
        foreach( (array) $blocks as $block){
            if( $block->tag ){
                $htmls[] = $this->getTag($block->tag, $block->value);
            } else {
                $htmls[] = $this->compileBlock($block);
            }
        }
        return implode( "\n", $htmls);
    }

    private function getTag($tag, $value)
    {
        $rendered = '';

        switch( $tag ){
            case 'h1':
                $rendered = '<h1>'.$value.'</h1>';
            break;
            case 'h2':
                $rendered = '<h2>'.$value.'</h2>';
            break;
            case 'h3':
                $rendered = '<h3>'.$value.'</h3>';
            break;
            case 'h4':
                $rendered = '<h4>'.$value.'</h4>';
            break;
            case 'h5':
                $rendered = '<h5>'.$value.'</h5>';
            break;
            case 'p':
                $rendered = '<p>'.$value.'</p>';
            break;
            case 'div':
                $rendered = '<div>'.$value.'</div>';
            break;
            case 'section':
                $rendered = '<section>'.$value.'</section>';
            break;
            case 'ul':
                $rendered = '<ul>'.$value.'</ul>';
            break;
            case 'ol':
                $rendered = '<ol>'.$value.'</ol>';
            break;
        }

        return $rendered;
    }

    private function compileBlock($block)
    {
        //dd($block);
        $compiled = '';

        if( isset($block->name) ){
            $config = $this->getBlockConfig($block->name);

            if( isset($config['compiler']) && function_exists( $config['compiler'] ) ){
                $compiled = $block->compiler;
            }
            if( isset($config['compiler']) && strpos($config['compiler'], '@' ) ){
                $classMethod = explode('@', $config['compiler'] );
                $class = $classMethod[0];
                $method = isset($classMethod[1])? $classMethod[1] : false;
                if( $method ){
                    $Class = new $class();
                    $compiled = $Class->$method($block);
                }
            }
        }
        return $compiled;
    }

    private function getBlockConfig($name)
    {
        $block = [];
        $blocks = config('neutrino.blocks');
        foreach( $blocks as $block ){
            if( $block['name'] === $name ){
                return $block;
            }
        }
        return $block;
    }

}
