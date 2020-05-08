@extends('neutrino::admin.template.header-footer')
@section('title', 'Edit Place | ')
@section('content')
<form action="/admin/places/{{$place->id}}" method="post">
	@method('put')
	@csrf
	<div class="container">
		<div class="content">
			<h2>Edit Place <a class="headline-btn" href="/admin/place" role="button">Create New Place</a></h2>

			<div class="form-row">
				<label class="label-col" for="name">Place Name</label>
				<div class="input-col">
					<input id="name" class="to-slug" type="text" name="place_name" value="{{ old('place_name', $place->place_name) }}" autocomplete="off" required>
				</div>
			</div>

			<div class="form-row">
				<label class="label-col" for="slug">Slug</label>
				<div class="input-col">
					<input id="slug" class="slug-input" type="text" name="slug" value="{{ old('slug', $place->slug) }}" required>
				</div>
			</div>

			<div class="form-row">
				<label class="label-col align-top full-width" for="description">Description</label>
				<div class="input-col full-width">
					<textarea class="editor" id="description" name="description">{!! old('description', $place->description) !!}</textarea>
				</div>
			</div>

			<div class="form-row">
				<label class="label-col" for="address">Address</label>
				<div class="input-col">
					<input id="address" type="text" name="address" value="{{ old('address', $place->address) }}" autocomplete="off">
				</div>
			</div>

			<div class="form-row">
				<label class="label-col" for="address2">Address 2</label>
				<div class="input-col">
					<input id="address2" type="text" name="address2" value="{{ old('address2', $place->address2) }}" autocomplete="off">
				</div>
			</div>

			<div class="form-row">
				<label class="label-col" for="city">City</label>
				<div class="input-col">
					<input id="city" type="text" name="city" value="{{ old('city', $place->city) }}" autocomplete="off">
				</div>
			</div>

			<div class="form-row">
				<label class="label-col" for="state">State</label>
				<div class="input-col">
					<input id="state" type="text" name="state" value="{{ old('state', $place->state) }}" autocomplete="off">
				</div>
			</div>

			<div class="form-row">
				<label class="label-col" for="zip">Zip/Postal Code</label>
				<div class="input-col">
					<input id="zip" type="text" name="zip" value="{{ old('zip', $place->postal) }}" autocomplete="off">
				</div>
			</div>

			<div class="form-row">
				<label class="label-col" for="country">Country</label>
				<div class="input-col">
					<input id="country" type="text" name="country" value="{{ old('country', $place->country) }}" autocomplete="off">
				</div>
			</div>

			<div class="form-row">
				<label class="label-col" for="email">Email</label>
				<div class="input-col">
					<input id="email" type="text" name="email" value="{{ old('email', $place->email) }}" autocomplete="off">
				</div>
			</div>

			<div class="form-row">
				<label class="label-col" for="url">Website</label>
				<div class="input-col">
					<input id="url" type="text" name="url" value="{{ old('url', $place->url) }}" autocomplete="off">
				</div>
			</div>

			<div class="form-row">
				<label class="label-col" for="lat">Lattitude</label>
				<div class="input-col">
					<input id="lat" type="text" name="lat" value="{{ old('lat', $place->lat) }}" autocomplete="off">
				</div>
			</div>

			<div class="form-row">
				<label class="label-col" for="lon">Longitude</label>
				<div class="input-col">
					<input id="lon" type="text" name="lon" value="{{ old('lon', $place->lon) }}" autocomplete="off">
				</div>
			</div>

		</div>

		<aside class="sidebar">

			<div class="side-fields">
				<div class="form-row">
					<label class="label-col">Featured Image
						<a class="lfm-featured-image" data-input="featured-image" data-preview="featured-image-preview">
							<i class="fas fa-image"></i> Choose
						</a>
					</label>
					<div class="input-col">
						<input id="featured-image" class="file-list-input" value="{{ $place->featuredImage? $place->featuredImage->file_path : '' }}" type="text" name="featured_image">
						<div id="featured-image-preview" class="featured-image-preview">
							<img class="lfm-preview-image" src="{{ $place->featuredImage? $place->featuredImage->file_path : '' }}" style="height: 160px;">
							@if($place->featuredImage)
							<a class="clear-featured-image" href="/">&times;</a>
							@endif
						</div>
					</div>
				</div>
				<button type="submit" class="btn full text-center">Update Place</button>
			</div>
		</aside>

	</div>
</form>
@endsection

@section('js')
<script>
window.editorStyles = <?php echo json_encode(config('neutrino.editor_styles')) ?>;
window.editorCss = '<?php echo getEditorCss(); ?>';
</script>
@endsection
