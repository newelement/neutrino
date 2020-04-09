@extends('neutrino::admin.template.header-footer')
@section('title', 'Taxonomy Terms | ')
@section('content')
	<div class="container">
		<div class="content terms-content">
			<h2>{{ str_plural($taxonomy->title) }}</h2>

			<ul class="term-list">
                @each('neutrino::admin.partials.term', $taxonomies, 'tax')
			</ul>
		</div>

		<aside class="sidebar terms-sidebar">
			@if( !$edit )
			<h3>Create {{ str_singular($taxonomy->title) }}</h3>
			<form action="/admin/taxonomies/{{ $taxonomy->id }}" method="post">
			@else
			<h3>Edit {{ str_singular($taxonomy->title) }}</h3>
			<form action="/admin/taxonomies/{{ $taxonomy->id }}/{{$edit_taxonomy->id}}" method="post">
			@endif
				@csrf
				<div class="side-fields">
					<div class="form-row">
						<label class="label-col" for="title">{{ $taxonomy->title }} Name</label>
						<div class="input-col">
							<input id="title" type="text" name="title" value="{{ old('title', $edit_taxonomy->title) }}">
						</div>
					</div>

					<div class="form-row">
						<label class="label-col" for="desc">Description</label>
						<div class="input-col">
							<textarea id="desc" class="small-editor" name="description" style="height: 180px;">{{ old('description', $edit_taxonomy->description) }}</textarea>
						</div>
					</div>

					<div class="form-row">
						<label class="label-col">Featured Image
							<a class="lfm-featured-image" data-input="featured-image" data-preview="featured-image-preview">
								<i class="fas fa-image"></i> Choose
							</a>
						</label>
						<div class="input-col">
							<input id="featured-image" class="file-list-input" value="{{ $edit_taxonomy->featuredImage? $edit_taxonomy->featuredImage->file_path : '' }}" type="text" name="featured_image">
							<div id="featured-image-preview" class="featured-image-preview">
								<img class="lfm-preview-image" src="{{ $edit_taxonomy->featuredImage? $edit_taxonomy->featuredImage->file_path : '' }}" style="height: 160px;">
								@if($edit_taxonomy->featuredImage)
								<a class="clear-featured-image" href="/">&times;</a>
								@endif
							</div>
						</div>
					</div>

                    @if( $taxonomy->hierarchical )

					<div class="form-row">
						<label class="label-col" for="parent">Parent {{ $taxonomy->title }}</label>
						<div class="input-col">
    						<div class="select-wrapper">
    							@if( !$edit )
    							<select id="parent" name="parent_id">
    								<option value="0">Choose &hellip;</option>
    								@foreach($taxes as $tax)
    								<option value="{{ $tax->id }}" {{ old('parent_id') === $tax->id ? 'selected="selected"' : '' }}>{{ $tax->title }}</option>
    								@endforeach
    							</select>
    							@else
    							<select id="parent" name="parent_id">
    								<option value="0">Choose &hellip;</option>
    								@foreach($taxes as $tax)
    								<option value="{{ $tax->id }}" {{ $edit_taxonomy->parent_id === $tax->id ? 'selected="selected"' : '' }}>{{ $tax->title }}</option>
    								@endforeach
    							</select>
    							@endif
							</div>
						</div>
					</div>

                    @endif

                    <div class="form-row">
                        <label class="label-col">Social Image
                            <a class="lfm-social-image" data-input="social-image" data-preview="social-image-preview">
                                <i class="fas fa-image"></i> Choose
                            </a>
                        </label>
                        <div class="input-col">
                            <input id="social-image" class="file-list-input" value="{{ $edit_taxonomy->social_image_1? $edit_taxonomy->social_image_1 : '' }}" type="text" name="social_image_1">
                            <div id="social-image-preview" class="featured-image-preview">
                                <img class="lfm-preview-image" src="{{ $edit_taxonomy->social_image_1? $edit_taxonomy->social_image_1 : '' }}" style="height: 160px;">
                                @if($edit_taxonomy->social_image_1)
                                <a class="clear-social-image" href="/">&times;</a>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="form-row">
                        <label class="label-col">Twitter Image
                            <a class="lfm-social-image" data-input="social-image1" data-preview="social-image-preview1">
                                <i class="fas fa-image"></i> Choose
                            </a>
                        </label>
                        <div class="input-col">
                            <input id="social-image1" class="file-list-input" value="{{ $edit_taxonomy->social_image_2? $edit_taxonomy->social_image_2 : '' }}" type="text" name="social_image_2">
                            <div id="social-image-preview1" class="featured-image-preview">
                                <img class="lfm-preview-image" src="{{ $edit_taxonomy->social_image_2? $edit_taxonomy->social_image_2 : '' }}" style="height: 160px;">
                                @if($edit_taxonomy->social_image_2)
                                <a class="clear-lfm-image" data-input="social-image1" data-preview="social-image-preview1" href="/">&times;</a>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="form-row">
                        <label class="label-col" for="social-description">Social Description</label>
                        <div class="input-col">
                            <textarea id="social-description" name="social_description" style="height: 130px;">{{ old('social_description', $edit_taxonomy->social_description) }}</textarea>
                        </div>
                    </div>

                    <div class="form-row">
                        <label class="label-col" for="meta-description">Meta Description</label>
                        <div class="input-col">
                            <textarea id="meta-description" name="meta_description" style="height: 80px;">{{ old('meta_description', $edit_taxonomy->meta_description) }}</textarea>
                        </div>
                    </div>

                    <div class="form-row">
                        <label class="label-col" for="keywords">Keywords</label>
                        <div class="input-col">
                            <input id="keywords" type="text" name="keywords" value="{{ old('keywords', $edit_taxonomy->keywords) }}">
                        </div>
                    </div>

					@foreach( $field_groups as $group )
					<h2 class="cf-group-title">{{ $group->title }}</h2>
						@if( $group->description )
							<p>{{ $group->description }}</p>
						@endif

						@if( !$edit )

						    @foreach( $group->fields as $field )
						        {!! _generateField($field) !!}
                            @endforeach

						@else

    						@foreach( $group->fields as $field )
    						    {!! _generateField($field, 'taxonomy', $edit_taxonomy->id) !!}
    						@endforeach

						@endif

					@endforeach


					@if( !$edit )
					<button type="submit" class="btn full">Create {{ $taxonomy->title }}</button>
					@else

					<button type="submit" class="btn full" style="margin-bottom: 24px;">Save {{ $taxonomy->title }}</button>
					<p class="text-center">
						<a href="/admin/taxonomies/{{ $taxonomy->id }}">Clear to create {{ strtolower($taxonomy->title) }} type</a>
					</p>
					@endif
				</div>
			</form>
		</aside>

	</div>
@endsection

@section('js')
<script>
window.editorCss = '<?php echo getEditorCss(); ?>';
window.blocks = <?php echo getBlocks() ?>;
window.currentBlocks = [];
</script>
@endsection
