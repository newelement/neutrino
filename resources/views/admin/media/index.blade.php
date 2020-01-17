@extends('neutrino::admin.template.header-footer')
@section('title', 'Media | ')
@section('content')
	<div class="container">
		<div class="content" style="width: 100%">
			<h2>Media</h2>

			<ul class="tabs">
				<li>
					<a class="active" href="#tab-files">Files</a>
				</li>
				<li>
					<a href="#tab-images">Images</a>
				</li>
			</ul>

			<div class="tabs-container">
				<div id="tab-files" class="tab-content active">
					<iframe src="/laravel-filemanager" style="width: 100%; height: 500px; overflow: hidden; border: none;"></iframe>
				</div>
				<div id="tab-images" class="tab-content">
					<iframe src="/laravel-filemanager?type=image" style="width: 100%; height: 500px; overflow: hidden; border: none;"></iframe>
				</div>
			</div>
		</div>

	</div>
@endsection
