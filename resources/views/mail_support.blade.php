<html>
<head>
</head>
<body>
<p>Enviado por: {!! $user !!}</p>
<p>Correo: {!! $user_mail !!}
<p>{!! $problem !!}</p>
<hr>

@if (isset($imagen))
 	<img src="{!! $message->embed('../storage/app/temporal_mail/'.$imagen) !!}">
@endif
</body>
</html>