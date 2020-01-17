@extends('neutrino::templates.header-footer')
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
    <div class="container">
		<form class="form-signin mt-3 mb-5 pt-5" method="post" action="{{ route('register') }}">
	        @csrf  
            <h1 class="h3 mb-3 font-weight-normal">Register</h1>
            <div class="form-group">
	            <label for="name" class="form-label">Name</label>
                <input id="name" type="text" class="form-control" name="name" value="{{ old('name') }}" required autofocus>
	        </div>
            <div class="form-group">
	            <label for="email" class="form-label">Email Address</label>
                <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required autocomplete="email">
	        </div>
            <div class="form-group">
	            <label for="password" class="form-label">Password</label>
                <input id="password" type="password" class="form-control" name="password" required autocomplete="current-password">
	        </div>
	            
            <div class="form-group">
	            <label for="password-confirm" class="form-label">Confirm Password</label>
                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required>
	        </div>
            <button type="submit" class="btn btn-block btn-primary">Register</button>
	    </form>
    </div>
@endsection