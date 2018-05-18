<html>
<head>
</head>
<body>
<p>Estimado administrador,</p>
<p>Se informa que se generó a través del sistema el siguiente error con sus datos correspondientes.</p>

<p>Enviado por: {!! $user !!}<br>
@if (Session::get('org') != NULL)
	Empresa: {{ Session::get('org') }}
@else
	Empresa: Servidor de Prueba (localhost)
@endif
<br>
Correo: {!! $user_mail !!}</p>

<p>Favor brindar una solución a la brevedad.</p>
<p>Mensaje enviado automáticamente a través de B-GRC</p>

<h4>Mensaje de error:</h4>
<hr>
<b><p>{!! $e !!}</p></b>
<hr>
</body>
</html>