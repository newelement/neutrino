<?php
use Illuminate\Support\Facades\View;
use Newelement\Neutrino\Models\ObjectMedia;
use Newelement\Neutrino\Models\Menu;
use Newelement\Neutrino\Models\MenuItem;
use Newelement\Neutrino\Models\Page;
use Newelement\Neutrino\Models\Entry;
use Newelement\Neutrino\Models\EntryType;
use Newelement\Neutrino\Models\Location;
use Newelement\Neutrino\Models\Taxonomy;
use Newelement\Neutrino\Models\TaxonomyType;
use Newelement\Neutrino\Models\User;
use Newelement\Neutrino\Models\CfObjectData;
use Newelement\Neutrino\Models\CfFields;
use Newelement\Neutrino\Models\CfRule;
use Newelement\Neutrino\Models\Setting;
use Newelement\Neutrino\Models\Gallery;
use Newelement\Neutrino\Models\Form;
use Newelement\Neutrino\Models\FormField;
use Newelement\Neutrino\Http\Controllers\ContentController;
use Newelement\Neutrino\Http\Controllers\BlocksController;
use Newelement\LaravelCalendarEvent\Models\CalendarEvent;
use TorMorten\Eventy\Facades\Events as Eventy;

function states(){
    $states	= array(
        'AL' => 'Alabama',
        'AK' => 'Alaska',
        'AZ' => 'Arizona',
        'AR' => 'Arkansas',
        'CA' => 'California',
        'CO' => 'Colorado',
        'CT' => 'Connecticut',
        'DE' => 'Delaware',
        'FL' => 'Florida',
        'GA' => 'Georgia',
        'HI' => 'Hawaii',
        'ID' => 'Idaho',
        'IL' => 'Illinois',
        'IN' => 'Indiana',
        'IA' => 'Iowa',
        'KS' => 'Kansas',
        'KY' => 'Kentucky',
        'LA' => 'Louisiana',
        'ME' => 'Maine',
        'MD' => 'Maryland',
        'MA' => 'Massachusetts',
        'MI' => 'Michigan',
        'MN' => 'Minnesota',
        'MS' => 'Mississippi',
        'MO' => 'Missouri',
        'MT' => 'Montana',
        'NE' => 'Nebraska',
        'NV' => 'Nevada',
        'NH' => 'New Hampshire',
        'NJ' => 'New Jersey',
        'NM' => 'New Mexico',
        'NY' => 'New York',
        'NC' => 'North Carolina',
        'ND' => 'North Dakota',
        'OH' => 'Ohio',
        'OK' => 'Oklahoma',
        'OR' => 'Oregon',
        'PA' => 'Pennsylvania',
        'RI' => 'Rhode Island',
        'SC' => 'South Carolina',
        'SD' => 'South Dakota',
        'TN' => 'Tennessee',
        'TX' => 'Texas',
        'UT' => 'Utah',
        'VT' => 'Vermont',
        'VA' => 'Virginia',
        'WA' => 'Washington',
        'WV' => 'West Virginia',
        'WI' => 'Wisconsin',
        'WY' => 'Wyoming',
        'DC' => 'Washington D.C.'
    );

    return $states;
}

/*
* MENUS Helper Functions
*
*
*/

// Get specific menu
/* @params $menuName, $type (array, html)
* returns array
*/
function getMenu($menuName, $type = 'array', $options = []){
	$arr = [];
	$menu = Menu::where('name', $menuName)->first();
	if( $menu ){
		$items = MenuItem::
				where('menu_id', $menu->id)
				->orderBy('order', 'asc')
				->select('id', 'title', 'url', 'target', 'parent_id', 'order', 'parameters')
				->get()->toArray();

		$arr = _buildMenuArr($items, 0);
		if( $type === 'html' ){
			return _buildMenuHtml( (array) $arr, false, $options);
		}
	}
	return $arr;
}

