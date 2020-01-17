@extends('neutrino::admin.template.header-footer')
@section('title', 'Pages Trash | ')
@section('content')
	<div class="container">
		<div class="content">
			<div class="title-search">
				<h2>Pages in Trash</h2>
				<div class="object-search">
				</div>
			</div>

			<table cellpadding="0" cellspacing="0" class="table">
				<thead>
					<tr>
						<th class="text-left">@sortablelink('title', 'Title')</th>
						<th>@sortablelink('status', 'Status')</th>
						<th width="90">Recover</th>
						<th width="90">Destroy</th>
					</tr>
				</thead>
				<tbody>
				@foreach( $pages as $page )
					<tr>
						<td data-label="Title">
							{{ $page->title }}
						</td>
						<td data-label="Status" class="text-center">
							{{ _translateStatus($page->status) }}
						</td>
						<td data-label="Recover" class="text-center"><a href="/admin/pages/recover/{{ $page->id }}">Recover</a></td>
						<td data-label="Destroy" class="text-center"><a class="destroy-btn" href="/admin/pages/destroy/{{ $page->id }}">Destroy</a></td>
					</tr>
				@endforeach
				</tbody>
			</table>

			<div class="pagination-links">
				{{ $pages->appends($_GET)->links() }}
			</div>
		</div>

		<aside class="sidebar">
		</aside>

	</div>
@endsection
