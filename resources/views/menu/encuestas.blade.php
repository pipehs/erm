<li class="dropdown">
	<a href="#" class="dropdown-toggle">
	@if (Session::get('org') == 'B-GRC Deloitte')
		<i class="fa fa-plus"></i>
	@else
		<i class="fa fa-pencil"></i>
	@endif
		<span class="hidden-xs">Gesti&oacute;n de Encuestas</span>
	</a>
	<ul class="dropdown-menu" style="{{ dropDown21() }}">
		<li>
		@foreach (Session::get('roles') as $role)
			@if ($role != 6)
				<li>{!!HTML::link('crear_encuesta','Crear Encuesta', ['class'=>activeMenu('crear_encuesta')])!!}</li>
				<?php break; ?>
			@endif
		@endforeach
		<li>{!!HTML::link('ver_encuestas','Ver Encuestas', ['class'=>activeMenu('ver_encuestas')])!!}</li>
		@foreach (Session::get('roles') as $role)
			@if ($role != 6)
				<li>{!!HTML::link('enviar_encuesta','Enviar Encuesta', ['class'=>activeMenu('enviar_encuesta')])!!}</li>
				<?php break; ?>
			@endif
		@endforeach
		<li>{!!HTML::link('encuestas','Revisi&oacute;n de Encuestas', ['class'=>activeMenu('encuestas')])!!}</li>
	</ul>
</li>