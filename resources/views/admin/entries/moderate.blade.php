@extends('neutrino::admin.template.header-footer')
@section('title', 'Moderate Comments | ')
@section('content')
	<div class="container">
		<div class="content full">
			<h2>Moderate Comments</h2>

			<table cellpadding="0" cellspacing="0" class="table">
				<thead>
					<tr>
						<th class="text-left">Entry</th>
						<th class="text-left">Comment</th>
						<th>Created At</th>
						<th>Created By</th>
						<th width="80">Approve</th>
						<th width="60"></th>
					</tr>
				</thead>
				<tbody>
					@if( count($comments) === 0 )
					<tr><td colspan="6">Congrats! There are no comments to moderate.</td></tr>
					@endif
				@foreach( $comments as $comment )
					<tr>
						<td data-label="Title">
							{{ $comment->entry()->title }}
						</td>
						<td data-label="Comment" class="text-center">
							{!! $comment->comment !!}
						</td>
						<td data-label="Created on" class="center">{{ $comment->created_at->format('Y-m-d g:i a') }}</td>
						<td  data-label="Created By"class="center">{{ $comment->createdUser()->name }}</td>
						<td data-label="Delete">
							<form class="delete-form" action="/admin/comment/{{ $comment->id }}/approve }}" method="get">
								@csrf
								<button type="submit">Approve</button>
							</form>
						</td>
						<td>
							<form class="delete-form" action="/admin/comment/{{ $comment->id }} }}" method="post">
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
				{{ $comments->appends($_GET)->links() }}
			</div>

		</div>
	</div>
@endsection
