@extends('neutrino::admin.template.header-footer')
@section('title', 'Roles | ')
@section('content')
	<div class="container">
		<div class="content">
			<h2>Roles</h2>

			<table cellpadding="0" cellspacing="0" class="table setting-table">
				<thead>
					<tr>
						<th class="text-left">Role Name</th>
						<th class="text-left">Display Name</th>
						<th width="80">Edit</th>
						<th width="60"></th>
					</tr>
				</thead>
				<tbody>
				@foreach( $roles as $role )
					<tr>
						<td data-label="Role Name" class="text-left">
							@if( $role->name !== 'admin' && $role->name !== 'editor' )
							<a href="/admin/roles/{{$role->id}}">{{ $role->name }}</a>
							@else
							{{ $role->name }}
							@endif
						</td>
						<td data-label="Display Name">
							{{ $role->display_name }}
						</td>
						<td data-label="Edit" class="text-center">
							@if( $role->name !== 'admin' && $role->name !== 'editor' )
							<a href="/admin/roles/{{$role->id}}">Edit</a>
							@endif
						</td>
						<td data-label="Delete">
							@if( $role->name !== 'admin' && $role->name !== 'editor' )
							<form action="/admin/roles/{{$role->id}}" method="post">
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
		</div>

		<aside class="sidebar">
			<form action="/admin/roles/{{$edit_role->id}}" method="post">
					@csrf
					@if( $edit )
					@method('put')
					@endif
				<div class="side-fields">
					<div class="form-row">
						<label class="label-col" for="setting-key">Role Name</label>
						<div class="input-col">
							<input id="setting-key" type="text" name="name" value="{{ old('name', $edit_role->name) }}">
							<span class="note">Lowercase. No spaces or special characters.</span>
						</div>
					</div>

					<div class="form-row">
						<label class="label-col" for="setting-value">Display Name</label>
						<div class="input-col">
							<input id="setting-value" type="text" name="display_name" value="{{ old('display_name', $edit_role->display_name) }}">
						</div>
					</div>
					@if( !$edit )
					<button type="submit" class="btn full">Create Role</button>
					@else
					<button type="submit" class="btn full text-center">Update Role</button>
					@endif
				</div>
			</form>
		</aside>

	</div>
@endsection
