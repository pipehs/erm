<li class="dropdown">
					<a href="#" class="dropdown-toggle">
						<i class="fa fa-bar-chart-o"></i>
						<span class="hidden-xs">Basics Reports</span>
					</a>
					<ul class="dropdown-menu" style="{{ dropDown4() }}">
						<li>{!!HTML::link('heatmap','Heat Map',['class'=>activeMenu('heatmap')])!!}</li>
						<li>{!!HTML::link('matriz_riesgos','Risks Matrix',['class'=>activeMenu('matriz_riesgos')])!!}</li>
						<li>{!!HTML::link('matrices','Controls Matrix',['class'=>activeMenu('matrices')])!!}</li>
						<li>{!!HTML::link('reporte_hallazgos','Issues',['class'=>activeMenu('reporte_hallazgos')])!!}</li>
						<li>{!!HTML::link('reporte_planes','Action Plans',['class'=>activeMenu('reporte_planes')])!!}</li>
						<li>{!!HTML::link('graficos_controles','Control Charts',['class'=>activeMenu('graficos_controles')])!!}</li>
						<li>{!!HTML::link('graficos_auditorias','Audit Charts',['class'=>activeMenu('graficos_auditorias')])!!}</li>
						<li>{!!HTML::link('graficos_planes_accion','Action Plans Charts',['class'=>activeMenu('graficos_planes_accion')])!!}</li>
					</ul>
				</li>