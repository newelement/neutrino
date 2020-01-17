@extends('neutrino::admin.template.header-footer')
@section('title', 'Create Form | ')
@section('content')
<form action="/admin/forms" method="post">
	<div class="container">
		<div class="content">
			<h2>Create Form</h2>
			@csrf

			<p>
				After you create the form you can add fields.
			</p>

			<div class="form-row">
				<label class="label-col" for="title">Title</label>
				<div class="input-col">
					<input id="title" class="to-slug" type="text" name="title" value="{{ old('title') }}" required>
				</div>
			</div>

			<div class="form-row">
				<label class="label-col" for="slug">Slug</label>
				<div class="input-col">
					<input id="slug" class="slug-input" type="text" name="slug" value="{{ old('slug') }}" required>
				</div>
			</div>

			<div class="form-row">
				<label class="label-col" for="content">Description</label>
				<div class="input-col">
					<textarea id="content" type="text" name="content">{{ old('content') }}</textarea>
				</div>
			</div>

			<div class="form-row">
				<label class="label-col" for="subject">Email Subject</label>
				<div class="input-col">
					<input id="subject" type="text" name="subject" value="{{ old('subject') }}" required>
				</div>
			</div>

			<div class="form-row">
				<label class="label-col" for="email-to">Email To</label>
				<div class="input-col">
					<input id="email-to" type="text" name="email_to" value="{{ old('email_to') }}" required>
				</div>
				<div class="input-notes">
				    <span class="note">Comma separated list</span>
				</div>
			</div>

			<div class="form-row">
				<label class="label-col" for="email-from">Email From</label>
				<div class="input-col">
					<input id="email-from" type="email" name="email_from" value="{{ old('email_from') }}">
				</div>
			</div>

			<div class="form-row">
				<label class="label-col" for="email-cc">Email CC</label>
				<div class="input-col">
					<input id="email-cc" type="text" name="email_cc" value="{{ old('email_cc') }}">
				</div>
				<div class="input-notes">
				    <span class="note">Comma separated list</span>
				</div>
			</div>


			<div class="form-row">
				<label class="label-col" for="email-bcc">Email BCC</label>
				<div class="input-col">
					<input id="email-bcc" type="text" name="email_bcc" value="{{ old('email_bcc') }}">
				</div>
				<div class="input-notes">
				    <span class="note">Comma separated list</span>
				</div>
			</div>
			
			<div class="form-row">
				<label class="label-col" for="form-style">Form Style</label>
				<div class="input-col">
    				<div class="select-wrapper">
    					<select id="form-style" name="form_style">
        					<option value="stacked" {{ old('form_style') === 'stacked'? 'selected="selected"' : '' }}>Stacked</option>
        					<option value="horizontal" {{ old('form_style') === 'horizontal'? 'selected="selected"' : '' }}>Horizontal</option>
    					</select>
    				</div>
				</div>
				<div class="input-notes">
				    <span class="note">Do you want the labels stacked on top of the inputs or beside the inputs?</span>
				</div>
			</div>
			
			<div class="form-row">
				<label class="label-col" for="button-label">Submit Button Label</label>
				<div class="input-col">
					<input id="button-label" type="text" name="submit_button_label" value="{{ old('submit_button_label', 'Submit') }}" required>
				</div>
			</div>
			
			<div class="form-row">
				<label class="label-col" for="button-size">Submit Button Size</label>
				<div class="input-col">
    				<div class="select-wrapper">
    					<select id="button_size" name="submit_button_size">
        					<option value="normal" {{ old('submit_button_size') === 'normal'? 'selected="selected"' : '' }} >Normal</option>
        					<option value="full_width" {{ old('submit_button_size') === 'full_width'? 'selected="selected"' : '' }}>Full Width</option>
    					</select>
    				</div>
				</div>
			</div>

			<div class="form-row">
				<label class="label-col" for="status">Status</label>
				<div class="input-col">
    				<div class="select-wrapper">
    					<select id="status" name="status">
    						<option value="A" {{ old('email_bcc') === 'A'? 'selected="selected"' : '' }} >Active</option>
    						<option value="I" {{ old('email_bcc') === 'I'? 'selected="selected"' : '' }} >Inactive</option>
    					</select>
    				</div>
				</div>
			</div>


		</div>

		<aside class="sidebar">
			<div class="side-fields">
				<button type="submit" class="btn full text-center">Save Form</button>
			</div>
		</aside>

	</div>
</form>
@endsection
