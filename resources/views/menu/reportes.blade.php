<li class="dropdown">
					<a href="#" class="dropdown-toggle">
						<i class="fa fa-bar-chart-o"></i>
						<span class="hidden-xs">Reportes</span>
					</a>
					<ul class="dropdown-menu" style="{{ dropDown4() }}">
						<li>{!!HTML::link('heatmap','Mapa de Calor',['class'=>activeMenu('heatmap')])!!}</li>
						<li>{!!HTML::link('risk_matrix','Matriz de Riesgos',['class'=>activeMenu('risk_matrix')])!!}</li>
						<li>{!!HTML::link('matrices','Matriz de Controles',['class'=>activeMenu('matrices')])!!}</li>
						<li>{!!HTML::link('reporte_hallazgos','Hallazgos',['class'=>activeMenu('reporte_hallazgos')])!!}</li>
						<li>{!!HTML::link('reporte_audits','Planes de Auditor&iacute;a',['class'=>activeMenu('reporte_audits')])!!}</li>
						<li>{!!HTML::link('reporte_planes','Planes de Acci&oacute;n',['class'=>activeMenu('reporte_planes')])!!}</li>
						<li>{!!HTML::link('graficos_controles','Gr&aacute;ficos Controles',['class'=>activeMenu('graficos_controles')])!!}</li>
						<li>{!!HTML::link('graficos_auditorias','Gr&aacute;ficos Auditor&iacute;as',['class'=>activeMenu('graficos_auditorias')])!!}</li>
						<li>{!!HTML::link('graficos_planes_accion','Gr&aacute;ficos Planes de Acci&oacute;n',['class'=>activeMenu('graficos_planes_accion')])!!}</li>
						<li>{!!HTML::link('reporte_personalizado','Reporte Personalizado',['class'=>activeMenu('reporte_personalizado')])!!}</li>
					</ul>
				</li>