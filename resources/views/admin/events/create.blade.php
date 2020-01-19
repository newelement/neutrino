@extends('neutrino::admin.template.header-footer')
@section('title', 'Create Event | ')
@section('content')
<form action="/admin/events" method="post" enctype="multipart/form-data">
		<div class="container">

			<div class="content">

				<h2>Create Event</h2>

					@csrf
					<div class="form-row">
						<label class="label-col" for="title">Title</label>
						<div class="input-col">
							<input id="title" class="to-slug" type="text" name="title" value="{{ old('title') }}" autocomplete="off" required>
						</div>
					</div>

					<div class="form-row">
						<label class="label-col" for="slug">Slug</label>
						<div class="input-col">
							<input id="slug" class="slug-input" type="text" name="slug" value="{{ old('slug') }}" required>
						</div>
					</div>

					<div class="form-row">
						<label class="label-col" for="start-date">Start Date &amp; Time</label>
						<div class="input-col date-time-col">
    						<div class="date-col">
        						<input id="start-date" type="date" name="start_date" value="{{ old('start_date') }}" required>
    						</div>
    						<div class="time-col">
        						<input id="start-time" type="time" name="start_time" value="{{ old('start_time') }}" required>
    						</div>
						</div>
						<div class="input-notes">
						    <span class="note">Example: 01/01/2020 05:30 PM</span>
				        </div>
					</div>

					<div class="form-row">
						<label class="label-col" for="end-date">End Date &amp; Time</label>
						<div class="input-col date-time-col">
    						<div class="date-col">
        						<input id="end-date" type="date" name="end_date" value="{{ old('end_date') }}" required>
    						</div>
    						<div class="time-col">
        						<input id="end-time" type="time" name="end_time" value="{{ old('end_time') }}" required>
    						</div>
						</div>
						<div class="input-notes">
						    <span class="note">Example: 01/01/2020 05:30 PM</span>
				        </div>
					</div>

					<div class="form-row">
						<label class="label-col" for="">Recurring</label>
						<div class="input-col">
							<div class="recurring-inputs">
								<label class="recurr-label"><input type="checkbox" name="recurring" value="1"> Yes</label>
								<div class="number-recurr">
    								<div class="select-wrapper">
    									<select name="recurr_times">
    										<option value="">I want to repeat this ...</option>
    										<option value="1">Every</option>
    										<option value="2">Every 2nd</option>
    										<option value="3">Every 3rd</option>
    										<option value="4">Every 4th</option>
    									</select>
    								</div>
								</div>
								<div class="recurr-freq">
    								<div class="select-wrapper">
    									<select name="frequency_type">
    										<option value="">Every ...</option>
    										<option value="DAY">Day</option>
    										<option value="WEEK">Week</option>
    										<option value="MONTH">Month</option>
    										<option value="YEAR">Year</option>
    										<option value="NTHWEEKDAY">Weekday</option>
    									</select>
    								</div>
								</div>
							</div>
						</div>
					</div>

					<div class="form-row">
						<label class="label-col" for="end-recurr-date">End Recurring Date &amp; Time</label>
						<div class="input-col date-time-col">
    						<div class="date-col">
        						<input id="end-recurr-date" type="date" name="end_recurring_date" value="{{ old('end_recurring_date') }}" required>
    						</div>
    						<div class="time-col">
        						<input id="end-recurr-time" type="time" name="end_recurring_time" value="{{ old('end_recurring_time') }}" required>
    						</div>
						</div>
						<div class="input-notes">
						    <span class="note">Example: 01/01/2020 05:30 PM</span>
				        </div>
					</div>

					<div class="form-row">
						<label class="label-col" for="location">Location</label>
						<div class="input-col">
    						<div class="select-wrapper">
    							<select id="location" name="place">
    								<option value="">Choose &hellip;</option>
    								@foreach( $locations as $location )
    								<option value="{{$location->id}}">{{$location->location_name }} {{ $location->address }}</option>
    								@endforeach
    							</select>
    						</div>
						</div>
					</div>

					<div class="form-row">
						<label class="label-col align-top full-width" for="content">Description</label>
						<div class="input-col full-width">
							<textarea id="content" class="editor" name="description">{{ old('description') }}</textarea>
						</div>
					</div>


					<div class="form-row">
						<label class="label-col" for="keywords">Keywords</label>
						<div class="input-col">
							<input id="keywords" type="text" name="keywords" value="{{ old('keywords') }}">
						</div>
					</div>

					<div class="form-row">
						<label class="label-col" for="meta_desc">Meta Description</label>
						<div class="input-col">
							<input id="meta-desc" type="text" name="meta_desc" value="{{ old('meta_desc') }}">
						</div>
					</div>

			</div>

			<aside class="sidebar">
				<div class="side-fields">
					@php $taxGroups = _getTaxonomyGroups('events') @endphp
					@foreach( $taxGroups as $taxGroup )
					<div class="form-row">
						<label class="label-col" for="{{ $taxGroup->slug }}">{{ str_plural($taxGroup->title) }}</label>
						<div class="input-col">
							<input type="text" name="tax_new[{{ $taxGroup->id }}]" placeholder="New {{ $taxGroup->title }}" style="margin-bottom: 4px">
							<div class="term-group-select">
								@foreach( $taxGroup->terms as $term )
								<label><input type="checkbox" name="taxes[{{ $taxGroup->id }}][]" value="{{ $term->id }}"> {{ $term->title }}</label>
								@endforeach
							</div>
						</div>
					</div>
					@endforeach

					<div class="form-row">
						<label class="label-col">Featured Image
							<a class="lfm-featured-image" data-input="featured-image" data-preview="featured-image-preview">
								<i class="fas fa-image"></i> Choose
							</a>
						</label>
						<div class="input-col">
							<input id="featured-image" class="file-list-input" value="" type="text" name="featured_image">
							<div id="featured-image-preview" class="featured-image-preview"></div>
						</div>
					</div>

					<div class="form-actions">
						<button type="submit" class="btn form-btn">Create</button>
					</div>
				</div>
			</aside>
		</div>
		</form>
@endsection

@section('js')
<script>
window.editorCss = '<?php echo getEditorCss(); ?>';
</script>
@endsection
