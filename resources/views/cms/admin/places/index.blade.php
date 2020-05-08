@extends('neutrino::admin.template.header-footer')
@section('title', 'Places | ')
@section('content')
	<div class="container">
		<div class="content full">
			<h2>Places <a class="headline-btn" href="/admin/place" role="button">Create New Place</a></h2>

			<table cellpadding="0" cellspacing="0" class="table setting-table">
				<thead>
					<tr>
						<th class="text-left">Place</th>
						<th class="text-left">Address</th>
						<th width="80">Edit</th>
						<th width="60"></th>
					</tr>
				</thead>
				<tbody>
				@foreach( $places as $place )
					<tr>
						<td data-label="Title" class="text-left">
							<a href="/admin/places/{{$place->id}}">{{ $place->place_name }}</a>
						</td>
						<td data-label="Address">
							{{ $place->address }}
						</td>
						<td data-label="Edit" class="text-center">
							<a href="/admin/places/{{$place->id}}">Edit</a>
						</td>
						<td data-label="Delete">
							<form action="/admin/Places/{{$place->id}}" method="post">
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