function _buildMenuHtml( $array = [], $children = false, $options = [])
{

    $ulClass = isset($options['ul_class']) && !$children? 'class="'.$options['ul_class'].'"' : '';
    $dropdownId = isset($options['dropdown_id'])? 'aria-labelledby="'.$options['dropdown_id'].'"' : '';
    if( $children ){
        $ulClass = 'dropdown-menu';
    }

    $html = '';

    if( isset($options['ul_parent']) && $options['ul_parent'] && !$children ){
        $html .= '<ul '.$ulClass.' '.$dropdownId.' >';
    } elseif ( $children ) {
        $html .= '<ul '.$ulClass.' '.$dropdownId.' >';
    }

    $s = 0;
    foreach ($array as $key => $value) {
		$isCurrent = _checkCurrentRequest($value['url'], $value['type']);
		$current = $isCurrent['current'] ? 'active' : '';
		$other = 'nav-item';
		$isParent = false;

		if( !$children ){
    		$url = \Request::path();
    		$key = array_search('/'.$url, array_column($value['children'], 'url'), true);
    		if( is_numeric($key) ){
        		$current = 'active';
    		}
		}

		$class = $value['children']? 'class="'.$other.' '.$current.' dropdown"' : 'class="'.$other.' '.$current.'"';

		if( $value['children'] ){
    		$dropdownId = 'nav-dropdown-'.uniqid();
		}

		$alink = $children? 'dropdown-item' : 'nav-link';

        $html .= '<li '.$class.'><a class="'.$alink.''.( $value['children']? ' dropdown-toggle' : '' ).'" href="'.$value['url'].'" '.( $value['children']? 'id="'.$dropdownId.'" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"' : '' ).' >'.$value['title'].'</a>';
		if( $value['children'] ){
    		$options['dropdown_id'] = $dropdownId;
			$html .= _buildMenuHtml($value['children'], true, $options);
		}
		$html .= '</li>';
		$s++;
    }

    if( isset($options['ul_parent']) && $options['ul_parent'] && !$children ){
        $html .= '</ul>';
    } else if( $children ){
        $html .= '</ul>';
    }

	return $html;
}

function _buildMenuArr($items, $parentId){
	$branch = [];
	foreach ($items as $element) {
		if ($element['parent_id'] === $parentId) {
			$children = _buildMenuArr($items, $element['id']);
			$element['children'] = $children;
			$element['type'] = $element['parameters'];
			$branch[] = $element;
		}
	}
	return $branch;
}

function _checkCurrentRequest($url, $type = false){
	$current = false;
	$parent = false;
	$url = parse_url($url, PHP_URL_PATH);
    $path = \Request::path();

    $url = ltrim($url, '/');
    $url = rtrim($url, '/');

    $current = $url === $path;

    if( $url === '' && $path === '/' ){
        $current = true;
    }

	return ['current' => $current];
}

function _checkCurrentParentRequest($arr, $url, $s){
	$parent = false;
	$url = parse_url($url, PHP_URL_PATH);
    $path = \Request::path();

    $url = ltrim($url, '/');
    $url = rtrim($url, '/');

    $current = $url === $path;

	return ['parent' => $parent];
}


/*
* PAGE helper functions
*
*
*/
function getContent($args = [], $content = false){

    if( !$content ){

        $data = view()->shared('objectData');

        if( $data ){
            if( $data->editor_type === 'B' ){

                if( getSetting('cache') ){
                    $content = Cache::rememberForever('block_'.$data->data_type.'_'.$data->slug, function () use ($data) {

                        $blocksJSON = json_decode($data->block_content);
                        $blocksController = new BlocksController;
                        $content = $blocksController->compileBlocks($blocksJSON);

                        return $content;
                    });
                } else {
                    $blocksJSON = json_decode($data->block_content);
                    $blocksController = new BlocksController;
                    $content = $blocksController->compileBlocks($blocksJSON);
                }


            } else {
                $content = $data->content;
            }
        } else {
            $content = false;
        }

    }

    if( isset($args['strip_shortcodes']) &&  $args['strip_shortcodes'] ){
        $content = stripShortcodes($content);
    } else {
        $content = ContentController::doShortCodes($content);
        $content = ContentController::doDynamicShortcodes($content);
    }

    $content = html_entity_decode($content);

    $content = Eventy::filter('content', $content);

    return $content;
}


function stripShortcodes($text_to_search) {
    $pattern = '|[[\/\!]*?[^\[\]]*?]|si';
    $replace = '';
    return preg_replace($pattern, $replace, $text_to_search);
}

/*
* FORMS Helper Functions
*
*
*/

