<?php
use Illuminate\Support\Str;
use Newelement\Neutrino\Models\ActivityLog;

function toSlug($text, $type = false)
{
	$text = preg_replace('~[^\pL\d]+~u', '-', $text);
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    $text = preg_replace('~[^-\w]+~', '', $text);
    $text = trim($text, '-');
    $text = preg_replace('~-+~', '-', $text);
    $text = strtolower($text);

    if (empty($text)) {
        return 'n-a';
    }

	$slug = checkSlug($text, $type);
    return $slug;
}

function str_plural($string)
{
    $string = Str::plural($string);
    return $string;
}

function str_singular($string)
{
    $string = Str::singular($string);
    return $string;
}

function checkSlug($slug, $type, $count = 0)
{
	if( $count ){
		$slug = $count > 1 ? preg_replace('/-[^-]*$/', '', $slug).'-'.$count : $slug.'-'.$count;
	}

	$exists = false;

	switch($type){
		case 'entry':
			$exists = DB::table('entries')->where(['slug' => $slug, 'entry_type' => 'entry' ])->first();
		break;
		case 'page':
			$exists = DB::table('pages')->where('slug', $slug)->first();
		break;
		case 'event':
			$exists = DB::table('event_slugs')->where('slug', $slug)->first();
		break;
		case 'product':
			$exists = DB::table('products')->where(['slug' => $slug ])->first();
		break;
		case 'entry_type':
			$exists = DB::table('entry_types')->where(['slug' => $slug ])->first();
		break;
		case 'taxonomy_type':
			$exists = DB::table('taxonomy_types')->where(['slug' => $slug ])->first();
		break;
		case 'taxonomy':
			$exists = DB::table('taxonomies')->where(['slug' => $slug ])->first();
		break;
		case 'place':
			$exists = DB::table('places')->where(['slug' => $slug ])->first();
		break;
        case 'attribute':
            $exists = DB::table('product_attributes')->where(['slug' => $slug ])->first();
        break;
		default:
			$exists = DB::table('entries')->where(['slug' => $slug, 'entry_type' => $type ])->first();
		break;
	}

	if( $exists ){
		$count++;
		return checkSlug($slug, $type, $count);
	} else {
		return $slug;
	}

}

