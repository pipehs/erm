<div id="sidebar-left" class="col-xs-2 col-sm-2">
	<ul class="nav main-menu">
	@if (!Auth::guest())
		@if(Session::has('roles'))
			@if (count(Session::get('roles')) > 1 || !in_array('9',Session::get('roles')))
			<li>
				<a href="home" class="{{ activeMenu('home') }}">
					<i class="fa fa-home"></i>
					<span class="hidden-xs">Inicio</span>
				</a>
			</li>	
			@else
			<li>
				<a href="denuncias" class="{{ activeMenu('denuncias') }}">
					<i class="fa fa-home"></i>
					<span class="hidden-xs">Inicio</span>
				</a>
			</li>
			@endif
		
			<?php $cont = 0; //contador en caso de tener rol de auditor y de audit manager 
			$verificador = 0; //Verifica que si tiene admin, no se debe mostrar nada más ?>
	     	@foreach (Session::get('roles') as $role)
				@if ($role == 1 || $role == 6) <!-- ADMIN TIENE ACCESO A TODO -->
					@include('menu.datos_maestros')
					@include('menu.encuestas')
					@include('menu.estrategia')
					@include('menu.riesgos')
					@include('menu.controles')
					@include('menu.auditorias')
					<?php $verificador += 1; ?>
					@if (isset(Auth::user()->superadmin) && Auth::user()->superadmin == 1)
						@include('menu.denuncias')
					@endif
				<?php break; //si es admin terminamos ciclo para no repetir menú ?>
				@endif
			@endforeach

			<!-- ACT 19-06-18: Si es que verificador es mayor a 0, no se hace nada más -->
			@if ($verificador == 0)
				<!-- ACT 11-04-17: Vuelvo a hacer Foreach sólo para ordenar el menú (ya que por ejemplo, datos maestros aparecía al final por tener id mayor -->
				@foreach (Session::get('roles') as $role)
					@if ($role == 8) <!-- Admin. de datos maestros -->
						@include('menu.datos_maestros')
					@endif
				@endforeach

				@foreach (Session::get('roles') as $role)
					@if ($role == 7) <!-- Admin. gestión estratégica -->
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

				@foreach (Session::get('roles') as $role)
					@if ($role == 9) <!-- Admin. canal de denuncias-->
						@include('menu.denuncias')
					@endif
				@endforeach
			@endif

			@if (count(Session::get('roles')) > 1 || !in_array('9',Session::get('roles')))
				@include('menu.hallazgos')
				@include('menu.planes_accion')
				@include('menu.reportes')
			@endif

			@foreach (Session::get('roles') as $role)
				@if ($role != 6 && $role != 9) <!-- Rol distinto de display y admin denuncias -->
					@include('menu.documentos')
					<?php break; ?>
				@endif
			@endforeach
			
			
		<li>
			<a href="cambiopass" class="{{ activeMenu('cambiopass') }}">
			@if (Session::get('org') == 'B-GRC Deloitte')
				<i class="fa fa-plus"></i>
			@else
				<i class="fa fa-lock"></i>
			@endif
				<span class="hidden-xs">Cambiar contraseña</span>
			</a>
		</li>

		<li>
			<a href="help" class="{{ activeMenu('help') }}">
			@if (Session::get('org') == 'B-GRC Deloitte')
				<i class="fa fa-plus"></i>
			@else
				<i class="fa fa-question"></i>
			@endif
				<span class="hidden-xs">Ayuda</span>
			</a>
		</li>

		<li>
			<a href="support" class="{{ activeMenu('support') }}">
			@if (Session::get('org') == 'B-GRC Deloitte')
				<i class="fa fa-plus"></i>
			@else
				<i class="fa fa-wrench"></i>
			@endif
				<span class="hidden-xs">Soporte</span>
			</a>
		</li>

		@foreach (Session::get('roles') as $role)
			@if ($role != 6)
				@if (isset(Auth::user()->superadmin) && Auth::user()->superadmin == 1)
					<li>
						<a href="logs" class="{{ activeMenu('logs') }}">
						@if (Session::get('org') == 'B-GRC Deloitte')
							<i class="fa fa-plus"></i>
						@else
							<i class="fa fa-th-list"></i>
						@endif
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
				@if (Session::get('org') == 'B-GRC Deloitte')
					<i class="fa fa-plus"></i>
				@else
					<i class="fa fa-wrench"></i>
				@endif
					<span class="hidden-xs">Importador Excel</span>
				</a>
			</li>
		@endif

		@if (isset(Auth::user()->superadmin) && Auth::user()->superadmin == 1)
			@include('menu.admin')
		@endif
	@endif
@else
					<li>&nbsp;</li>
@endif
			</ul>

		
		</div>