@extends('neutrino::admin.template.header-footer')
@section('title', 'Menu Items | ')

@section('content')
	<div class="container">
		<div class="content">
			<h2>{{ $menu->name }} Menu Items</h2>
			<input type="hidden" id="menu-id" value="{{ $menu->id }}">
			<div class="menu-builder-panes">
				<div class="current-menu-items">
					<ul class="menu-items-drop">
						{!! _buildMenuList($menu_items) !!}
					</ul>
				</div>

				<div class="choose-menu-items">
					<h4>Add Menu Item</h4>
					<div class="form-row">
						<label class="label-col" for="item-name">Title</label>
						<div class="input-col">
							<input id="item-name" autocomplete="off" type="text">
						</div>
					</div>
					<div class="form-row">
						<label class="label-col" for="item-type">Type</label>
						<div class="input-col">
    						<div class="select-wrapper">
    							<select id="item-type">
    								<option value="">Choose &hellip;</option>
    								<option value="url">URL</option>
    								<option value="page">Page</option>
    								<option value="entry_type">Entry Type</option>
    								<option value="entry">Entry</option>
    								<option value="taxonomy">Taxonomy</option>
    								<option value="taxonomy_term">Taxonomy Term</option>
    								<option value="file">File</option>
    							</select>
    						</div>
						</div>
					</div>
					<div id="item-url-group" class="form-row menu-item-type">
						<label class="label-col"  for="item-url">URL</label>
						<div class="input-col">
							<input id="item-url" autocomplete="off" type="text" placeholder="/">
						</div>
					</div>
					<div id="item-page-group" class="form-row menu-item-type">
						<label class="label-col" for="item-page">Page</label>
						<div class="input-col">
    						<div class="select-wrapper">
    							<select id="item-page">
    								<option value="">Choose &hellip;</option>
    								@foreach( $pages as $page )
    								<option value="{{ $page->url() }}">{!! $page->title !!}</option>
    								@endforeach
    							</select>
    						</div>
						</div>
					</div>
					<div id="item-entry_type-group" class="form-row menu-item-type">
						<label class="label-col" for="item-entry-type">Entry Type</label>
						<div class="input-col">
    						<div class="select-wrapper">
    							<select id="item-entry-type">
    								<option value="">Choose &hellip;</option>
    								@foreach( $entry_types as $entryType )
    								<option value="{{ $entryType->slug }}">{{ $entryType->entry_type }}</option>
    								@endforeach
    							</select>
    						</div>
						</div>
					</div>
					<div id="item-entry-group" class="form-row menu-item-type">
						<label class="label-col" for="item-entry">Entry</label>
						<div class="input-col">
    						<div class="select-wrapper">
    							<select id="item-entry">
    								<option value="">Choose &hellip;</option>
    								@foreach( $entries as $entry )
    								<option value="{{ $entry->entry_type }}:{{$entry->slug}}">{{ $entry->entry_type }}: {!! $entry->title !!}</option>
    								@endforeach
    							</select>
    						</div>
						</div>
					</div>
					<div id="item-taxonomy-group" class="form-row menu-item-type">
						<label class="label-col" for="item-taxonomy">Taxonomy</label>
						<div class="input-col">
    						<div class="select-wrapper">
    							<select id="item-taxonomy">
    								<option value="">Choose &hellip;</option>
    								@foreach( $taxonomies as $tax )
    								<option value="/{{ $tax->slug }}">{!! $tax->title !!}</option>
    								@endforeach
    							</select>
    						</div>
						</div>
					</div>
					<div id="item-taxonomy-id-group" class="form-row menu-item-type">
						<label class="label-col" for="item-taxonomy">Taxonomy</label>
						<div class="input-col">
    						<div class="select-wrapper">
    							<select id="item-taxonomy-id">
    								<option value="">Choose &hellip;</option>
    								@foreach( $taxonomies as $tax )
    								<option value="{{ $tax->id }}">{!! $tax->title !!}</option>
    								@endforeach
    							</select>
    						</div>
						</div>
					</div>
					<div id="item-taxonomy_term-group" class="form-row menu-item-type">
						<label class="label-col" for="item-taxonomy-term">Terms</label>
						<div class="input-col">
    						<div class="select-wrapper">
    							<select id="item-taxonomy-term" disabled>
    								<option value="">Choose &hellip;</option>
    							</select>
    						</div>
						</div>
					</div>
					<div id="item-file-group" class="form-row menu-item-type">
						<label class="label-col" for="item-file">File</label>
						<div class="input-col">
							<a id="item-file" data-input="item-input-file">
								<i class="fas fa-file"></i> Choose File
							</a>
							<input type="text" id="item-input-file" readonly>
						</div>
					</div>
					<div class="form-row">
						<label class="label-col" for="item-target">Target</label>
						<div class="input-col">
    						<div class="select-wrapper">
    							<select id="item-target">
    								<option value="self">Self (Normal)</option>
    								<option value="blank">Blank ( New tab )</option>
    							</select>
    						</div>
						</div>
					</div>
					<button id="add-menu-item-btn" type="button" class="btn full" style="margin-bottom: 24px;">Add Menu Item</button>
				</div>
			</div>

		</div>

		<aside class="sidebar">

			<form action="/admin/menus/{{$menu->id}}" method="post">
				@csrf
				<div class="side-fields">
					<div class="form-row">
						<label class="label-col" for="menu-name">Menu Name</label>
						<div class="input-col">
							<input id="menu-name" type="text" name="name" value="{{ old('name', $menu->name) }}">
						</div>
					</div>

					<button type="submit" class="btn full" style="margin-bottom: 24px;">Edit Menu Name</button>
				</div>
			</form>

		</aside>

	</div>
@endsection
