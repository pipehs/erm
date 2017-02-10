	
		<div id="sidebar-left" class="col-xs-2 col-sm-2">
		
			<ul class="nav main-menu">
@if (!Auth::guest())
	@if(Session::has('roles'))
		<li>
			<a href="home" class="{{ activeMenu('home') }}">
				<i class="fa fa-home"></i>
				<span class="hidden-xs">Inicio</span>
			</a>
		</li>
		
		
     	@foreach (Session::get('roles') as $role)
			@if ($role == 1 || $role == 6) <!-- ADMIN TIENE ACCESO A TODO -->
				@include('menu.datos_maestros')
				@include('menu.estrategia')
				@include('menu.riesgos')
				@include('menu.controles')
				@include('menu.auditorias')
			<?php break; //si es admin terminamos ciclo para no repetir menú ?>
			@elseif ($role == 8) <!-- Admin. de datos maestros -->
				@include('menu.datos_maestros')
			@elseif ($role == 7) <!-- Admin. de estrategía -->
				@include('menu.estrategia')
			@elseif ($role == 2) <!-- Admin. de riesgo -->
				@include('menu.riesgos')
			@elseif ($role == 3) <!-- Admin. de control -->
				@include('menu.controles')
			@elseif ($role == 4) <!-- Auditor Manager -->
				@include('menu.auditorias')
			@elseif ($role == 5) <!-- Auditor -->
				@include('menu.auditorias')
			@elseif ($role == 6) <!-- Display -->
				<!-- Ver como hacer el visitante que solo puede ver -->
			@endif

		@endforeach

		@include('menu.hallazgos')
		@include('menu.planes_accion')
		@include('menu.reportes')

		@foreach (Session::get('roles') as $role)
			@if ($role != 6) <!-- Rol distinto de display -->
				@include('menu.documentos')
				<?php break; ?>
			@endif
		@endforeach

		@foreach (Session::get('roles') as $role)
			@if ($role == 1) {{-- Administración del sistema --}}
				@include('menu.admin')
			@endif
		@endforeach


	@endif
@else
					<li>&nbsp;</li>
@endif
			</ul>

		
		</div>