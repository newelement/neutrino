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
		@foreach( $data->events as $event )
		<article>
			<h2><a href="{{ $event->url }}">{{ $event->title }}</a></h2>
			<p>
    			{{ $event->start_datetime }}<br>
    			{{ $event->end_datetime }}
			</p>
		</article>
		@endforeach
	</section>

    <div>
        {{ $data->events->links() }}
    </div>

@endsection
