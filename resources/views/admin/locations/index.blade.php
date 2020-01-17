@extends('neutrino::admin.template.header-footer')
@section('title', 'Locations | ')
@section('content')
	<div class="container">
		<div class="content full">
			<h2>Locations <a class="headline-btn" href="/admin/location" role="button">Create New Location</a></h2>

			<table cellpadding="0" cellspacing="0" class="table setting-table">
				<thead>
					<tr>
						<th class="text-left">Location</th>
						<th class="text-left">Address</th>
						<th width="80">Edit</th>
						<th width="60"></th>
					</tr>
				</thead>
				<tbody>
				@foreach( $locations as $location )
					<tr>
						<td data-label="Title" class="text-left">
							<a href="/admin/locations/{{$location->id}}">{{ $location->location_name }}</a>
						</td>
						<td data-label="Address">
							{{ $location->address }}
						</td>
						<td data-label="Edit" class="text-center">
							<a href="/admin/locations/{{$location->id}}">Edit</a>
						</td>
						<td data-label="Delete">
							<form action="/admin/locations/{{$location->id}}" method="post">
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
