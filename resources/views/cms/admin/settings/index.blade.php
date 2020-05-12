@extends('neutrino::admin.template.header-footer')
@section('title', 'Settings | ')
@section('content')
	<div class="container">
		<div class="content">
			<h2>Settings</h2>

            @if( $health['update_available'] )
            <div class="alert alert-info" style="margin-bottom: 24px;">
                <p>
                An update is available for Neutrino. {{ $health['update_message'] }}
                </p>
            </div>
            @endif

			<ul class="tabs smaller" style="margin-bottom: 12px; border-bottom: 1px solid #444444">
				<li>
					<a class="active" href="#settings">Settings</a>
				</li>
				<li>
					<a  href="#custom-settings">Custom Settings</a>
				</li>
                <li>
                    <a href="#sitemap">Sitemap</a>
                </li>
                <li>
                    <a id="activity-log-tab"  href="#activity-log">Activity Log</a>
                </li>
				<li>
					<a href="#cache">Cache</a>
				</li>
                <li>
                    <a href="#info">Info</a>
                </li>
			</ul>

			<div class="tabs-container">
				<div id="settings" class="tab-content active">
					<table cellpadding="0" cellspacing="0" class="table setting-table">
						<thead>
							<tr>
								<th class="text-left">Setting</th>
								<th class="text-left">Setting Name</th>
								<th class="text-left">Setting Value</th>
								<th width="80">Edit</th>
								<th width="60"></th>
							</tr>
						</thead>
						<tbody>
						@foreach( $settings as $setting )
							<tr>
								<td data-label="Setting" class="text-left">
									<a href="/admin/settings/{{$setting->id}}">{{ $setting->label }}</a>
									@if( $setting->details )
									<br><span class="setting-details">{{ $setting->details }}</span>
									@endif
								</td>
								<td data-label="Setting Name" class="text-left">
									<a href="/admin/settings/{{$setting->id}}">{{ $setting->key }}</a>
								</td>
								<td data-label="Setting Value">
									@php
									if( $setting->type === 'BOOL' ){
										echo $setting->value_bool? 'True' : 'False';
									}
									if( $setting->type === 'STRING' ){
										echo $setting->value;
									}
									@endphp
								</td>
								<td data-label="Edit" class="text-center">
									<a href="/admin/settings/{{$setting->id}}">Edit</a>
								</td>
								<td>
								</td>
							</tr>
						@endforeach
						</tbody>
					</table>
				</div>
				<div id="custom-settings" class="tab-content">

					<table cellpadding="0" cellspacing="0" class="table setting-table">
						<thead>
							<tr>
								<th class="text-left">Setting</th>
								<th class="text-left">Setting Name</th>
								<th class="text-left">Setting Value</th>
								<th width="80">Edit</th>
								<th width="60"></th>
							</tr>
						</thead>
						<tbody>
						@foreach( $custom_settings as $csetting )
							<tr>
								<td data-label="Setting" class="text-left">
									<a href="/admin/settings/{{$csetting->id}}">{{ $csetting->label }}</a>
									@if( $csetting->details )
									<br><span class="setting-details">{{ $csetting->details }}</span>
									@endif
								</td>
								<td data-label="Setting Name" class="text-left">
									<a href="/admin/settings/{{$csetting->id}}">{{ $csetting->key }}</a>
								</td>
								<td data-label="Setting Value">
									@php
									if( $csetting->type === 'BOOL' ){
										echo $csetting->value_bool? 'True' : 'False';
									}
									if( $csetting->type === 'STRING' ){
										echo $csetting->value;
									}
									@endphp
								</td>
								<td data-label="Edit" class="text-center">
									<a href="/admin/settings/{{$csetting->id}}">Edit</a>
								</td>
								<td data-label="Delete">
									@if( !$setting->protected )
									<form action="/admin/settings/{{$csetting->id}}" method="post">
										@csrf
										@method('delete')
										<button type="submit" class="delete-btn">&times;</button>
									</form>
									@endif
								</td>
							</tr>
						@endforeach
						</tbody>
					</table>

				</div>
				<div id="cache" class="tab-content" style="padding-top: 24px;">

					<p>
						<a href="/admin/cache/clear/all">Clear all cache</a>
					</p>
                    <p>
                        <a href="/admin/cache/clear-asset-cache">Clear asset cache</a>
                    </p>

				</div>

                <div id="info" class="tab-content" style="padding-top: 24px;">

                    <p>
                    Current Version: {{ $health['this_version']['major'].'.'.$health['this_version']['minor'].'.'.$health['this_version']['patch'] }}<br>
                    Latest Version {{$health['latest_version']['major'].'.'.$health['latest_version']['minor'].'.'.$health['latest_version']['patch'] }} - <strong>{{ $health['update_available']? 'Update available' : 'Up to date' }}</strong>
                    </p>

                    @if( $packages )
                    <h3 style="margin-bottom: 6px;">Neutrino Packages</h3>

                    <ul class="installed-neutrino-packages-list">
                    @foreach( $packages as $package )
                        @if( isset( $package['package_name'] ) )
                        <li>
                            @if( isset( $package['image'] ) && strlen($package['image']) )
                            <div class="package-image">
                                <img src="{{ $package['image'] }}" style="max-width: 100px; height: auto;" alt="{{ $package['package_name'] }}">
                            </div>
                            @endif
                            <div class="package-info">
                                <div class="package-name">
                                    {{ $package['package_name'] }}
                                    @if( isset($package['version']) ) <span class="package-version">v {{$package['version']}}</span>@endif
                                </div>
                                @if( isset($package['description']) && strlen($package['description']) )
                                    <div class="package-description">
                                    {{$package['description']}}
                                    </div>
                                @endif
                                <div class="package-meta">
                                    @if( isset($package['website']) && strlen($package['website']) )
                                    <a href="{{$package['website']}}" target="_blank">Website</a>
                                    @endif

                                    @if( isset($package['website']) && strlen($package['website']) && isset($package['repo']) && strlen($package['repo']) )
                                    <span> | </span>
                                    @endif

                                    @if( isset($package['repo']) && strlen($package['repo']) )
                                    <a href="{{$package['repo']}}" target="_blank"><i class="fab fa-github"></i></a>
                                    @endif
                                </div>
                            </div>
                        </li>
                    @endif
                    @endforeach
                    </ul>
                    @endif

                </div>


                <div id="sitemap" class="tab-content" style="padding-top: 24px;">

                    <form action="/admin/settings/sitemap" method="post">
                    @csrf

                        <div class="form-row">
                            <label class="label-col" for="cache-houra">Cache Time (hours)</label>
                            <div class="input-col">
                                <input id="cache-houra" type="number" name="cache_hours" style="text-align: right" value="{{ $sitemap_settings->cache_hours }}">
                            </div>
                        </div>

                        <div class="form-row">
                            <label class="label-col" for="page-default-change">Page Default Change Feq.</label>
                            <div class="input-col">
                                <div class="select-wrapper">
                                    <select id="page-default-change" name="page_default_change">
                                        <option value="always" {{ $sitemap_settings->page_default_change === 'always'? 'selected="selected"' : '' }}>Always</option>
                                        <option value="hourly" {{ $sitemap_settings->page_default_change === 'hourly'? 'selected="selected"' : '' }}>Hourly</option>
                                        <option value="daily" {{ $sitemap_settings->page_default_change === 'daily'? 'selected="selected"' : '' }}>Daily</option>
                                        <option value="weekly" {{ $sitemap_settings->page_default_change === 'weekly'? 'selected="selected"' : '' }}>Weekly</option>
                                        <option value="monthly" {{ $sitemap_settings->page_default_change === 'monthly'? 'selected="selected"' : '' }}>Monthly</option>
                                        <option value="yearly" {{ $sitemap_settings->page_default_change === 'yearly'? 'selected="selected"' : '' }}>Yearly</option>
                                        <option value="never" {{ $sitemap_settings->page_default_change === 'never'? 'selected="selected"' : '' }}>Never</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-row">
                            <label class="label-col" for="page-default-priority">Page Default Priority</label>
                            <div class="input-col">
                                <input id="page-default-priority" type="number" name="page_default_priority" style="text-align: right" value="{{ $sitemap_settings->page_default_priority }}">
                            </div>
                        </div>

                        <div class="form-row">
                            <label class="label-col" for="entry-default-change">Entry Default Change Feq.</label>
                            <div class="input-col">
                                <div class="select-wrapper">
                                    <select id="entry-default-change" name="entry_default_change">
                                        <option value="always" {{ $sitemap_settings->entry_default_change === 'always'? 'selected="selected"' : '' }}>Always</option>
                                        <option value="hourly" {{ $sitemap_settings->entry_default_change === 'hourly'? 'selected="selected"' : '' }}>Hourly</option>
                                        <option value="daily" {{ $sitemap_settings->entry_default_change === 'daily'? 'selected="selected"' : '' }}>Daily</option>
                                        <option value="weekly" {{ $sitemap_settings->entry_default_change === 'weekly'? 'selected="selected"' : '' }}>Weekly</option>
                                        <option value="monthly" {{ $sitemap_settings->entry_default_change === 'monthly'? 'selected="selected"' : '' }}>Monthly</option>
                                        <option value="yearly" {{ $sitemap_settings->entry_default_change === 'yearly'? 'selected="selected"' : '' }}>Yearly</option>
                                        <option value="never" {{ $sitemap_settings->entry_default_change === 'never'? 'selected="selected"' : '' }}>Never</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-row">
                            <label class="label-col" for="entry-default-priority">Entry Default Priority</label>
                            <div class="input-col">
                                <input id="entry-default-priority" type="number" name="entry_default_priority" style="text-align: right" value="{{ $sitemap_settings->entry_default_priority }}">
                            </div>
                        </div>

                        <div class="form-row">
                            <label class="label-col" for="entrytype-default-change">Entry Type Default Change Feq.</label>
                            <div class="input-col">
                                <div class="select-wrapper">
                                    <select id="entrytype-default-change" name="entry_type_default_change">
                                        <option value="always" {{ $sitemap_settings->entry_type_default_change === 'always'? 'selected="selected"' : '' }}>Always</option>
                                        <option value="hourly" {{ $sitemap_settings->entry_type_default_change === 'hourly'? 'selected="selected"' : '' }}>Hourly</option>
                                        <option value="daily" {{ $sitemap_settings->entry_type_default_change === 'daily'? 'selected="selected"' : '' }}>Daily</option>
                                        <option value="weekly" {{ $sitemap_settings->entry_type_default_change === 'weekly'? 'selected="selected"' : '' }}>Weekly</option>
                                        <option value="monthly" {{ $sitemap_settings->entry_type_default_change === 'monthly'? 'selected="selected"' : '' }}>Monthly</option>
                                        <option value="yearly" {{ $sitemap_settings->entry_type_default_change === 'yearly'? 'selected="selected"' : '' }}>Yearly</option>
                                        <option value="never" {{ $sitemap_settings->entry_type_default_change === 'never'? 'selected="selected"' : '' }}>Never</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-row">
                            <label class="label-col" for="entrytype-default-priority">Entry Type Default Priority</label>
                            <div class="input-col">
                                <input id="entrytype-default-priority" type="number" name="entry_type_default_priority" style="text-align: right" value="{{ $sitemap_settings->entry_type_default_priority }}">
                            </div>
                        </div>

                        <div class="form-row">
                            <label class="label-col" for="taxonomy-default-change">Taxonomy Default Change Feq.</label>
                            <div class="input-col">
                                <div class="select-wrapper">
                                    <select id="taxonomy-default-change" name="taxonomy_default_change">
                                        <option value="always" {{ $sitemap_settings->taxonomy_default_change === 'always'? 'selected="selected"' : '' }}>Always</option>
                                        <option value="hourly" {{ $sitemap_settings->taxonomy_default_change === 'hourly'? 'selected="selected"' : '' }}>Hourly</option>
                                        <option value="daily" {{ $sitemap_settings->taxonomy_default_change === 'daily'? 'selected="selected"' : '' }}>Daily</option>
                                        <option value="weekly" {{ $sitemap_settings->taxonomy_default_change === 'weekly'? 'selected="selected"' : '' }}>Weekly</option>
                                        <option value="monthly" {{ $sitemap_settings->taxonomy_default_change === 'monthly'? 'selected="selected"' : '' }}>Monthly</option>
                                        <option value="yearly" {{ $sitemap_settings->taxonomy_default_change === 'yearly'? 'selected="selected"' : '' }}>Yearly</option>
                                        <option value="never" {{ $sitemap_settings->taxonomy_default_change === 'never'? 'selected="selected"' : '' }}>Never</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-row">
                            <label class="label-col" for="taxonomy-default-priority">Taxonomy Default Priority</label>
                            <div class="input-col">
                                <input id="taxonomy-default-priority" type="number" name="taxonomy_default_priority" style="text-align: right" value="{{ $sitemap_settings->taxonomy_default_priority }}">
                            </div>
                        </div>

                        <div class="form-row">
                            <label class="label-col" for="term-default-change">Taxonomy Term Default Change Feq.</label>
                            <div class="input-col">
                                <div class="select-wrapper">
                                    <select id="term-default-change" name="term_default_change">
                                        <option value="always" {{ $sitemap_settings->term_default_change === 'always'? 'selected="selected"' : '' }}>Always</option>
                                        <option value="hourly" {{ $sitemap_settings->term_default_change === 'hourly'? 'selected="selected"' : '' }}>Hourly</option>
                                        <option value="daily" {{ $sitemap_settings->term_default_change === 'daily'? 'selected="selected"' : '' }}>Daily</option>
                                        <option value="weekly" {{ $sitemap_settings->term_default_change === 'weekly'? 'selected="selected"' : '' }}>Weekly</option>
                                        <option value="monthly" {{ $sitemap_settings->term_default_change === 'monthly'? 'selected="selected"' : '' }}>Monthly</option>
                                        <option value="yearly" {{ $sitemap_settings->term_default_change === 'yearly'? 'selected="selected"' : '' }}>Yearly</option>
                                        <option value="never" {{ $sitemap_settings->term_default_change === 'never'? 'selected="selected"' : '' }}>Never</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-row">
                            <label class="label-col" for="term-default-priority">Term Default Priority</label>
                            <div class="input-col">
                                <input id="term-default-priority" type="number" name="term_default_priority" style="text-align: right" value="{{ $sitemap_settings->term_default_priority }}">
                            </div>
                        </div>

                        <div class="form-row">
                            <label class="label-col" for="event-default-change">Event Default Change Feq.</label>
                            <div class="input-col">
                                <div class="select-wrapper">
                                    <select id="event-default-change" name="event_default_change">
                                        <option value="always" {{ $sitemap_settings->event_default_change === 'always'? 'selected="selected"' : '' }}>Always</option>
                                        <option value="hourly" {{ $sitemap_settings->event_default_change === 'hourly'? 'selected="selected"' : '' }}>Hourly</option>
                                        <option value="daily" {{ $sitemap_settings->event_default_change === 'daily'? 'selected="selected"' : '' }}>Daily</option>
                                        <option value="weekly" {{ $sitemap_settings->event_default_change === 'weekly'? 'selected="selected"' : '' }}>Weekly</option>
                                        <option value="monthly" {{ $sitemap_settings->event_default_change === 'monthly'? 'selected="selected"' : '' }}>Monthly</option>
                                        <option value="yearly" {{ $sitemap_settings->event_default_change === 'yearly'? 'selected="selected"' : '' }}>Yearly</option>
                                        <option value="never" {{ $sitemap_settings->event_default_change === 'never'? 'selected="selected"' : '' }}>Never</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-row">
                            <label class="label-col" for="event-default-priority">Event Default Priority</label>
                            <div class="input-col">
                                <input id="event-default-priority" type="number" name="event_default_priority" style="text-align: right" value="{{ $sitemap_settings->event_default_priority }}">
                            </div>
                        </div>

                        <button type="submit" class="btn">Update Sitemap Settings</button>
                    </form>

                </div>

                <div id="activity-log" class="tab-content" style="padding-top: 24px;">
                    <table id="activity-log-table" class="table" cellpadding="0" cellspacing="0" border="0">
                        <thead>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
			</div>


		</div>

		<aside class="sidebar">
            @if($edit)
			<form action="/admin/settings/{{$edit_setting->id}}" method="post">
            @else
            <form action="/admin/settings" method="post">
            @endif
				@csrf
				@if( $edit )
				@method('put')
				@endif
			<div class="side-fields">
				<div class="form-row">
					<label class="label-col" for="setting-key">Setting Name</label>
					<div class="input-col">
						<input id="setting-key" type="text" name="setting_name" value="{{ old('setting_name', $edit_setting->key) }}" @if($edit_setting->protected) readonly @endif>
						<span class="note">Lowercase. No spaces or special characters.</span>
					</div>
				</div>

				<div class="form-row">
					<label class="label-col" for="setting-label">Setting Label</label>
					<div class="input-col">
						<input id="setting-label" type="text" name="setting_label" value="{{ old('setting_label', $edit_setting->label) }}" @if($edit_setting->protected) readonly @endif>
					</div>
				</div>

                @if(!$edit_setting->protected)
				<div class="form-row">
					<label class="label-col" for="setting-type">Setting Type</label>
					<div class="input-col">
    					<div class="select-wrapper">
    						<select id="setting-type" name="setting_type" required>
    							<option value="">Choose ...</option>
    							<option value="BOOL" {{ $edit_setting->type === 'BOOL'? 'selected="selected"' : '' }}>True/False (Boolean)</option>
    							<option value="STRING" {{ $edit_setting->type === 'STRING'? 'selected="selected"' : '' }}>Text (String)</option>
    						</select>
						</div>
					</div>
				</div>
				@else
                    <input id="setting-type" type="hidden" name="setting_type" value="{{ $edit_setting->type }}">
				@endif

				<div id="setting-type-string" class="form-row setting-type-field {{ $edit_setting->type === 'STRING'? 'show' : '' }}">
					<label class="label-col" for="setting-value">Setting Value</label>
					<div class="input-col">
						<textarea id="setting-value" name="setting_value">{{ old('setting_value', $edit_setting->value) }}</textarea>
					</div>
				</div>

				<div id="setting-type-bool" class="form-row setting-type-field {{ $edit_setting->type === 'BOOL'? 'show' : '' }}">
					<label class="label-col" for="setting-value-bool">Setting Value</label>
					<div class="input-col">
						<label><input id="setting-value-bool" type="checkbox" name="setting_value_bool" {{ $edit_setting->value_bool? 'checked="checked"' : '' }}> Yes</label>
					</div>
				</div>

				@if( !$edit )
				<button type="submit" class="btn full">Create Setting</button>
				@else
				<button type="submit" class="btn full text-center">Update Setting</button>
				@endif
			</div>
		</aside>

	</div>
@endsection