// Get form attributes and fields
/*
* returns array
*/
function getForm( $id ){
    $id = (int) $id;
    $form = false;
    try{
	    $form = Form::where(
	                [
	                    'id' => $id,
                        'status' => 'A'
	                ]
                )->first()->toArray();

	} catch(\Exception $e){
    	$form = false;
	}

	return $form;
}

// Get form html and fields
/*
* returns mixed html
*/
function getFormHTML($id, $args = []){
	$form = getForm( $id );

	if( !$form ){
    	return '';
	}

    $honeypotConfig = config('honeypot');
    $nameFieldName = $honeypotConfig['name_field_name'];
    $randomNameFieldName = $honeypotConfig['randomize_name_field_name'];
    $enabled = $honeypotConfig['enabled'];
    $validFromFieldName = $honeypotConfig['valid_from_field_name'];
    $validFrom = now()->addSeconds($honeypotConfig['amount_of_seconds']);

    $encryptedValidFrom = Spatie\Honeypot\EncryptedTime::create($validFrom);

    if ($randomNameFieldName) {
        $nameFieldName = sprintf('%s_%s', $nameFieldName, \Str::random());
    }

	$html = '';

	$html .= '<form class="form" action="/neutrino-form" method="post" enctype="multipart/form-data">';
	    $html .= csrf_field();
        if($enabled){
            $html .= '
            <div id="'.$nameFieldName.'_wrap" style="display:none;">
            <input name="'.$nameFieldName.'" type="text" value="" id="'.$nameFieldName.'">
            <input name="'.$validFromFieldName.'" type="text" value="'.$encryptedValidFrom.'">
            </div>';
        }
	    $html .= isset( $args['show_title']) && !$args['show_title']? '' : '<h2 class="form-title">'.$form['title'].'</h2>';
	    $html .= '<input type="hidden" name="form_id" value="'.$form['id'].'">';

	    $fields = $form['fields'];

	    foreach( $fields as $field ){

            if( $field['field_type'] === 'descriptive_text' ){
                $html .= '<div class="form-group '.( $form['form_style'] === 'horizontal'? 'row' : '' ).'">';
                    $html .= '<div class="col-md-12 form-descriptive-text">';
                        $html .= parseFieldType($field);
                    $html .= '</div>';
                $html .= '</div>';

            } else {
                $html .= '<div class="form-group '.( $form['form_style'] === 'horizontal'? 'row' : '' ).'">';
                $html .= '<label for="input-'.$field['field_name'].'" class="'.( $form['form_style'] === 'horizontal'? 'col-sm-3 col-form-label' : '' ).'">'.$field['field_label'].'</label>
                            <div class="form-input '.( $form['form_style'] === 'horizontal'? 'col-sm-9' : '' ).'">';
                    $html .= parseFieldType($field);
                    $html .= '</div>';
                $html .= '</div>';
            }

	    }

	    $html .= '<div class="form-footer">';
	    $html .= '<button type="submit" class="btn btn-primary '.( $form['submit_button_size'] === 'full_width' ? 'btn-block' : '' ).'">'.$form['submit_button_label'].'</button>';
	    $html .= '</div>';

	$html .= '</form>';

	return $html;
}


