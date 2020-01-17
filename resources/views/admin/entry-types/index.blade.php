@extends('neutrino::admin.template.header-footer')
@section('title', 'Entry Types | ')
@section('content')
	<div class="container">
		<div class="content">
			<h2>Entry Types</h2>

			<table cellpadding="0" cellspacing="0" class="table">
				<thead>
					<tr>
						<th class="text-left">Title</th>
						<th>Slug</th>
						<th>Searchable</th>
						<th width="80">Delete</th>
					</tr>
				</thead>
				<tbody>
				@foreach( $entry_types as $entry_type )
					<tr>
						<td data-label="Title">
							<a href="/admin/entry-types/{{ $entry_type->id }}">{{ $entry_type->entry_type }}</a>
						</td>
						<td data-label="Slug" class="text-center">
							{{ $entry_type->slug }}
						</td>
						<td data-label="Searchable" class="text-center">
							{{ $entry_type->searchable? 'Yes' : 'No' }}
						</td>
						<td data-label="Delete" class="text-center">
							@if( $entry_type->slug !== 'post' )
							<form action="/admin/entry-types/{{$entry_type->id}}" method="post">
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
				{{ $entry_types->links() }}
			</div>
		</div>

		<aside class="sidebar">
			@if( !$edit )
			<form action="/admin/entry-types" method="post">
			@else
			<form action="/admin/entry-types/{{$edit_entry_type->id}}" method="post">
			@endif
				@csrf
				<div class="side-fields">
					<div class="form-row">
						<label class="label-col" for="entry-type-slug">Entry Type Name</label>
						<div class="input-col">
							<input id="entry-type-slug" type="text" name="entry_type" value="{{ old('entry_type', $edit_entry_type->entry_type) }}">
						</div>
					</div>

					<div class="form-row">
						<label class="label-col" for="status">Searchable</label>
						<div class="input-col">
							<label><input type="checkbox" name="searchable" value="1" {{ $edit_entry_type->searchable? 'checked="checked"' : '' }}> Yes</label>
						</div>
					</div>
					@if( !$edit )
					<button type="submit" class="btn full">Create Entry Type</button>
					@else

					<button type="submit" class="btn full" style="margin-bottom: 24px;">Edit Entry Type</button>
					<p class="text-center">
					<a href="/admin/entry-types">Clear to create entry type</a>
					</p>
					@endif
				</div>
			</form>
		</aside>

	</div>
@endsection
