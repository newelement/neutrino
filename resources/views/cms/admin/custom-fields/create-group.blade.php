@extends('neutrino::admin.template.header-footer')
@section('title', 'Create Custom Field Group | ')
@section('content')
<form action="/admin/custom-field-groups" method="post">
	<div class="container">
		<div class="content">
			<h2>Create Field Group</h2>

			<p>
				After the group is created you can add the fields.
			</p>

			@csrf
			<div class="form-row">
				<label class="label-col" for="title">Group Title</label>
				<div class="input-col">
					<input id="title" type="text" name="title" autocomplete="off" value="{{ old('title') }}" required>
				</div>
			</div>

			<div class="form-row">
				<label class="label-col" for="description">Description</label>
				<div class="input-col">
					<textarea id="description" name="description">{{ old('description') }}</textarea>
				</div>
			</div>

			<h3>Rules</h3>

			<div class="set-rule-row">
				<div class="rule-category">
					<select class="rule-category-select">
						<option value="">Show on &hellip;</option>
						<option value="entries">Entries</option>
                        <option value="entry_type">Entry Type</option>
						<option value="pages">Pages</option>
						<option value="taxonomy">Taxonomy</option>
						@if( shoppeExists() )
						<option value="products">Products</option>
						@endif
					</select>
				</div>
				<div class="rule-category-type">
					<select class="rule-category-type-select" disabled>
					</select>
				</div>
				<div class="rule-category-specific">
					<select class="rule-category-specific-select" disabled>
					</select>
				</div>
				<div class="add-rule">
					<button type="button" class="add-rule-btn" disabled>&plus;</button>
				</div>
			</div>

			<h4>Show this field group on:</h4>
			<ul class="rules-list">
			</ul>

		</div>

		<aside class="sidebar">
			<button type="submit" class="btn full">Create Group</button>
		</aside>
	</div>
</form>
@endsection
