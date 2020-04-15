@extends('neutrino::admin.template.header-footer')
@section('title', 'Create Page | ')
@section('content')
<form action="/admin/pages" method="post" enctype="multipart/form-data">

		<div class="container">
			<div class="content">

				<h2>Create Page</h2>
					@csrf
					<div class="form-row">
						<label class="label-col" for="title">Title</label>
						<div class="input-col">
							<input id="title" class="to-slug" type="text" name="title" value="{{ old('title') }}" autocomplete="off" required>
						</div>
					</div>

					<div class="form-row">
						<label class="label-col" for="slug">Slug</label>
						<div class="input-col">
							<input id="slug" class="slug-input" type="text" name="slug" value="{{ old('slug') }}" required>
						</div>
					</div>

                    @if( request('editor_type') !== 'C' )
                    <div class="form-row">
                        <label class="label-col align-top full-width" for="content">Block Editor</label>
                        <div class="input-col full-width">
                            <div id="block-editor" style="width: 100%;">
                                <input type="hidden" name="block_content" v-model="blockContent">
                                <div class="initial-blocks" v-if="currentBlocks.length === 0" v-cloak>
                                    <a href="#" class="block-append-btn" v-for="block in blocks" @click.prevent="appendBlock(block)" :title="block.title" role="button">
                                        <span>@{{ block.title }}</span> <i v-if="block.icon" :class="'fal fa-'+block.icon+' fa-fw'"></i>
                                    </a>
                                </div>
                                <div v-if="currentBlocks.length > 0" style="width: 100%" v-cloak>
                                    <draggable v-model="currentBlocks" class="blocks-list" group="blocks" handle=".block-drag-handle">
                                        <div v-if="currentBlocks.length > 0" v-for="(block, blockIndex) in currentBlocks" :key="block.id" class="block-item" v-cloak>
                                            <div class="block-chooser-block">
                                                <div class="inner">
                                                    <a class="choose-block-btn" href="#" @click.prevent="showBlockPicker = !showBlockPicker" role="button">
                                                        <i class="fal fa-plus"></i>
                                                    </a>
                                                    <div class="block-chooser" v-on-clickaway="closeBlockChooser" v-if="showBlockPicker" v-cloak>
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
                                                <keep-alive>
                                                    <tinymce-editor v-if="block.contentEditable && !block.template && block.tag" v-model="block.value" :init="tinyInitInlineParagraph" :tag-name="block.tag"></tinymce-editor>
                                                </keep-alive>
                                                <keep-alive>
                                                    <tinymce-editor v-if="block.contentEditable && !block.template && !block.tag" v-model="block.value" :init="tinyInitInlineFreeText"></tinymce-editor>
                                                </keep-alive>
                                                <keep-alive>
                                                    <component v-if="!block.contentEditable" :block="block" :block-index="blockIndex" v-bind:is="block.name" />
                                                </keep-alive>
                                            </div>
                                        </div>
                                    </draggable>
                                </div>
                            </div>
                        </div>
                    </div>
                    @else
                    <div class="form-row">
                        <label class="label-col align-top full-width" for="content">Content</label>
                        <div class="input-col full-width">
                            <textarea name="content" class="editor">{{ old('content') }}</textarea>
                        </div>
                    </div>
                    @endif

                    <div class="form-row">
                        <label class="label-col align-top full-width" for="short_content">Content Excerpt</label>
                        <div class="input-col full-width">
                            <div class="tiny-mce-wrapper">
                                <textarea id="short-content" class="small-editor" name="short_content">{!! old('short_content') !!}</textarea>
                            </div>
                        </div>
                    </div>

					<div class="form-row">
						<label class="label-col" for="keywords">Keywords</label>
						<div class="input-col">
							<input id="keywords" type="text" name="keywords" value="{{ old('keywords') }}">
						</div>
					</div>

					<div class="form-row">
						<label class="label-col" for="meta-desc">Meta Description</label>
						<div class="input-col">
							<input id="meta-desc" type="text" name="meta_description" value="{{ old('meta_description') }}">
						</div>
					</div>

					@foreach( $field_groups as $group )
					<h2 class="cf-group-title">{{ $group->title }}</h2>
						@if( $group->description )
							<p>{{ $group->description }}</p>
						@endif
						@foreach( $group->fields as $field )
						{!! _generateField($field) !!}
						@endforeach
					@endforeach

			</div>

			<aside class="sidebar">
				<div class="side-fields">
					<div class="form-row">
						<label class="label-col" for="status">Status</label>
						<div class="input-col">
    						<div class="select-wrapper">
    							<select id="status" name="status">
    								<option value="P" {{ old('status') === 'P'? 'selected="selected"' : '' }}>Publish</option>
    								<option value="D" {{ old('status') === 'D'? 'selected="selected"' : '' }}>Draft</option>
    							</select>
    						</div>
						</div>
					</div>

                    <div class="form-row">
                        <label class="label-col" for="status">Editor Type
                            <div class="editor-type-options">
                                <a href="?editor_type=B" class="editor-type-option change-editor {{ request('editor_type') !== 'C' ? 'active' : '' }}">
                                    <i class="fal fa-tasks-alt fa-fw"></i>
                                    <span class="active-bull">&bull;</span>
                                    <div class="tooltip">
                                        <div class="inner">Block</div>
                                    </div>
                                    <input type="radio" name="editor_type" {{ !request('editor_type') || request('editor_type') === 'B' ? 'checked="checked"' : '' }} value="B">
                                </a>
                                <a href="?editor_type=C" class="editor-type-option change-editor {{ request('editor_type') === 'C'? 'active' : '' }}">
                                    <i class="fal fa-window-maximize"></i>
                                    <span class="active-bull">&bull;</span>
                                    <div class="tooltip">
                                        <div class="inner">Legacy</div>
                                    </div>
                                    <input type="radio" name="editor_type" {{ request('editor_type') === 'C' ? 'checked="checked"' : '' }} value="C">
                                </a>
                            </div>
                        </label>
                    </div>

					<div class="form-row">
						<label class="label-col" for="protected">Protected</label>
						<div class="input-col">
                            <select id="protected" name="protected[]" style="height: 100px" multiple>
                                @foreach($roles as $role)
                                <option value="{{ $role->name }}">{{ $role->display_name }}</option>
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
    								{!! _getPageList() !!}
    							</select>
    						</div>
						</div>
					</div>

					<div class="form-row">
						<label class="label-col">Featured Image
							<a class="lfm-featured-image" data-input="featured-image" data-multiple="0" data-preview="featured-image-preview">
								<i class="fas fa-image"></i> Choose
							</a>
						</label>
						<div class="input-col">
							<input id="featured-image" class="file-list-input" value="" type="text" name="featured_image">
							<div id="featured-image-preview" class="featured-image-preview"></div>
						</div>
					</div>

					<div class="form-row">
						<label class="label-col">Social Image
							<a class="lfm-social-image" data-input="social-image" data-preview="social-image-preview">
								<i class="fas fa-image"></i> Choose
							</a>
						</label>
						<div class="input-col">
							<input id="social-image" class="file-list-input" value="" type="text" name="social_image">
							<div id="social-image-preview" class="featured-image-preview"></div>
						</div>
					</div>

                    <div class="form-actions">
                        <button id="save-button" type="submit" class="btn form-btn">Create</button>
                    </div>

				</div>
			</aside>
		</div>
	</form>
@endsection

@section('js')
<script>
window.editorCss = '<?php echo getEditorCss(); ?>';
window.blocks = <?php echo getBlocks() ?>;
window.currentBlocks = [];
</script>
@endsection