function _parseCustomField($field, $repeater = 0)
{
	$html = '<li id="'.$field->field_id.'" data-row-id="'.$field->field_id.'">';
	$html .= '<div class="field-type-row" data-row-id="'.$field->field_id.'">';

	$fields = [
		[ 'label' => 'Text', 'type' => 'text'],
		[ 'label' => 'Checkbox', 'type' => 'checkbox'],
		[ 'label' => 'Radio', 'type' => 'radio'],
		[ 'label' => 'Multi-line Text', 'type' => 'textarea'],
		[ 'label' => 'Email', 'type' => 'email'],
		[ 'label' => 'Date', 'type' => 'date'],
		[ 'label' => 'Dropdown', 'type' => 'select'],
		[ 'label' => 'Number', 'type' => 'number'],
		[ 'label' => 'Decimal', 'type' => 'decimal'],
		[ 'label' => 'File', 'type' => 'file'],
		[ 'label' => 'Image', 'type' => 'image'],
		[ 'label' => 'Rich Text Editor', 'type' => 'editor']
	];

	switch( $field->field_type ){
		case 'text':
			$html .= '<div class="field-row">
				<div class="field-sort">
					<i class="far fa-sort"></i>
				</div>
				<div class="field-type-title">Text</div>
				<div class="field-row-options">
					<a class="collapse-field-row" href="/" data-row-id="'.$field->field_id.'">
						<i class="fas fa-angle-down"></i></a> <a class="remove-field-row" data-row-id="'.$field->field_id.'" href="/">&times;</a>
				</div>
			</div>

			<div id="field-group-'.$field->field_id.'" class="field-group" data-row-id="'.$field->field_id.'">
				<div class="field-row">
					<div class="label-col">Field Label</div>
					<div class="field-col">
						<input id="label-'.$field->field_id.'" class="field-label" type="text" name="field_label['.$field->field_id.']" value="'.$field->field_label.'">
						<input type="hidden" name="field_type['.$field->field_id.']" value="text">
					</div>
				</div>

				<div class="field-row">
					<div class="label-col">Field Name</div>
					<div class="field-col"><input id="name-'.$field->field_id.'" type="text" name="field_name['.$field->field_id.']" value="'.$field->field_name.'"></div>
				</div>

				<div class="field-row">
					<div class="label-col">Required</div>
					<div class="field-col">
						<label><input type="radio" name="field_required['.$field->field_id.']" '.($field->field_required === 1? 'checked' : '').' value="1"> Yes</label>
						<label><input type="radio" name="field_required['.$field->field_id.']" '.($field->field_required === 0? 'checked' : '').' value="0"> No</label>
					</div>
				</div>
			</div>
			';
		break;
		case 'email':
			$html .= '<div class="field-row">
				<div class="field-sort">
					<i class="far fa-sort"></i>
				</div>
				<div class="field-type-title">Email</div>
				<div class="field-row-options">
					<a class="collapse-field-row" href="/" data-row-id="'.$field->field_id.'">
						<i class="fas fa-angle-down"></i></a> <a class="remove-field-row" data-row-id="'.$field->field_id.'" href="/">&times;</a>
				</div>
			</div>

			<div id="field-group-'.$field->field_id.'" class="field-group" data-row-id="'.$field->field_id.'">
				<div class="field-row">
					<div class="label-col">Field Label</div>
					<div class="field-col">
						<input id="label-'.$field->field_id.'" class="field-label" type="text" name="field_label['.$field->field_id.']" value="'.$field->field_label.'">
						<input type="hidden" name="field_type['.$field->field_id.']" value="email">
					</div>
				</div>

				<div class="field-row">
					<div class="label-col">Field Name</div>
					<div class="field-col"><input id="name-'.$field->field_id.'" type="text" name="field_name['.$field->field_id.']" value="'.$field->field_name.'"></div>
				</div>

				<div class="field-row">
					<div class="label-col">Required</div>
					<div class="field-col">
						<label><input type="radio" name="field_required['.$field->field_id.']" '.($field->field_required === 1? 'checked' : '').' value="1"> Yes</label>
						<label><input type="radio" name="field_required['.$field->field_id.']" '.($field->field_required === 0? 'checked' : '').' value="0"> No</label>
					</div>
				</div>
			</div>
			';
		break;
		case 'date':
			$html .= '<div class="field-row">
				<div class="field-sort">
					<i class="far fa-sort"></i>
				</div>
				<div class="field-type-title">Date</div>
				<div class="field-row-options">
					<a class="collapse-field-row" href="/" data-row-id="'.$field->field_id.'">
						<i class="fas fa-angle-down"></i></a> <a class="remove-field-row" data-row-id="'.$field->field_id.'" href="/">&times;</a>
				</div>
			</div>

			<div id="field-group-'.$field->field_id.'" class="field-group" data-row-id="'.$field->field_id.'">
				<div class="field-row">
					<div class="label-col">Field Label</div>
					<div class="field-col">
						<input id="label-'.$field->field_id.'" class="field-label" type="text" name="field_label['.$field->field_id.']" value="'.$field->field_label.'">
						<input type="hidden" name="field_type['.$field->field_id.']" value="date">
					</div>
				</div>

				<div class="field-row">
					<div class="label-col">Field Name</div>
					<div class="field-col"><input id="name-'.$field->field_id.'" type="text" name="field_name['.$field->field_id.']" value="'.$field->field_name.'"></div>
				</div>

				<div class="field-row">
					<div class="label-col">Required</div>
					<div class="field-col">
						<label><input type="radio" name="field_required['.$field->field_id.']" '.($field->field_required === 1? 'checked' : '').' value="1"> Yes</label>
						<label><input type="radio" name="field_required['.$field->field_id.']" '.($field->field_required === 0? 'checked' : '').' value="0"> No</label>
					</div>
				</div>
			</div>
			';
		break;
		case 'number':
			$html .= '<div class="field-row">
				<div class="field-sort">
					<i class="far fa-sort"></i>
				</div>
				<div class="field-type-title">Number</div>
				<div class="field-row-options">
					<a class="collapse-field-row" href="/" data-row-id="'.$field->field_id.'">
						<i class="fas fa-angle-down"></i></a> <a class="remove-field-row" data-row-id="'.$field->field_id.'" href="/">&times;</a>
				</div>
			</div>

			<div id="field-group-'.$field->field_id.'" class="field-group" data-row-id="'.$field->field_id.'">
				<div class="field-row">
					<div class="label-col">Field Label</div>
					<div class="field-col">
						<input id="label-'.$field->field_id.'" class="field-label" type="text" name="field_label['.$field->field_id.']" value="'.$field->field_label.'">
						<input type="hidden" name="field_type['.$field->field_id.']" value="number">
					</div>
				</div>

				<div class="field-row">
					<div class="label-col">Field Name</div>
					<div class="field-col"><input id="name-'.$field->field_id.'" type="text" name="field_name['.$field->field_id.']" value="'.$field->field_name.'"></div>
				</div>

				<div class="field-row">
					<div class="label-col">Required</div>
					<div class="field-col">
						<label><input type="radio" name="field_required['.$field->field_id.']" '.($field->field_required === 1? 'checked' : '').' value="1"> Yes</label>
						<label><input type="radio" name="field_required['.$field->field_id.']" '.($field->field_required === 0? 'checked' : '').' value="0"> No</label>
					</div>
				</div>
			</div>
			';
		break;
		case 'decimal':
			$html .= '<div class="field-row">
				<div class="field-sort">
					<i class="far fa-sort"></i>
				</div>
				<div class="field-type-title">Decimal</div>
				<div class="field-row-options">
					<a class="collapse-field-row" href="/" data-row-id="'.$field->field_id.'">
						<i class="fas fa-angle-down"></i></a> <a class="remove-field-row" data-row-id="'.$field->field_id.'" href="/">&times;</a>
				</div>
			</div>

			<div id="field-group-'.$field->field_id.'" class="field-group" data-row-id="'.$field->field_id.'">
				<div class="field-row">
					<div class="label-col">Field Label</div>
					<div class="field-col">
						<input id="label-'.$field->field_id.'" class="field-label" type="text" name="field_label['.$field->field_id.']" value="'.$field->field_label.'">
						<input type="hidden" name="field_type['.$field->field_id.']" value="decimal">
					</div>
				</div>

				<div class="field-row">
					<div class="label-col">Field Name</div>
					<div class="field-col"><input id="name-'.$field->field_id.'" type="text" name="field_name['.$field->field_id.']" value="'.$field->field_name.'"></div>
				</div>

				<div class="field-row">
					<div class="label-col">Required</div>
					<div class="field-col">
						<label><input type="radio" name="field_required['.$field->field_id.']" '.($field->field_required === 1? 'checked' : '').' value="1"> Yes</label>
						<label><input type="radio" name="field_required['.$field->field_id.']" '.($field->field_required === 0? 'checked' : '').' value="0"> No</label>
					</div>
				</div>
			</div>
			';
		break;
		case 'textarea':
			$html .= '<div class="field-row">
				<div class="field-sort">
					<i class="far fa-sort"></i>
				</div>
				<div class="field-type-title">Multi-line Text</div>
				<div class="field-row-options">
					<a class="collapse-field-row" href="/" data-row-id="'.$field->field_id.'">
						<i class="fas fa-angle-down"></i></a> <a class="remove-field-row" data-row-id="'.$field->field_id.'" href="/">&times;</a>
				</div>
			</div>

			<div id="field-group-'.$field->field_id.'" class="field-group" data-row-id="'.$field->field_id.'">
				<div class="field-row">
					<div class="label-col">Field Label</div>
					<div class="field-col">
						<input id="label-'.$field->field_id.'" class="field-label" type="text" name="field_label['.$field->field_id.']" value="'.$field->field_label.'">
						<input type="hidden" name="field_type['.$field->field_id.']" value="textarea">
					</div>
				</div>

				<div class="field-row">
					<div class="label-col">Field Name</div>
					<div class="field-col"><input id="name-'.$field->field_id.'" type="text" name="field_name['.$field->field_id.']" value="'.$field->field_name.'"></div>
				</div>

				<div class="field-row">
					<div class="label-col">Required</div>
					<div class="field-col">
						<label><input type="radio" name="field_required['.$field->field_id.']" '.($field->field_required === 1? 'checked' : '').' value="1"> Yes</label>
						<label><input type="radio" name="field_required['.$field->field_id.']" '.($field->field_required === 0? 'checked' : '').' value="0"> No</label>
					</div>
				</div>
			</div>
			';
		break;
		case 'file':
			$html .= '<div class="field-row">
				<div class="field-sort">
					<i class="far fa-sort"></i>
				</div>
				<div class="field-type-title">File Upload</div>
				<div class="field-row-options">
					<a class="collapse-field-row" href="/" data-row-id="'.$field->field_id.'">
						<i class="fas fa-angle-down"></i></a> <a class="remove-field-row" data-row-id="'.$field->field_id.'" href="/">&times;</a>
				</div>
			</div>

			<div id="field-group-'.$field->field_id.'" class="field-group" data-row-id="'.$field->field_id.'">
				<div class="field-row">
					<div class="label-col">Field Label</div>
					<div class="field-col">
						<input id="label-'.$field->field_id.'" class="field-label" type="text" name="field_label['.$field->field_id.']" value="'.$field->field_label.'">
						<input type="hidden" name="field_type['.$field->field_id.']" value="file">
					</div>
				</div>

				<div class="field-row">
					<div class="label-col">Field Name</div>
					<div class="field-col"><input id="name-'.$field->field_id.'" type="text" name="field_name['.$field->field_id.']" value="'.$field->field_name.'"></div>
				</div>

				<div class="field-row">
					<div class="label-col">Multiple Files</div>
					<div class="field-col">
						<label><input type="radio" name="field_multiple['.$field->field_id.']" '.($field->multiple_files === 1? 'checked' : '').' value="1"> Yes</label>
						<label><input type="radio" name="field_multiple['.$field->field_id.']" '.($field->multiple_files === 0? 'checked' : '').' value="0"> No</label>
					</div>
				</div>

				<div class="field-row">
					<div class="label-col">Allowed File Types</div>
					<div class="field-col"><label><input type="text" name="field_filetypes['.$field->field_id.']" value="*"></div>
				</div>

				<div class="field-row">
					<div class="label-col">Required</div>
					<div class="field-col">
						<label><input type="radio" name="field_required['.$field->field_id.']" '.($field->field_required === 1? 'checked' : '').' value="1"> Yes</label>
						<label><input type="radio" name="field_required['.$field->field_id.']" '.($field->field_required === 0? 'checked' : '').' value="0"> No</label>
					</div>
				</div>
			</div>
			';
		break;
		case 'image':
			$html .= '<div class="field-row">
				<div class="field-sort">
					<i class="far fa-sort"></i>
				</div>
				<div class="field-type-title">Image Upload</div>
				<div class="field-row-options">
					<a class="collapse-field-row" href="/" data-row-id="'.$field->field_id.'">
						<i class="fas fa-angle-down"></i></a> <a class="remove-field-row" data-row-id="'.$field->field_id.'" href="/">&times;</a>
				</div>
			</div>

			<div id="field-group-'.$field->field_id.'" class="field-group" data-row-id="'.$field->field_id.'">
				<div class="field-row">
					<div class="label-col">Field Label</div>
					<div class="field-col">
						<input id="label-'.$field->field_id.'" class="field-label" type="text" name="field_label['.$field->field_id.']" value="'.$field->field_label.'">
						<input type="hidden" name="field_type['.$field->field_id.']" value="image">
					</div>
				</div>

				<div class="field-row">
					<div class="label-col">Field Name</div>
					<div class="field-col"><input id="name-'.$field->field_id.'" type="text" name="field_name['.$field->field_id.']" value="'.$field->field_name.'"></div>
				</div>

				<div class="field-row">
					<div class="label-col">Multiple Files</div>
					<div class="field-col">
						<label><input type="radio" name="field_multiple['.$field->field_id.']" '.($field->multiple_files === 1? 'checked' : '').' value="1"> Yes</label>
						<label><input type="radio" name="field_multiple['.$field->field_id.']" '.($field->multiple_files === 0? 'checked' : '').' value="0"> No</label>
					</div>
				</div>

				<div class="field-row">
					<div class="label-col">Allowed Image Types</div>
					<div class="field-col"><label><input type="text" name="field_filetypes['.$field->field_id.']" value="*"></div>
				</div>

				<div class="field-row">
					<div class="label-col">Required</div>
					<div class="field-col">
						<label><input type="radio" name="field_required['.$field->field_id.']" '.($field->field_required === 1? 'checked' : '').' value="1"> Yes</label>
						<label><input type="radio" name="field_required['.$field->field_id.']" '.($field->field_required === 0? 'checked' : '').' value="0"> No</label>
					</div>
				</div>
			</div>
			';
		break;
		case 'select':
		$html .= '<div class="field-row">
			<div class="field-sort">
				<i class="far fa-sort"></i>
			</div>
				<div class="field-type-title">Dropdown</div>
				<div class="field-row-options">
					<a class="collapse-field-row" href="/" data-row-id="'.$field->field_id.'">
						<i class="fas fa-angle-down"></i></a> <a class="remove-field-row" data-row-id="'.$field->field_id.'" href="/">&times;</a>
				</div>
			</div>

		<div id="field-group-'.$field->field_id.'" class="field-group" data-row-id="'.$field->field_id.'">
			<div class="field-row">
				<div class="label-col">Field Label</div>
				<div class="field-col">
					<input id="label-'.$field->field_id.'" class="field-label" type="text" name="field_label['.$field->field_id.']" value="'.$field->field_label.'">
					<input type="hidden" name="field_type['.$field->field_id.']" value="select">
				</div>
			</div>

			<div class="field-row">
				<div class="label-col">Field Name</div>
				<div class="field-col"><input id="name-'.$field->field_id.'" type="text" name="field_name['.$field->field_id.']" value="'.$field->field_name.'"></div>
			</div>

			<div class="field-row">
				<div class="label-col">Options</div>
				<div class="field-col"><textarea name="field_options['.$field->field_id.']" placeholder="Label:value">'._explodeConfig($field->field_config, 'select').'</textarea><span class="notes">Enter each option setup on a new line. Example:<br>Label:value<br>Label:value</span></div>
			</div>

			<div class="field-row">
				<div class="label-col">Empty First Option?</div>
				<div class="field-col"><label>
					<input type="radio" name="field_firstoption['.$field->field_id.']" '.($field->empty_first_option === 1? 'checked' : '').' value="1"> Yes</label>
					<label><input type="radio" name="field_firstoption['.$field->field_id.']" '.($field->empty_first_option === 0? 'checked' : '').'  value="0"> No</label>
				</div>
			</div>

			<div class="field-row">
				<div class="label-col">Required</div>
				<div class="field-col">
					<label><input type="radio" name="field_required['.$field->field_id.']" '.($field->field_required === 1? 'checked' : '').' value="1"> Yes</label>
					<label><input type="radio" name="field_required['.$field->field_id.']" '.($field->field_required === 0? 'checked' : '').' value="0"> No</label>
				</div>
			</div>
		</div>';
		break;

		case 'checkbox':
		$html .= '<div class="field-row">
			<div class="field-sort">
				<i class="far fa-sort"></i>
			</div>
				<div class="field-type-title">Checkboxes</div>
				<div class="field-row-options">
					<a class="collapse-field-row" href="/" data-row-id="'.$field->field_id.'">
						<i class="fas fa-angle-down"></i></a> <a class="remove-field-row" data-row-id="'.$field->field_id.'" href="/">&times;</a>
				</div>
			</div>

		<div id="field-group-'.$field->field_id.'" class="field-group" data-row-id="'.$field->field_id.'">
			<div class="field-row">
				<div class="label-col">Field Label</div>
				<div class="field-col">
					<input id="label-'.$field->field_id.'" class="field-label" type="text" name="field_label['.$field->field_id.']" value="'.$field->field_label.'">
					<input type="hidden" name="field_type['.$field->field_id.']" value="checkbox">
				</div>
			</div>

			<div class="field-row">
				<div class="label-col">Field Name</div>
				<div class="field-col"><input id="name-'.$field->field_id.'" type="text" name="field_name['.$field->field_id.']" value="'.$field->field_name.'"></div>
			</div>

			<div class="field-row">
				<div class="label-col">Checkboxes</div>
				<div class="field-col"><textarea name="field_options['.$field->field_id.']" placeholder="Label:value">'._explodeConfig($field->field_config, 'checkbox').'</textarea><span class="notes">Enter each checkbox setup on a new line. Example:<br>Label:value<br>Label:value</span></div>
			</div>

			<div class="field-row">
				<div class="label-col">Required</div>
				<div class="field-col">
					<label><input type="radio" name="field_required['.$field->field_id.']" '.($field->field_required === 1? 'checked' : '').' value="1"> Yes</label>
					<label><input type="radio" name="field_required['.$field->field_id.']" '.($field->field_required === 0? 'checked' : '').' value="0"> No</label>
				</div>
			</div>
		</div>';
		break;

		case 'radio':
		$html .= '<div class="field-row">
			<div class="field-sort">
				<i class="far fa-sort"></i>
			</div>
				<div class="field-type-title">Radios</div>
				<div class="field-row-options">
					<a class="collapse-field-row" href="/" data-row-id="'.$field->field_id.'">
						<i class="fas fa-angle-down"></i></a> <a class="remove-field-row" data-row-id="'.$field->field_id.'" href="/">&times;</a>
				</div>
			</div>

		<div id="field-group-'.$field->field_id.'" class="field-group" data-row-id="'.$field->field_id.'">
			<div class="field-row">
				<div class="label-col">Field Label</div>
				<div class="field-col">
					<input id="label-'.$field->field_id.'" class="field-label" type="text" name="field_label['.$field->field_id.']" value="'.$field->field_label.'">
					<input type="hidden" name="field_type['.$field->field_id.']" value="radio">
				</div>
			</div>

			<div class="field-row">
				<div class="label-col">Field Name</div>
				<div class="field-col"><input id="name-'.$field->field_id.'" type="text" name="field_name['.$field->field_id.']" value="'.$field->field_name.'"></div>
			</div>

			<div class="field-row">
				<div class="label-col">Radios</div>
				<div class="field-col"><textarea name="field_options['.$field->field_id.']" placeholder="Label:value">'._explodeConfig($field->field_config, 'radio').'</textarea><span class="notes">Enter each radio setup on a new line. Example:<br>Label:value<br>Label:value</span></div>
			</div>

			<div class="field-row">
				<div class="label-col">Required</div>
				<div class="field-col">
					<label><input type="radio" name="field_required['.$field->field_id.']" '.($field->field_required === 1? 'checked' : '').' value="1"> Yes</label>
					<label><input type="radio" name="field_required['.$field->field_id.']" '.($field->field_required === 0? 'checked' : '').' value="0"> No</label>
				</div>
			</div>
		</div>';
		break;

		case 'editor':

		$html .= '<div class="field-row">
			<div class="field-sort">
				<i class="far fa-sort"></i>
			</div>
			<div class="field-type-title">Rich Text Editor</div>
			<div class="field-row-options">
				<a class="collapse-field-row" href="/" data-row-id="'.$field->field_id.'"><i class="fas fa-angle-down"></i></a>
				<a class="remove-field-row" data-row-id="'.$field->field_id.'" href="/">&times;</a>
			</div>
		</div>

		<div id="field-group-'.$field->field_id.'" class="field-group" data-row-id="'.$field->field_id.'">
			<div class="field-row">
				<div class="label-col">Field Label</div>
				<div class="field-col"><input id="label-'.$field->field_id.'" class="field-label" type="text" name="field_label['.$field->field_id.']" value="'.$field->field_label.'">
				<input type="hidden" name="field_type['.$field->field_id.']" value="editor">
			</div>
			</div>

			<div class="field-row">
				<div class="label-col">Field Name</div>
				<div class="field-col"><input id="name-'.$field->field_id.'" type="text" name="field_name['.$field->field_id.']" value="'.$field->field_name.'"></div>
			</div>';

		break;

		case 'repeater':

		$html .= '<div class="field-row">
			<div class="field-sort">
				<i class="far fa-sort"></i>
			</div>
			<div class="field-type-title">Repeater</div>
			<div class="field-row-options">
				<a class="collapse-field-row" href="/" data-row-id="'.$field->field_id.'"><i class="fas fa-angle-down"></i></a>
				<a class="remove-field-row" data-row-id="'.$field->field_id.'" data-row-id="'.$field->field_id.'" href="/">&times;</a>
			</div>
		</div>

		<div id="field-group-'.$field->field_id.'" class="field-group" data-row-id="'.$field->field_id.'">
			<div class="field-row">
				<div class="label-col">Field Label</div>
				<div class="field-col"><input id="label-'.$field->field_id.'" class="field-label" type="text" name="field_label['.$field->field_id.']" value="'.$field->field_label.'">
				<input type="hidden" name="field_type['.$field->field_id.']" value="repeater">
			</div>
			</div>

			<div class="field-row">
				<div class="label-col">Field Name</div>
				<div class="field-col"><input id="name-'.$field->field_id.'" type="text" name="field_name['.$field->field_id.']" value="'.$field->field_name.'"></div>
			</div>

			<div class="field-row">
				<div class="label-col full">Repeater Fields <div class="choose-repeater-fields">'._fieldsDropDown($field->field_id, $fields).'</div></div>
				<div class="field-col full has-repeater">
					<ul class="repeater-fields-list" id="repeater-fields'.$field->field_id.'">
					'._getRepeaterFields($field->field_id).'
					</ul>
				</div>
			</div>
			';

		break;
	}

	if( $repeater ){
		$html .= '<input type="hidden" name="field_repeater['.$field->field_id.']" value="'.$repeater.'" >';
	}

	$html .= '</div>';
	$html .= '</li>';

	return $html;

}

