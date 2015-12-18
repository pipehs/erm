	
		<div id="sidebar-left" class="col-xs-2 col-sm-2">
			<ul class="nav main-menu">
				<li>
					<a href="home" class="active ajax-link">
						<i class="fa fa-dashboard"></i>
						<span class="hidden-xs">Inicio</span>
					</a>
				</li>
				<li class="dropdown">
					<a href="#" class="dropdown-toggle">
						<i class="fa fa-bars"></i>
						<span class="hidden-xs">Datos Maestros</span>
					</a>
					<ul class="dropdown-menu">
						<li>{!!HTML::link('organization','Organizaciones')!!}</li>
						<li>{!!HTML::link('categorias_objetivos','Categor&iacute;as de Objetivos')!!}</li>
						<li>{!!HTML::link('objetivos','Objetivos Corporativos')!!}</li>
						<li>{!!HTML::link('procesos','Procesos')!!}</li>
						<li>{!!HTML::link('subprocesos','Subprocesos')!!}</li>
						<li>{!!HTML::link('categorias_riesgos','Categor&iacute;as de Riesgos')!!}</li>
						<li>{!!HTML::link('riesgostipo','Riesgos Tipo')!!}</li>
						<li>{!!HTML::link('stakeholders','Stakeholders')!!}</li>
						<li>{!!HTML::link('causas','Causas')!!}</li>
						<li>{!!HTML::link('efectos','Efectos')!!}</li>					
					</ul>
				</li>
				<li class="dropdown">
					<a href="#" class="dropdown-toggle">
						<i class="fa fa-warning"></i>
						<span class="hidden-xs">Identificaci&oacute;n Evento de Riesgo</span>
					</a>
					<ul class="dropdown-menu">
						<li>{!!HTML::link('crear_encuesta','Crear Encuesta')!!}</li>
						<li>{!!HTML::link('enviar_encuesta','Enviar Encuesta')!!}</li>
						<li>{!!HTML::link('ver_encuesta','Ver Encuestas')!!}</li>
					</ul>
				</li>
				<li><a href="riesgos"><i class="fa fa-hand-o-right"></i>Identificar Riesgo</a></li>
				<li class="dropdown">
					<a href="#" class="dropdown-toggle">
						<i class="fa fa-list-alt"></i>
						<span class="hidden-xs">Evaluaci&oacute;n de Riesgos</span>
					</a>
					<ul class="dropdown-menu">
						<li><a class="ajax-link" href="evaluacion">Creacion de encuestas de evaluacion</a></li>
						<li>{!!HTML::link('evaluacion.encuestas','Encuestas agregadas')!!}</li>
					</ul>
				</li>

				<li><a href="#"><i class="fa fa-folder-open"></i>Gesti&oacute;n de Controles</a></li>

				<li class="dropdown">
					<a href="#" class="dropdown-toggle">
						<i class="fa fa-pencil"></i>
						<span class="hidden-xs">Auditor&iacute;a de Controles</span>
					</a>
					<ul class="dropdown-menu">
						<li>{!!HTML::link('#','Plan de auditor&iacute;a')!!}</li>
						<li>{!!HTML::link('#','Revisar plan de auditor&iacute;a')!!}</li>
						<li>{!!HTML::link('#','Observaciones')!!}</li>
					</ul>
				</li>

				<li class="dropdown">
					<a href="#" class="dropdown-toggle">
						<i class="fa fa-bar-chart-o"></i>
						<span class="hidden-xs">Reportes B&aacute;sicos</span>
					</a>
					<ul class="dropdown-menu">
						<li>{!!HTML::link('heatmap','Ver Mapa de Calor')!!}</li>
						<li>{!!HTML::link('#','Ver Matrices de Control')!!}</li>
						<li>{!!HTML::link('#','Ver Planes de Acci&oacute;n')!!}</li>
						<li>{!!HTML::link('encuestas','Revisi&oacute;n de Encuestas')!!}</li>
					</ul>
				</li>
			</ul>
		</div>