function parseFieldType($field){
    $fieldHtml= '';
    $settings = getFieldSettings($field['settings']);

    switch( $field['field_type'] ){
        case 'text':
        case 'email':
        case 'number':
        case 'date':
            $fieldHtml = '<input type="'.$field['field_type'].'" name="'.$field['field_name'].'" class="form-control" id="input-'.$field['field_name'].'" '.( $field['required']? 'required' : '' ).'>';
        break;
        case 'textarea':
            $fieldHtml = '<textarea class="form-control" name="'.$field['field_name'].'" id="input-'.$field['field_name'].'" '.( $field['required']? 'required' : '' ).'></textarea>';
        break;
        case 'descriptive_text':
            $fieldHtml = $field['descriptive_text'];
        break;
        case 'checkbox':

            $fieldHtml = '<div class="checkbox-group '.( $field['required']? 'required' : '' ).'">';
            $i = 0;
            foreach( $settings->options as $checkbox ){
                $fieldHtml .= '<div class="custom-control custom-checkbox">
                    <input type="checkbox" id="radio-'.$field['field_name'].'-'.$i.'" name="'.$field['field_name'].'[]" class="custom-control-input" value="'.$checkbox->value.'">
                    <label class="custom-control-label" for="radio-'.$field['field_name'].'-'.$i.'">'.$checkbox->label.'</label>
                </div>';
            $i++;
            }
            $fieldHtml .= '</div>';

        break;
        case 'radio':

            $fieldHtml = '<div class="radio-group">';
            $i = 0;
            foreach( $settings->options as $radio ){
                $fieldHtml .= '<div class="custom-control custom-radio">
                    <input type="radio" id="radio-'.$field['field_name'].'-'.$i.'" name="'.$field['field_name'].'" class="custom-control-input" value="'.$radio->value.'" '.( $field['required']? 'required' : '' ).'>
                    <label class="custom-control-label" for="radio-'.$field['field_name'].'-'.$i.'">'.$radio->label.'</label>
                </div>';
                $i++;
            }

            $fieldHtml .= '</div>';

        break;
        case 'select':

            $fieldHtml = '<select id="input-'.$field['field_name'].'" class="form-control" name="'.$field['field_name'].'" '.( $field['select_multiple']? 'multiple' : '' ).' '.( $field['required']? 'required' : '' ).' >';

            if( $settings->empty_first_option ){
                $fieldHtml .= '<option value="">Choose ...</option>';
            }

                foreach( $settings->options as $option ){
                    $fieldHtml .= '<option value="'.$option->value.'">'.$option->label.'</option>';
                }
            $fieldHtml .= '</select>';

        break;
        case 'file':
        case 'image':
            $fieldHtml = '<div class="custom-file">
                            <input type="file" class="custom-file-input" name="'.$field['field_name'].'" id="input-'.$field['field_name'].'" '.($settings->multiple_files? 'multiple' : '').' '.( $field['required']? 'required' : '' ).'>
                            <label class="custom-file-label" for="customFile">Choose '.$field['field_type'].'</label>
                        </div>
                        ';
        break;
    }

    return $fieldHtml;
}

function getFieldSettings($settings){
    return json_decode($settings);
}


/*
*
* GALLERY SHORTCODE
*
*
*/

function getGalleryHTML($id, $args){

}

function getGallery($id){
    $gallery = Gallery::find($id);
    return $gallery;
}


/*
* CUSTOM FIELDS Helper Functions
*
*
*/

// Get custom fields for specific object
function getCustomFields($object, $key = false, $term = false, $repeater = false, $repeaterId = 0, $i = 0){

    $fields = _compileFields($object, $key = false, $term, $repeater, $repeaterId, $i);
	return $fields;
}

