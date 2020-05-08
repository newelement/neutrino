@extends('neutrino::layouts.header-footer')
@section('title', $data->title.' | ')
@section('meta_keywords', $data->keywords)
@section('meta_description', $data->meta_description)
@section('og')
<meta property="og:title" content="{{ $data->title }}" />
<meta property="og:description" content="{{ $data->meta_description }}" />
@if( isset($data->social_image) && strlen($data->social_image) )
@php
$socialImages = getImageSizes($data->social_image);
@endphp
<meta property="og:image" content="{{ env('APP_URL') }}{{ $socialImages['original'] }}"/>
@endif
@endsection

@section('content')

	<div class="jumbotron jumbotron-fluid" style="background-image: url('/vendor/newelement/neutrino/images/diagram.jpg')">
        <div class="container">
            <h1 class="display-4">Neutrino CMS</h1>
            <p class="lead">A developer friendly Laravel based CMS.</p>
            <hr class="my-4">
            <p>To find out more about the project or help contribute, visit the Github project.</p>
            <a class="btn btn-primary btn-lg" href="https://github.com/newelement/neutrino" role="button">Learn more</a>
        </div>
    </div>

	<div class="container pt-3 mb-2">
    	<div class="row">
        	<div class="col-md-6">
        	    {!! getContent(['strip_shortcodes' => false]) !!}
        	</div>
        	<div class="col-md-6 text-center">
            	<img src="/vendor/newelement/neutrino/images/head-science.png" class="img-fluid" alt="Placeholder Image">
        	</div>
    	</div>
	</div>

	<section class="features-section pt-4 pb-5">

    	<h2 class="text-center mb-4">Features</h2>

    	<div class="container">
        	<div class="row">
            	<div class="col-md-4 mb-3">
            	    <div class="card">
                      <h5 class="card-header">Featured</h5>
                      <div class="card-body">
                        <h5 class="card-title">Special title treatment</h5>
                        <p class="card-text">With supporting text below as a natural lead-in to additional content.</p>
                        <a href="#" class="btn btn-primary">Go somewhere</a>
                      </div>
                    </div>
            	</div>
            	<div class="col-md-4 mb-3">
            	    <div class="card">
                      <h5 class="card-header">Featured</h5>
                      <div class="card-body">
                        <h5 class="card-title">Special title treatment</h5>
                        <p class="card-text">With supporting text below as a natural lead-in to additional content.</p>
                        <a href="#" class="btn btn-primary">Go somewhere</a>
                      </div>
                    </div>
            	</div>
            	<div class="col-md-4 mb-3">
            	    <div class="card">
                      <h5 class="card-header">Featured</h5>
                      <div class="card-body">
                        <h5 class="card-title">Special title treatment</h5>
                        <p class="card-text">With supporting text below as a natural lead-in to additional content.</p>
                        <a href="#" class="btn btn-primary">Go somewhere</a>
                      </div>
                    </div>
            	</div>
        	</div>
    	</div>
	</section>

@endsection
