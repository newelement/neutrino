<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
	<head>
	    <meta charset="utf-8">
	    <meta http-equiv="X-UA-Compatible" content="IE=edge">
	    <meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="csrf-token" content="{{ csrf_token() }}">
		<meta name="msapplication-TileColor" content="#1c2529">
	    <meta name="theme-color" content="#1c2529">
		<link rel="profile" href="http://gmpg.org/xfn/11">
	    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
	    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
	    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
	    <link rel="mask-icon" href="/safari-pinned-tab.svg" color="#1c2529">
	    <title>@yield('title'){{ env('APP_NAME') }} Admin</title>
		<script>
        window.app =  <?php echo json_encode([
            'csrfToken' => csrf_token(),
			'baseUrl' => URL::to('/')
        ]); ?>
        </script>

        <!--
        <script src="https://unpkg.com/react@16.8.6/umd/react.production.min.js"></script>
        <script src="https://unpkg.com/react-dom@16.8.6/umd/react-dom.production.min.js"></script>
        <script src="{{ asset('vendor/laraberg/js/laraberg.js') }}"></script>-->

		<link href="/vendor/newelement/neutrino/css/app.css" rel="stylesheet">
        <!--<link href="{{asset('vendor/laraberg/css/laraberg.css')}}" rel="stylesheet">-->
        {{ getAdminStyles() }}
	</head>
	<body class="@if( isset($_COOKIE['menu_open']) && $_COOKIE['menu_open'] === "Y" ) menu-open @endif">


		<div class="sidebar-main">
			<div class="sidebar-nav-group">
				<div class="sidebar-header">
					<a href="/admin" class="menu-toggle">
    					<i class="fal @if( isset($_COOKIE['menu_open']) && $_COOKIE['menu_open'] === "Y" ) fa-long-arrow-left @else fa-long-arrow-right @endif fa-fw"></i>
    				</a>
				</div>
				@include('neutrino::admin.partials.sidebar-nav')
			</div>
			<div class="group-main">
				<header class="header">
        			<div class="brand-nav">
        				<div class="brand">
        					<!--<a href="/admin" class="menu-toggle"><i class="fal fa-bars fa-fw"></i> <span>Admin Menu</span></a>-->
        				</div>
        				@include('neutrino::admin.partials.top-nav')
        			</div>
        		</header>

        		@if ($errors->any() || session('success') || session('error') )
				<div class="messages">
					@if ($errors->any())
					<div class="alert alert-danger">
					    <ul>
					        @foreach ($errors->all() as $error)
				             <li>{{ $error }}</li>
				          @endforeach
					    </ul>
				    </div>
					@endif
					@if (session('success'))
					    <div class="alert alert-success">
					        {{ session('success') }}
					    </div>
					@endif
					@if (session('error'))
					    <div class="alert alert-danger">
					        {{ session('error') }}
					    </div>
					@endif
				</div>
				@endif

				<main class="main @if($errors->any() || session('success') || session('error') ) has-message @endif">
					@yield('content')
				</main>
			</div>
		</div>
        <div class="expired-csrf-modal">
            <div class="inner">
                <a href="#" role="button" class="close-csrf-modal-action close-csrf-modal">&times;</a>
                <h3>Warning. Expired Security Session.</h3>
                <p>Close this message and refresh this page.</p>
                <a href="#" role="button" class="close-csrf-modal-action"><strong>Close</strong></a>
            </div>
        </div>

        @include('neutrino::admin.partials.image-media-dialog')
        @include('neutrino::admin.partials.media-dialog')
        <div class="media-dialog-overlay hide"></div>

		<div id="snackbar"></div>
		@include('neutrino::admin.partials.filemanager')
        @yield('js')
		<script src="/vendor/newelement/neutrino/js/app.js" defer></script>
        {{ getAdminScripts() }}
	</body>
</html>
