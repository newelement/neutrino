@extends('neutrino::admin.template.header-footer')
@section('title', 'Menus | ')
@section('content')
	<div class="container">
		<div class="content">
			<h2>Menus</h2>

			<table cellpadding="0" cellspacing="0" class="table setting-table">
				<thead>
					<tr>
						<th class="text-left">Menu Name</th>
						<th width="80">Edit</th>
						<th width="60"></th>
					</tr>
				</thead>
				<tbody>
				@foreach( $menus as $menu )
					<tr>
						<td class="text-left">
							<a href="/admin/menus/{{$menu->id}}">{{ $menu->name }}</a>
						</td>
						<td class="text-center">
							<a href="/admin/menus/{{$menu->id}}">Edit</a>
						</td>
						<td>
							<form action="/admin/menus/{{$menu->id}}" method="post">
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

		<aside class="sidebar">

			@if( !$edit )
			<form action="/admin/menus" method="post">
			@else
			<form action="/admin/menus/{{$edit_menu->id}}" method="post">
			@endif
				@csrf
				<div class="side-fields">
					<div class="form-row">
						<label class="label-col" for="menu-name">Menu Name</label>
						<div class="input-col">
							<input id="menu-name" type="text" name="name" autocomplete="off" value="{{ old('name', $edit_menu->name) }}">
						</div>
					</div>
					@if( !$edit )
					<button type="submit" class="btn full">Create Menu</button>
					@else

					<button type="submit" class="btn full" style="margin-bottom: 24px;">Edit Menu</button>
					<p class="text-center">
					<a href="/admin/menus">Clear to create menu</a>
					</p>
					@endif
				</div>
			</form>

		</aside>

	</div>
@endsection
