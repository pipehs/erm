	
		<div id="sidebar-left" class="col-xs-2 col-sm-2">
		
			<ul class="nav main-menu">
@if (!Auth::guest())
	@if(Session::has('roles'))
		<li>
			<a href="home" class="{{ activeMenu('home') }}">
				<i class="fa fa-dashboard"></i>
				<span class="hidden-xs">Home</span>
			</a>
		</li>
		@include('en.menu.datos_maestros')
		
     	@foreach (Session::get('roles') as $role)
			@if ($role == 1 || $role == 6) <!-- ADMIN TIENE ACCESO A TODO -->
				@include('en.menu.estrategia')
				@include('en.menu.riesgos')
				@include('en.menu.controles')
				@include('en.menu.auditorias')
			<?php break; //si es admin terminamos ciclo para no repetir menú ?>
			@elseif ($role == 2) <!-- Admin. de riesgo -->
				@include('en.menu.riesgos')
			@elseif ($role == 3) <!-- Admin. de control -->
				@include('en.menu.controles')
			@elseif ($role == 4) <!-- Auditor Manager -->
				@include('en.menu.auditorias')
			@elseif ($role == 5) <!-- Auditor -->
				@include('en.menu.auditorias')
			@elseif ($role == 6) <!-- Auditor -->
				<!-- Ver como hacer el visitante que solo puede ver -->
			@endif

		@endforeach

		@include('en.menu.hallazgos')
		@include('en.menu.planes_accion')
		@include('en.menu.reportes')
	@endif
@else
					<li>&nbsp;</li>
@endif
			</ul>

		
		</div>