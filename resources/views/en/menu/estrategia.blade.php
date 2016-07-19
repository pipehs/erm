<li class="dropdown">
	<a href="#" class="dropdown-toggle">
		<i class="fa fa-sitemap"></i>
		<span class="hidden-xs">Strategic Management</span>
	</a>
	<ul class="dropdown-menu" style="{{ dropDown7() }}">
		<li><a href="mapas" class="{{ activeMenu('mapas') }}">Strategic Map</a></li>
		<li><a href="kpi" class="{{ activeMenu('kpi') }}">KPI</a></li>
	@foreach (Session::get('roles') as $role)
		@if ($role != 6)
			<li><a href="monitor_kpi" class="{{ activeMenu('monitor_kpi') }}">KPI Monitor</a></li>
		<?php break; ?>
		@endif
	@endforeach
	</ul>
</li>