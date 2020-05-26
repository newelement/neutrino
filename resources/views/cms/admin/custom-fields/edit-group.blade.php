@extends('neutrino::admin.template.header-footer')
@section('title', 'Edit Custom Field Group | ')
@section('content')
<form action="/admin/custom-fields/group/{{$group->id}}" method="post">
	<div class="container">
		<div class="content">
			<h2>Edit Field Group</h2>

			@csrf
			<input type="hidden" id="group-id" value="{{ $group->id }}">

			<div class="form-row">
				<label class="label-col" for="title">Group Title</label>
				<div class="input-col">
					<input id="title" type="text" name="title" value="{{ $group->title }}" required>
				</div>
			</div>

			<div class="form-row">
				<label class="label-col" for="description">Description</label>
				<div class="input-col">
					<textarea id="description" name="description">{{ $group->description }}</textarea>
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

			<h4>Showing this field group on:</h4>
			<ul class="rules-list">
				@foreach( $rules as $rule )
				@php
				$spec = '';
				if( $rule->rule_category_specific === '*' ){
					$spec = 'All';
				} else {
					$spec = $rule->title;
				}
				if( $rule->rule_category_type === 'null' ) {
					$rule->rule_category_type = '--';
				}
				@endphp
				<li id="ruleid-{{ $rule->id }}">
					<div>
						{{ $rule->rule_category }}
					</div>
					<div>
						{{ $rule->rule_category_type }}
					</div>
					<div>
						{{ $spec }}
					</div>
					<div>
						<a class="delete-custom-field-rule" data-id="{{ $rule->id }}" href="">&minus;</a>
					</div>
				</li>
				@endforeach
			</ul>

		</div>

		<aside class="sidebar">
			<button type="submit" class="btn full">Save Group</button>
		</aside>
	</div>
</form>
@endsection
