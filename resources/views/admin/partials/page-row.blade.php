<tr>
    <td data-label="Title">
        <div class="object-edit-wrapper">
            <a href="/admin/page/{{ $page->id }}">{{ $page->title }}</a>
            @if( $page->system_page )
            <br>
            <span class="system-page">system page</span>
            @endif
            <div class="object-editing {{ $page->editing && $page->editing->object_id === $page->id? '' : 'hide' }}" data-editing-object-type="page" data-editing-object-id="{{ $page->id }}">
                @if( $page->editing && $page->editing->object_id === $page->id )
                {{ $page->editing->user->name }} is currently editing.
                @endif
            </div>
        </div>
    </td>
    <td data-label="Status" class="text-center">
        {{ _translateStatus($page->status) }}
    </td>
    <td data-label="Parent">
    @if( $page->parent )
        {{ $page->parent->title }}
    @endif
    </td>
    <td data-label="Created by" class="center">{{ $page->createdUser ? $page->createdUser->name : '' }}</td>
    <td data-label="Updated by" class="center">{{ $page->updatedUser ? $page->updatedUser->name : '' }}</td>
    <td>
    @if( $page->protected )
        <i class="fal fa-lock"></i>
    @endif
    </td>
    <td data-label="Delete" class="text-center">
        <form class="delete-form" action="/admin/pages/{{ $page->id }}" method="post">
            @method('delete')
            @csrf
            <button type="submit" class="delete-btn">&times;</button>
        </form>
    </td>

</tr>
@if( $page->children->count() )
</tbody>
<tbody class="table-row-children">
    @each('neutrino::admin.partials.page-row', $page->children->sortBy('title'), 'page')
</tbody>
<tbody>
@endif

