<div class="dashboard-col">
	<div class="dashboard-card">
		<div class="d-card-header">
			<h3>Quick Actions</h3>
		</div>
		<div class="d-card-body">
			<div class="quick-action-btns">
				<a href="/admin/page" class="btn">New Page</a>
				@foreach( $entry_types as $entry_type )
				<a href="/admin/entries?entry_type={{$entry_type->slug}}" class="btn">New {{ $entry_type->entry_type }}</a>
				@endforeach
				<a href="/admin/event" class="btn">New Event</a>
				<a href="/admin/form" class="btn">New Form</a>
			</div>
		</div>
	</div>
</div>
