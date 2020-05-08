<nav>
	<ul class="main-nav">
		<li><a href="{{ env('APP_URL') }}">{{ env('APP_NAME') }}</a></li>
		<li><a href="/admin/logout" onclick="event.preventDefault(); document.getElementById('frm-logout').submit();"><i class="fal fa-unlock"></i></a></li>
	</ul>
	<form id="frm-logout" action="{{ route('logout') }}" method="POST" style="display: none;">
		@csrf
	</form>
</nav>
