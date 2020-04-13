@extends('neutrino::admin.template.header-footer')
@section('title', ucwords(request('entry_type')).' | ')
@section('content')
	<div class="container">
		<div class="content full">
			<div class="title-search">
				<h2>{{ str_plural($entry_type->entry_type) }} <a class="headline-btn" href="/admin/entry?entry_type={{ request('entry_type') }}" role="button">Create New {{ ucwords(request('entry_type')) }}</a></h2>
				<div class="object-search" style="padding-top: 6px;">
					<form class="search-form" action="{{url()->full()}}" method="get">
						<input type="text" name="s" value="{{ request('s') }}" placeholder="Search {{ strtolower($entry_type->label_plural) }}" autocomplete="off">
						<input type="hidden" name="entry_type" value="{{ request('entry_type') }}">
						<button type="submit"><i class="fas fa-search"></i></button>
					</form>
				</div>
			</div>
            <div class="pages-options-row text-right">
                <a class="trash-link" href="/admin/entries-trash?entry_type={{ request('entry_type') }}"><i class="fal fa-trash-alt"></i> Trashed ({{ $trashed }})</a>
            </div>
		</div>
	</div>
    <div class="container">
        <div class="content full">
			<table cellpadding="0" cellspacing="0" class="table">
				<thead>
					<tr>
						<th class="text-left">@sortablelink('title', 'Title')</th>
						<th width="80">@sortablelink('status', 'Status')</th>
						<th width="140">Created By</th>
						<th width="140">Updated By</th>
						<th width="140">@sortablelink('created_at', 'Created On')</th>
						<th width="140">@sortablelink('updated_at', 'Updated On')</th>
						<th width="60"></th>
						<th width="50"></th>
					</tr>
				</thead>
				<tbody>
				@foreach( $entries as $entry )
					<tr>
						<td data-label="Title">
                            <div class="object-edit-wrapper">
    							<a href="/admin/entry/{{ $entry->id }}?entry_type={{ request('entry_type') }}">{{ $entry->title }}</a>
                                <div class="object-editing {{ $entry->editing && $entry->editing->object_id === $entry->id? '' : 'hide' }}" data-editing-object-type="entry" data-editing-object-id="{{ $entry->id }}">
                                    @if( $entry->editing && $entry->editing->object_id === $entry->id )
                                    {{ $entry->editing->user->name }} is currently editing.
                                    @endif
                                </div>
                            </div>
						</td>
						<td data-label="Status" class="text-center">
							{{ $entry->publish_date <= now() ? _translateStatus($entry->status) : 'Scheduled' }}
						</td>
						<td data-label="Created by" class="center"><small>{{ $entry->createdUser->name }}</small></td>
						<td data-label="Updated by" class="center"><small>{{ $entry->updatedUser->name }}</small></td>
						<td data-label="Created On">
    						<small>{{ $entry->created_at->timezone( config('neutrino.timezone') )->format('m-j-y g:i a') }}</small>
						</td>
						<td data-label="Updated On">
    						<small>{{ $entry->updated_at->timezone( config('neutrino.timezone') )->format('m-j-y g:i a') }}</small>
						</td>
						<td data-label="Protected">
    						@if( $entry->protected )
    						<i class="fal fa-lock"></i>
    						@endif
						</td>
						<td data-label="Delete">
							<form class="delete-form" action="/admin/entries/{{ $entry->id }}?entry_type={{ request('entry_type') }}" method="post">
								@method('delete')
								@csrf
								<input type="hidden" name="entry_type" value="{{ request('entry_type') }}">
								<button type="submit" class="delete-btn">&times;</button>
							</form>
						</td>
					</tr>
				@endforeach
				</tbody>
			</table>

			<div class="pagination-links">
				{{ $entries->appends($_GET)->links() }}
			</div>
        </div>
    </div>

@endsection

@section('js')
<script>
window.object_type = 'entry';
</script>
@endsection
