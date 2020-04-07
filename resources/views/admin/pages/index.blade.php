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
                        @each('neutrino::admin.partials.page-row', $pages, 'page')
					</tbody>
				</table>
			</div>

			<div class="simple-pagination-links">
                @php
                $total = $page_count;
                $paged = \Request::get('page')? \Request::get('page') : 1;
                $splitPages = ceil( $total / 30 );
                @endphp
                @if( $splitPages > 0 )
                @if( $paged > 1 ) <a class="simple-prev" href="?page={{ $paged-1 }}">&larr; Prev page</a> @endif <div class="simple-page-count">Page {{ $paged }} of {{ $splitPages }} page{{ (int) $splitPages === 1? '' : 's' }}</div>  @if( $splitPages -1 >= $paged  ) <a class="simple-next" href="?page={{ $paged+1 }}">Next page &rarr;</a> @endif
                @endif
			</div>
		</div>

	</div>
@endsection
