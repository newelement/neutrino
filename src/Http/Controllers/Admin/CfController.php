<?php
namespace Newelement\Neutrino\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Newelement\Neutrino\Facades\Neutrino;
use Newelement\Neutrino\Models\CfGroups;
use Newelement\Neutrino\Models\CfFields;
use Newelement\Neutrino\Models\CfRule;
use Newelement\Neutrino\Models\EntryType;
use Newelement\Neutrino\Models\TaxonomyType;
use Newelement\Neutrino\Models\Entry;
use Newelement\Neutrino\Models\Page;
use Newelement\Neutrino\Models\CfObjectData;

class CfController extends Controller
{

	public function __construct(){}

	public function index()
	{

		$customGroups = CfGroups::paginate(20);

		return view('neutrino::admin.custom-fields.index', ['custom_groups' => $customGroups]);
	}

	public function getCreateGroup()
	{
		return view('neutrino::admin.custom-fields.create-group');
	}

	public function createGroup(Request $request)
	{

		$validatedData = $request->validate([
			'title' => 'required|max:255',
		]);

		$group = new CfGroups;
		$group->title = $request->title;
		$group->description = $request->description;
		$group->sort = 0;
		$group->save();

		if( $request->rules ){
			foreach( $request->rules as $rule ){

				$ar = explode(':', $rule);
				$rule_category = $ar[0];
				$rule_category_type = $ar[1];
				$rule_category_specific = $ar[2];

				$title = '';
				$object_id = 0;

				if( $rule_category_specific !== '*' ){
					$arr = explode('|', $rule_category_specific);
					$title = $arr[0];
					$object_id = (int) $arr[1];
				}

				$cfRule = new CfRule;
				$cfRule->group_id = $group->id;
				$cfRule->rule_category = $rule_category;
				$cfRule->rule_category_type = $rule_category_type;
				$cfRule->rule_category_specific = $rule_category_specific;
				$cfRule->title = $title;
				$cfRule->object_id = $object_id;
				$cfRule->save();

			}
		}

		return redirect('/admin/custom-fields/group/'.$group->id.'/fields')->with('success', 'Field group created.');
	}

	public function getGroup(Request $request, $id)
	{
		$group = CfGroups::find($id);
		$rules = CfRule::where('group_id', $id)->get();
		return view('neutrino::admin.custom-fields.edit-group', ['group' => $group, 'rules' => $rules]);
	}

	public function updateGroup(Request $request, $id)
	{
		$group = CfGroups::find($id);
		$group->title = $request->title;
		$group->description = $request->description;
		$group->save();

		return redirect('/admin/custom-fields/group/'.$group->id)->with('success', 'Field group updated.');
	}

	public function deleteGroup($id)
	{
		$group = CfGroups::find($id);

		$fields = CfFields::where('group_id', $group->id)->get();
		foreach( $fields as $field ){
			CfObjectData::where('field_id', $field->id)->delete();
			CfFields::where('id', $field->id)->delete();
		}

		$group->delete();
		return redirect('/admin/custom-fields')->with('success', 'Field group deleted.');
	}

	public function getGroupFields($id)
	{
		$group = CfGroups::find($id);
		$fields = CfFields::where('group_id', $id)->whereNull('repeater_id')->orderBy('sort', 'asc')->get();
		return view('neutrino::admin.custom-fields.group-fields', ['fields' => $fields, 'group' => $group]);
	}

	public function createGroupRule(Request $request)
	{
		$group_id = $request->group_id;
		$rule_category = $request->rule_category;
		$rule_category_type = $request->rule_category_type;
		$rule_category_specific = $request->rule_category_specific;
		$title = '';
		$object_id = 0;

		if( $rule_category_specific !== '*' ){
			$arr = explode('|', $rule_category_specific);
			$title = $arr[0];
			$object_id = (int) $arr[1];
		}

		$cfRule = new CfRule;
		$cfRule->group_id = $group_id;
		$cfRule->rule_category = $rule_category;
		$cfRule->rule_category_type = $rule_category_type;
		$cfRule->rule_category_specific = $rule_category_specific;
		$cfRule->title = $title;
		$cfRule->object_id = $object_id;
		$cfRule->save();

		return response()->json(['id' => $cfRule->id]);
	}

