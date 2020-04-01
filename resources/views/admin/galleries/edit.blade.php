@extends('neutrino::admin.template.header-footer')
@section('title', 'Edit Gallery | ')
@section('content')
<form action="/admin/galleries/{{ $gallery->id }}" method="post">
    @csrf
    @method('put')
    <div class="container">
        <div class="content">
            <h2>Edit Gallery</h2>

            <div class="form-row">
                <label class="label-col" for="title">Gallery Title</label>
                <div class="input-col">
                    <input id="title" class="to-slug" type="text" name="title" value="{{ old('title', $gallery->title) }}" autocomplete="off" required>
                </div>
            </div>

            <div class="form-row">
                <label class="label-col" for="slug">Slug</label>
                <div class="input-col">
                    <input id="slug" class="slug-input" type="text" name="slug" value="{{ old('slug', $gallery->slug) }}" required>
                </div>
            </div>

            <div class="form-row">
                <label class="label-col align-top full-width" for="description">Description</label>
                <div class="input-col full-width">
                    <textarea class="small-editor" id="description" name="description">{!! old('description', $gallery->description) !!}</textarea>
                </div>
            </div>

            <div class="form-row">
                <label class="label-col" for="theme">Theme</label>
                <div class="input-col">
                    <div class="select-wrapper">
                        <select id="theme" name="theme">
                            <option value="carousel" {{ $gallery->theme === 'carousel' ? 'selected="selected"' : '' }}>Carousel</option>
                            <option value="masonry" {{ $gallery->theme === 'masonry' ? 'selected="selected"' : '' }}>Masonry</option>
                            <option value="grid" {{ $gallery->theme === 'grid' ? 'selected="selected"' : '' }}>Grid</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="form-row">
                <label class="label-col full-width">Gallery
                    <a class="lfm-gallery-images" style="margin-left: auto">
                        <i class="fas fa-image"></i> Choose Images
                    </a>
                </label>
                <ul id="gallery-images-list" class="gallery-images-list">
                @foreach( $gallery->images as $image )
                    <li class="gallery-item" data-id="{{ $image->id }}">
                        <span class="gal-sort-handle"><i class="fal fa-sort"></i></span>
                        <div class="gallery-image-item"><img src="{{ $image->image_path }}" alt="gallery image"><input type="hidden" name="gallery_items[{{ $image->id }}][image]" value="{{ $image->image_path }}"></div>
                        <div class="form-grid-col"><label for="title-{{ $image->id }}">Title</label><div class="input-grid-col"><input id="title-{{ $image->id }}" name="gallery_items[{{ $image->id }}][title]" type="text" value="{{ $image->title }}"></div></div>
                        <div class="form-grid-col"><label for="caption-{{ $image->id }}">Caption</label><div class="input-grid-col"><textarea id="caption-{{ $image->id }}" name="gallery_items[{{ $image->id }}][caption]">{{ $image->caption }}</textarea></div></div>
                        <div class="form-grid-col"><label for="featured-{{ $image->id }}">Featured</label><div class="input-grid-col"><label><input id="featured-{{ $image->id }}" name="gallery_items[{{ $image->id }}][featured]" type="checkbox" {{ $image->featured? 'checked="checked"' : '' }}> Yes</label></div></div>
                        <div class="remove-gallery-item"><a class="remove-gallery-item-btn" data-id="{{ $image->id }}" href="#">&times;</a></div>
                    </li>
                @endforeach
                </ul>
            </div>

        </div>

        <aside class="sidebar">

            <div class="side-fields">
                <button type="submit" class="btn full">Save Gallery</button>
            </div>

        </aside>

    </div>
</form>
@endsection

@section('js')
<script>
window.editorCss = '<?php echo getEditorCss(); ?>';
</script>
@endsection
