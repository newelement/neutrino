@extends('neutrino::admin.template.header-footer')
@section('title', 'Taxonomies | ')
@section('content')
	<div class="container">
		<div class="content">
			<h2>Taxonomies</h2>

			<table cellpadding="0" cellspacing="0" class="table">
				<thead>
					<tr>
						<th class="text-left">Taxonomy</th>
						<th>Slug</th>
						<th>Hierarchical</th>
						<th>Edit Terms</th>
						<th width="80"></th>
					</tr>
				</thead>
				<tbody>
				@foreach( $taxonomies as $tax )
					<tr>
						<td data-label="Title">
							<a href="/admin/taxonomy-types/{{ $tax->id }}">{{ $tax->title }}</a>
						</td>
						<td data-label="Slug" class="text-center">
							{{ $tax->slug }}
						</td>
						<td data-label="Hierarchical" class="text-center">
							{{ $tax->hierarchical? 'Yes' : 'No' }}
						</td>
						<td data-label="Edit Terms" class="text-center">
							<a href="/admin/taxonomies/{{$tax->id}}">Edit Terms</a>
						</td>
						<td data-label="Delete" class="text-center">
							@if( $tax->slug !== 'category' )
							<form action="/admin/taxonomy-types/{{$tax->id}}" method="post">
								@csrf
								@method('delete')
								<button type="submit" class="delete-btn-taxonomy">&times;</button>
							</form>
							@endif
						</td>
					</tr>
				@endforeach
				</tbody>
			</table>

			<div class="pagination-links">
				{{ $taxonomies->links() }}
			</div>
		</div>

		<aside class="sidebar">
			@if( !$edit )
			<h3>Create Taxonomy</h3>
			<form action="/admin/taxonomy-types" method="post">
			@else
			<h3>Edit Taxonomy</h3>
			<form id="object-form" action="/admin/taxonomy-types/{{$edit_taxonomy->id}}" method="post">
			@endif
				@csrf
				<div class="side-fields">
					<div class="form-row">
						<label class="label-col" for="title">Taxonomy Name</label>
						<div class="input-col">
							<input id="title" type="text" name="title" value="{{ old('title', $edit_taxonomy->title) }}">
						</div>
					</div>

					<div class="form-row">
						<label class="label-col" for="desc">Description</label>
						<div class="input-col">
							<textarea id="desc" name="description" style="height: 100px;">{{ old('description', $edit_taxonomy->description) }}</textarea>
						</div>
					</div>
					
					<div class="form-row">
						<label class="label-col" for="status">Hierarchical</label>
						<div class="input-col">
							<label><input type="checkbox" name="hierarchical" value="1" {{ $edit_taxonomy->hierarchical? 'checked="checked"' : '' }}> Yes</label>
						</div>
					</div>

					<div class="form-row">
						<label class="label-col" for="desc">Display On Entry Types</label>
						<div class="input-col">
							<select name="show_on[]" style="height: 100px;" multiple>
								@foreach($entry_types as $entry_type)
								<option value="{{ $entry_type->slug }}" {{ $edit_taxonomy->show_on->contains($entry_type->slug)? 'selected="selected"' : '' }}>{{ $entry_type->entry_type }}</option>
								@endforeach
								<option value="events" {{ $edit_taxonomy->show_on->contains('events')? 'selected="selected"' : '' }}">Events</option>
							    @if( _shoppeExists() )	
								<option value="products" {{ $edit_taxonomy->show_on->contains('products')? 'selected="selected"' : '' }}">Products</option>
                                @endif
							</select>
							<span class="note">Ctrl/Command+click to select/unselect</span>
						</div>
					</div>

					@if( !$edit )
					<button type="submit" class="btn full">Create Taxonomy</button>
					@else

					<button type="submit" class="btn full" style="margin-bottom: 24px;">Edit Taxonomy</button>
					<p class="text-center">
						<a href="/admin/taxonomy-types">Clear to create taxonomy type</a>
					</p>
					@endif
				</div>
			</form>
		</aside>

	</div>
@endsection
