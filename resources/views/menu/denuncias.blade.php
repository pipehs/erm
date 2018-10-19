<li class="dropdown">
	<a href="#" class="dropdown-toggle">
	@if (Session::get('org') == 'B-GRC Deloitte')
		<i class="fa fa-plus"></i>
	@else
		<i class="fa fa-warning"></i>
	@endif
		<span class="hidden-xs">Sistema de Denuncias</span>
	</a>
	<ul class="dropdown-menu" style="{{ dropDown9() }}">
		<li class="dropdown">
			<li><a href="denuncias" class="{{ activeMenu('denuncias') }}">Home</a></li>

			<li><a href="seguimiento_admin" class="{{ activeMenu('seguimiento_admin') }}">Seguimiento de Denuncia</a></li>

			<li><a href="reportes_denuncias" class="{{ activeMenu('reportes_denuncias') }}">Reportes</a></li>

			@if (isset(Auth::user()->superadmin) && (Auth::user()->superadmin == 1 || Auth::user()->superadmin == 2 ))
				<li><a href="cc_config" class="{{ activeMenu('configdenuncias') }}">Configuración general</a></li>
			@endif
		</ul>
	</li>