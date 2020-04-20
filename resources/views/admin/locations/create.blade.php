@extends('neutrino::admin.template.header-footer')
@section('title', 'Create Location | ')
@section('content')
<form action="/admin/locations" method="post">
	@csrf
	<div class="container">
		<div class="content">
			<h2>Create Location</h2>

			<div class="form-row">
				<label class="label-col" for="name">Location Name</label>
				<div class="input-col">
					<input id="name" class="to-slug" type="text" name="location_name" value="{{ old('location_name') }}" autocomplete="off" required>
				</div>
			</div>

			<div class="form-row">
				<label class="label-col" for="slug">Slug</label>
				<div class="input-col">
					<input id="slug" class="slug-input" type="text" name="slug" value="{{ old('slug') }}" required>
				</div>
			</div>

			<div class="form-row">
				<label class="label-col align-top full-width" for="description">Description</label>
				<div class="input-col full-width">
					<textarea class="editor" id="description" name="description">{!! old('description') !!}</textarea>
				</div>
			</div>

			<div class="form-row">
				<label class="label-col" for="address">Address</label>
				<div class="input-col">
					<input id="address" type="text" name="address" value="{{ old('address') }}" autocomplete="off">
				</div>
			</div>

			<div class="form-row">
				<label class="label-col" for="address2">Address 2</label>
				<div class="input-col">
					<input id="address2" type="text" name="address2" value="{{ old('address2') }}" autocomplete="off">
				</div>
			</div>

			<div class="form-row">
				<label class="label-col" for="city">City</label>
				<div class="input-col">
					<input id="city" type="text" name="city" value="{{ old('city') }}" autocomplete="off">
				</div>
			</div>

			<div class="form-row">
				<label class="label-col" for="state">State</label>
				<div class="input-col">
					<input id="state" type="text" name="state" value="{{ old('state') }}" autocomplete="off">
				</div>
			</div>

			<div class="form-row">
				<label class="label-col" for="zip">Zip/Postal Code</label>
				<div class="input-col">
					<input id="zip" type="text" name="zip" value="{{ old('zip') }}" autocomplete="off">
				</div>
			</div>

			<div class="form-row">
				<label class="label-col" for="country">Country</label>
				<div class="input-col">
					<input id="country" type="text" name="country" value="{{ old('country') }}" autocomplete="off">
				</div>
			</div>

			<div class="form-row">
				<label class="label-col" for="email">Email</label>
				<div class="input-col">
					<input id="email" type="text" name="email" value="{{ old('email') }}" autocomplete="off">
				</div>
			</div>

			<div class="form-row">
				<label class="label-col" for="url">Website</label>
				<div class="input-col">
					<input id="url" type="text" name="url" value="{{ old('url') }}" autocomplete="off">
				</div>
			</div>

			<div class="form-row">
				<label class="label-col" for="lat">Lattitude</label>
				<div class="input-col">
					<input id="lat" type="text" name="lat" value="{{ old('lat') }}" autocomplete="off">
				</div>
			</div>

			<div class="form-row">
				<label class="label-col" for="lon">Longitude</label>
				<div class="input-col">
					<input id="lon" type="text" name="lon" value="{{ old('lon') }}" autocomplete="off">
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
						<input id="featured-image" class="file-list-input" value="" type="text" name="featured_image">
						<div id="featured-image-preview" class="featured-image-preview">
						</div>
					</div>
				</div>
				<button type="submit" class="btn full text-center">Create Location</button>
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