function _compileFields($object, $key = false, $term = false, $repeater = false, $repeaterId = 0, $i = 0){

    $parts = explode('_', $object);
	$type = $parts[0];
	$id = 0;
    $fields = [];

	if(isset($parts[1])){
		$id = (int) $parts[1];
	}

    if(isset($parts[2])){
        $type = $parts[0].'_'.$parts[1];
        $id = (int) $parts[2];
    }

	$where = [
		['object_id', '=', $id],
		['object_type', '=', $type]
	];

	if( $key && !$repeater ){
		$where[] = ['cf_fields.field_name', '=' , $key];
	}

	$where[] = $repeater? ['parent_field_id', '=', $repeaterId] : ['parent_field_id'];

	$objectDatas = CfObjectData::
		join('cf_fields', 'cf_fields.field_id', '=', 'cf_object_data.field_id')
		->where($where)
		->orderBy('batch_sort', 'asc')
		->orderBy('sort', 'asc')
		->get();

	foreach( $objectDatas as $fieldRow ){

		switch( $fieldRow->field_type ){
			case 'text':
			case 'email':
			case 'textarea':
			if( $repeater ){
                $fields[$fieldRow->batch_id][ $fieldRow->field_name ] = $fieldRow->field_text;
            } else {
        		$fields[ $fieldRow->field_name ] = $fieldRow->field_text;
            }
			break;
			case 'number':
			if( $repeater ){
    			$fields[$fieldRow->batch_id][ $fieldRow->field_name ] = $fieldRow->field_number;
			} else {
    			$fields[ $fieldRow->field_name ] = $fieldRow->field_number;
			}
			break;
			case 'decimal':
			if( $repeater ){
    			$fields[$fieldRow->batch_id][ $fieldRow->field_name ] = $fieldRow->field_decimal;
			} else {
    			$fields[ $fieldRow->field_name ] = $fieldRow->field_decimal;
			}
			break;
			case 'date':
			if( $repeater ){
    			$fields[$fieldRow->batch_id][ $fieldRow->field_name ] = $fieldRow->field_date;
			} else {
    			$fields[ $fieldRow->field_name ] = $fieldRow->field_date;
			}
			break;
			case 'file':
			if( $repeater ){
    			$fields[$fieldRow->batch_id][ $fieldRow->field_name ] = $fieldRow->field_file;
			} else {
    			$fields[ $fieldRow->field_name ] = $fieldRow->field_file;
			}
			break;
			case 'image':
			if( $repeater ){
    			$fields[$fieldRow->batch_id][ $fieldRow->field_name ] = getImageSizes($fieldRow->field_image);
			} else {
    			$fields[ $fieldRow->field_name ] = getImageSizes($fieldRow->field_image);
			}
			break;
			case 'select':
			case 'checkbox':
			case 'radio':
			$values = explode(',', $fieldRow->field_text);

			$v = is_numeric($values[0])? $values[0] + 0 : $values[0] ;

			$value = count($values) > 1 ? $values : $v;

            if( count( $values ) > 1 ){
                $value = [];
                $configs = json_decode($fieldRow->field_config);
                foreach( $configs as $key => $config ){
                    $value[$config->label] = is_numeric($values[$key])? $values[$key]+0 : $values[$key];
                }
            }

			if( $repeater ){
    			$fields[$fieldRow->batch_id][ $fieldRow->field_name ] = $value;
			} else {
    			$fields[ $fieldRow->field_name ] = $value;
			}
			break;
			case 'editor':
			if( $repeater ){
    			$fields[$fieldRow->batch_id][ $fieldRow->field_name ] = $fieldRow->field_editor;
			} else {
			    $fields[ $fieldRow->field_name ] = $fieldRow->field_editor;
            }
			break;
			case 'repeater':
			$repeaterFields = _compileFields($object, $key, $term, true, $fieldRow->field_id, $fields, $i++);
			$repeaterFields = array_values($repeaterFields);
            if( $repeater ){
                $fields[$fieldRow->batch_id][ $fieldRow->field_name ] = $repeaterFields? $repeaterFields : [];
            } else {
                $fields[ $fieldRow->field_name ] = $repeaterFields? $repeaterFields : [];
            }

			break;
		}

	}

	$data = $key && !$repeater? $fields[$key] : $fields;

	return $data;
}

function getField($key){
    $customFields = view()->shared('customFields');
    return isset( $customFields[$key] )? $customFields[$key] : false;
}

function getRepeater($repeater){
    return is_array($repeater)? $repeater : [];
}

function getRepeaterField($key, $repeater){
    return isset( $repeater[$key] )? $repeater[$key] : false;
}


/*
* EVENTS Helper Functions
*
*
*/

function getEvents($id, $type){

}

function getEvent($id, $type, $fieldName){

}

function getUpcomingEvents($limit = 5){

    $events = CalendarEvent::
    join('template_calendar_events AS tc', 'tc.id' ,'=' ,'calendar_events.template_calendar_event_id')
    ->join('event_slugs AS es', 'es.event_id', '=', 'tc.id')
    ->orderBy('calendar_event.created_at', 'desc')
    ->limit($limit)
    ->get();

    foreach( $events as $key => $event ){
        $start = $event->start_datetime->timestamp;
        $end = $event->end_datetime->timestamp;
        $events[$key]->url = '/event/'.$event->slug.'/'.$start.'/'.$end;
    }

    return $events;
}

/*
* IMAGES
*
*
*/
function getFeaturedImage($objectId, $objectType){
    $featuredImage = ObjectMedia::where(['object_id' => $objectId, 'object_type' => $objectType, 'featured' => 1])->first();
    return getImageSizes($featuredImage);
}

function getImageSizes($image){
    if( !$image ){
        return [];
    }
    $sizes = [];
    $basename = basename($image);
    $urlInfo = parse_url($image);
    $fullpath = str_replace('/storage/', '', $urlInfo['path']);
    $justPath = str_replace( $basename, '', $fullpath);

    $imageSizes = config('neutrino.media.image_sizes');

    foreach( $imageSizes as $key => $value ){
        $path = $justPath.'_'.$key.'/'.$basename;

        $url = Storage::disk(config('neutrino.storage.filesystem'))->url($path);
        if( $url ){
            $sizes[$key] = $url;
        }

    }

    $ogPath = $justPath.'_original/'.$basename;

    $url = Storage::disk(config('neutrino.storage.filesystem'))->url($ogPath);
    if( $url ){
        $sizes['original'] = $url;
    }

    return $sizes;
}

