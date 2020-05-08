@extends('neutrino::admin.template.header-footer')
@section('title', 'Create User | ')
@section('content')
<form action="/admin/users" method="post" enctype="multipart/form-data" autocomplete="off">
	@csrf
	<div class="container">
		<div class="content">
			<h2>Create User</h2>

			<div class="form-row">
				<label class="label-col" for="name">Name</label>
				<div class="input-col">
					<input id="name" type="text" name="name" value="{{ old('name') }}" autocomplete="off" required>
				</div>
			</div>

			<div class="form-row">
				<label class="label-col" for="email">Email</label>
				<div class="input-col">
					<input id="email" type="email" name="email" value="{{ old('email') }}" autocomplete="off" required>
				</div>
			</div>

			<div class="form-row">
				<label class="label-col" for="password">Password</label>
				<div class="input-col">
					<input id="password" type="password" name="password" value="{{ old('password') }}" autocomplete="off" required>
				</div>
			</div>

			<div class="form-row">
				<label class="label-col" for="password-confirm">Confirm Password</label>
				<div class="input-col">
					<input id="password-confirm" type="password" name="password_confirmation" value="{{ old('password_confirm') }}" autocomplete="off" required>
				</div>
			</div>

			<div class="form-row">
				<label class="label-col" for="avatar">Avatar</label>
				<div class="input-col">
					<a class="lfm-avatar" data-input="featured-image" data-preview="featured-image-preview">
						<i class="fas fa-image"></i> Choose Image
					</a>
					<input id="featured-image" class="file-list-input" value="" type="text" name="avatar">
					<div id="featured-image-preview" class="featured-image-preview"></div>
				</div>
			</div>

			<div class="form-row">
				<label class="label-col" for="role">Role</label>
				<div class="input-col">
    				<div class="select-wrapper">
    					<select id="role" name="role">
    						@foreach( $roles as $role )
    						<option value="{{$role->id}}">{{ $role->display_name }}</option>
    						@endforeach
    					</select>
    				</div>
				</div>
			</div>

		</div>

		<aside class="sidebar">
			<button type="submit" class="btn full text-center">Save User</button>
		</aside>

	</div>
</form>
@endsection
