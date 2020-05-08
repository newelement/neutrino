@extends('neutrino::admin.template.header-footer')
@section('title', 'Edit Form Fields | ')
@section('content')
<form action="/admin/forms/{{$form->id}}/fields" id="form-fields" method="post">
	<div class="container">
		<div class="content">
			<h2>Form: {{ $form->title }} <a class="headline-btn" href="/admin/forms/{{$form->id}}" role="button">Edit form settings</a></h2>

			<h3>Fields</h3>
			@csrf
			<input type="hidden" id="form-id" value="{{ $form->id }}">
			<ul class="form-fields-list">
				@foreach( $fields as $field )
				{!! _parseField($field) !!}
				@endforeach
			</ul>

		</div>

		<aside class="sidebar">
			<div class="side-fields">

				<div class="main-fields-dropdown">
					<div class="form-fields-drop-down">
						<span>Add Field <i class="fas fa-plus"></i></span>
						<ul>
							<li class="form-field-type" data-type="text">Text</li>
							<li class="form-field-type" data-type="checkbox">Checkbox</li>
							<li class="form-field-type" data-type="radio">Radio</li>
							<li class="form-field-type" data-type="textarea">Multi-line Text</li>
							<li class="form-field-type" data-type="email">Email</li>
							<li class="form-field-type" data-type="date">Date</li>
							<li class="form-field-type" data-type="select">Dropdown</li>
							<li class="form-field-type" data-type="file">File Upload</li>
							<li class="form-field-type" data-type="image">Image Upload</li>
						</ul>
					</div>
				</div>

				<button type="submit" class="btn full text-center">Save Form Fields</button>
			</div>
		</aside>

	</div>
</form>
@endsection
