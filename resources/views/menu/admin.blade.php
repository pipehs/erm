<li class="dropdown">
	<a href="#" class="dropdown-toggle">
		<i class="fa fa-wrench"></i>
		<span class="hidden-xs">Administraci&oacute;n del Sistema</span>
	</a>
	<ul class="dropdown-menu" style="{{ dropDown8() }}">
		<li>{!!HTML::link('usuarios','Gestionar usuarios de sistema',['class'=>activeMenu('usuarios')])!!}</li>
		<li>{!!HTML::link('controlled_risk_criteria','Gestionar riesgo controlado',['class'=>activeMenu('controlled_risk_criteria')])!!}</li>		
	</ul>
</li>