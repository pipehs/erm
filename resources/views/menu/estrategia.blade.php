<li class="dropdown">
	<a href="#" class="dropdown-toggle">
		<i class="fa fa-sitemap"></i>
		<span class="hidden-xs">Gesti&oacute;n Estrat&eacute;gica</span>
	</a>
	<ul class="dropdown-menu" style="{{ dropDown7() }}">
		<li><a href="mapas" class="{{ activeMenu('mapas') }}">Mapa estrat&eacute;gico</a></li>
		<li><a href="kpi" class="{{ activeMenu('kpi') }}">KPI</a></li>
	@foreach (Session::get('roles') as $role)
		@if ($role != 6)
			<li><a href="monitor_kpi" class="{{ activeMenu('monitor_kpi') }}">Monitorear KPI</a></li>
		<?php break; ?>
		@endif
	@endforeach
	</ul>
</li>