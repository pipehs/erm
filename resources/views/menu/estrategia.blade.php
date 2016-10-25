<li class="dropdown">
	<a href="#" class="dropdown-toggle">
		<i class="fa fa-sitemap"></i>
		<span class="hidden-xs">Gesti&oacute;n Estrat&eacute;gica</span>
	</a>
	<ul class="dropdown-menu" style="{{ dropDown7() }}">
	<!--	<li>{!!HTML::link('plan_estrategico','Plan estratégico',['class'=>activeMenu('plan_estrategico')])!!}</li> -->
		<li>{!!HTML::link('objetivos','Planes y objetivos estratégicos',['class'=>activeMenu('objetivos')])!!}</li>
		<li><a href="mapas" class="{{ activeMenu('mapas') }}">Mapa estrat&eacute;gico</a></li>
		<li><a href="kpi" class="{{ activeMenu('kpi') }}">Monitor KPI</a></li>
	</ul>
</li>