function _getRepeaterFields($fieldId)
{
	$html = '';
	$fields = DB::table('cf_fields')
				->where('repeater_id', $fieldId)
				->orderBy('sort', 'asc')
				->get();

	foreach( $fields as $field ){
		$html .= _parseCustomField($field, $field->repeater_id);
	}
	return $html;
}

function _parseField($field, $repeater = 0)
{
	$html = '<li id="'.$field->field_id.'" data-row-id="'.$field->field_id.'">';
	$html .= '<div class="field-type-row" data-row-id="'.$field->field_id.'">';

	$fields = [
		[ 'label' => 'Text', 'type' => 'text'],
		[ 'label' => 'Checkbox', 'type' => 'checkbox'],
		[ 'label' => 'Radio', 'type' => 'radio'],
		[ 'label' => 'Multi-line Text', 'type' => 'textarea'],
		[ 'label' => 'Email', 'type' => 'email'],
		[ 'label' => 'Date', 'type' => 'date'],
		[ 'label' => 'Dropdown', 'type' => 'select'],
		[ 'label' => 'File', 'type' => 'file'],
		[ 'label' => 'Image', 'type' => 'image']
	];

	$attrs = json_decode($field->settings);

	switch( $field->field_type ){
		case 'text':
			$html .= '<div class="field-row">
				<div class="field-sort">
					<i class="far fa-sort"></i>
				</div>
				<div class="field-type-title">Text</div>
				<div class="field-row-options">
					<a class="collapse-field-row" href="/" data-row-id="'.$field->field_id.'">
						<i class="fas fa-angle-down"></i></a> <a class="remove-field-row" data-row-current="1" data-row-id="'.$field->field_id.'" href="/">&times;</a>
				</div>
			</div>

			<div id="field-group-'.$field->field_id.'" class="field-group" data-row-id="'.$field->field_id.'">
				<div class="field-row">
					<div class="label-col">Field Label</div>
					<div class="field-col">
						<input id="label-'.$field->field_id.'" class="field-label" type="text" name="field_label['.$field->field_id.']" value="'.$field->field_label.'" required>
						<input type="hidden" name="field_type['.$field->field_id.']" value="text">
					</div>
				</div>

				<div class="field-row">
					<div class="label-col">Field Name</div>
					<div class="field-col"><input id="name-'.$field->field_id.'" type="text" name="field_name['.$field->field_id.']" value="'.$field->field_name.'" required></div>
				</div>

				<div class="field-row">
					<div class="label-col">Required</div>
					<div class="field-col">
						<label><input type="radio" name="field_required['.$field->field_id.']" '.($field->required === 1? 'checked' : '').' value="1"> Yes</label>
						<label><input type="radio" name="field_required['.$field->field_id.']" '.($field->required === 0? 'checked' : '').' value="0"> No</label>
					</div>
				</div>';
				/*
				<div class="field-row">
					<div class="label-col">Conditional</div>
					<div class="field-col"><label><input type="checkbox" data-conditional-toggle="'.$field->field_id.'" name="field_conditional['.$field->field_id.']" value="1"> Yes</label></div>
				</div>

				<div id="conditional-toggle-'.$field->field_id.'" class="field-row field-conditonal-rules">
					<div class="label-col">Conditional Rules</div>
					<div class="field-col">
					    <div class="select-wrapper">
					        <select name="field_conditional_view['.$field->field_id.']">
					            <option value="show">Show</option>
					            <option value="hide">Hide</option>
                            </select>
					   </div>
                        <span class="notes">You must add the field you want to use for the conditional value and save the form first.</span>
					</div>
				</div>*/

			$html .= '</div>
			';
		break;
		case 'email':
			$html .= '<div class="field-row">
				<div class="field-sort">
					<i class="far fa-sort"></i>
				</div>
				<div class="field-type-title">Email</div>
				<div class="field-row-options">
					<a class="collapse-field-row" href="/" data-row-id="'.$field->field_id.'">
						<i class="fas fa-angle-down"></i></a> <a class="remove-field-row" data-row-current="1" data-row-id="'.$field->field_id.'" href="/">&times;</a>
				</div>
			</div>

			<div id="field-group-'.$field->field_id.'" class="field-group" data-row-id="'.$field->field_id.'">
				<div class="field-row">
					<div class="label-col">Field Label</div>
					<div class="field-col">
						<input id="label-'.$field->field_id.'" class="field-label" type="text" name="field_label['.$field->field_id.']" value="'.$field->field_label.'" required>
						<input type="hidden" name="field_type['.$field->field_id.']" value="email">
					</div>
				</div>

				<div class="field-row">
					<div class="label-col">Field Name</div>
					<div class="field-col"><input id="name-'.$field->field_id.'" type="text" name="field_name['.$field->field_id.']" value="'.$field->field_name.'" required></div>
				</div>

				<div class="field-row">
					<div class="label-col">Required</div>
					<div class="field-col">
						<label><input type="radio" name="field_required['.$field->field_id.']" '.($field->required === 1? 'checked' : '').' value="1"> Yes</label>
						<label><input type="radio" name="field_required['.$field->field_id.']" '.($field->required === 0? 'checked' : '').' value="0"> No</label>
					</div>
				</div>
			</div>
			';
		break;
		case 'date':
			$html .= '<div class="field-row">
				<div class="field-sort">
					<i class="far fa-sort"></i>
				</div>
				<div class="field-type-title">Date</div>
				<div class="field-row-options">
					<a class="collapse-field-row" href="/" data-row-id="'.$field->field_id.'">
						<i class="fas fa-angle-down"></i></a> <a class="remove-field-row" data-row-current="1" data-row-id="'.$field->field_id.'" href="/">&times;</a>
				</div>
			</div>

			<div id="field-group-'.$field->field_id.'" class="field-group" data-row-id="'.$field->field_id.'">
				<div class="field-row">
					<div class="label-col">Field Label</div>
					<div class="field-col">
						<input id="label-'.$field->field_id.'" class="field-label" type="text" name="field_label['.$field->field_id.']" value="'.$field->field_label.'" required>
						<input type="hidden" name="field_type['.$field->field_id.']" value="date">
					</div>
				</div>

				<div class="field-row">
					<div class="label-col">Field Name</div>
					<div class="field-col"><input id="name-'.$field->field_id.'" type="text" name="field_name['.$field->field_id.']" value="'.$field->field_name.'" required></div>
				</div>

				<div class="field-row">
					<div class="label-col">Required</div>
					<div class="field-col">
						<label><input type="radio" name="field_required['.$field->field_id.']" '.($field->required === 1? 'checked' : '').' value="1"> Yes</label>
						<label><input type="radio" name="field_required['.$field->field_id.']" '.($field->required === 0? 'checked' : '').' value="0"> No</label>
					</div>
				</div>
			</div>
			';
		break;
		case 'number':
			$html .= '<div class="field-row">
				<div class="field-sort">
					<i class="far fa-sort"></i>
				</div>
				<div class="field-type-title">Number</div>
				<div class="field-row-options">
					<a class="collapse-field-row" href="/" data-row-id="'.$field->field_id.'">
						<i class="fas fa-angle-down"></i></a> <a class="remove-field-row" data-row-current="1" data-row-id="'.$field->field_id.'" href="/">&times;</a>
				</div>
			</div>

			<div id="field-group-'.$field->field_id.'" class="field-group" data-row-id="'.$field->field_id.'">
				<div class="field-row">
					<div class="label-col">Field Label</div>
					<div class="field-col">
						<input id="label-'.$field->field_id.'" class="field-label" type="text" name="field_label['.$field->field_id.']" value="'.$field->field_label.'" required>
						<input type="hidden" name="field_type['.$field->field_id.']" value="number">
					</div>
				</div>

				<div class="field-row">
					<div class="label-col">Field Name</div>
					<div class="field-col"><input id="name-'.$field->field_id.'" type="text" name="field_name['.$field->field_id.']" value="'.$field->field_name.'" required></div>
				</div>

				<div class="field-row">
					<div class="label-col">Required</div>
					<div class="field-col">
						<label><input type="radio" name="field_required['.$field->field_id.']" '.($field->required === 1? 'checked' : '').' value="1"> Yes</label>
						<label><input type="radio" name="field_required['.$field->field_id.']" '.($field->required === 0? 'checked' : '').' value="0"> No</label>
					</div>
				</div>
			</div>
			';
		break;
		case 'decimal':
			$html .= '<div class="field-row">
				<div class="field-sort">
					<i class="far fa-sort"></i>
				</div>
				<div class="field-type-title">Decimal</div>
				<div class="field-row-options">
					<a class="collapse-field-row" href="/" data-row-id="'.$field->field_id.'">
						<i class="fas fa-angle-down"></i></a> <a class="remove-field-row" data-row-current="1" data-row-id="'.$field->field_id.'" href="/">&times;</a>
				</div>
			</div>

			<div id="field-group-'.$field->field_id.'" class="field-group" data-row-id="'.$field->field_id.'">
				<div class="field-row">
					<div class="label-col">Field Label</div>
					<div class="field-col">
						<input id="label-'.$field->field_id.'" class="field-label" type="text" name="field_label['.$field->field_id.']" value="'.$field->field_label.'" required>
						<input type="hidden" name="field_type['.$field->field_id.']" value="decimal">
					</div>
				</div>

				<div class="field-row">
					<div class="label-col">Field Name</div>
					<div class="field-col"><input id="name-'.$field->field_id.'" type="text" name="field_name['.$field->field_id.']" value="'.$field->field_name.'" required></div>
				</div>

				<div class="field-row">
					<div class="label-col">Required</div>
					<div class="field-col">
						<label><input type="radio" name="field_required['.$field->field_id.']" '.($field->required === 1? 'checked' : '').' value="1"> Yes</label>
						<label><input type="radio" name="field_required['.$field->field_id.']" '.($field->required === 0? 'checked' : '').' value="0"> No</label>
					</div>
				</div>
			</div>
			';
		break;
		case 'textarea':
			$html .= '<div class="field-row">
				<div class="field-sort">
					<i class="far fa-sort"></i>
				</div>
				<div class="field-type-title">Multi-line Text</div>
				<div class="field-row-options">
					<a class="collapse-field-row" href="/" data-row-id="'.$field->field_id.'">
						<i class="fas fa-angle-down"></i></a> <a class="remove-field-row" data-row-current="1" data-row-id="'.$field->field_id.'" href="/">&times;</a>
				</div>
			</div>

			<div id="field-group-'.$field->field_id.'" class="field-group" data-row-id="'.$field->field_id.'">
				<div class="field-row">
					<div class="label-col">Field Label</div>
					<div class="field-col">
						<input id="label-'.$field->field_id.'" class="field-label" type="text" name="field_label['.$field->field_id.']" value="'.$field->field_label.'" required>
						<input type="hidden" name="field_type['.$field->field_id.']" value="textarea">
					</div>
				</div>

				<div class="field-row">
					<div class="label-col">Field Name</div>
					<div class="field-col"><input id="name-'.$field->field_id.'" type="text" name="field_name['.$field->field_id.']" value="'.$field->field_name.'" required></div>
				</div>

				<div class="field-row">
					<div class="label-col">Required</div>
					<div class="field-col">
						<label><input type="radio" name="field_required['.$field->field_id.']" '.($field->required === 1? 'checked' : '').' value="1"> Yes</label>
						<label><input type="radio" name="field_required['.$field->field_id.']" '.($field->required === 0? 'checked' : '').' value="0"> No</label>
					</div>
				</div>
			</div>
			';
		break;
		case 'file':
			$html .= '<div class="field-row">
				<div class="field-sort">
					<i class="far fa-sort"></i>
				</div>
				<div class="field-type-title">File Upload</div>
				<div class="field-row-options">
					<a class="collapse-field-row" href="/" data-row-id="'.$field->field_id.'">
						<i class="fas fa-angle-down"></i></a> <a class="remove-field-row" data-row-current="1" data-row-id="'.$field->field_id.'" href="/">&times;</a>
				</div>
			</div>

			<div id="field-group-'.$field->field_id.'" class="field-group" data-row-id="'.$field->field_id.'">
				<div class="field-row">
					<div class="label-col">Field Label</div>
					<div class="field-col">
						<input id="label-'.$field->field_id.'" class="field-label" type="text" name="field_label['.$field->field_id.']" value="'.$field->field_label.'" required>
						<input type="hidden" name="field_type['.$field->field_id.']" value="file">
					</div>
				</div>

				<div class="field-row">
					<div class="label-col">Field Name</div>
					<div class="field-col"><input id="name-'.$field->field_id.'" type="text" name="field_name['.$field->field_id.']" value="'.$field->field_name.'" required></div>
				</div>

				<div class="field-row">
					<div class="label-col">Multiple Files</div>
					<div class="field-col">
						<label><input type="radio" name="field_multiple['.$field->field_id.']" '.($attrs->multiple_files === 1? 'checked' : '').' value="1"> Yes</label>
						<label><input type="radio" name="field_multiple['.$field->field_id.']" '.($attrs->multiple_files === 0? 'checked' : '').' value="0"> No</label>
					</div>
				</div>

				<div class="field-row">
					<div class="label-col">Allowed File Types</div>
					<div class="field-col"><label><input type="text" name="field_filetypes['.$field->field_id.']" value="*"></div>
				</div>

				<div class="field-row">
					<div class="label-col">Required</div>
					<div class="field-col">
						<label><input type="radio" name="field_required['.$field->field_id.']" '.($field->required === 1? 'checked' : '').' value="1"> Yes</label>
						<label><input type="radio" name="field_required['.$field->field_id.']" '.($field->required === 0? 'checked' : '').' value="0"> No</label>
					</div>
				</div>
			</div>
			';
		break;
		case 'image':
			$html .= '<div class="field-row">
				<div class="field-sort">
					<i class="far fa-sort"></i>
				</div>
				<div class="field-type-title">Image Upload</div>
				<div class="field-row-options">
					<a class="collapse-field-row" href="/" data-row-id="'.$field->field_id.'">
						<i class="fas fa-angle-down"></i></a> <a class="remove-field-row" data-row-current="1" data-row-id="'.$field->field_id.'" href="/">&times;</a>
				</div>
			</div>

			<div id="field-group-'.$field->field_id.'" class="field-group" data-row-id="'.$field->field_id.'">
				<div class="field-row">
					<div class="label-col">Field Label</div>
					<div class="field-col">
						<input id="label-'.$field->field_id.'" class="field-label" type="text" name="field_label['.$field->field_id.']" value="'.$field->field_label.'" required>
						<input type="hidden" name="field_type['.$field->field_id.']" value="image">
					</div>
				</div>

				<div class="field-row">
					<div class="label-col">Field Name</div>
					<div class="field-col"><input id="name-'.$field->field_id.'" type="text" name="field_name['.$field->field_id.']" value="'.$field->field_name.'" required></div>
				</div>

				<div class="field-row">
					<div class="label-col">Multiple Files</div>
					<div class="field-col">
						<label><input type="radio" name="field_multiple['.$field->field_id.']" '.($attrs->multiple_files === 1? 'checked' : '').' value="1"> Yes</label>
						<label><input type="radio" name="field_multiple['.$field->field_id.']" '.($attrs->multiple_files === 0? 'checked' : '').' value="0"> No</label>
					</div>
				</div>

				<div class="field-row">
					<div class="label-col">Allowed Image Types</div>
					<div class="field-col"><label><input type="text" name="field_filetypes['.$field->field_id.']" value="*"></div>
				</div>

				<div class="field-row">
					<div class="label-col">Required</div>
					<div class="field-col">
						<label><input type="radio" name="field_required['.$field->field_id.']" '.($field->required === 1? 'checked' : '').' value="1"> Yes</label>
						<label><input type="radio" name="field_required['.$field->field_id.']" '.($field->required === 0? 'checked' : '').' value="0"> No</label>
					</div>
				</div>
			</div>
			';
		break;
		case 'select':
		$html .= '<div class="field-row">
			<div class="field-sort">
				<i class="far fa-sort"></i>
			</div>
				<div class="field-type-title">Dropdown</div>
				<div class="field-row-options">
					<a class="collapse-field-row" href="/" data-row-id="'.$field->field_id.'">
						<i class="fas fa-angle-down"></i></a> <a class="remove-field-row" data-row-current="1" data-row-id="'.$field->field_id.'" href="/">&times;</a>
				</div>
			</div>

		<div id="field-group-'.$field->field_id.'" class="field-group" data-row-id="'.$field->field_id.'">
			<div class="field-row">
				<div class="label-col">Field Label</div>
				<div class="field-col">
					<input id="label-'.$field->field_id.'" class="field-label" type="text" name="field_label['.$field->field_id.']" value="'.$field->field_label.'" required>
					<input type="hidden" name="field_type['.$field->field_id.']" value="select">
				</div>
			</div>

			<div class="field-row">
				<div class="label-col">Field Name</div>
				<div class="field-col"><input id="name-'.$field->field_id.'" type="text" name="field_name['.$field->field_id.']" value="'.$field->field_name.'" required></div>
			</div>

			<div class="field-row">
				<div class="label-col">Options</div>
				<div class="field-col"><textarea name="field_options['.$field->field_id.']" placeholder="Label:value">'._explodeOptionsConfig($field->settings, 'select').'</textarea><span class="notes">Enter each option setup on a new line. Example:<br>Label:value<br>Label:value</span></div>
			</div>

			<div class="field-row">
				<div class="label-col">Empty First Option?</div>
				<div class="field-col"><label>
					<input type="radio" name="field_firstoption['.$field->field_id.']" '.($attrs->empty_first_option === 1? 'checked' : '').' value="1"> Yes</label>
					<label><input type="radio" name="field_firstoption['.$field->field_id.']" '.($attrs->empty_first_option === 0? 'checked' : '').'  value="0"> No</label>
				</div>
			</div>

			<div class="field-row">
				<div class="label-col">Select Multiple</div>
				<div class="field-col">
					<label><input type="radio" name="field_select_multiple['.$field->field_id.']" '.($field->select_multiple === 1? 'checked' : '').' value="1"> Yes</label>
					<label><input type="radio" name="field_select_multiple['.$field->field_id.']" '.($field->select_multiple === 0? 'checked' : '').' value="0"> No</label>
				</div>
			</div>

			<div class="field-row">
				<div class="label-col">Required</div>
				<div class="field-col">
					<label><input type="radio" name="field_required['.$field->field_id.']" '.($field->required === 1? 'checked' : '').' value="1"> Yes</label>
					<label><input type="radio" name="field_required['.$field->field_id.']" '.($field->required === 0? 'checked' : '').' value="0"> No</label>
				</div>
			</div>
		</div>';
		break;

		case 'checkbox':
		$html .= '<div class="field-row">
			<div class="field-sort">
				<i class="far fa-sort"></i>
			</div>
				<div class="field-type-title">Checkboxes</div>
				<div class="field-row-options">
					<a class="collapse-field-row" href="/" data-row-id="'.$field->field_id.'">
						<i class="fas fa-angle-down"></i></a> <a class="remove-field-row" data-row-current="1" data-row-id="'.$field->field_id.'" href="/">&times;</a>
				</div>
			</div>

		<div id="field-group-'.$field->field_id.'" class="field-group" data-row-id="'.$field->field_id.'">
			<div class="field-row">
				<div class="label-col">Field Label</div>
				<div class="field-col">
					<input id="label-'.$field->field_id.'" class="field-label" type="text" name="field_label['.$field->field_id.']" value="'.$field->field_label.'" required>
					<input type="hidden" name="field_type['.$field->field_id.']" value="checkbox">
				</div>
			</div>

			<div class="field-row">
				<div class="label-col">Field Name</div>
				<div class="field-col"><input id="name-'.$field->field_id.'" type="text" name="field_name['.$field->field_id.']" value="'.$field->field_name.'" required></div>
			</div>

			<div class="field-row">
				<div class="label-col">Checkboxes</div>
				<div class="field-col"><textarea name="field_options['.$field->field_id.']" placeholder="Label:value">'._explodeOptionsConfig($field->settings, 'checkbox').'</textarea><span class="notes">Enter each checkbox setup on a new line. Example:<br>Label:value<br>Label:value</span></div>
			</div>

			<div class="field-row">
				<div class="label-col">Required</div>
				<div class="field-col">
					<label><input type="radio" name="field_required['.$field->field_id.']" '.($field->required === 1? 'checked' : '').' value="1"> Yes</label>
					<label><input type="radio" name="field_required['.$field->field_id.']" '.($field->required === 0? 'checked' : '').' value="0"> No</label>
				</div>
			</div>
		</div>';
		break;

		case 'radio':
		$html .= '<div class="field-row">
			<div class="field-sort">
				<i class="far fa-sort"></i>
			</div>
				<div class="field-type-title">Radios</div>
				<div class="field-row-options">
					<a class="collapse-field-row" href="/" data-row-id="'.$field->field_id.'">
						<i class="fas fa-angle-down"></i></a> <a class="remove-field-row" data-row-current="1" data-row-id="'.$field->field_id.'" href="/">&times;</a>
				</div>
			</div>

		<div id="field-group-'.$field->field_id.'" class="field-group" data-row-id="'.$field->field_id.'">
			<div class="field-row">
				<div class="label-col">Field Label</div>
				<div class="field-col">
					<input id="label-'.$field->field_id.'" class="field-label" type="text" name="field_label['.$field->field_id.']" value="'.$field->field_label.'" required>
					<input type="hidden" name="field_type['.$field->field_id.']" value="radio">
				</div>
			</div>

			<div class="field-row">
				<div class="label-col">Field Name</div>
				<div class="field-col"><input id="name-'.$field->field_id.'" type="text" name="field_name['.$field->field_id.']" value="'.$field->field_name.'" required></div>
			</div>

			<div class="field-row">
				<div class="label-col">Radios</div>
				<div class="field-col"><textarea name="field_options['.$field->field_id.']" placeholder="Label:value">'._explodeOptionsConfig($field->settings, 'radio').'</textarea><span class="notes">Enter each radio setup on a new line. Example:<br>Label:value<br>Label:value</span></div>
			</div>

			<div class="field-row">
				<div class="label-col">Required</div>
				<div class="field-col">
					<label><input type="radio" name="field_required['.$field->field_id.']" '.($field->required === 1? 'checked' : '').' value="1"> Yes</label>
					<label><input type="radio" name="field_required['.$field->field_id.']" '.($field->required === 0? 'checked' : '').' value="0"> No</label>
				</div>
			</div>
		</div>';
		break;
	}

	$html .= '</div>';
	$html .= '</li>';

	return $html;

}

