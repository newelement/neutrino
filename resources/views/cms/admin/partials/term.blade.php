
<li class="tax-item" data-id="{{ $tax['id'] }}">
    <div class="term-list-inner">
        <span class="tax-sort-handle"><i class="fal fa-sort"></i></span>
        <a href="/admin/taxonomies/{{ $tax['taxonomy_id'] }}/{{ $tax['id'] }}">{{ $tax['title'] }}</a>
            <span class="term-slug-badge">slug: {{ $tax['slug'] }}</span>
        @if( $tax['slug'] !== 'category' )
        <form action="/admin/taxonomies/{{ $tax['taxonomy_id'] }}/{{$tax['id']}}" method="post">
            @csrf
            @method('delete')
            <button type="submit" class="delete-btn">&times;</button>
        </form>
        @endif
    </div>

    @if ($tax['children'])
        <ul class="term-children">
            @each('neutrino::admin.partials.term', $tax['children'], 'tax')
        </ul>
    @endif
</li>

