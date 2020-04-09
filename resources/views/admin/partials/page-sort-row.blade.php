<li class="page-item" data-id="{{ $page->id }}">
    <i class="fal fa-sort"></i>
    <span class="sort-page-title">{{ $page->title }}</span>
    @if( $page->parent )
        <span class="sort-page-parent">{{ $page->parent->title }}</span>
    @endif
    @if( $page->children->count() )
    <ul class="sort-pages-children-list">
    @each('neutrino::admin.partials.page-sort-row', $page->children->sortBy('sort'), 'page')
    </ul>
    @endif
</li>


