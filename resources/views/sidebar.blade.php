	
		<div id="sidebar-left" class="col-xs-2 col-sm-2">
		
			<ul class="nav main-menu">
@if (!Auth::guest())
	@if(Session::has('roles'))
     	@foreach (Session::get('roles') as $role)
				<li>
					<a href="home" class="{{ activeMenu('home') }}">
						<i class="fa fa-dashboard"></i>
						<span class="hidden-xs">Inicio</span>
					</a>
				</li>
			@if ($role == 1) <!-- ADMIN TIENE ACCESO A TODO -->
				@include('menu.datos_maestros')
				@include('menu.riesgos')
				@include('menu.controles')
				@include('menu.auditorias')
			<?php break; //si es admin terminamos ciclo para no repetir menú ?>
			@elseif ($role == 2) <!-- Admin. de riesgo -->
				@include('menu.riesgos')
			@elseif ($role == 3) <!-- Admin. de control -->
				@include('menu.controles')
			@elseif ($role == 4) <!-- Auditor -->
				@include('menu.auditorias')
			@endif

				@include('menu.hallazgos')
				@include('menu.reportes')

		@endforeach
	@endif
@else
					<li>&nbsp;</li>
@endif
			</ul>

		
		</div>