function _explodeConfig($config, $type)
{
	$string = '';

	if($type === 'select'){
		$options = json_decode($config);
		foreach($options as $option){
			$string .= $option->label.':'.$option->value."\n";
		}
	}

	if($type === 'radio'){
		$options = json_decode($config);
		foreach($options as $option){
			$string .= $option->label.':'.$option->value."\n";
		}
	}

	if($type === 'checkbox'){
		$options = json_decode($config);
		foreach($options as $option){
			$string .= $option->label.':'.$option->value."\n";
		}
	}

	$string = trim($string);

	return $string;
}

function _explodeOptionsConfig($settings, $type)
{
	$string = '';

	$settings = json_decode($settings);
	$options = $settings->options;

	if($type === 'select'){
		foreach($options as $option){
			$string .= $option->label.':'.$option->value."\n";
		}
	}

	if($type === 'radio'){
		foreach($options as $option){
			$string .= $option->label.':'.$option->value."\n";
		}
	}

	if($type === 'checkbox'){
		foreach($options as $option){
			$string .= $option->label.':'.$option->value."\n";
		}
	}

	$string = trim($string);

	return $string;
}

function _fieldsDropDown($fieldId, $fields)
{
	$html = '<div class="fields-drop-down">';
	$html .= '<span>Add Field <i class="fas fa-plus"></i></span>';
	$html .= '<ul>';
	foreach( $fields as $field ){
		$html .= '<li data-type="'.$field['type'].'" data-id="'.$fieldId.'">'.$field['label'].'</li>';
	};
	$html .= '</ul>';
	$html .= '</div>';
	return $html;
}

