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
    <div class="container">
        <div class="row">
            <div class="col-md-8">
            	<h1 class="mt-4">{{ $data->title }}</h1>
            	<section class="entries mb-5">
            		@foreach( $data->entries as $entry )
            		<article class="row mb-4">
                		<div class="col-md-12">
                			<h2><a href="{{ $entry->url() }}">{{ $entry->title }}</a></h2>
                			<p>{!! trimWords( $entry->content, 20, '...', ['strip_shortcodes' => true] ) !!}</p>
                			<a href="{{ $entry->url() }}">Read more</a>
                		</div>
            		</article>
            		@endforeach
            	</section>
                <div class="row">
                    <div class="col-md-12">
                    	<div class="pagination-links mb-5">
                    		{{ $data->entries->links() }}
                        </div>
                    </div>
                </div>
            </div>
            @include('partials.sidebar')
        </div>
    </div>

@endsection
