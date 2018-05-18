<!--<li><a href="action_plans" class="{{ activeMenu('mantenplanesaccion') }}"><i class="fa fa-plus-circle"></i>
Planes Acci&oacute;n</a></li>-->

<li class="dropdown">
	<a href="#" class="dropdown-toggle">
		<i class="fa fa-warning"></i>
		<span class="hidden-xs">Planes de acción</span>
	</a>
	<ul class="dropdown-menu" style="{{ dropDown10() }}">
		<li class="dropdown">
			<li><a href="action_plans" class="{{ activeMenu('action_plans') }}">Planes de acción</a></li>

			<li><a href="alert_action_plans" class="{{ activeMenu('alert_action_plans') }}">Alerta Planes Acción</a></li>
		</ul>
	</li>