function _generateField($field, $objectType = false, $id = 0, $repeater = false, $isClone = false, $count = 0, $newName = false, $newFieldId = false)
{
	$html = '';
	$rpt = false;
	$origFieldId = $field->field_id;

	$nameAppend = '';
	if( $repeater && $id ){
		$nameAppend = '['.$field->field_id.']';
		$origFieldId = $id;
	}

	$fieldId = 'csf-field'.$field->field_id;
	$fieldName = '['.$origFieldId.']'.$nameAppend;

	if( $newFieldId ){
		$fieldId = 'csf-field'.$newFieldId;
	}

	if( $newName ){
		$fieldName = $newName;
		$rpt = true;
	}

	$newId = uniqid();

	switch($field->field_type){

		case 'text':
		case 'email':
		case 'date':

			$html .= '<div class="form-row">';

				$html .= '<label class="label-col" for="'.$fieldId.'">'.$field->field_label.'</label>';
				$html .= '<div class="input-col">';

				$html .= '<input id="'.$fieldId.'"
							type="'.$field->field_type.'"
							name="cfs'.$fieldName.'"
							value="'._getFieldValue($field, $objectType, $id, $rpt).'"
							'.($isClone? 'disabled' : '').'
							'.( $field->field_required && !$isClone ? 'required' : '' ).'
						>';

				$html .= '</div>';
			$html .= '</div>';

		break;

		case 'number':

			$html .= '<div class="form-row">';

				$html .= '<label class="label-col" for="'.$fieldId.'">'.$field->field_label.'</label>';
				$html .= '<div class="input-col">';

                $numberValue = _getFieldValue($field, $objectType, $id, $rpt);

				$html .= '<input id="'.$fieldId.'"
							type="number"

							name="cfs'.$fieldName.'"
							value="'.$numberValue.'"
							'.($isClone? 'disabled' : '').'
							'.( $field->field_required && !$isClone ? 'required' : '' ).'
						>';

				$html .= '</div>';
			$html .= '</div>';

		break;

		case 'decimal':

			$html .= '<div class="form-row">';

				$html .= '<label class="label-col" for="'.$fieldId.'">'.$field->field_label.'</label>';
				$html .= '<div class="input-col">';

				$html .= '<input id="'.$fieldId.'"
							type="number"
							step="any"
							name="cfs'.$fieldName.'"
							value="'._getFieldValue($field, $objectType, $id, $rpt).'"
							'.($isClone? 'disabled' : '').'
							'.( $field->field_required && !$isClone ? 'required' : '' ).'
						>';

				$html .= '</div>';
			$html .= '</div>';

		break;

		case 'file':
		case 'image':

			$html .= '<div class="form-row">';

			$accept = $field->allowed_filetypes === '*'? '' : 'accept="'.$field->allowed_filetypes.'"';

				$html .= '<label class="label-col" for="'.$fieldId.'">'.$field->field_label.'</label>';
				$html .= '<div class="input-col row">';

				$filename = _getFieldValue($field, $objectType, $id, $rpt);

				$html .= '<div class="input-group">
						  <span class="input-group-btn">
						    <a class="lfm-'.$field->field_type.'" data-lfm data-input="lfm-'.$newId.'" data-preview="lfm-preview-'.$newId.'">
						      <i class="fas fa-'.$field->field_type.'"></i> Choose '.$field->field_type.'
						    </a>
						  </span>
						  <input id="lfm-'.$newId.'" class="file-list-input" data-lfm-input value="'.$filename.'" type="text" name="cfs'.$fieldName.'" '.($isClone? 'disabled' : '').' '.( $field->field_required && !$isClone ? 'required' : '' ).'>
						</div>';

				if( $field->field_type === 'image' ){
    				$html .= '<div id="lfm-preview-'.$newId.'" data-lfm-holder class="lfm-image-preview">';
					$html .= strlen($filename)? '<a href="#" role="button" data-preview-id="lfm-preview-'.$newId.'" data-input-id="lfm-'.$newId.'" class="clear-lfm-image">&times;</a><img class="repeater-image-preview" src="'.$filename.'">' : '';
					$html .= '</div>';
				} else {
					$html .= strlen($filename)? '<a href="'.$filename.'" target="_blank">View '.$field->field_label.' file</a>' : '';
				}

				$html .= '</div>';
			$html .= '</div>';

		break;

		case 'textarea':

			$html .= '<div class="form-row">';

				$html .= '<label class="label-col align-top full-width" for="'.$fieldId.'">'.$field->field_label.'</label>';
				$html .= '<div class="input-col">';

				$html .= '<textarea id="'.$fieldId.'"
							type="'.$field->field_type.'"
							name="cfs'.$fieldName.'"

							'.($isClone? 'disabled' : '').'
							'.( $field->field_required && !$isClone ? 'required' : '' ).'
						>'._getFieldValue($field, $objectType, $id, $rpt).'</textarea>';

				$html .= '</div>';
			$html .= '</div>';

		break;

		case 'editor':

			$html .= '<div class="form-row">';

				$html .= '<label class="label-col align-top full-width" for="'.$fieldId.'">'.$field->field_label.'</label>';
				$html .= '<div class="input-col full-width">';

				$html .= '<textarea data-type="editor" class="cf-editor" id="'.$fieldId.'"
							name="cfs'.$fieldName.'"
							'.($isClone? 'disabled' : '').'
							'.( $field->field_required && !$isClone ? 'required' : '' ).'
						>'._getFieldValue($field, $objectType, $id, $rpt).'</textarea>';

				$html .= '</div>';
			$html .= '</div>';

		break;

		case 'select':

			$html .= '<div class="form-row">';

				$html .= '<label class="label-col" for="'.$fieldId.'">'.$field->field_label.'</label>';
				$html .= '<div class="input-col"><div class="select-wrapper">';
				$selectValueArr = (array) _getFieldValue($field, $objectType, $id, $rpt);

				$html .= '<select id="'.$fieldId.'"

							name="cfs'.$fieldName.'[]"

							'.($isClone? 'disabled' : '').'
							'.( $field->field_required && !$isClone ? 'required' : '' ).'
						>';
				if( $field->empty_first_option ){
					$html .= '<option value="">Choose &hellip;</option>';
				}
				$options = json_decode($field->field_config);
				foreach($options as $option){
					$html .= '<option value="'.$option->value.'" '.( in_array($option->value, $selectValueArr)? 'selected="selected"' : '' ).' >'.$option->label.'</option>';
				}

				$html .= '</select>';

				$html .= '</div></div>';
			$html .= '</div>';

		break;

		case 'radio':

			$html .= '<div class="form-row">';

				$html .= '<label class="label-col" for="'.$fieldId.'">'.$field->field_label.'</label>';
				$html .= '<div class="input-col radio-group">';

				$radioValue = _getFieldValue($field, $objectType, $id, $rpt);

				$radios = json_decode($field->field_config);
				foreach($radios as $radio){
					$html .= '<label><input type="radio" name="cfs'.$fieldName.'" value="'.$radio->value.'" '.( $radio->value === $radioValue? 'checked="checked"' : '' ).' '.($isClone? 'disabled' : '').' > '.$radio->label.'</label>';
				}

				$html .= '</select>';

				$html .= '</div>';
			$html .= '</div>';

		break;

		case 'checkbox':

			$html .= '<div class="form-row">';

				$html .= '<label class="label-col" for="'.$fieldId.'">'.$field->field_label.'</label>';
				$html .= '<div class="input-col radio-group">';

				$checkValue = (array) _getFieldValue($field, $objectType, $id, $rpt);

				$checkboxes = json_decode($field->field_config);
				foreach($checkboxes as $checkbox){
					$html .= '<label><input type="checkbox" name="cfs'.$fieldName.'[]" value="'.$checkbox->value.'" '.( in_array($checkbox->value, $checkValue)? 'checked="checked"' : '' ).' '.($isClone? 'disabled' : '').' > '.$checkbox->label.'</label>';
				}

				$html .= '</select>';

				$html .= '</div>';
			$html .= '</div>';

		break;

		case 'repeater' :

			$html .= '<div class="repeater-container">';
				$html .= '<div class="repeater-title-header">';
					$html .= '<h3>'.$field->field_label.'</h3>';
					$html .= '<input type="hidden" name="cfs['.$field->field_id.']" value="">';
					$html .= '<a class="add-repeater-row-btn" data-id="'.$field->field_id.'" href="/">Add Row <i class="fas fa-plus"></i></a>';
				$html .= '</div>';
				$html .= '<div id="repeater-fields-group'.$field->field_id.'" class="repeater-fields-group">';
					$html .= '<div id="repeater-set'.$field->field_id.'" class="repeater-fields-clone">';
						$html .= '<div class="repeater-fields-container">';
							$html .= '<div class="repeater-fields-group-header"><span class="repeater-sort-handle"><i class="fal fa-sort"></i></span> <span>Repeater Row</span> <a href="/" class="repeater-group-toggle open"><i class="fal fa-angle-down"></i></a> <a href="/" data-repeater-row-id="" class="delete-repeater-row">&times;</a></div>';
							$html .= '<div class="repeater-fields-group-row open">';
							$repeaters = DB::table('cf_fields')->where('repeater_id', $field->field_id)->orderBy('sort', 'asc')->get();
							$allRepeaterFieldsIds = [];
							$allRepeaterFieldsData = [];
							foreach($repeaters as $repeater){
								$repeater->content_id = null;
								$allRepeaterFieldsIds[$repeater->field_id] = $repeater->field_id;
								$allRepeaterFieldsData[$repeater->field_id] = [ 'field' => $repeater, 'repeater' => $field ];
								$html .= _generateField($repeater, false, $field->field_id, true, true);
							}
							$html .= '</div>';
						$html .= '</div>';
					$html .= '</div>';

					$batches = _getRepeaterFieldBatches($field->field_id, $objectType, $id);
					if( isset($batches[$field->field_id]) ){
						foreach( $batches[$field->field_id] as $key => $batch ){
							$html .= '<div class="repeater-fields-container">';
								$html .= '<div class="repeater-fields-group-header"><span class="repeater-sort-handle"><i class="fal fa-sort"></i></span> <span>Repeater Row</span> <a href="/" class="repeater-group-toggle"><i class="fal fa-angle-down"></i></a> <a href="/" data-repeater-row-id="'.$key.'" class="delete-repeater-row">&times;</a></div>';
								$html .= '<div class="repeater-fields-group-row">';
								foreach( $batch as $fld ){
									$name = '['.$fld['repeater_id'].']['.$fld['og_field']->field_id.']['.$fld['batch_id'].']['.$fld['content_id'].']';
									$newid = $fld['repeater_id'].$fld['og_field']->field_id.$fld['batch_id'].$fld['content_id'];
									$html .= _generateField($fld['og_field'], $objectType, $id, false, false, 0, $name, $newid);
									//dump($allRepeaterFieldsIds);
									unset($allRepeaterFieldsData[$fld['og_field']->field_id]);
								}
								//dump($allRepeaterFieldsIds);
								foreach( $allRepeaterFieldsData as $extraField ){
									$contentId = '_'.uniqId();
									$name = '['.$extraField['repeater']->field_id.']['.$extraField['field']->field_id.']['.$key.']['.$contentId.']';
									$newid = $extraField['repeater']->field_id.$extraField['field']->field_id.$key.$contentId;
									$html .= _generateField($extraField['field'], $objectType, $id, false, false, 0, $name, $newid);
								}
								$html .= '</div>';
							$html .= '</div>';
						}
					}

				$html .= '</div>';
			$html .= '</div>';

		break;

	}

	return $html;
}

