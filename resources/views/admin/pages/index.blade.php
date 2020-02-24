@extends('neutrino::admin.template.header-footer')
@section('title', 'Pages | ')
@section('content')
	<div class="container">
		<div class="content full">
			<div class="title-search">
				<h2>Pages <a class="headline-btn" href="/admin/page" role="button">Create New Page</a></h2>
				<div class="object-search">
					<form class="search-form" action="{{url()->full()}}" method="get">
						<input type="text" name="s" value="{{ request('s') }}" placeholder="Search pages" autocomplete="off">
						<button type="submit"><i class="fas fa-search"></i></button>
					</form>
				</div>
			</div>

			<div class="pages-options-row text-right">
				<a class="trash-link" href="/admin/pages-trash"><i class="fal fa-trash-alt"></i> Trashed ({{ $trashed }})</a>
			</div>

			<div class="responsive-table">
				<table cellpadding="0" cellspacing="0" class="table">
					<thead>
						<tr>
							<th class="text-left">@sortablelink('title', 'Title')</th>
							<th width="100">@sortablelink('status', 'Status')</th>
							<th class="text-left">Parent</th>
							<th>Created By</th>
							<th>Updated By</th>
							<th></th>
							<th width="60"></th>
						</tr>
					</thead>
					<tbody>
					@foreach( $pages as $page )
						<tr>
							<td data-label="Title">
								<a href="/admin/page/{{ $page->id }}">{{ $page->title }}</a>
                                @if( $page->system_page )
                                <br>
                                <span class="system-page">system page</span>
                                @endif
							</td>
							<td data-label="Status" class="text-center">
								{{ _translateStatus($page->status) }}
							</td>
							<td data-label="Parent">
								@if( $page->parent )
								{{ $page->parent->title }}
								@endif
							</td>
							<td data-label="Created by" class="center">{{ $page->createdUser ? $page->createdUser->name : '' }}</td>
							<td data-label="Updated by" class="center">{{ $page->updatedUser ? $page->updatedUser->name : '' }}</td>
							<td>
    							@if( $page->protected )
    							<i class="fal fa-lock"></i>
    							@endif
							</td>
							<td data-label="Delete" class="text-center">
								<form class="delete-form" action="/admin/pages/{{ $page->id }}" method="post">
									@method('delete')
									@csrf
									<button type="submit" class="delete-btn">&times;</button>
								</form>
							</td>
						</tr>
					@endforeach
					</tbody>
				</table>
			</div>

			<div class="pagination-links">
				{{ $pages->appends($_GET)->links() }}
			</div>
		</div>

	</div>
@endsection
