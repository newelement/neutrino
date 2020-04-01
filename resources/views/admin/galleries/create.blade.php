@extends('neutrino::admin.template.header-footer')
@section('title', 'Create Gallery | ')
@section('content')
<form action="/admin/galleries" method="post">
    @csrf
    <div class="container">
        <div class="content">
            <h2>Create Gallery</h2>

            <div class="form-row">
                <label class="label-col" for="title">Gallery Title</label>
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

            <div class="form-row">
                <label class="label-col align-top full-width" for="description">Description</label>
                <div class="input-col full-width">
                    <textarea class="small-editor" id="description" name="description">{!! old('description') !!}</textarea>
                </div>
            </div>

            <div class="form-row">
                <label class="label-col" for="theme">Theme</label>
                <div class="input-col">
                    <div class="select-wrapper">
                        <select id="theme" name="theme">
                            <option value="carousel">Carousel</option>
                            <option value="masonry">Masonry</option>
                            <option value="grid">Grid</option>
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
                </ul>
            </div>

        </div>

        <aside class="sidebar">

            <div class="side-fields">
                <button type="submit" class="btn full">Create Gallery</button>
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