	public function deleteGroupRule(Request $request)
	{
		$id = $request->id;
		CfRule::find($id)->delete();

		return response()->json(['success' => true]);
	}

	public function createGroupFields(Request $request, $id)
	{
		$group = CfGroups::find($id);
		$i = 0;
		$r = 0;
		foreach( $request->field_type as $key => $value ){

				CfFields::updateOrCreate(['field_id' => $key],
				[
					'group_id' => $group->id,
					'field_type' => $request->field_type[$key],
					'field_label' => $request->field_label[$key],
					'field_name' => $request->field_name[$key],
					'field_required' => (isset($request->field_required[$key]) && (int) $request->field_required[$key] === 1) ? 1 : 0,
					'multiple_files' => ( isset($request->field_multiple[$key]) && (int) $request->field_multiple[$key]) === 1 ? 1 : 0,
					'allowed_filetypes' => isset($request->field_filetypes[$key])? $request->field_filetypes[$key] : '*' ,
					'empty_first_option' => (isset($request->field_firstoption[$key]) && (int) $request->field_firstoption[$key] === 1) ? 1 : 0,
					'field_config' => isset($request->field_options[$key])? $this->_parseFieldOptions($request->field_options[$key], $request->field_type[$key]) : json_encode([]) ,
					'repeater_id' => isset($request->field_repeater[$key])? $request->field_repeater[$key] : null,
					'sort' => $i
				]);

				$i++;

		}

		if( $request->ajax() ){
			return response()->json(['success' => true]);
		} else {
			return redirect('/admin/custom-fields/group/'.$group->id.'/fields')->with('success', 'Field group updated.');
		}
	}

	public function getEntryTypes()
	{
		$entryTypes = EntryType::orderBy('entry_type', 'asc')->get();
		return response()->json(['entry_types' => $entryTypes]);
	}

	public function getPages()
	{
		$pages = Page::orderBy('title', 'asc')->get();
		return response()->json(['pages' => $pages]);
	}

	public function getTaxonomyTypes()
	{
		$taxTypes = TaxonomyType::orderBy('title', 'asc')->get();
		return response()->json(['taxonomy_types' => $taxTypes]);
	}

	public function getEntryType(Request $request)
	{
		$entryType = $request->entry_type;
		$entryTypes = Entry::where('entry_type', $entryType)->orderBy('title', 'asc')->get();
		return response()->json(['entry_types' => $entryTypes]);
	}

	private function _parseFieldOptions($fieldOptions, $fieldType)
	{
		$fieldOptions = trim($fieldOptions);

		$options = [];

		if( $fieldType === 'select' ){
			$lines = preg_split('/\r\n|[\r\n]/', $fieldOptions);
			foreach( $lines as $line ){
				$option = explode(':', $line);
				$options[] = [ 'label' => trim($option[0]), 'value' => trim($option[1]) ];
			}
		}

		if( $fieldType === 'radio' ){
			$lines = preg_split('/\r\n|[\r\n]/', $fieldOptions);
			foreach( $lines as $line ){
				$option = explode(':', $line);
				$options[] = [ 'label' => trim($option[0]), 'value' => trim($option[1]) ];
			}
		}

		if( $fieldType === 'checkbox' ){
			$lines = preg_split('/\r\n|[\r\n]/', $fieldOptions);
			foreach( $lines as $line ){
				$option = explode(':', $line);
				$options[] = [ 'label' => trim($option[0]), 'value' => trim($option[1]) ];
			}
		}

		return json_encode($options);
	}

	public function deleteRepeaterGroup($id)
	{
		CfObjectData::where('batch_id', $id)->delete();

		return response()->json(['success' => true]);
	}

	public function deleteField(Request $request)
	{
		$id = $request->id;
		CfFields::where('field_id', $id)->delete();

		return response()->json(['success' => true]);
	}

	private function createJsonConfig($fieldType, $key, $request)
	{
		return null;
	}

}