function shoppeExists(){
    return class_exists('Newelement\Shoppe\Http\Controllers\Admin\ProductController');
}


/*
* SETTINGS Helper Functions
*
*
*/

// Get custom fields for specific object
function getSetting($name){
	$setting = Setting::where('key', $name)->first();
	$value = false;
	if( $setting ){
		switch($setting->type){
			case 'BOOL':
			$value = $setting->value_bool? true : false;
			break;
			case 'STRING':
			$value = $setting->value;
			break;
			default:
			$value = $setting->value;
		}
	}
	return $value;
}

function getScripts(){
    $bond = app('NeutrinoBond');
    $scripts = $bond->getScripts();
    $configScripts = config('neutrino.enqueue_js');
    $allScripts = [];
    if (file_exists(public_path('js/app.js'))){
        $allScripts[] = '/js/app.js';
    }
    foreach( $configScripts as $configScript ){
        $allScripts[] = ltrim($configScript, '/');
    }
    foreach( $scripts as $script ){
        $allScripts[] = $script;
    }
    if( shoppeExists() ){
        $allScripts[] = '/vendor/newelement/shoppe/js/shoppe.js';
    }

    if( getSetting('enable_asset_cache') ){

        $scriptContent = '';
        foreach ( $allScripts as $scriptFile ) {
            $scriptContent .= file_get_contents( public_path( $scriptFile ) );
        }

        //$scriptContent = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $scriptContent);
        //$scriptContent = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $scriptContent);
        $exists = \Storage::disk('public')->exists('assets/js/all.js');
        if( !$exists ){
            \Storage::disk('public')->put('assets/js/all.js', $scriptContent);
        }
        $allScript = \Storage::disk('public')->url('assets/js/all.js');
        echo  '<script src="'.$allScript.'"></script>'."\n";

    } else {
        foreach( $allScripts as $script ){
            echo  '<script src="'.$script.'"></script>'."\n";
        }
    }

}

function getStyles(){
    $bond = app('NeutrinoBond');
    $styles = $bond->getStyles();
    $configStyles = config('neutrino.enqueue_css');
    $allStyles = [];
    if (file_exists(public_path('css/app.css'))){
        $allStyles[] = 'css/app.css';
    }
    foreach( $configStyles as $configStyle ){
        $allStyles[] = ltrim($configStyle, '/');
    }
    foreach( $styles as $style ){
        $allStyles[] = ltrim($style, '/');
    }
    if( shoppeExists() ){
        $allStyles[] = 'vendor/newelement/shoppe/css/shoppe.css';
    }

    if( getSetting('enable_asset_cache') ){

        $cssContent = '';
        foreach ( $allStyles as $cssFile ) {
            $cssContent .= file_get_contents( public_path( $cssFile ) );
        }

        $cssContent = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $cssContent);
        $cssContent = str_replace(': ', ':', $cssContent);
        $cssContent = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $cssContent);
        $exists = \Storage::disk('public')->exists('assets/css/all.css');
        if( !$exists ){
            \Storage::disk('public')->put('assets/css/all.css', $cssContent);
        }
        $allCss = \Storage::disk('public')->url('assets/css/all.css');
        echo '<link href="'.$allCss.'" rel="stylesheet">'."\n";

    } else {
        foreach( $allStyles as $style ){
            echo '<link href="/'.$style.'" rel="stylesheet">'."\n";
        }
    }

}

function getAdminScripts(){
    $bond = app('NeutrinoBond');
    $adminScripts = $bond->getAdminScripts();
    foreach( $adminScripts as $script ){
        echo '<script src="'.$script.'"></script>'."\n";
    }
}

function getAdminStyles(){
    $bond = app('NeutrinoBond');
    $adminStyles = $bond->getAdminStyles();
    foreach( $adminStyles as $style ){
        echo '<link href="'.$style.'" rel="stylesheet">'."\n";
    }
}

