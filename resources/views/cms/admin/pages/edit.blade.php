@extends('neutrino::admin.template.header-footer')
@section('title', 'Edit Page | ')
@section('content')
	<form  id="object-form" action="/admin/page/{{ $page->id }}" method="post" enctype="multipart/form-data">
		<input type="hidden" id="object-id" value="{{ $page->id }}">
		<input type="hidden" id="object-type" value="page">
		<div class="container">
			<div class="content">

				<h2>Edit Page <a class="headline-btn" href="/admin/page" role="button">Create Page</a></h2>

                    @if( $page->system_page )
                    <div class="system-page-warning">Warning. This page is being used by a package in Neutrino.</div>
                    @endif

					@method('PUT')
					@csrf
					<div class="form-row">
						<label class="label-col" for="title">Title</label>
						<div class="input-col">
							<input id="title" type="text" name="title" autocomplete="off" value="{{ old('title', $page->title) }}" required>
						</div>
					</div>

					<div class="form-row">
						<label class="label-col" for="slug">Slug</label>
						<div class="input-col">
							<input id="slug" type="text" name="slug" value="{{ old('slug', $page->slug) }}" {{ $page->system_page? 'readonly' : '' }} required>
						</div>
					</div>

                    @if( $page->editor_type === 'B' )
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
                            <div id="tiny-mce-wrapper">
                                <textarea id="content" class="editor" data-fm-backup name="content">{!! old('content', $page->content) !!}</textarea>
                            </div>
                        </div>
                    </div>
                    @endif

                    <div class="form-row">
                        <label class="label-col align-top full-width" for="short_content">Content Excerpt</label>
                        <div class="input-col full-width">
                            <div class="tiny-mce-wrapper">
                                <textarea id="short-content" class="small-editor" name="short_content">{!! old('short_content', $page->short_content) !!}</textarea>
                            </div>
                        </div>
                    </div>

                    @foreach( $field_groups as $group )
                    <h2 class="cf-group-title">{{ $group->title }}</h2>
                        @if( $group->description )
                            <p>{{ $group->description }}</p>
                        @endif
                        @foreach( $group->fields as $field )
                        {!! _generateField($field, 'page', $page->id) !!}
                        @endforeach
                    @endforeach

                    <h3 class="cf-group-title">SEO</h3>

                    <div class="form-row">
                        <label class="label-col" for="seo-title">SEO Title</label>
                        <div class="input-col">
                            <input id="seo-title" type="text" name="seo_title" value="{{ old('seo_title', $page->seo_title) }}">
                        </div>
                    </div>

					<div class="form-row">
						<label class="label-col" for="keywords">Keywords</label>
						<div class="input-col">
							<input id="keywords" type="text" name="keywords" value="{{ old('keywords', $page->keywords) }}">
						</div>
					</div>

					<div class="form-row">
						<label class="label-col" for="meta_desc">Meta Description</label>
						<div class="input-col">
							<input id="meta-desc" type="text" name="meta_description" value="{{ old('meta_description', $page->meta_description) }}">
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
                                        <option value="always" {{ $page->sitemap_change === 'always'? 'selected="selected"' : '' }}>Always</option>
                                        <option value="hourly" {{ $page->sitemap_change === 'hourly'? 'selected="selected"' : '' }}>Hourly</option>
                                        <option value="daily" {{ $page->sitemap_change === 'daily'? 'selected="selected"' : '' }}>Daily</option>
                                        <option value="weekly" {{ $page->sitemap_change === 'weekly'? 'selected="selected"' : '' }}>Weekly</option>
                                        <option value="monthly" {{ $page->sitemap_change === 'monthly'? 'selected="selected"' : '' }}>Monthly</option>
                                        <option value="yearly" {{ $page->sitemap_change === 'yearly'? 'selected="selected"' : '' }}>Yearly</option>
                                        <option value="never" {{ $page->sitemap_change === 'never'? 'selected="selected"' : '' }}>Never</option>
                                    </select>
                                </div>
                            </div>
                            <div class="input-col">
                                <label for="sitemap-priority">Priority (0.1 - 1.0)</label>
                                <input id="sitemap-priority" type="number" name="sitemap_priority" value="{{ $page->sitemap_priority }}">
                            </div>
                        </div>
                    </div>

			</div>

			<aside class="sidebar">
				<div class="side-fields">
					<div class="form-row">
						<label class="label-col" for="status">Status</label>
						<div class="input-col">
    						<div class="select-wrapper">
    							<select id="status" name="status">
    								<option value="P" {{ $page->status === 'P'? 'selected="selected"' : '' }}>Publish</option>
    								<option value="D" {{ $page->status === 'D'? 'selected="selected"' : '' }}>Draft</option>
    							</select>
							</div>
						</div>
					</div>

                    <div class="form-row">
                        <label class="label-col" for="status">Editor Type
                            <div class="editor-type-options">
                                <a href="#" role="button" data-editor-type="B" class="editor-type-option change-editor {{ $page->editor_type === 'B' ? 'active' : '' }}">
                                    <i class="fal fa-tasks-alt fa-fw"></i>
                                    <span class="active-bull">&bull;</span>
                                    <div class="tooltip">
                                        <div class="inner">Block</div>
                                    </div>
                                    <input type="radio" name="editor_type" {{ $page->editor_type === 'B' ? 'checked="checked"' : '' }} value="B">
                                </a>
                                <a href="#" role="button" data-editor-type="C" class="editor-type-option change-editor {{ $page->editor_type === 'C'? 'active' : '' }}">
                                    <i class="fal fa-window-maximize"></i>
                                    <span class="active-bull">&bull;</span>
                                    <div class="tooltip">
                                        <div class="inner">Legacy</div>
                                    </div>
                                    <input type="radio" name="editor_type" {{ $page->editor_type === 'C' ? 'checked="checked"' : '' }} value="C">
                                </a>
                            </div>
                        </label>
                    </div>

                    @php
                    $templates = _getTemplates();
                    @endphp
                    @if( count($templates) > 0 )
                    <div class="form-row">
                        <label class="label-col" for="template">Page Template</label>
                        <div class="input-col">
                            <div class="select-wrapper">
                                <select id="template" name="template">
                                    <option value=""></option>
                                    @foreach( $templates as $template )
                                    <option value="{{ $template['filename'] }}" {{ $page->template === $template['filename']? 'selected="selected"' : '' }}>{{$template['name']}}</option>
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
                                <option value="{{ $role->name }}" {{ $page->protected->contains($role->name)? 'selected="selected"' : '' }}>{{ $role->display_name }}</option>
                                @endforeach
    				        </select>
                            <span class="note">Ctrl/Command+click to select/unselect</span>
						</div>
					</div>

					<div class="form-row">
						<label class="label-col" for="status">Parent Page</label>
						<div class="input-col">
    						<div class="select-wrapper">
    							<select id="parent" name="parent_id">
    								<option value="0">None</option>
    								{!! _getPageList($page->id, $page->parent_id) !!}
    							</select>
    						</div>
						</div>
					</div>

					<div class="form-row">
						<label class="label-col">Featured Image
							<a class="lfm-featured-image" data-input="featured-image" data-preview="featured-image-preview">
								<i class="fas fa-image"></i> Choose
							</a>
						</label>
						<div class="input-col">
							<input id="featured-image" class="file-list-input" value="{{ $page->featuredImage? $page->featuredImage->file_path : '' }}" type="text" name="featured_image">
							<div id="featured-image-preview" class="featured-image-preview">
								<img class="lfm-preview-image" src="{{ $page->featuredImage? $page->featuredImage->file_path : '' }}" style="height: 160px;">
								@if($page->featuredImage)
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
							<input id="social-image" class="file-list-input" value="{{ $page->social_image? $page->social_image : '' }}" type="text" name="social_image">
							<div id="social-image-preview" class="featured-image-preview">
								<img class="lfm-preview-image" src="{{ $page->social_image? $page->social_image : '' }}" style="height: 160px;">
								@if($page->social_image)
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
window.object_user_edit = { object_type: 'page', id: <?php echo $page->id ?>, user_id: <?php echo auth()->user()->id; ?>, user_name: '<?php echo auth()->user()->name; ?>' };
window.editorStyles = <?php echo json_encode(config('neutrino.editor_styles')) ?>;
window.editorCss = '<?php echo getEditorCss(); ?>';
window.blocks = <?php echo getBlocks() ?>;
window.currentBlocks = <?php echo $page->block_content; ?>;
</script>
@endsection
