<nav>
	<ul class="sidebar-nav">
		<li @if( \Route::currentRouteName() === 'neutrino.dashboard') class="active" @endif>
		    <a href="/admin">
    		    <i class="fal fa-tachometer-alt-slow fa-fw"></i> <span class="parent-nav-title">Dashboard</span>
            </a>
        </li>
        @php
           $menuItems0 = _getMenuSlot(0);
        @endphp
        @foreach( $menuItems0 as $menuItem0 )
        <li class="@if( count($menuItem0['children']) > 0) has-dropdown @endif @if( \Route::currentRouteName() === $menuItem0['named_route']) active open @endif">
            <a href="{{ $menuItem0['url'] }}">
                <i class="fal {{ $menuItem0['fa-icon'] }} fa-fw"></i> <span class="parent-nav-title">{{ $menuItem0['parent_title'] }}</span>
            </a>
            @if( count($menuItem0['children']) > 0)
            <ul @if( \Route::currentRouteName() === $menuItem0['named_route']) class="open" @endif>
                @foreach( $menuItem0['children'] as $menuChildren0 )
                <li><a href="{{ $menuChildren0['url'] }}">{{ $menuChildren0['title'] }}</a></li>
                @endforeach
            </ul>
            @endif
        </li>
        @endforeach
		<li class="has-dropdown @if( isRouteGroup('pages.') ) active open @endif">
		    <a href="/admin/pages">
    		    <i class="fal fa-file fa-fw"></i> <span class="parent-nav-title">Pages</span>
            </a>
			<ul @if( isRouteGroup('pages.') ) class="open" @endif>
				<li><a @if( isRouteGroup('pages.all') ) class="active" @endif href="/admin/pages">All Pages</a></li>
				<li><a @if( isRouteGroup('pages.show') ) class="active" @endif href="/admin/page">Create Page</a></li>
			</ul>
		</li>
		@php
           $menuItems1 = _getMenuSlot(1);
        @endphp
        @foreach( $menuItems1 as $menuItem1 )
        <li class="@if( count($menuItem1['children']) > 0) has-dropdown @endif @if( \Route::currentRouteName() === $menuItem1['named_route']) active open @endif">
            <a href="{{ $menuItem1['url'] }}">
                <i class="fal {{ $menuItem1['fa-icon'] }} fa-fw"></i> <span class="parent-nav-title">{{ $menuItem1['parent_title'] }}</span>
            </a>
            @if( count($menuItem1['children']) > 0)
            <ul @if( \Route::currentRouteName() === $menuItem1['named_route']) class="open" @endif>
                @foreach( $menuItem1['children'] as $menuChildren1 )
                <li><a href="{{ $menuChildren1['url'] }}">{{ $menuChildren1['title'] }}</a></li>
                @endforeach
            </ul>
            @endif
        </li>
        @endforeach
		<li class="has-dropdown @if( isRouteGroup('entries.')  ) active open @endif">
			@php
			$commentCount = _getCommentCount();
			@endphp
			<a href="/admin/entries?entry_type=post">
    			<i class="fal fa-newspaper fa-fw"></i> <span class="parent-nav-title">Entries</span>
            </a>
			@php
			if( $commentCount > 0 ){
				echo '<span class="moderate-counter">'.$commentCount.'</span>';
			}
			@endphp
			<ul @if( isRouteGroup('entries.')) class="open" @endif>
				<li>
				    <a href="/admin/entries?entry_type=post">Post</a>
				</li>
				@php
				$entryTypes = _getEntryTypes();
				@endphp
				@foreach( $entryTypes as $entryType )
					<li><a href="/admin/entries?entry_type={{ $entryType->slug }}">{{ $entryType->entry_type }}</a></li>
				@endforeach
				<li><a @if( isRouteGroup('comments.all')) class="active" @endif href="/admin/comments">Comments</a></li>
				<li><a @if( isRouteGroup('comments.moderate')) class="active" @endif href="/admin/moderate-comments">Moderate Comments</a>
				@php
				if( $commentCount > 0 ){
					echo '<span class="moderate-counter">'.$commentCount.'</span>';
				}
				@endphp
				</li>
				<li><a href="/admin/entry-types">Edit Entry Types</a></li>
			</ul>
		</li>
		@php
           $menuItems2 = _getMenuSlot(2);
        @endphp
        @foreach( $menuItems2 as $menuItem2 )
        <li class="@if( count($menuItem2['children']) > 0) has-dropdown @endif @if( \Route::currentRouteName() === $menuItem2['named_route']) active open @endif">
            <a href="{{ $menuItem2['url'] }}">
                <i class="fal {{ $menuItem2['fa-icon'] }} fa-fw"></i> <span class="parent-nav-title">{{ $menuItem2['parent_title'] }}</span>
            </a>
            @if( count($menuItem2['children']) > 0)
            <ul @if( \Route::currentRouteName() === $menuItem2['named_route']) class="open" @endif>
                @foreach( $menuItem2['children'] as $menuChildren2 )
                <li><a href="{{ $menuChildren2['url'] }}">{{ $menuChildren2['title'] }}</a></li>
                @endforeach
            </ul>
            @endif
        </li>
        @endforeach
		<li class="has-dropdown @if( isRouteGroup('taxonomies.') ) active open @endif">
		    <a href="/admin/taxonomy-types">
    		    <i class="fal fa-folders fa-fw"></i> <span class="parent-nav-title">Taxonomies</span>
		    </a>
			<ul @if( isRouteGroup('taxonomies.')) class="open" @endif>
				<li><a href="/admin/taxonomies/1">Categories</a></li>
				@php $taxTypes = _getTaxonomyTypes(); @endphp
				@foreach( $taxTypes as $taxType )
				<li><a href="/admin/taxonomies/{{ $taxType->id }}">{{ str_plural($taxType->title) }}</a></li>
				@endforeach
				<li><a href="/admin/taxonomy-types">Edit Taxonomies</a></li>
			</ul>
		</li>
		@php
           $menuItems3 = _getMenuSlot(3);
        @endphp
        @foreach( $menuItems3 as $menuItem3 )
        <li class="@if( count($menuItem3['children']) > 0) has-dropdown @endif @if( \Route::currentRouteName() === $menuItem3['named_route']) active open @endif">
            <a href="{{ $menuItem3['url'] }}">
                <i class="fal {{ $menuItem3['fa-icon'] }} fa-fw"></i> <span class="parent-nav-title">{{ $menuItem3['parent_title'] }}</span>
            </a>
            @if( count($menuItem3['children']) > 0)
            <ul @if( \Route::currentRouteName() === $menuItem3['named_route']) class="open" @endif>
                @foreach( $menuItem3['children'] as $menuChildren3 )
                <li><a href="{{ $menuChildren3['url'] }}">{{ $menuChildren3['title'] }}</a></li>
                @endforeach
            </ul>
            @endif
        </li>
        @endforeach
		@if( shoppeExists() )
        @php
            $orderCount = getNewOrderCount();
        @endphp
		<li class="has-dropdown @if( isRouteGroup('shoppe.') ) active open @endif">
		    <a href="/admin/products">
    		    <i class="fal fa-shopping-cart fa-fw"></i> <span class="parent-nav-title">Products</span>
            </a>
		    <ul @if( \Route::currentRouteName() === 'shoppe.products') class="open" @endif>
    		    <li><a href="/admin/products">All Products</a></li>
    		    <li><a href="/admin/product">Create Product</a></li>
                <li><a href="/admin/product-attributes">Attributes</a></li>
		    </ul>
		</li>
		<li class="has-dropdown @if( isRouteGroup('shoppe.orders') ) active open @endif">
		    <a href="/admin/shoppe">
    		    <i class="fal fa-store fa-fw"></i> <span class="parent-nav-title">Shoppe</span>
                @if( $orderCount > 0 )
                <span class="new-order-counter parent-order-count">{{ $orderCount }}</span>
                @endif
		    </a>
		    <ul @if( \Route::currentRouteName() === 'shoppe.shoppe') class="open" @endif>
                <li><a href="/admin/shoppe">Dashboard</a>
                <li><a href="/admin/orders">Orders</a>
                @if( $orderCount > 0 )
                    <span class="new-order-counter">{{ $orderCount }}</span>
                @endif
                </li>
                <li><a href="/admin/subscriptions">Subscriptions</a>
                <li><a href="/admin/subscription-plans">Subscription Plans</a>
                <li><a href="/admin/stripe/tax-rates">Subscription Tax Rates</a>
                <li><a href="/admin/discount-codes">Discount Codes</a>
                <li><a href="/admin/shoppe-reports">Reports</a></li>
    		    <li><a href="/admin/shoppe-settings">Shoppe Settings</a></li>
		    </ul>
		</li>
		@endif
		@php
           $menuItems4 = _getMenuSlot(4);
        @endphp
        @foreach( $menuItems4 as $menuItem4 )
        <li class="@if( count($menuItem4['children']) > 0) has-dropdown @endif @if( \Route::currentRouteName() === $menuItem4['named_route']) active open @endif">
            <a href="{{ $menuItem4['url'] }}">
                <i class="fal {{ $menuItem4['fa-icon'] }} fa-fw"></i> <span class="parent-nav-title">{{ $menuItem4['parent_title'] }}</span>
            </a>
            @if( count($menuItem4['children']) > 0)
            <ul @if( \Route::currentRouteName() === $menuItem4['named_route']) class="open" @endif>
                @foreach( $menuItem4['children'] as $menuChildren4 )
                <li><a href="{{ $menuChildren4['url'] }}">{{ $menuChildren4['title'] }}</a></li>
                @endforeach
            </ul>
            @endif
        </li>
        @endforeach
		<li class="has-dropdown @if( isRouteGroup('events.') ) active open @endif">
		    <a href="/admin/events">
    		    <i class="fal fa-calendar-alt fa-fw"></i> <span class="parent-nav-title">Events</span>
            </a>
			<ul @if( isRouteGroup('events.')) class="open" @endif>
				<li><a href="/admin/events">All Events</a></li>
				<li><a href="/admin/event">Create Event</a></li>
				<li><a href="/admin/places">Places</a></li>
			</ul>
		</li>
		@php
           $menuItems5 = _getMenuSlot(5);
        @endphp
        @foreach( $menuItems5 as $menuItem5 )
        <li class="@if( count($menuItem5['children']) > 0) has-dropdown @endif @if( \Route::currentRouteName() === $menuItem5['named_route']) active open @endif">
            <a href="{{ $menuItem5['url'] }}">
                <i class="fal {{ $menuItem5['fa-icon'] }} fa-fw"></i> <span class="parent-nav-title">{{ $menuItem5['parent_title'] }}</span>
            </a>
            @if( count($menuItem5['children']) > 0)
            <ul @if( \Route::currentRouteName() === $menuItem5['named_route']) class="open" @endif>
                @foreach( $menuItem5['children'] as $menuChildren5 )
                <li><a href="{{ $menuChildren5['url'] }}">{{ $menuChildren5['title'] }}</a></li>
                @endforeach
            </ul>
            @endif
        </li>
        @endforeach
		<li class="has-dropdown @if( isRouteGroup('forms.') ) active open @endif">
		    <a href="/admin/forms">
    		    <i class="fal fa-clipboard-list-check fa-fw"></i> <span class="parent-nav-title">Forms</span>
            </a>
			<ul @if( isRouteGroup('forms.') ) class="open" @endif>
				<li><a href="/admin/forms">All Forms</a></li>
				<li><a href="/admin/form">Create Form</a></li>
			</ul>
		</li>
		@php
           $menuItems6 = _getMenuSlot(6);
        @endphp
        @foreach( $menuItems6 as $menuItem6 )
        <li class="@if( count($menuItem6['children']) > 0) has-dropdown @endif @if( \Route::currentRouteName() === $menuItem6['named_route']) active open @endif">
            <a href="{{ $menuItem6['url'] }}">
                <i class="fal {{ $menuItem6['fa-icon'] }} fa-fw"></i> <span class="parent-nav-title">{{ $menuItem6['parent_title'] }}</span>
            </a>
            @if( count($menuItem6['children']) > 0)
            <ul @if( \Route::currentRouteName() === $menuItem6['named_route']) class="open" @endif>
                @foreach( $menuItem6['children'] as $menuChildren6 )
                <li><a href="{{ $menuChildren6['url'] }}">{{ $menuChildren6['title'] }}</a></li>
                @endforeach
            </ul>
            @endif
        </li>
        @endforeach
		<li @if( \Route::currentRouteName() === 'neutrino.media') class="active" @endif>
		    <a class="toggle-file-manager" href="#">
    		    <i class="fal fa-photo-video fa-fw"></i> <span class="parent-nav-title">Media</span>
            </a>
        </li>
        @php
           $menuItems7 = _getMenuSlot(7);
        @endphp
        @foreach( $menuItems7 as $menuItem7 )
        <li class="@if( count($menuItem7['children']) > 0) has-dropdown @endif @if( \Route::currentRouteName() === $menuItem7['named_route']) active open @endif">
            <a href="{{ $menuItem7['url'] }}">
                <i class="fal {{ $menuItem7['fa-icon'] }} fa-fw"></i> <span class="parent-nav-title">{{ $menuItem7['parent_title'] }}</span>
            </a>
            @if( count($menuItem7['children']) > 0)
            <ul @if( \Route::currentRouteName() === $menuItem7['named_route']) class="open" @endif>
                @foreach( $menuItem7['children'] as $menuChildren7 )
                <li><a href="{{ $menuChildren7['url'] }}">{{ $menuChildren7['title'] }}</a></li>
                @endforeach
            </ul>
            @endif
        </li>
        @endforeach
		<li @if( isRouteGroup('menus.') ) class="active" @endif>
		    <a href="/admin/menus">
    		    <i class="fal fa-stream fa-fw"></i> <span class="parent-nav-title">Menus</span>
		    </a>
        </li>
        @php
           $menuItems8 = _getMenuSlot(8);
        @endphp
        @foreach( $menuItems8 as $menuItem8 )
        <li class="@if( count($menuItem8['children']) > 0) has-dropdown @endif @if( \Route::currentRouteName() === $menuItem8['named_route']) active open @endif">
            <a href="{{ $menuItem8['url'] }}">
                <i class="fal {{ $menuItem8['fa-icon'] }} fa-fw"></i> <span class="parent-nav-title">{{ $menuItem8['parent_title'] }}</span>
            </a>
            @if( count($menuItem8['children']) > 0)
            <ul @if( \Route::currentRouteName() === $menuItem8['named_route']) class="open" @endif>
                @foreach( $menuItem8['children'] as $menuChildren8 )
                <li><a href="{{ $menuChildren8['url'] }}">{{ $menuChildren8['title'] }}</a></li>
                @endforeach
            </ul>
            @endif
        </li>
        @endforeach
        <li class="has-dropdown @if( isRouteGroup('galleries.')) active open @endif">
            <a href="/admin/galleries">
                <i class="fal fa-images fa-fw"></i> <span class="parent-nav-title">Galleries</span>
            </a>
            <ul @if( isRouteGroup('galleries.') ) class="open" @endif>
                <li><a href="/admin/galleries">All Galleries</a></li>
                <li><a href="/admin/gallery">Create Gallery</a></li>
            </ul>
        </li>
		<li class="has-dropdown @if( isRouteGroup('custom-fields.')) active open @endif">
		    <a href="/admin/custom-fields">
    		    <i class="fal fa-ballot fa-fw"></i> <span class="parent-nav-title">Custom Fields</span>
            </a>
            <ul @if( isRouteGroup('custom-fields.')) class="open" @endif>
            <li><a href="/admin/custom-fields">Custom Fields</a></li>
        </ul>
        </li>
		@if( auth()->user()->hasRole('admin') )
		@php
           $menuItems9 = _getMenuSlot(9);
        @endphp
        @foreach( $menuItems9 as $menuItem9 )
        <li class="@if( count($menuItem9['children']) > 0) has-dropdown @endif @if( \Route::currentRouteName() === $menuItem9['named_route']) active open @endif">
            <a href="{{ $menuItem9['url'] }}">
                <i class="fal {{ $menuItem9['fa-icon'] }} fa-fw"></i> <span class="parent-nav-title">{{ $menuItem9['parent_title'] }}</span>
            </a>
            @if( count($menuItem9['children']) > 0)
            <ul @if( \Route::currentRouteName() === $menuItem9['named_route']) class="open" @endif>
                @foreach( $menuItem9['children'] as $menuChildren9 )
                <li><a href="{{ $menuChildren9['url'] }}">{{ $menuChildren9['title'] }}</a></li>
                @endforeach
            </ul>
            @endif
        </li>
        @endforeach

        <li class="has-dropdown @if( isRouteGroup('shortcodes.') ) active open @endif">
            <a href="/admin/shortcodes">
                <i class="fal fa-brackets fa-fw"></i> <span class="parent-nav-title">Shortcodes</span>
            </a>
            <ul @if( isRouteGroup('shortcodes.')) class="open" @endif>
                <li><a href="/admin/shortcodes">Shortcodes</a></li>
            </ul>
        </li>

		<li class="has-dropdown @if( isRouteGroup('users.') ) active open @endif">
		    <a href="/admin/users">
    		    <i class="fal fa-users fa-fw"></i> <span class="parent-nav-title">Users</span>
            </a>
			<ul @if( isRouteGroup('users.') ) class="open" @endif>
				<li><a href="/admin/users">All Users</a></li>
				<li><a href="/admin/user">Create User</a></li>
				<li><a href="/admin/roles">Roles</a></li>
			</ul>
		</li>
		@php
           $menuItems10 = _getMenuSlot(10);
        @endphp
		@foreach( $menuItems10 as $menuItem10 )
        <li class="@if( count($menuItem10['children']) > 0) has-dropdown @endif @if( \Route::currentRouteName() === $menuItem10['named_route']) active open @endif">
            <a href="{{ $menuItem10['url'] }}">
                <i class="fal {{ $menuItem10['fa-icon'] }} fa-fw"></i> <span class="parent-nav-title">{{ $menuItem10['parent_title'] }}</span>
            </a>
            @if( count($menuItem10['children']) > 0)
            <ul @if( \Route::currentRouteName() === $menuItem10['named_route']) class="open" @endif>
                @foreach( $menuItem10['children'] as $menuChildren10 )
                <li><a href="{{ $menuChildren9['url'] }}">{{ $menuChildren10['title'] }}</a></li>
                @endforeach
            </ul>
            @endif
        </li>
        @endforeach
		<li class="has-dropdown @if( isRouteGroup('settings.') ) active open @endif">
		    <a href="/admin/settings">
    		    <i class="fal fa-cog fa-fw"></i> <span class="parent-nav-title">Settings</span>
            </a>
            <ul @if( isRouteGroup('settings.')) class="open" @endif>
                <li><a href="/admin/settings">Settings</a></li>
                <li><a href="/admin/redirects">Redirects</a></li>
            </ul>
        </li>
		@endif
	</ul>
</nav>