function _getRepeaterFieldBatches($repeaterId, $objectType, $objectId)
{

	$groups = DB::table('cf_fields AS cf')
		->join('cf_object_data AS od', 'od.field_id', '=', 'cf.field_id')
		->where('od.parent_field_id', $repeaterId)
		->where('od.object_id', $objectId)
		->where('od.object_type', $objectType)
		->select('cf.*', 'od.*', 'cf.field_label AS field_label')
		->orderBy('cf.sort', 'asc')
		->orderBy('od.batch_sort', 'asc')
		->get();

	$batches = [];

	foreach( $groups as $group ){
		$ogField = DB::table('cf_fields')->where('field_id', $group->field_id)->first();
		$ogField->content_id = $group->content_id;

		$batches[$repeaterId][$group->batch_id][$group->content_id] = [
			'repeater_id' => $repeaterId,
			'batch_id' => $group->batch_id,
			'content_id' => $group->content_id,
			'field' => $group,
			'og_field' => $ogField
			];
	}

	return $batches;
}

function _getFieldValue($field, $objectType, $id, $repeater = false)
{
	if( !$objectType || !$id ){
		return '';
	}

	$where = [
		'field_id' => $field->field_id,
		'object_type' => $objectType,
		'object_id' => $id
	];

	if( $repeater ){
		unset($where['field_id']);
		$where['content_id'] = $field->content_id;
	}

	$data = DB::table('cf_object_data')
				->where($where)
				->first();
	$value = '';

	switch( $field->field_type ){
		case 'text' :
		case 'email' :
		case 'textarea' :
		case 'radio' :
		    $value = isset($data->field_text)? $data->field_text : '';
		break;
		case 'file' :
		    $value = isset($data->field_file)? $data->field_file : '';
		break;
		case 'image' :
			$value = isset($data->field_image)? $data->field_image : '';
		break;
		case 'select' :
			$value = isset($data->field_text)? explode(',', $data->field_text) : [];
		break;
		case 'checkbox' :
			$value = isset($data->field_text) && strlen($data->field_text)? explode(',', $data->field_text) : [];
		break;
		case 'date' :
			$value = isset($data->field_date)? $data->field_date : '';
        break;
		case 'number' :
			$value = isset($data->field_number)? $data->field_number : '';
			break;
		case 'decimal' :
			$value = isset($data->field_decimal)? $data->field_decimal : '';
            break;
		case 'editor' :
			$value = isset($data->field_editor)? $data->field_editor : '';
			break;
	}

	return $value;
}

