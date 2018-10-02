<li class="dropdown">
	<a href="#" class="dropdown-toggle">
		<i class="fa fa-warning"></i>
		<span class="hidden-xs">Sistema de Denuncias</span>
	</a>
	<ul class="dropdown-menu" style="{{ dropDown9() }}">
		<li class="dropdown">
			<li><a href="registro_denuncia" class="{{ activeMenu('registro_denuncia') }}">Registro de Denuncia</a></li>

			<li><a href="seguimiento_denuncia" class="{{ activeMenu('seguimiento_denuncia') }}">Seguimiento de Denuncia</a></li>

			<li><a href="reportes_denuncias" class="{{ activeMenu('reportes_denuncias') }}">Reportes</a></li>

			@if (isset(Auth::user()->superadmin) && (Auth::user()->superadmin == 1 || Auth::user()->superadmin == 2 ))
				<li><a href="cc_questions" class="{{ activeMenu('preguntasdenuncias') }}">Configurar preguntas</a></li>
				<li><a href="cc_config" class="{{ activeMenu('configdenuncias') }}">Configuración general</a></li>
			@endif
		</ul>
	</li>