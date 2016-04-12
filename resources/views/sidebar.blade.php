	
		<div id="sidebar-left" class="col-xs-2 col-sm-2">
			<ul class="nav main-menu">
				<li>
					<a href="home" class="{{ activeMenu('home') }}">
						<i class="fa fa-dashboard"></i>
						<span class="hidden-xs">Inicio</span>
					</a>
				</li>
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
						<li>{!!HTML::link('roles','Roles de Stakeholders',['class'=>activeMenu('roles')])!!}</li>
						<li>{!!HTML::link('stakeholders','Stakeholders',['class'=>activeMenu('stakeholders')])!!}</li>
						<li>{!!HTML::link('causas','Causas',['class'=>activeMenu('causas')])!!}</li>
						<li>{!!HTML::link('efectos','Efectos',['class'=>activeMenu('efectos')])!!}</li>					
					</ul>
				</li>
				<li class="dropdown">
					<a href="#" class="dropdown-toggle">
						<i class="fa fa-warning"></i>
						<span class="hidden-xs">Identificaci&oacute;n Evento de Riesgo</span>
					</a>
					<ul class="dropdown-menu" style="{{ dropDown2() }}">
						<li>{!!HTML::link('crear_encuesta','Crear Encuesta',['class'=>activeMenu('crear_encuesta')])!!}</li>
						<li>{!!HTML::link('enviar_encuesta','Enviar Encuesta',['class'=>activeMenu('enviar_encuesta')])!!}</li>
						<!--<li>{!!HTML::link('ver_encuesta','Ver Encuestas',['class'=>activeMenu('ver_encuesta')])!!}</li>-->
					</ul>
				</li>
				<li><a href="riesgos" class="{{ activeMenu('riesgos') }}"><i class="fa fa-hand-o-right"></i>Identificaci&oacute;n de Riesgo</a></li>
				<li class="dropdown">
					<a href="#" class="dropdown-toggle">
						<i class="fa fa-list-alt"></i>
						<span class="hidden-xs">Evaluaci&oacute;n de Riesgos</span>
					</a>
					<ul class="dropdown-menu" style="{{ dropDown3() }}">
						<li>{!!HTML::link('evaluacion','Crear Encuesta',['class'=>activeMenu('evaluacion')])!!}</li>
						<li>{!!HTML::link('evaluacion_encuestas','Encuestas agregadas',['class'=>activeMenu('evaluacion_encuestas')])!!}</li>
						<li>{!!HTML::link('evaluacion_manual','Evaluar riesgo',['class'=>activeMenu('evaluacion_manual')])!!}</li>
					</ul>
				</li>

				<li class="dropdown">
					<a href="#" class="dropdown-toggle">
						<i class="fa fa-wrench"></i>
						<span class="hidden-xs">Gesti&oacute;n de Controles</span>
					</a>
					<ul class="dropdown-menu" style="{{ dropDown5() }}">
						<li><a href="controles" class="{{ activeMenu('controles') }}">Gesti&oacute;n de Controles</a></li>
						<li><a href="evaluar_controles" class="{{ activeMenu('evaluar_controles') }}">Evaluaci&oacute;n de Controles</a></li>
					</ul>
				</li>
				<li class="dropdown">
					<a href="#" class="dropdown-toggle">
						<i class="fa fa-check-circle"></i>
						<span class="hidden-xs">Auditor&iacute;a de Riesgos</span>
					</a>
					<ul class="dropdown-menu" style="{{ dropDown6() }}">
						<!--<li><a href="auditorias" class="{{ activeMenu('auditorias') }}">Auditor&iacute;as</a></li>-->
						<li><a href="plan_auditoria" class="{{ activeMenu('plan_auditoria') }}">Planes de auditor&iacute;a</a></li>
						<li><a href="crear_pruebas" class="{{ activeMenu('crear_pruebas') }}">Generar programa de <br>auditor&iacute;a</a></li>
						<!--<li><a href="pruebas" class="{{ activeMenu('pruebas') }}">Reporte pruebas de <br>auditor&iacute;a</a></li>-->
						<li><a href="ejecutar_pruebas" class="{{ activeMenu('ejecutar_pruebas') }}">Ejecutar plan de <br>auditor&iacute;a</a></li>
						<li><a href="supervisar" class="{{ activeMenu('supervisar') }}">Supervisar planes de <br>auditor&iacute;a</a></li>
						<li><a href="notas" class="{{ activeMenu('notas') }}">Revisi&oacute;n de notas</a></li>
						<li><a href="planes_accion" class="{{ activeMenu('planes_accion') }}">Planes de acci&oacute;n</a></li>
					</ul>
				</li>

				<li class="dropdown">
					<a href="#" class="dropdown-toggle">
						<i class="fa fa-bar-chart-o"></i>
						<span class="hidden-xs">Reportes B&aacute;sicos</span>
					</a>
					<ul class="dropdown-menu" style="{{ dropDown4() }}">
						<li>{!!HTML::link('heatmap','Mapa de Calor',['class'=>activeMenu('heatmap')])!!}</li>
						<li>{!!HTML::link('matriz_riesgos','Matriz de Riesgos',['class'=>activeMenu('matriz_riesgos')])!!}</li>
						<li>{!!HTML::link('matrices','Matriz de Control',['class'=>activeMenu('matrices')])!!}</li>
						<li>{!!HTML::link('reporte_hallazgos','Hallazgos',['class'=>activeMenu('hallazgos')])!!}</li>
						<li>{!!HTML::link('reporte_planes','Planes de Acci&oacute;n',['class'=>activeMenu('reporte_planes')])!!}</li>
						<li>{!!HTML::link('encuestas','Revisi&oacute;n de Encuestas',['class'=>activeMenu('encuestas')])!!}</li>
					</ul>
				</li>

				<li class="dropdown">
					<a href="#" class="dropdown-toggle">
						<i class="fa fa-check-circle"></i>
						<span class="hidden-xs">Gestionar KRI</span>
					</a>
					<ul class="dropdown-menu" style="{{ dropDown7() }}">
						<li><a href="kri" class="{{ activeMenu('kri') }}">Monitor KRI</a></li>
						<li><a href="riesgo_kri" class="{{ activeMenu('riesgo_kri') }}">Riesgo - KRI</a></li>
						<li><a href="enlazar_riesgos" class="{{ activeMenu('enlazar_riesgos') }}">Vincular Riesgos</a></li>
					</ul>
				</li>
			</ul>
		</div>