function _translateStatus($s)
{
	$status = '';

	switch($s){
		case 'P':
			$status = 'Published';
		break;

		case 'D':
		 	$status = 'Draft';
		break;
	}

	return $status;
}

function _getPageList($id = 0, $parentId = 0)
{
	$html = '';
	$pages = DB::table('pages')->where('status', 'P')->where('id', '<>', $id)->whereNull('deleted_at')->orderBy('title', 'asc')->get();
	foreach( $pages as $page ){
		$html .= '<option value="'.$page->id.'" '.( $page->id === $parentId? 'selected="selected"' : '' ).'>'.$page->title.'</option>';
	}

	return $html;
}

function _getEntryTypes($all = false)
{
	if( $all ){
		$entryTypes = DB::table('entry_types')->orderBy('entry_type', 'asc')->get();
	} else {
		$entryTypes = DB::table('entry_types')->where('slug' ,'<>', 'post')->orderBy('entry_type', 'asc')->get();
	}
	return $entryTypes;
}

function _getTaxonomyTypes($all = false)
{
	if( $all ){
		$types = DB::table('taxonomy_types')->orderBy('title', 'asc')->get();
	} else {
		$types = DB::table('taxonomy_types')->where('slug' ,'<>', 'category')->orderBy('title', 'asc')->get();
	}
	return $types;
}

