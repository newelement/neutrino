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
    <div class="container">
        <form class="form-signin text-center mt-3 pt-5" method="post" action="{{ route('login') }}">
    	    @csrf

            <img class="mb-4" src="/vendor/newelement/neutrino/images/neutrino-star.png" alt="Neutrino CMS" width="90" style="height: auto;">

            <h1 class="h3 mb-3 font-weight-normal">Please sign in</h1>
            <label for="email" class="sr-only">Email address</label>
            <input type="email" id="email" class="form-control" name="email" placeholder="Email address" value="{{ old('email') }}" required autofocus>
            <label for="password" class="sr-only">Password</label>
            <input type="password" id="password" class="form-control" name="password" placeholder="Password" required>
            <div class="checkbox mb-3">
                <label>
                    <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}> Remember me
                </label>
            </div>
            <p>
                <button class="btn btn-lg btn-primary btn-block" type="submit">Sign in</button>
            </p>
            <p class="text-center">
                <a class="btn-link" href="/email-reset-password">
		            Forgot Password
		        </a>
            </p>
        </form>
    </div>
@endsection
