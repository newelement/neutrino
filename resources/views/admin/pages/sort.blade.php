@extends('neutrino::admin.template.header-footer')
@section('title', 'Sort Pages | ')
@section('content')
    <div class="container">
        <div class="content full">
            <div class="title-search">
                <h2>Sort Pages <a class="headline-btn" href="/admin/pages" role="button">&larr; Back to pages</a></h2>
            </div>
            <ul class="sort-pages-list">
                @each('neutrino::admin.partials.page-sort-row', $pages, 'page')
            </ul>
        </div>

    </div>
@endsection
