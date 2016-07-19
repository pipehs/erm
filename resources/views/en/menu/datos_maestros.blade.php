<li class="dropdown">
	<a href="#" class="dropdown-toggle">
		<i class="fa fa-bars"></i>
		<span class="hidden-xs">Master Data</span>
	</a>
	<ul class="dropdown-menu" style="{{ dropDown1() }}">
		<li>{!!HTML::link('organization','Organizations',['class'=>activeMenu('organization')])!!}</li>
		<li>{!!HTML::link('categorias_objetivos','Objective Categories',['class'=>activeMenu('categorias_objetivos')])!!}</li>
		<li>{!!HTML::link('objetivos','Business Objectives',['class'=>activeMenu('objetivos')])!!}</li>
		<li>{!!HTML::link('procesos','Processes',['class'=>activeMenu('procesos')])!!}</li>
		<li>{!!HTML::link('subprocesos','Subprocesses',['class'=>activeMenu('subprocesos')])!!}</li>
		<li>{!!HTML::link('categorias_risks','Risk Categories',['class'=>activeMenu('categorias_risks')])!!}</li>
		<li>{!!HTML::link('riskstype','Template risks',['class'=>activeMenu('riskstype')])!!}</li>
		<li>{!!HTML::link('roles','Stakeholders roles',['class'=>activeMenu('roles')])!!}</li>
		<li>{!!HTML::link('stakeholders','Stakeholders',['class'=>activeMenu('stakeholders')])!!}</li>
		<li>{!!HTML::link('causas','Causes',['class'=>activeMenu('causas')])!!}</li>
		<li>{!!HTML::link('efectos','Effects',['class'=>activeMenu('efectos')])!!}</li>					
	</ul>
</li>