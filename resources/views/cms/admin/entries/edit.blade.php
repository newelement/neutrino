@extends('neutrino::admin.template.header-footer')
@section('title', 'Edit '.ucwords(request('entry_type')).' | ')
@section('content')
<form id="object-form" action="/admin/entry/{{ $entry->id }}" method="post" enctype="multipart/form-data">
	<input type="hidden" id="object-id" value="{{ $entry->id }}">
	<input type="hidden" id="object-type" value="{{ $entry->entry_type }}">
	<input type="hidden" name="entry_type" value="{{ $entry->entry_type }}">
		<div class="container">
			<div class="content">

				<h2>Edit {{ ucwords(str_replace('-', ' ',request('entry_type'))) }} Entry <a class="headline-btn" href="/admin/entry?entry_type={{ $entry->entry_type }}" role="button">Create {{ ucwords(str_replace('-', ' ',request('entry_type'))) }} Entry</a></h2>

					@method('PUT')
					@csrf
					<div class="form-row">
						<label class="label-col" for="title">Title</label>
						<div class="input-col">
							<input id="title" class="to-slug" type="text" name="title" autocomplete="off" value="{{ old('title', $entry->title) }}">
						</div>
					</div>

					<div class="form-row">
						<label class="label-col" for="slug">Slug</label>
						<div class="input-col">
							<input id="slug" class="slug-input" type="text" name="slug" value="{{ old('slug', $entry->slug) }}">
						</div>
					</div>

                   @if( $entry->editor_type === 'B' )
                    <div class="form-row">
                        <label class="label-col align-top full-width" for="content">Block Editor</label>
                        <div class="input-col full-width">
                            <div id="block-editor" style="width: 100%;">
                                <input type="hidden" name="block_content" v-model="blockContent">
                                <div class="initial-blocks" v-if="!currentBlocks.length" v-cloak>
                                    <a href="#" class="block-append-btn" v-for="block in blocks" @click.prevent="appendBlock(block)" :title="block.title" role="button">
                                        <span>@{{ block.title }}</span> <i v-if="block.icon" :class="'fal fa-'+block.icon+' fa-fw'"></i>
                                    </a>
                                </div>
                                    <draggable v-if="currentBlocks.length" v-model="currentBlocks" ref="dragItem" @end="dragEnd()" @update="dragUpdate()" @start="dragStart()" class="blocks-list" group="blocks" handle=".block-drag-handle">
                                        <div v-if="currentBlocks.length > 0" v-for="(block, blockIndex) in currentBlocks" :key="block.id" class="block-item" v-cloak>
                                            <div class="block-chooser-block">
                                                <div class="inner">
                                                    <a class="choose-block-btn" href="#" @click.prevent="showBlockPicker = !showBlockPicker" role="button">
                                                        <i class="fal fa-plus"></i>
                                                    </a>
                                                    <div class="block-chooser" v-if="showBlockPicker" v-cloak>
                                                        <a href="#" class="block-append-btn" v-for="newblock in blocks" @click.prevent="appendBlock(newblock, blockIndex)" :title="newblock.title" role="button">
                                                           <span>@{{ newblock.title }}</span> <i v-if="newblock.icon" :class="'fal fa-'+newblock.icon+' fa-fw'"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                            <span class="block-drag-handle"><i class="far fa-sort"></i></span>
                                            <a href="#" class="delete-block-btn" @click.prevent="removeBlock(block)"><i class="fal fa-times"></i></a>
                                            <h3 class="block-title">@{{ block.title }}
                                                <div class="b-title-inner">
                                                    <a v-if="block.group" href="#" class="append-block-group-btn" @click.prevent="addBlockGroup(block)" role="button">
                                                        <i class="fal fa-plus"></i>
                                                    </a>
                                                    <block-options v-if="block.group" :block="block" :block-index="blockIndex"></block-options>
                                                </div>
                                            </h3>
                                            <div class="block-template" :class="{ grouped: block.group }">
                                                <tinymce-editor v-if="block.contentEditable && !block.template && block.tag" v-model="block.value" :init="tinyInitInlineParagraph" :tag-name="block.tag"></tinymce-editor>
                                                <tinymce-editor v-if="block.contentEditable && !block.template && !block.tag" v-model="block.value" ref="editor" :init="tinyInitInlineFreeText"></tinymce-editor>
                                                <keep-alive>
                                                    <component v-if="!block.contentEditable" :block="block" :block-index="blockIndex" v-bind:is="block.name" />
                                                </keep-alive>
                                            </div>
                                        </div>
                                    </draggable>
                            </div>
                        </div>
                    </div>
                    @else
					<div class="form-row">
						<label class="label-col align-top full-width" for="content">Content
							<a class="restore-content-btn" href="#" role="button">
								<i class="fal fa-file-download"></i> Restore Content
							</a>
							<div class="restore-content-modal">
								<ul class="restore-content-list">
									@foreach( $backups as $backup )
									<li><a href="#" data-backup-id="{{$backup->id}}" role="button">Backup - {{ $backup->updated_at->format('M d, Y g:i:s a') }}</a></li>
									@endforeach
								</ul>
							</div>
						</label>
						<div class="input-col full-width">
							<textarea id="content" class="editor" data-fm-backup name="content">{!! old('content', $entry->content) !!}</textarea>
						</div>
					</div>
                    @endif

                    <div class="form-row">
                        <label class="label-col align-top full-width" for="short_content">Content Excerpt</label>
                        <div class="input-col full-width">
                            <div class="tiny-mce-wrapper">
                                <textarea id="short-content" class="small-editor" name="short_content">{!! old('short_content', $entry->short_content) !!}</textarea>
                            </div>
                        </div>
                    </div>

					@foreach( $field_groups as $group )
					<h2 class="cf-group-title">{{ $group->title }}</h2>
						@if( $group->description )
							<p>{{ $group->description }}</p>
						@endif
						@foreach( $group->fields as $field )
						{!! _generateField($field, 'entry', $entry->id) !!}
						@endforeach
					@endforeach

                    <h3 class="cf-group-title">SEO</h3>

                    <div class="form-row">
                        <label class="label-col" for="seo-title">SEO Title</label>
                        <div class="input-col">
                            <input id="seo-title" type="text" name="seo_title" value="{{ old('seo_title', $entry->seo_title) }}">
                        </div>
                    </div>

					<div class="form-row">
						<label class="label-col" for="keywords">Keywords</label>
						<div class="input-col">
							<input id="keywords" type="text" name="keywords" value="{{ old('keywords', $entry->keywords) }}">
						</div>
					</div>

					<div class="form-row">
						<label class="label-col" for="meta_desc">Meta Description</label>
						<div class="input-col">
							<input id="meta-desc" type="text" name="meta_desc" value="{{ old('meta_desc', $entry->meta_description) }}">
						</div>
					</div>

                    <div class="form-row">
                        <div class="label-col full-width">Sitemap</div>
                        <div class="input-cols">
                            <div class="input-col">
                                <label for="sitemap-change">Change Frequency</label>
                                <div class="select-wrapper">
                                    <select id="sitemap-change" name="sitemap_change">
                                        <option value=""></option>
                                        <option value="always" {{ $entry->sitemap_change === 'always'? 'selected="selected"' : '' }}>Always</option>
                                        <option value="hourly" {{ $entry->sitemap_change === 'hourly'? 'selected="selected"' : '' }}>Hourly</option>
                                        <option value="daily" {{ $entry->sitemap_change === 'daily'? 'selected="selected"' : '' }}>Daily</option>
                                        <option value="weekly" {{ $entry->sitemap_change === 'weekly'? 'selected="selected"' : '' }}>Weekly</option>
                                        <option value="monthly" {{ $entry->sitemap_change === 'monthly'? 'selected="selected"' : '' }}>Monthly</option>
                                        <option value="yearly" {{ $entry->sitemap_change === 'yearly'? 'selected="selected"' : '' }}>Yearly</option>
                                        <option value="never" {{ $entry->sitemap_change === 'never'? 'selected="selected"' : '' }}>Never</option>
                                    </select>
                                </div>
                            </div>
                            <div class="input-col">
                                <label for="sitemap-priority">Priority (0.1 - 1.0)</label>
                                <input id="sitemap-priority" type="number" name="sitemap_priority" value="{{ $entry->sitemap_priority }}">
                            </div>
                        </div>
                    </div>

			</div>

			<aside class="sidebar">
				<div class="side-fields">
					<div class="form-row">
						<label class="label-col" for="entry-status">Status</label>
						<div class="input-col">
    						<div class="select-wrapper">
    							<select id="entry-status" name="status">
    								<option value="P" {{ $entry->status === 'P'? 'selected="selected"' : '' }}>Publish</option>
    								<option value="D" {{ $entry->status === 'D'? 'selected="selected"' : '' }}>Draft</option>
    							</select>
							</div>
						</div>
					</div>

                    <div id="publish-date-toggle" class="form-row" style="{{  $entry->status === 'P'? 'display: block' : '' }}">
                        <label class="label-col" for="publish-date">Publish Date</label>
                        <div class="input-col">
                            <input id="publish-date" class="datetime-picker" type="text" name="publish_date" value="{{ $entry->publish_date->timezone( config('neutrino.timezone') )->format('Y-m-d g:i a') }}" readonly>
                            <span class="note">Will default to now if not set.</span>
                        </div>
                    </div>

                    <div class="form-row">
                        <label class="label-col" for="status">Editor Type
                            <div class="editor-type-options">
                                <a href="#" role="button" data-editor-type="B" class="editor-type-option change-editor {{ $entry->editor_type === 'B' ? 'active' : '' }}">
                                    <i class="fal fa-tasks-alt fa-fw"></i>
                                    <span class="active-bull">&bull;</span>
                                    <div class="tooltip">
                                        <div class="inner">Block</div>
                                    </div>
                                    <input type="radio" name="editor_type" {{ $entry->editor_type === 'B' ? 'checked="checked"' : '' }} value="B">
                                </a>
                                <a href="#" role="button" data-editor-type="C" class="editor-type-option change-editor {{ $entry->editor_type === 'C'? 'active' : '' }}">
                                    <i class="fal fa-window-maximize"></i>
                                    <span class="active-bull">&bull;</span>
                                    <div class="tooltip">
                                        <div class="inner">Legacy</div>
                                    </div>
                                    <input type="radio" name="editor_type" {{ $entry->editor_type === 'C' ? 'checked="checked"' : '' }} value="C">
                                </a>
                            </div>
                        </label>
                    </div>

                    @php
                    $templates = _getTemplates('entry');
                    @endphp
                    @if( count($templates) > 0 )
                    <div class="form-row">
                        <label class="label-col" for="template">{{ ucwords(str_replace('-','',request('entry_type'))) }} Template</label>
                        <div class="input-col">
                            <div class="select-wrapper">
                                <select id="template" name="template">
                                    <option value=""></option>
                                    @foreach( $templates as $template )
                                    <option value="{{ $template['filename'] }}" {{ $entry->template === $template['filename']? 'selected="selected"' : '' }}>{{$template['name']}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    @endif

					<div class="form-row">
						<label class="label-col" for="protected">Protected</label>
						<div class="input-col">
                            <select id="protected" name="protected[]" style="height: 100px" multiple>
                                @foreach($roles as $role)
                                <option value="{{ $role->name }}" {{ $entry->protected->contains($role->name)? 'selected="selected"' : '' }}>{{ $role->display_name }}</option>
                                @endforeach
    				        </select>
                            <span class="note">Ctrl/Command+click to select/unselect</span>
						</div>
					</div>

					<div class="form-row">
						<label class="label-col" for="allow-comments">Allow Comments</label>
						<div class="input-col">
							<label><input id="allow-comments" type="checkbox" name="allow_comments" value="1" {{ $entry->allow_comments? 'checked="checked"' : '' }}> Allow</label>
						</div>
					</div>

					@php $taxGroups = _getTaxonomyGroups(request('entry_type')) @endphp
					@foreach( $taxGroups as $taxGroup )
					<div class="form-row">
						<label class="label-col" for="{{ $taxGroup->slug }}">{{ str_plural($taxGroup->title) }}</label>
						<div class="input-col">
							<input type="text" name="tax_new[{{ $taxGroup->id }}]" placeholder="New {{ $taxGroup->title }}" style="margin-bottom: 4px">
							<div class="term-group-select">
								@foreach( $taxGroup->terms as $term )
								<label><input type="checkbox" class="object-term-checkbox" data-entry-id="{{ $entry->id }}" data-object-type="entry" data-taxonomy-type="{{ $taxGroup->id }}" data-term-id="{{ $term->id }}" name="taxes[{{ $taxGroup->id }}][]" value="{{ $term->id }}" {{ $terms->contains('taxonomy_id', $term->id) ? 'checked="checked"' : '' }}> <span>{{ $term->title }}</span></label>
								@endforeach
							</div>
						</div>
					</div>
					@endforeach

					<div class="form-row">
						<label class="label-col">Featured Image
							<a class="lfm-featured-image" data-input="featured-image" data-preview="featured-image-preview">
								<i class="fas fa-image"></i> Choose
							</a>
						</label>
						<div class="input-col">
							<input id="featured-image" class="file-list-input" value="{{ $entry->featuredImage? $entry->featuredImage->file_path : '' }}" type="text" name="featured_image">
							<div id="featured-image-preview" class="featured-image-preview">
								<img class="lfm-preview-image" src="{{ $entry->featuredImage? $entry->featuredImage->file_path : '' }}" style="height: 160px;">
								@if($entry->featuredImage)
								<a class="clear-featured-image" href="/">&times;</a>
								@endif
							</div>
						</div>
					</div>

					<div class="form-row">
						<label class="label-col">Social Image
							<a class="lfm-social-image" data-input="social-image" data-preview="social-image-preview">
								<i class="fas fa-image"></i> Choose
							</a>
						</label>
						<div class="input-col">
							<input id="social-image" class="file-list-input" value="{{ $entry->social_image? $entry->social_image : '' }}" type="text" name="social_image">
							<div id="social-image-preview" class="featured-image-preview">
								<img class="lfm-preview-image" src="{{ $entry->social_image? $entry->social_image : '' }}" style="height: 160px;">
								@if($entry->social_image)
								<a class="clear-social-image" href="/">&times;</a>
								@endif
							</div>
						</div>
					</div>

					<div class="form-actions">
						<button type="submit" class="btn form-btn">Save</button>
					</div>
				</div>
			</aside>
		</div>
	</form>
@endsection

@section('js')
<script>
window.object_user_edit = { object_type: 'entry', id: <?php echo $entry->id ?>, user_id: <?php echo auth()->user()->id; ?>, user_name: '<?php echo auth()->user()->name; ?>' };
window.editorStyles = <?php echo json_encode(config('neutrino.editor_styles')) ?>;
window.editorCss = '<?php echo getEditorCss(); ?>';
window.blocks = <?php echo getBlocks() ?>;
window.currentBlocks = <?php echo $entry->block_content; ?>;
</script>
@endsection