function _getTaxonomyGroups($entryType = '')
{
	$types = DB::table('taxonomy_types')->orderBy('title', 'asc')->get();

	$c = 0;
	foreach( $types as $type ){
		$collection = collect(explode(',',$type->show_on));
		if( !$collection->contains($entryType) ){
			unset($types[$c]);
		} else {
    		$types[$c]->terms = DB::table('taxonomies')->where('taxonomy_type_id', $type->id)->orderBy('title', 'asc')->get();
		}
		$c++;
	}

	return $types;
}

function _buildMenuList($items, $level = 0){
	$html = '';
	if( $level > 0 ) { $html = '<ul class="nested">'; }
	foreach( $items as $index => $item ){
		$html .= '<li data-type="'.$item['parameters'].'" data-target="'.$item['target'].'" data-title="'.$item['title'].'" data-url="'.$item['url'].'">'.$item['title'];
			$html .= '<div class="menu-url">URL: '.$item['url'].'</div>';
			$html .= '<a class="remove-menu-item" href="/">&times;</a>';
			if( isset($item['children']) ){
				$level++;
				$html .= _buildMenuList($item['children'], $level);
			} else{
				$html .= '<ul class="nested"></ul>';
			}
		$html .='</li>';
	}
	if( $level > 0 ) { $html .= '</ul>'; }

	return $html;
}

function _getCommentCount(){
	$comments = DB::table('comments')->where('approved', 0)->orderBy('created_at', 'desc')->get();
	return count($comments);
}

function pluralTitle($string){
	return str_plural( ucwords(str_replace('-',' ',$string)) );
}

function _getMenuSlot($num = 0){
    $menuItems = config('neutrino.admin_menu_items', []);
    $arrs = [];
    foreach( $menuItems as $key => $menu ){
        if( $menu['slot'] === $num ){
            $arrs[] = $menuItems[$key];
        }
    }

    return $arrs;
}

function isRouteGroup($group){
    $routeName = Route::currentRouteName();
    return strpos($routeName, $group);
}

function getBlocks(){
    $Blocks = new Newelement\Neutrino\Http\Controllers\BlocksController;
    $blocks = $Blocks->getBlocks();
    return $blocks;
}
