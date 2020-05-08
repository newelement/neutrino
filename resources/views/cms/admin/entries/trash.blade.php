@extends('neutrino::admin.template.header-footer')
@section('title', 'Trash for '.ucwords(request('entry_type')).' | ')
@section('content')
	<div class="container">
		<div class="content">
			<div class="title-search">
				<h2>{{ str_plural( ucwords(str_replace('-','', request('entry_type'))) ) }} in Trash</h2>
				<div class="object-search">
				</div>
			</div>

			<table cellpadding="0" cellspacing="0" class="table">
				<thead>
					<tr>
						<th class="text-left">Title</th>
						<th>Status</th>
						<th width="80">Recover</th>
						<th width="80">Destroy</th>
					</tr>
				</thead>
				<tbody>
				@foreach( $entries as $entry )
					<tr>
						<td data-label="Title">
							<a href="/admin/entry/{{ $entry->id }}?entry_type={{ request('entry_type') }}">{{ $entry->title }}</a>
						</td>
						<td data-label="Status" class="text-center">
							{{ _translateStatus($entry->status) }}
						</td>
						<td data-label="Recover" class="text-center"><a href="/admin/entries/recover/{{ $entry->id }}?entry_type={{ request('entry_type') }}">Recover</a></td>
						<td data-label="Delete" class="text-center"><a class="destroy-btn" href="/admin/entries/destroy/{{ $entry->id }}?entry_type={{ request('entry_type') }}">Destroy</a></td>
					</tr>
				@endforeach
				</tbody>
			</table>

			<div class="pagination-links">
				{{ $entries->appends($_GET)->links() }}
			</div>
		</div>

		<aside class="sidebar">
		</aside>

	</div>
@endsection
