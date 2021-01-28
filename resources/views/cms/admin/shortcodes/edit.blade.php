@extends('neutrino::admin.template.header-footer')
@section('title', 'Shortcodes | ')
@section('content')
<form action="/admin/shortcodes/{{ $shortcode->id }}" method="post">
@csrf
@method('put')
    <div class="container">

        <div class="content">
            <h2>Edit Shortcode</h2>

            <p>
            <a href="/admin/shortcodes">&larr; Back</a>
            </p>

            <p>
            <strong>Shortcode:</strong> [{{ $shortcode->slug }}]
            </p>

            <div class="form-row">
                <label class="label-col" for="title">Shortcode Title</label>
                <div class="input-col">
                    <input id="title" type="text" name="title" value="{{ $shortcode->title }}" required>
                </div>
            </div>

            <div class="form-row">
                <label class="label-col" for="title">Code</label>
                <div class="input-col">
                    <textarea id="embed" name="embed">{!! html_entity_decode($shortcode->embed) !!}</textarea>
                </div>
            </div>

            </form>

        </div>

        <aside class="sidebar">

            <button type="submit" class="btn full">Update Shortcode</button>

        </aside>

    </div>
</form>
@endsection
