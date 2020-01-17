@extends('neutrino::admin.template.header-footer')
@section('title', 'Users | ')
@section('content')
	<div class="container">
		<div class="content full">
			<h2>Users <a class="headline-btn" href="/admin/user" role="button">Create New User</a></h2>

			<table cellpadding="0" cellspacing="0" class="table">
				<thead>
					<tr>
						<th width="100">Avatar</th>
						<th class="text-left">Name</th>
						<th class="text-left">Email</th>
						<th>Role</th>
						<th width="80">Edit</th>
						<th width="60"></th>
					</tr>
				</thead>
				<tbody>
				@foreach( $users as $user )
					<tr>
						<td data-label="Avatar" class="list-avatar text-center">
							<a href="/admin/users/{{ $user->id }}">{!! $user->avatar ? '<img src="'.$user->avatar.'">' : '<i class="fas fa-user"></i>' !!}</a>
						</td>
						<td data-label="Name">
							<a href="/admin/users/{{ $user->id }}">{{ $user->name }}</a>
						</td>
						<td data-label="Email">{{ $user->email }}</td>
						<td data-label="Role" class="text-center">
							{{ $user->role->display_name }}
						</td>
						<td data-label="Edit" class="text-center">
							<a href="/admin/users/{{ $user->id }}">Edit</a>
						</td>
						<td data-label="Delete">
							@if( Auth::user()->id !== $user->id )
							<form action="/admin/users/{{$user->id}}" method="post">
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
				{{ $users->links() }}
			</div>
		</div>

	</div>
@endsection
