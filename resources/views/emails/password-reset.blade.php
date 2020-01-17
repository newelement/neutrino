<html>
	<body>
	    <p>
			Follow the link below to reset your password:
		</p>
		<p>
			<a href="{{ env('APP_URL') }}/reset-password/{{$email}}/{{$token}}">Reset password here</a>
		</p>

	</body>
</html>