function getEditorCss(){
    $styles = config('neutrino.equeue_editor_css', []);
    echo implode(',', $styles);
}

function getBlockField($block, $field = ''){
    $value = false;
    foreach( $block->fields as $field ){
        if( $field->name === $field ){
            return $field->value;
        }
    }
    return $value;
}

function hex2rgba($color, $opacity = false) {

	$default = 'rgb(0,0,0)';

	//Return default if no color provided
	if(empty($color))
          return $default;

	//Sanitize $color if "#" is provided
        if ($color[0] == '#' ) {
        	$color = substr( $color, 1 );
        }

        //Check if color has 6 or 3 characters and get values
        if (strlen($color) == 6) {
                $hex = array( $color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5] );
        } elseif ( strlen( $color ) == 3 ) {
                $hex = array( $color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2] );
        } else {
                return $default;
        }

        //Convert hexadec to rgb
        $rgb =  array_map('hexdec', $hex);

        //Check if opacity is set(rgba or rgb)
        if($opacity){
        	if(abs($opacity) > 1)
        		$opacity = 1.0;
        	$output = 'rgba('.implode(",",$rgb).','.$opacity.')';
        } else {
        	$output = 'rgb('.implode(",",$rgb).')';
        }

        //Return rgb(a) color string
        return $output;
}

/*
*
* BOND HELPERS
*
*
*/

/*
    $arr = [
        'package_name' => 'My Package Name',
        'version' => 1.0,
        'website' => 'https://mypackagewebsite.io',
        'repo' => 'https://github.com/vendor/package',
        'image' => 'https://url-to-my-package-image/image.png'
    ];

    Highly encouraged to create a .bond file. This will allow neutrino to check for version updates.

    .bond file -

    {
        package_name: 'My Package Name',
        version: 1.0
    }

*/
function registerPackage($arr){
    $bond = app('NeutrinoBond');
    $bond->registerPackage($arr);
}

/*
    $menuItems = [
        [
            'slot' => 4,
            'url' => '/admin/locations',
            'parent_title' => 'Locations',
            'named_route' => 'neutrino.locations',
            'fa-icon' => 'fa-map-marked',
            'children' => [
                [ 'url' => '/admin/locations', 'title' => 'All Locations' ],
                [ 'url' => '/admin/location', 'title' => 'Create Location' ],
            ]
        ]
    ];
*/
function registerAdminMenus($arr){
    $bond = app('NeutrinoBond');
    $bond->registerMenuItems($arr);
}

/*
    $styles = [
        '/vendor/newelement/packagename/css/app.css',
    ];
*/
function registerStyles($arr){
    $bond = app('NeutrinoBond');
    $bond->enqueueStyles($arr);
}

/*
    $scripts = [
        '/vendor/newelement/packagename/js/app.js',
    ];
*/
function registerScripts($arr){
    $bond = app('NeutrinoBond');
    $bond->enqueueScripts($arr);
}

function registerAdminStyles($arr){
    $bond = app('NeutrinoBond');
    $bond->enqueueAdminStyles($arr);
}

function registerAdminScripts($arr){
    $bond = app('NeutrinoBond');
    $bond->enqueueAdminScripts($arr);
}

/*
    $arr = [ 'model' => '\\Newelement\\Locations\\Models\\Location', 'key' => 'locations'];
*/
function registerSiteMap($arr){
    $bond = app('NeutrinoBond');
    $bond->registerSiteMap($arr);
}



function trimWords($text, $num_words = 5, $more = '...', $args = [], $remove_breaks = false){
    $text = getContent($args, $text);
    $original_text = $text;

    $text = preg_replace( '@<(script|style)[^>]*?>.*?</\\1>@si', '', $text );
    $text = strip_tags( $text );

    if ( $remove_breaks ) {
        $text = preg_replace( '/[\r\n\t ]+/', ' ', $text );
    }

    $text = trim( $text );

    $words_array = preg_split( "/[\n\r\t ]+/", $text, $num_words + 1, PREG_SPLIT_NO_EMPTY );
    $sep = ' ';

    if ( count( $words_array ) > $num_words ) {
        array_pop( $words_array );
        $text = implode( $sep, $words_array );
        $text = $text . $more;
    } else {
        $text = implode( $sep, $words_array );
    }

    $text = stripShortcodes($text);

    return $text;
}

