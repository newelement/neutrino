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

	<section>
		
		<article>			
			<p>
    			{{ $data->start_datetime }}<br>
    			{{ $data->end_datetime }}
			</p>
			
			<div>
    			{!! $data->description !!}
			</div>
			
			<div>
    			{!! $data->place->location_name !!}
			</div>
		</article>
		
	</section>


@endsection
