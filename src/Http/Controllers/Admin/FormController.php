<?php
namespace Newelement\Neutrino\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Newelement\Neutrino\Facades\Neutrino;
use Newelement\Neutrino\Models\Form;
use Newelement\Neutrino\Models\FormField;

class FormController extends Controller
{

	public function index()
	{
		$forms = Form::orderBy('title')->paginate(20);
		return view('neutrino::admin.forms.index', ['forms' => $forms]);
	}

	public function getCreate()
	{
		return view('neutrino::admin.forms.create');
	}

	public function create(Request $request)
	{
		$validatedData = $request->validate([
	   		'title' => 'required|max:255',
	   		'slug' => 'required',
			'subject' => 'required',
			'email_to' => 'required'
   		]);

		$form = new Form;
		$form->title = $request->title;
		$form->slug = $request->slug;
		$form->content = $request->content;
		$form->subject = $request->subject;
		$form->email_to = $request->email_to;
		$form->email_from = $request->email_from;
		$form->email_cc = $request->email_cc;
		$form->email_bcc = $request->email_bcc;
		$form->status = $request->status;
		$form->form_style = $request->form_style;
		$form->submit_button_label = $request->submit_button_label;
		$form->submit_button_size = $request->submit_button_size;
		$form->save();

		return redirect('/admin/forms/'.$form->id.'/fields')->with('success', 'Form created.');
	}

	public function get($id)
	{
		$form = Form::find($id);
		return view('neutrino::admin.forms.edit', ['form' => $form]);
	}

	public function update(Request $request, $id)
	{
		$validatedData = $request->validate([
	   		'title' => 'required|max:255',
	   		'slug' => 'required',
			'subject' => 'required',
			'email_to' => 'required'
   		]);

		$form = Form::find($id);
		$form->title = $request->title;
		$form->slug = $request->slug;
		$form->content = $request->content;
		$form->subject = $request->subject;
		$form->email_to = $request->email_to;
		$form->email_from = $request->email_from;
		$form->email_cc = $request->email_cc;
		$form->email_bcc = $request->email_bcc;
		$form->status = $request->status;
		$form->form_style = $request->form_style;
		$form->submit_button_label = $request->submit_button_label;
		$form->submit_button_size = $request->submit_button_size;
		$form->save();

		return redirect('/admin/forms')->with('success', 'Form updated.');
	}

	public function getCreateFields($id)
	{
		$form = Form::find($id);
		$fields = FormField::where('form_id', $id)->orderBy('sort', 'asc')->get();
		return view('neutrino::admin.forms.edit-fields', ['form' => $form, 'fields' => $fields]);
	}

	public function createFields(Request $request, $id)
	{
		$form = Form::find($id);

		$i = 0;
		foreach( (array) $request->field_type as $key => $value ){

			$settings = [];
			$settings['multiple_files'] = isset($request->field_multiple[$key]) && $request->field_multiple[$key] === '1' ? 1 : 0;
			$settings['allowed_filetypes'] = isset($request->field_filetypes[$key])? $request->field_filetypes[$key] : '*' ;
			$settings['placeholder'] = isset($request->field_placeholder[$key])? $request->field_placeholder[$key] : '' ;
			$settings['empty_first_option'] = isset($request->field_firstoption[$key]) && (int) $request->field_firstoption[$key] === 1 ? 1 : 0;
			$settings['options'] = isset($request->field_options[$key])? $this->parseFieldOptions($request->field_options[$key], $request->field_type[$key]) : [];

			FormField::updateOrCreate(['field_id' => $key],
			[
				'form_id' => $form->id,
				'field_type' => $request->field_type[$key],
				'field_label' => $request->field_label[$key],
				'field_name' => $request->field_name[$key],
				'required' => (isset($request->field_required[$key]) && (int) $request->field_required[$key] === 1) ? 1 : 0,
				'select_multiple' => (isset($request->field_select_multiple[$key]) && (int) $request->field_select_multiple[$key] === 1) ? 1 : 0,
				'settings' => json_encode($settings),
				'sort' => $i
			]);
			$i++;

		}

		if( $request->ajax() ){
    		return response()->json(['sorted' => true]);
		}

		return redirect('/admin/forms/'.$id.'/fields')->with('success', 'Fields updated.');
	}

	private function parseFieldOptions($options, $fieldType)
	{
		$fieldOptions = trim($options);

		$options = [];

		if( $fieldType === 'select' ){
			$lines = preg_split('/\r\n|[\r\n]/', $fieldOptions);
			foreach( $lines as $line ){
				$option = explode(':', $line);
				$options[] = [ 'label' => trim($option[0]), 'value' => trim(isset($option[1])? $option[1] : $option[0] ) ];
			}
		}

		if( $fieldType === 'radio' ){
			$lines = preg_split('/\r\n|[\r\n]/', $fieldOptions);
			foreach( $lines as $line ){
				$option = explode(':', $line);
				$options[] = [ 'label' => trim($option[0]), 'value' => trim(isset($option[1])? $option[1] : $option[0] ) ];
			}
		}

		if( $fieldType === 'checkbox' ){
			$lines = preg_split('/\r\n|[\r\n]/', $fieldOptions);
			foreach( $lines as $line ){
				$option = explode(':', $line);
				$options[] = [ 'label' => trim($option[0]), 'value' => trim(isset($option[1])? $option[1] : $option[0] ) ];
			}
		}

		return $options;
	}

	public function delete(Request $request, $id)
	{
		Form::find($id)->delete();
		return redirect('/admin/forms')->with('success', 'Form deleted.');
	}

	public function deleteField(Request $request)
	{
		$validatedData = $request->validate([
	   		'id' => 'required',
   		]);

		$id = $request->id;

		FormField::where('field_id', $id)->delete();

		return response()->json(['success' => true]);
	}
}
