<li class="dropdown">
	<a href="#" class="dropdown-toggle">
		<i class="fa fa-sitemap"></i>
		<span class="hidden-xs">Gesti&oacute;n Estrat&eacute;gica</span>
	</a>
	<ul class="dropdown-menu" style="{{ dropDown7() }}">
		<li>{!!HTML::link('objetivos','Planes y objetivos estratégicos',['class'=>activeMenu('objetivos')])!!}</li>
		<li>{!!HTML::link('mapas','Mapa estratégico',['class'=>activeMenu('mapas')])!!}</li>
		<li>{!!HTML::link('kpi','Monitor KPI',['class'=>activeMenu('kpi')])!!}</li>
	</ul>
</li>