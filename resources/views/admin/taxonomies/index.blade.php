@extends('neutrino::admin.template.header-footer')
@section('title', 'Taxonomy Terms | ')
@section('content')
	<div class="container">
		<div class="content">
			<h2>{{ str_plural($taxonomy->title) }}</h2>

			<table cellpadding="0" cellspacing="0" class="table">
				<thead>
					<tr>
						<th class="text-left">{{ $taxonomy->title }}</th>
						<th>Slug</th>
						<th>Parent</th>
						<th width="80"></th>
					</tr>
				</thead>
				<tbody>
				@foreach( $taxonomies as $tax )
					<tr>
						<td data-label="Title">
							<a href="/admin/taxonomies/{{ $taxonomy->id }}/{{ $tax->id }}">{{ $tax->title }}</a>
						</td>
						<td data-label="Slug" class="text-center">
							{{ $tax->slug }}
						</td>
						<td data-label="Parent" class="text-center">
							@if( $tax->parent_id > 0 )
							{{ $tax->parentId->title }}
							@endif
						</td>
						<td data-label="Delete" class="text-center">
							@if( $tax->slug !== 'category' )
							<form action="/admin/taxonomies/{{ $taxonomy->id }}/{{$tax->id}}" method="post">
								@csrf
								@method('delete')
								<button type="submit" class="delete-btn">&times;</button>
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
			<h3>Create {{ str_singular($taxonomy->title) }}</h3>
			<form action="/admin/taxonomies/{{ $taxonomy->id }}" method="post">
			@else
			<h3>Edit {{ str_singular($taxonomy->title) }}</h3>
			<form action="/admin/taxonomies/{{ $taxonomy->id }}/{{$edit_taxonomy->id}}" method="post">
			@endif
				@csrf
				<div class="side-fields">
					<div class="form-row">
						<label class="label-col" for="title">{{ $taxonomy->title }} Name</label>
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
						<label class="label-col">Image
							<a class="lfm-featured-image" data-input="featured-image" data-preview="featured-image-preview">
								<i class="fas fa-image"></i> Choose
							</a>
						</label>
						<div class="input-col">
							<input id="featured-image" class="file-list-input" value="{{ $edit_taxonomy->featuredImage? $edit_taxonomy->featuredImage->file_path : '' }}" type="text" name="featured_image">
							<div id="featured-image-preview" class="featured-image-preview">
								<img class="lfm-preview-image" src="{{ $edit_taxonomy->featuredImage? $edit_taxonomy->featuredImage->file_path : '' }}" style="height: 160px;">
								@if($edit_taxonomy->featuredImage)
								<a class="clear-featured-image" href="/">&times;</a>
								@endif
							</div>
						</div>
					</div>

                    @if( $taxonomy->hierarchical )

					<div class="form-row">
						<label class="label-col" for="parent">Parent {{ $taxonomy->title }}</label>
						<div class="input-col">
    						<div class="select-wrapper">
    							@if( !$edit )
    							<select id="parent" name="parent_id">
    								<option value="0">Choose &hellip;</option>
    								@foreach($taxes as $tax)
    								<option value="{{ $tax->id }}" {{ old('parent_id') === $tax->id ? 'selected="selected"' : '' }}>{{ $tax->title }}</option>
    								@endforeach
    							</select>
    							@else
    							<select id="parent" name="parent_id">
    								<option value="0">Choose &hellip;</option>
    								@foreach($taxes as $tax)
    								<option value="{{ $tax->id }}" {{ $edit_taxonomy->parent_id === $tax->id ? 'selected="selected"' : '' }}>{{ $tax->title }}</option>
    								@endforeach
    							</select>
    							@endif
							</div>
						</div>
					</div>

					@endif

					@foreach( $field_groups as $group )
					<h2 class="cf-group-title">{{ $group->title }}</h2>
						@if( $group->description )
							<p>{{ $group->description }}</p>
						@endif

						@if( !$edit )

						    @foreach( $group->fields as $field )
						        {!! _generateField($field) !!}
                            @endforeach

						@else

    						@foreach( $group->fields as $field )
    						    {!! _generateField($field, 'taxonomy', $edit_taxonomy->id) !!}
    						@endforeach

						@endif

					@endforeach


					@if( !$edit )
					<button type="submit" class="btn full">Create {{ $taxonomy->title }}</button>
					@else

					<button type="submit" class="btn full" style="margin-bottom: 24px;">Edit {{ $taxonomy->title }}</button>
					<p class="text-center">
						<a href="/admin/taxonomies/{{ $taxonomy->id }}">Clear to create {{ strtolower($taxonomy->title) }} type</a>
					</p>
					@endif
				</div>
			</form>
		</aside>

	</div>
@endsection
