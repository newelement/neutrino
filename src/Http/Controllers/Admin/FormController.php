<?php
namespace Newelement\Neutrino\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Newelement\Neutrino\Facades\Neutrino;
use Newelement\Neutrino\Models\Form;
use Newelement\Neutrino\Models\FormField;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Crypt;

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
        $form->private = $request->boolean('private');
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
        $form->private = $request->boolean('private');
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
        //dd($request->all());
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
				'field_label' => isset( $request->field_label[$key])? $request->field_label[$key] : 'NA' ,
				'field_name' => isset( $request->field_name[$key] )? $request->field_name[$key] : 'NA' ,
                'descriptive_text' => isset( $request->field_text[$key] )? $request->field_text[$key] : null ,
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

    public function getSubmissions(Request $request, $id)
    {
        $form = Form::findOrFail($id);

        $data = $form;
        $data->submissions = $form->submissions()->paginate(30);

        return view('neutrino::admin.forms.submissions', ['data' => $data]);
    }

    public function getPrivateFile(Request $request)
    {
        $fileName = $request->file;

        $file = base_path().'/storage/app/'.$fileName;

        return response()->download($file);
    }

    public function exportForm(Request $request, $id)
    {
        $form = Form::findOrFail($id);
        $submissions = $form->submissions()->get();
        $filename = 'form-'.$id.'-export-'.date("Y-m-d").'.csv';

        $headers = array(
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=".$filename,
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        );

        $columns = [];
        $rows = [];

        $i = 0;
        foreach( $submissions as $submission ){
            $fields = json_decode( $submission->fields, true );
            unset($fields['_token']);
            unset($fields['form_id']);
            unset($fields['valid_from']);
            unset($fields['submit']);
            unset($fields['q']);
            unset($fields['gr_score']);
            unset($fields['recaptcha_challenge_field']);
            unset($fields['recaptcha_response_field']);

            foreach($fields as $key => $value){
                if (strpos($key, 'my_name_') === 0){
                  unset($fields[$key]);
                }
            }

            foreach( $fields as $key => $value ){
                $columns[$this->cleanKey($key)] = $this->cleanKey($key);
                $rows[$i][$this->cleanKey($key)] = is_array($value)? implode(' | ', $value) : $value;
            }

            $columns['submission_date'] = 'submission_date';
            $rows[$i]['submission_date'] = $submission->created_at->format('Y-m-d H:i');

            $i++;
        }

        $columns = array_values($columns);

        $callback = function() use ($rows, $columns)
        {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach($rows as $row) {
                fputcsv($file, array_values($row) );
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function cleanKey($string)
    {
        $string = strtolower($string);
        $string = preg_replace("/[^A-Za-z0-9 ]/", '', $string);
        $string = htmlentities($string);
        $string = str_replace('"', '', $string);
        $string = str_replace('\'', '', $string);
        $string = str_replace('\\', '', $string);
        $string = str_replace('/', '', $string);
        return $string;
    }
}
