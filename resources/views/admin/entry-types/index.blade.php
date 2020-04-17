@extends('neutrino::admin.template.header-footer')
@section('title', 'Entry Types | ')
@section('content')
	<div class="container">
		<div class="content">
			<h2>Entry Types</h2>

			<table cellpadding="0" cellspacing="0" class="table">
				<thead>
					<tr>
						<th class="text-left">Title</th>
						<th>Slug</th>
						<th>Searchable</th>
						<th width="80">Delete</th>
					</tr>
				</thead>
				<tbody>
				@foreach( $entry_types as $entry_type )
					<tr>
						<td data-label="Title">
							<a href="/admin/entry-types/{{ $entry_type->id }}">{{ $entry_type->entry_type }}</a>
						</td>
						<td data-label="Slug" class="text-center">
							{{ $entry_type->slug }}
						</td>
						<td data-label="Searchable" class="text-center">
							{{ $entry_type->searchable? 'Yes' : 'No' }}
						</td>
						<td data-label="Delete" class="text-center">
							@if( $entry_type->slug !== 'post' )
							<form action="/admin/entry-types/{{$entry_type->id}}" method="post">
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
				{{ $entry_types->links() }}
			</div>
		</div>

		<aside class="sidebar">
			@if( !$edit )
			<form action="/admin/entry-types" method="post">
			@else
			<form action="/admin/entry-types/{{$edit_entry_type->id}}" method="post">
			@endif
				@csrf
				<div class="side-fields">
					<div class="form-row">
						<label class="label-col" for="entry-type-slug">Entry Type Name</label>
						<div class="input-col">
							<input id="entry-type-slug" type="text" name="entry_type" value="{{ old('entry_type', $edit_entry_type->entry_type) }}">
						</div>
					</div>

                    <div class="form-row">
                        <label class="label-col">Featured Image
                            <a class="lfm-featured-image" data-input="featured-image" data-preview="featured-image-preview">
                                <i class="fas fa-image"></i> Choose
                            </a>
                        </label>
                        <div class="input-col">
                            <input id="featured-image" class="file-list-input" value="{{ $edit_entry_type->featured_image? $edit_entry_type->featured_image : '' }}" type="text" name="featured_image">
                            <div id="featured-image-preview" class="featured-image-preview">
                                <img class="lfm-preview-image" src="{{ $edit_entry_type->featured_image? $edit_entry_type->featured_image : '' }}" style="height: 160px;">
                                @if($edit_entry_type->featured_image)
                                <a class="clear-featured-image" href="/">&times;</a>
                                @endif
                            </div>
                        </div>
                    </div>

					<div class="form-row">
						<label class="label-col" for="status">Searchable</label>
						<div class="input-col">
							<label><input type="checkbox" name="searchable" value="1" {{ $edit_entry_type->searchable? 'checked="checked"' : '' }}> Yes</label>
						</div>
					</div>

                    <div class="form-row">
                        <label class="label-col">Social Image
                            <a class="lfm-social-image" data-input="social-image" data-preview="social-image-preview">
                                <i class="fas fa-image"></i> Choose
                            </a>
                        </label>
                        <div class="input-col">
                            <input id="social-image" class="file-list-input" value="{{ $edit_entry_type->social_image_1? $edit_entry_type->social_image_1 : '' }}" type="text" name="social_image_1">
                            <div id="social-image-preview" class="featured-image-preview">
                                <img class="lfm-preview-image" src="{{ $edit_entry_type->social_image_1? $edit_entry_type->social_image_1 : '' }}" style="height: 160px;">
                                @if($edit_entry_type->social_image_1)
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
                            <input id="social-image1" class="file-list-input" value="{{ $edit_entry_type->social_image_2? $edit_entry_type->social_image_2 : '' }}" type="text" name="social_image_2">
                            <div id="social-image-preview1" class="featured-image-preview">
                                <img class="lfm-preview-image" src="{{ $edit_entry_type->social_image_2? $edit_entry_type->social_image_2 : '' }}" style="height: 160px;">
                                @if($edit_entry_type->social_image_2)
                                <a class="clear-lfm-image" data-input="social-image1" data-preview="social-image-preview1" href="/">&times;</a>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="form-row">
                        <label class="label-col" for="social-description">Social Description</label>
                        <div class="input-col">
                            <textarea id="social-description" name="social_description" style="height: 130px;">{{ old('social_description', $edit_entry_type->social_description) }}</textarea>
                        </div>
                    </div>

                    <div class="form-row">
                        <label class="label-col" for="meta-description">Meta Description</label>
                        <div class="input-col">
                            <textarea id="meta-description" name="meta_description" style="height: 80px;">{{ old('meta_description', $edit_entry_type->meta_description) }}</textarea>
                        </div>
                    </div>

                    <div class="form-row">
                        <label class="label-col" for="keywords">Keywords</label>
                        <div class="input-col">
                            <input id="keywords" type="text" name="keywords" value="{{ old('keywords', $edit_entry_type->keywords) }}">
                        </div>
                    </div>

                    <div class="form-row">
                        <label class="label-col" for="sitemap-change">Sitemap Change Frequency</label>
                        <div class="input-col">
                            <div class="select-wrapper">
                                    <select id="sitemap-change" name="sitemap_change">
                                        <option value=""></option>
                                        <option value="always" {{ $edit_entry_type->sitemap_change === 'always'? 'selected="selected"' : '' }}>Always</option>
                                        <option value="hourly" {{ $edit_entry_type->sitemap_change === 'hourly'? 'selected="selected"' : '' }}>Hourly</option>
                                        <option value="daily" {{ $edit_entry_type->sitemap_change === 'daily'? 'selected="selected"' : '' }}>Daily</option>
                                        <option value="weekly" {{ $edit_entry_type->sitemap_change === 'weekly'? 'selected="selected"' : '' }}>Weekly</option>
                                        <option value="monthly" {{ $edit_entry_type->sitemap_change === 'monthly'? 'selected="selected"' : '' }}>Monthly</option>
                                        <option value="yearly" {{ $edit_entry_type->sitemap_change === 'yearly'? 'selected="selected"' : '' }}>Yearly</option>
                                        <option value="never" {{ $edit_entry_type->sitemap_change === 'never'? 'selected="selected"' : '' }}>Never</option>
                                    </select>
                                </div>
                        </div>
                    </div>

                    <div class="form-row">
                        <label class="label-col" for="site-priority">Sitemap Priority ( 0.1 - 1.0 )</label>
                        <div class="input-col">
                            <input id="sitemap-priority" type="number" name="sitemap_priority" style="text-align: right" value="{{ old('sitemap_priority', $edit_entry_type->sitemap_priority ) }}">
                        </div>
                    </div>

					@if( !$edit )
					<button type="submit" class="btn full">Create Entry Type</button>
					@else

					<button type="submit" class="btn full" style="margin-bottom: 24px;">Edit Entry Type</button>
					<p class="text-center">
					<a href="/admin/entry-types">Clear to create entry type</a>
					</p>
					@endif
				</div>
			</form>
		</aside>

	</div>
@endsection
