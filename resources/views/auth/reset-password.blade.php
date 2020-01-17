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
        <form class="form-signin mt-3 mb-5 pt-5" method="post" action="/reset-password">
	        @csrf
	        <h1 class="h3 mb-3 font-weight-normal">Reset Password</h1>
			<input type="hidden" name="email" value="{{ $data->email }}">
			<input type="hidden" name="token" value="{{ $data->token }}">
	        
	        <div class="form-group">
	            <label for="password" class="form-label">New Password</label>
	            <input id="password" type="password" class="form-control" name="password"  required autofocus>
	        </div>

			<div class="form-group">
	            <label for="confirm" class="form-label">Confirm Password</label>
	            <input id="confirm" type="password" class="form-control" name="password_confirmation" required>
	        </div>
            <button class="btn btn-primary" type="submit">Reset Password</button>
	    </form>
    </div> 
		
@endsection
