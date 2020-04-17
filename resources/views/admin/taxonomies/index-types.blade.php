@extends('neutrino::admin.template.header-footer')
@section('title', 'Taxonomies | ')
@section('content')
	<div class="container">
		<div class="content terms-content">
			<h2>Taxonomies</h2>

			<table cellpadding="0" cellspacing="0" class="table">
				<thead>
					<tr>
                        <th width="20"></th>
						<th class="text-left">Taxonomy</th>
						<th>Slug</th>
						<th>Hierarchical</th>
						<th>Edit Terms</th>
						<th width="80"></th>
					</tr>
				</thead>
				<tbody class="tax-type-table">
				@foreach( $taxonomies as $tax )
					<tr>
                        <td class="tax-sort-handle"><i class="fal fa-sort"></i></td>
						<td class="tax-item" data-id="{{ $tax->id }}" data-label="Title">
							<a href="/admin/taxonomy-types/{{ $tax->id }}">{{ $tax->title }}</a>
						</td>
						<td data-label="Slug" class="text-center">
							{{ $tax->slug }}
						</td>
						<td data-label="Hierarchical" class="text-center">
							{{ $tax->hierarchical? 'Yes' : 'No' }}
						</td>
						<td data-label="Edit Terms" class="text-center">
							<a href="/admin/taxonomies/{{$tax->id}}">Edit Terms</a>
						</td>
						<td data-label="Delete" class="text-center">
							@if( $tax->slug !== 'category' )
							<form action="/admin/taxonomy-types/{{$tax->id}}" method="post">
								@csrf
								@method('delete')
								<button type="submit" class="delete-btn-taxonomy">&times;</button>
							</form>
							@endif
						</td>
					</tr>
				@endforeach
				</tbody>
			</table>
		</div>

		<aside class="sidebar terms-sidebar">
			@if( !$edit )
			<h3>Create Taxonomy</h3>
			<form action="/admin/taxonomy-types" method="post">
			@else
			<h3>Edit Taxonomy</h3>
			<form id="object-form" action="/admin/taxonomy-types/{{$edit_taxonomy->id}}" method="post">
			@endif
				@csrf
				<div class="side-fields">
					<div class="form-row">
						<label class="label-col" for="title">Taxonomy Name</label>
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
						<label class="label-col" for="status">Hierarchical</label>
						<div class="input-col">
							<label><input type="checkbox" name="hierarchical" value="1" {{ $edit_taxonomy->hierarchical? 'checked="checked"' : '' }}> Yes</label>
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

					<div class="form-row">
						<label class="label-col" for="desc">Display On Entry Types</label>
						<div class="input-col">
							<select name="show_on[]" style="height: 100px;" multiple>
								@foreach($entry_types as $entry_type)
								<option value="{{ $entry_type->slug }}" {{ $edit_taxonomy->show_on->contains($entry_type->slug)? 'selected="selected"' : '' }}>{{ $entry_type->entry_type }}</option>
								@endforeach
								<option value="events" {{ $edit_taxonomy->show_on->contains('events')? 'selected="selected"' : '' }}">Events</option>
							    @if( shoppeExists() )
								<option value="products" {{ $edit_taxonomy->show_on->contains('products')? 'selected="selected"' : '' }}">Products</option>
                                @endif
							</select>
							<span class="note">Ctrl/Command+click to select/unselect</span>
						</div>
					</div>

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

                    <div class="form-row">
                        <label class="label-col" for="sitemap-change">Sitemap Change Frequency</label>
                        <div class="input-col">
                            <div class="select-wrapper">
                                    <select id="sitemap-change" name="sitemap_change">
                                        <option value=""></option>
                                        <option value="always" {{ $edit_taxonomy->sitemap_change === 'always'? 'selected="selected"' : '' }}>Always</option>
                                        <option value="hourly" {{ $edit_taxonomy->sitemap_change === 'hourly'? 'selected="selected"' : '' }}>Hourly</option>
                                        <option value="daily" {{ $edit_taxonomy->sitemap_change === 'daily'? 'selected="selected"' : '' }}>Daily</option>
                                        <option value="weekly" {{ $edit_taxonomy->sitemap_change === 'weekly'? 'selected="selected"' : '' }}>Weekly</option>
                                        <option value="monthly" {{ $edit_taxonomy->sitemap_change === 'monthly'? 'selected="selected"' : '' }}>Monthly</option>
                                        <option value="yearly" {{ $edit_taxonomy->sitemap_change === 'yearly'? 'selected="selected"' : '' }}>Yearly</option>
                                        <option value="never" {{ $edit_taxonomy->sitemap_change === 'never'? 'selected="selected"' : '' }}>Never</option>
                                    </select>
                                </div>
                        </div>
                    </div>

                    <div class="form-row">
                        <label class="label-col" for="site-priority">Sitemap Priority ( 0.1 - 1.0 )</label>
                        <div class="input-col">
                            <input id="sitemap-priority" type="number" name="sitemap_priority" style="text-align: right" value="{{ old('sitemap_priority', $edit_taxonomy->sitemap_priority ) }}">
                        </div>
                    </div>

					@if( !$edit )
					<button type="submit" class="btn full">Create Taxonomy</button>
					@else

					<button type="submit" class="btn full" style="margin-bottom: 24px;">Edit Taxonomy</button>
					<p class="text-center">
						<a href="/admin/taxonomy-types">Clear to create taxonomy type</a>
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
