@extends('neutrino::admin.template.header-footer')
@section('title', 'Events | ')
@section('content')
	<div class="container">
		<div class="content full">
			<h2>Events <a class="headline-btn" href="/admin/event" role="button">Create New Event</a></h2>

			<table cellpadding="0" cellspacing="0" class="table setting-table">
				<thead>
					<tr>
						<th class="text-left">Event</th>
						<th class="text-left">Date</th>
						<th>Recurring</th>
						<th width="80">Edit</th>
						<th width="60"></th>
					</tr>
				</thead>
				<tbody>
				@foreach( $events as $event )
					<tr>
						<td data-label="Title" class="text-left">
							<a href="/admin/events/{{$event->id}}">{{ $event->template->title }}</a>
						</td>
						<td data-label="Date">
							{{ $event->start_datetime->format('Y-m-d g:i a') }}
						</td>
						<td data-label="Recurring" class="text-center">
							{{ $event->template->is_recurring? 'Yes' : 'No' }}
						</td>
						<td data-label="Edit" class="text-center">
							<a href="/admin/events/{{$event->id}}">Edit</a>
						</td>
						<td data-label="Delete">
							<form action="/admin/events/{{$event->id}}" method="post">
								@csrf
								@method('delete')
								<button type="submit" class="delete-btn">&times;</button>
							</form>
						</td>
					</tr>
				@endforeach
				</tbody>
			</table>
		</div>

	</div>
@endsection
