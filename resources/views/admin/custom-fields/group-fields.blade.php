@extends('neutrino::admin.template.header-footer')
@section('title', 'Custom Field Group | ')
@section('content')
<form id="group-fields" action="/admin/custom-fields/group/{{$group->id}}/fields" method="post">
	@csrf
	<input type="hidden" id="group-id" value="{{ $group->id }}">
	<div class="container">
		<div class="content">
			<h2>Field Group: {{ $group->title }}</h2>

			<h3>Fields</h3>

			<ul class="fields-list">
				@foreach( $fields as $field )
				{!! _parseCustomField($field) !!}
				@endforeach
			</ul>

		</div>

		<aside class="sidebar">

			<div class="" style="position: sticky; top: 56px;">
				<div class="main-fields-dropdown">
					<div class="fields-drop-down">
						<span>Add Field <i class="fas fa-plus"></i></span>
						<ul>
							<li class="field-type" data-type="text">Text</li>
							<li class="field-type" data-type="checkbox">Checkbox</li>
							<li class="field-type" data-type="radio">Radio</li>
							<li class="field-type" data-type="textarea">Multi-line Text</li>
							<li class="field-type" data-type="email">Email</li>
							<li class="field-type" data-type="date">Date</li>
							<li class="field-type" data-type="select">Dropdown</li>
							<li class="field-type" data-type="number">Number</li>
							<li class="field-type" data-type="decimal">Decimal</li>
							<li class="field-type" data-type="file">File</li>
							<li class="field-type" data-type="image">Image</li>
							<li class="field-type" data-type="editor">Rich Text Editor</li>
							<li class="field-type" data-type="repeater">Repeater</li>
						</ul>
					</div>
				</div>

				<button type="submit" class="btn full">Save Fields</button>

			</div>

		</aside>
	</div>
</form>
@endsection
