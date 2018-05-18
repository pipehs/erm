	
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
		
		<?php $cont = 0; //contador en caso de tener rol de auditor y de audit manager ?>
     	@foreach (Session::get('roles') as $role)
			@if ($role == 1 || $role == 6) <!-- ADMIN TIENE ACCESO A TODO -->
				@include('menu.datos_maestros')
				@include('menu.estrategia')
				@include('menu.riesgos')
				@include('menu.controles')
				@include('menu.auditorias')
				@include('menu.denuncias')
			<?php break; //si es admin terminamos ciclo para no repetir menú ?>
			@endif
		@endforeach
		<!-- ACT 11-04-17: Vuelvo a hacer Foreach sólo para ordenar el menú (ya que por ejemplo, datos maestros aparecía al final por tener id mayor -->
		@foreach (Session::get('roles') as $role)
			@if ($role == 8) <!-- Admin. de datos maestros -->
				@include('menu.datos_maestros')
			@endif
		@endforeach

		@foreach (Session::get('roles') as $role)
			@if ($role == 7) <!-- Admin. de datos maestros -->
				@include('menu.estrategia')
			@endif
		@endforeach

		@foreach (Session::get('roles') as $role)
			@if ($role == 2) <!-- Admin. de riesgo -->
				@include('menu.riesgos')
			@elseif ($role == 3) <!-- Admin. de control -->
				@include('menu.controles')
			@elseif ($role == 4 || $role == 5) <!-- Auditor Manager o Auditor-->
				@if ($cont == 0)
					@include('menu.auditorias')
					<?php $cont = 1; //contador para ver si ya se agregó este menú ?>
				@endif
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
		<!--
			@foreach (Session::get('roles') as $role)
				@if ($role == 1) {{-- Administración del sistema --}}
					@include('menu.admin')
				@endif
			@endforeach
		-->
		<li>
			<a href="cambiopass" class="{{ activeMenu('cambiopass') }}">
				<i class="fa fa-lock"></i>
				<span class="hidden-xs">Cambiar contraseña</span>
			</a>
		</li>

		<li>
			<a href="help" class="{{ activeMenu('help') }}">
				<i class="fa fa-question"></i>
				<span class="hidden-xs">Ayuda</span>
			</a>
		</li>

		<li>
			<a href="support" class="{{ activeMenu('support') }}">
				<i class="fa fa-wrench"></i>
				<span class="hidden-xs">Soporte</span>
			</a>
		</li>

		@foreach (Session::get('roles') as $role)
			@if ($role != 6)
				@if (isset(Auth::user()->superadmin) && Auth::user()->superadmin == 1)
					<li>
						<a href="logs" class="{{ activeMenu('logs') }}">
							<i class="fa fa-th-list"></i>
							<span class="hidden-xs">Registro actividades</span>
						</a>
					</li>
					<?php break; //si es admin terminamos ciclo para no repetir menú ?>
				@endif
			
			@endif
		@endforeach

		@if (isset(Auth::user()->superadmin) && Auth::user()->superadmin == 1)
			<li>
				<a href="importador" class="{{ activeMenu('importador') }}">
					<i class="fa fa-wrench"></i>
					<span class="hidden-xs">Importador Excel</span>
				</a>
			</li>
		@endif

	@endif
@else
					<li>&nbsp;</li>
@endif
			</ul>

		
		</div>