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
            
		<form class="form-signin mt-3 mb-5 pt-5" method="post" action="/email-reset-password">
	        @csrf
	        
	        <h1 class="h3 mb-3 font-weight-normal">Reset Password</h1>
	        
	        <p>
    	        Enter your account email address and we will send you a reset password link.
	        </p>

	        <div class="form-group">
	            <label for="email" class="form-label">Email Address</label>
	            <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
	        </div>

            <button type="submit" class="btn btn-primary">Send Reset Password Link</button>

	    </form>
    </div>
@endsection
