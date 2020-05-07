<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
	<head>
	    <meta charset="utf-8">
	    <meta http-equiv="X-UA-Compatible" content="IE=edge">
	    <meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="csrf-token" content="{{ csrf_token() }}">
		<title>@yield('title'){{ env('APP_NAME') }}</title>
		<meta name="keywords" content="@yield('meta_keywords')">
		<meta name="description" content="@yield('meta_description')">
		<meta name="msapplication-TileColor" content="#1c2529">
	    <meta name="theme-color" content="#1c2529">
		<link rel="profile" href="http://gmpg.org/xfn/11">

		<meta property="og:locale" content="{{ app()->getLocale() }}" />
		<meta property="og:type" content="website" />
    	@yield('og')
		<meta property="og:url" content="{{ url()->current() }}"/>
		<meta property="og:site_name" content="{{ env('APP_NAME') }}" />

		<meta name="twitter:card" content="summary_large_image" />
		<meta name="twitter:description" content="" />
		<meta name="twitter:title" content="" />
		<meta name="twitter:site" content="@" />
		<meta name="twitter:creator" content="@" />

		<script>
        window.app = <?php echo json_encode([ 'csrfToken' => csrf_token(), 'baseUrl' => URL::to('/')]);?>;
		</script>
		<script async src="https://www.googletagmanager.com/gtag/js?id={{ env('GOOGLE_ANALYTICS_ID') }}"></script>
		<script>
		  window.dataLayer = window.dataLayer || [];
		  function gtag(){dataLayer.push(arguments);}
		  gtag('js', new Date());
		  gtag('config', '{{ env('GOOGLE_ANALYTICS_ID') }}');
		</script>
	    {{ getStyles() }}
        @stack('headerscripts')
	</head>
	<body class="@if($data->data_type) {{'data-type-'.$data->data_type}} @endif">
		<div id="app">
			<header class="header">
    			<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        			<div class="container">
                        <a class="navbar-brand" href="/">{{ env('APP_NAME') }}</a>
                        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                            <span class="navbar-toggler-icon"></span>
                        </button>
                        <div class="collapse navbar-collapse" id="navbarSupportedContent">
                            <ul class="navbar-nav ml-auto">
                            {!! getMenu('Main Menu', 'html', [ 'ul_parent' => false, 'ul_class' => 'navbar-nav ml-auto'] ) !!}
                            @if( shoppeExists() )
                            <li class="nav-item"><a class="nav-link" href="/{{ config('shoppe.slugs.store_landing') }}">Products</a></li>
                            <li class="nav-item"><a class="nav-link" href="/cart">Cart <span class="shoppe-cart-count">{{ cartCount() }}</span></a></li>
                            <li class="nav-item"><a class="nav-link" href="/{{ config('shoppe.slugs.customer_account') }}">Account</a></li>
                            @endif
                            @if( Auth::guest() )
                            <li class="nav-item {{ request()->path() === 'login'? ' active' : '' }}"><a class="nav-link" href="/login">Login</a></li>
                            @else
                            <li class="nav-item"><a class="nav-link" href="/logout" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a></li>
                            @endif
                            </ul>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                @csrf
	                        </form>
                        </div>
        			</div>
                </nav>
			</header>

            @if ($errors->any() || session('success') || session('error') || session('info') )
            <div class="container">
                <div class="row justify-content-md-center">
                    <div class="col-6 pt-4">
                        @if ($errors->any())
                        <div class="alert alert-danger" role="alert">
                            <ul>
            				@foreach ($errors->all() as $error)
            				    <li>{{ $error }}</li>
                            @endforeach
            				</ul>
                        </div>
                        @endif
                        @if (session('success'))
                        <div class="alert alert-success" role="alert">
            				{{ session('success') }}
                        </div>
                        @endif
                        @if (session('error'))
                        <div class="alert alert-danger" role="alert">
            				{{ session('error') }}
                        </div>
                        @endif
                        @if ( session('info') )
                        <div class="alert alert-info" role="alert">
                            {{ session('info') }}
                        </div>
                        @endif

                    </div>
                </div>
            </div>
            @endif

            @yield('content')

			<footer class="footer bg-dark pt-5 pb-5 text-light">
    			<div class="container">
        			<div class="row">
        			</div>
    			</div>

    			<div class="container">
        			<div class="row justify-content-md-center">
            			<span>&copy; {{ date('Y') }} Your website.</span>
        			</div>
    			</div>
			</footer>

		</div>
        @stack('footerscripts')
        @yield('js')
		{{ getScripts() }}
	</body>
</html>
