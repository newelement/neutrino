@extends('neutrino::admin.template.header-footer')
@section('title', 'Edit User | ')
@section('content')
<form action="/admin/users/{{$user->id}}" method="post">
	@method('put')
	@csrf
	<div class="container">
		<div class="content">
			<h2>Edit User <a class="headline-btn" href="/admin/user" role="button">Create New User</a></h2>

			<div class="form-row">
				<label class="label-col" for="name">Name</label>
				<div class="input-col">
					<input id="name" type="text" name="name" value="{{ old('name', $user->name) }}" autocomplete="off" required>
				</div>
			</div>

			<div class="form-row">
				<label class="label-col" for="email">Email</label>
				<div class="input-col">
					<input id="email" type="email" name="email" value="{{ old('email', $user->email) }}" autocomplete="off" required>
				</div>
			</div>

			<div class="form-row">
				<label class="label-col" for="password">New Password</label>
				<div class="input-col">
					<input id="password" type="password" name="password" value="{{ old('password') }}" autocomplete="off" >
				</div>
			</div>

			<div class="form-row">
				<label class="label-col" for="password-confirm">Confirm Password</label>
				<div class="input-col">
					<input id="password-confirm" type="password" name="password_confirmation" value="{{ old('password_confirm') }}" autocomplete="off">
				</div>
			</div>

			<div class="form-row">
				<label class="label-col" for="avatar">Avatar</label>
				<div class="input-col">
					<a class="lfm-avatar" data-input="featured-image" data-preview="featured-image-preview" style="margin-bottom: 12px;">
						<i class="fas fa-image"></i> Choose Image
					</a>
					<input id="featured-image" class="file-list-input" value="{{ $user->avatar }}" type="text" name="avatar">
					<div id="featured-image-preview" class="featured-image-preview">
						@if($user->avatar)
						<img class="lfm-preview-image" src="{{ $user->avatar }}" style="height: 160px;">

						@endif
					</div>
				</div>
			</div>

			<div class="form-row">
				<label class="label-col" for="role">Role</label>
				<div class="input-col">
    				<div class="select-wrapper">
    					<select id="role" name="role">
    						@foreach( $roles as $role )
    						<option value="{{$role->id}}" {{ $user->role_id === $role->id? 'selected="selected"' : '' }} >{{ $role->display_name }}</option>
    						@endforeach
    					</select>
					</div>
				</div>
			</div>

		</div>

		<aside class="sidebar">
			<button type="submit" class="btn full text-center">Update User</button>
		</aside>

	</div>
</form>
@endsection
