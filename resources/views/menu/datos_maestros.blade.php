<li class="dropdown">
	<a href="#" class="dropdown-toggle">
		<i class="fa fa-bars"></i>
		<span class="hidden-xs">Datos Maestros</span>
	</a>
	<ul class="dropdown-menu" style="{{ dropDown1() }}">
		<li>{!!HTML::link('organization','Organizaciones',['class'=>activeMenu('organization')])!!}</li>
		<li>{!!HTML::link('categorias_objetivos','Categor&iacute;as de Objetivos',['class'=>activeMenu('categorias_objetivos')])!!}</li>
		<li>{!!HTML::link('objetivos','Objetivos Corporativos',['class'=>activeMenu('objetivos')])!!}</li>
		<li>{!!HTML::link('procesos','Procesos',['class'=>activeMenu('procesos')])!!}</li>
		<li>{!!HTML::link('subprocesos','Subprocesos',['class'=>activeMenu('subprocesos')])!!}</li>
		<li>{!!HTML::link('categorias_riesgos','Categor&iacute;as de Riesgos',['class'=>activeMenu('categorias_riesgos')])!!}</li>
		<li>{!!HTML::link('riskstype','Riesgos Tipo',['class'=>activeMenu('riskstype')])!!}</li>
		<li>{!!HTML::link('roles','Roles de Usuarios',['class'=>activeMenu('roles')])!!}</li>
		<li>{!!HTML::link('stakeholders','Usuarios',['class'=>activeMenu('stakeholders')])!!}</li>
		<li>{!!HTML::link('causas','Causas',['class'=>activeMenu('causas')])!!}</li>
		<li>{!!HTML::link('efectos','Efectos',['class'=>activeMenu('efectos')])!!}</li>					
	</ul>
</li>