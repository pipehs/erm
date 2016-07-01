<li class="dropdown">
					<a href="#" class="dropdown-toggle">
						<i class="fa fa-warning"></i>
						<span class="hidden-xs">Gesti&oacute;n de Riesgos</span>
					</a>
					<ul class="dropdown-menu" style="{{ dropDown2() }}">
						<li class="dropdown">
							<a href="#" class="dropdown-toggle">
								<i class="fa fa-plus-square"></i>
								<span class="hidden-xs">Eventos de Riesgo</span>
							</a>
							<ul class="dropdown-menu" style="{{ dropDown21() }}">
								<li>{!!HTML::link('crear_encuesta','Crear Encuesta',['class'=>activeMenu('crear_encuesta')])!!}</li>
								<li>{!!HTML::link('ver_encuestas','Ver encuestas agregadas',['class'=>activeMenu('ver_encuestas')])!!}</li>
								<li>{!!HTML::link('enviar_encuesta','Enviar Encuesta',['class'=>activeMenu('enviar_encuesta')])!!}</li>
								<li>{!!HTML::link('encuestas','Revisi&oacute;n de Encuestas',['class'=>activeMenu('encuestas')])!!}</li>
							</ul>
						</li>

						<li><a href="riesgos" class="{{ activeMenu('riesgos') }}">Identificaci&oacute;n de Riesgo</a></li>

						<li class="dropdown">
							<a href="#" class="dropdown-toggle">
								<i class="fa fa-plus-square"></i>
								<span class="hidden-xs">Evaluaci&oacute;n de Riesgos</span>
							</a>
							<ul class="dropdown-menu" style="{{ dropDown22() }}">
								<li>{!!HTML::link('evaluacion','Crear Encuesta',['class'=>activeMenu('evaluacion')])!!}</li>
								<li>{!!HTML::link('evaluacion_agregadas','Encuestas agregadas',['class'=>activeMenu('evaluacion_agregadas')])!!}</li>
								<li>{!!HTML::link('evaluacion_manual','Evaluar riesgo',['class'=>activeMenu('evaluacion_manual')])!!}</li>
							</ul>
						</li>

						<li class="dropdown">
							<a href="#" class="dropdown-toggle">
								<i class="fa fa-plus-square"></i>
								<span class="hidden-xs">KRI</span>
							</a>
							<ul class="dropdown-menu" style="{{ dropDown23() }}">
								<li><a href="kri" class="{{ activeMenu('kri') }}">Monitor KRI</a></li>
								<li><a href="riesgo_kri" class="{{ activeMenu('riesgo_kri') }}">Riesgo - KRI</a></li>
								<li><a href="enlazar_riesgos" class="{{ activeMenu('enlazar_riesgos') }}">Vincular Riesgos</a></li>
							</ul>
						</li>
					</ul>
				</li>