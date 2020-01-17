@extends('neutrino::templates.header-footer')
@section('title', $data->title.' | ')
@section('meta_keywords', '')
@section('meta_description', '')
@section('og')
<meta property="og:title" content="{{ $data->title }}" />
   		<meta property="og:description" content="" />
		<meta property="og:image" content="{{url('/')}}">
   		<meta property="og:url" content="{{ url()->current() }}"/>
   		<meta property="og:site_name" content="{{ env('APP_NAME') }}" />
@endsection

@section('content')

	<h1>{{ $data->title }}</h1>
	
	@php 
	$fields = getCustomFields('taxonomy_'.$data->id);
	@endphp

	<section class="terms">
		@foreach( $data->terms as $term )
		<article>
			<h2><a href="{{ $term->url() }}">{{ $term->title }}</a></h2>
		</article>
		@endforeach
	</section>

	<section class="entries">
		@foreach( $data->entries as $entry )
		<article>
			<h2><a href="{{ $entry->url() }}">{{ $entry->title }}</a></h2>
			<a href="{{ $entry->url() }}">Read more</a>
		</article>
		@endforeach
	</section>

	<div class="pagination-links">
		{{ $data->entries->links() }}
	</div>

@endsection
