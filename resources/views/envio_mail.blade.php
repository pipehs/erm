<html>
<head>
</head>
<body>

@foreach ($mensaje as $m)
	@if (strpos($m,'http://'))
		<p><a href="{{ $m }}">Ingresar a encuesta</a>
	@else
		<p>{!! $m !!}</p>
	@endif
@endforeach
</hr>
</body>
</html>