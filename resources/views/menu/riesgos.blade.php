<li class="dropdown">
	<a href="#" class="dropdown-toggle">
	@if (Session::get('org') == 'B-GRC Deloitte')
		<i class="fa fa-plus"></i>
	@else
		<i class="fa fa-warning"></i>
	@endif
		<span class="hidden-xs">Gesti&oacute;n de Riesgos</span>
	</a>
	<ul class="dropdown-menu" style="{{ dropDown2() }}">
		<li>{!!HTML::link('riesgos','Identificación de Riesgos',['class'=>activeMenu('riesgos')])!!}</li>
		<li class="dropdown">
			<a href="#" class="dropdown-toggle">
				<i class="fa fa-plus-square"></i>
				<span class="hidden-xs">Evaluaci&oacute;n de Riesgos</span>
			</a>
			<ul class="dropdown-menu" style="{{ dropDown22() }}">
				@foreach (Session::get('roles') as $role)
					@if ($role != 6)
					<li>{!!HTML::link('evaluacion','Crear Encuesta',['class'=>activeMenu('evaluacion')])!!}</li>
					<?php break; ?>
					@endif
				@endforeach
				<li>{!!HTML::link('evaluacion_agregadas','Ver Encuestas',['class'=>activeMenu('evaluacion_agregadas')])!!}</li>
				@foreach (Session::get('roles') as $role)
					@if ($role != 6)
						<li>{!!HTML::link('evaluacion_manual','Evaluar Riesgo',['class'=>activeMenu('evaluacion_manual')])!!}</li>
						<?php break; ?>
					@endif
				@endforeach
			</ul>
		</li>

		<li class="dropdown">
			<a href="#" class="dropdown-toggle">
				<i class="fa fa-plus-square"></i>
				<span class="hidden-xs">KRI</span>
			</a>
			<ul class="dropdown-menu" style="{{ dropDown23() }}">
				<li><a href="kri" class="{{ activeMenu('kri') }}">Monitor KRI</a></li>
				<li><a href="riesgo_kri" class="{{ activeMenu('riesgo_kri') }}">Riesgo - KRI</a></li>
				@foreach (Session::get('roles') as $role)
					@if ($role != 6)
					<li><a href="enlazar_riesgos" class="{{ activeMenu('enlazar_riesgos') }}">Vincular Riesgos</a></li>
				<?php break; ?>
				@endif
			@endforeach
			</ul>
		</li>
	</ul>
</li>