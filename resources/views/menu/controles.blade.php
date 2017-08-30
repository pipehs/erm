<li class="dropdown">
	<a href="#" class="dropdown-toggle">
		<i class="fa fa-check-circle"></i>
		<span class="hidden-xs">Gesti&oacute;n de Controles</span>
	</a>
	<ul class="dropdown-menu" style="{{ dropDown5() }}">
		<li><a href="controles" class="{{ activeMenu('controles') }}">Mantenedor de Controles</a></li>
	<!--
	@foreach (Session::get('roles') as $role)
		@if ($role != 6)
			<li><a href="evaluar_controles" class="{{ activeMenu('evaluar_controles') }}">Evaluaci&oacute;n de Controles</a></li>
		<?php break; ?>
		@endif
	@endforeach
	-->
	</ul>
</li>