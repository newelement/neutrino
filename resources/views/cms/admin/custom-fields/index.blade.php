@extends('neutrino::admin.template.header-footer')
@section('title', 'Custom Fields | ')
@section('content')
	<div class="container">
		<div class="content full">
			<h2>Field Groups <a class="headline-btn" href="/admin/custom-field-group" role="button">Create Field Group</a></h2>

			<table cellpadding="0" cellspacing="0" class="table">
				<thead>
					<tr>
						<th class="text-left">Title</th>
						<th width="120">Edit Fields</th>
						<th width="60"></th>
					</tr>
				</thead>
				<tbody>
				@foreach( $custom_groups as $group )
					<tr>
						<td data-label="Title">
							<a href="/admin/custom-fields/group/{{ $group->id }}">{{ $group->title }}</a>
						</td>
						<td data-label="Edit" class="text-center">
							<a href="/admin/custom-fields/group/{{ $group->id }}/fields">Edit Fields</a>
						</td>
						<td data-label="Delete" class="text-center">
							<form class="delete-form" action="/admin/custom-fields/group/{{ $group->id }}" method="post">
								@method('delete')
								@csrf
								<button type="submit" class="delete-btn">&times;</button>
							</form>
						</td>
					</tr>
				@endforeach
				</tbody>
			</table>

			<div class="pagination-links">
				{{ $custom_groups->links() }}
			</div>
		</div>

	</div>
@endsection
