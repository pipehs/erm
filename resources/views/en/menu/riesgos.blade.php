<li class="dropdown">
					<a href="#" class="dropdown-toggle">
						<i class="fa fa-warning"></i>
						<span class="hidden-xs">Risks Management</span>
					</a>
					<ul class="dropdown-menu" style="{{ dropDown2() }}">
						<li class="dropdown">
							<a href="#" class="dropdown-toggle">
								<i class="fa fa-plus-square"></i>
								<span class="hidden-xs">Risk Events</span>
							</a>
							<ul class="dropdown-menu" style="{{ dropDown21() }}">
							@foreach (Session::get('roles') as $role)
								@if ($role != 6)
								<li>{!!HTML::link('crear_encuesta','Create Poll',['class'=>activeMenu('crear_encuesta')])!!}</li>
								<?php break; ?>
								@endif
							@endforeach
								<li>{!!HTML::link('ver_encuestas','Polls',['class'=>activeMenu('ver_encuestas')])!!}</li>
							@foreach (Session::get('roles') as $role)
								@if ($role != 6)
								<li>{!!HTML::link('enviar_encuesta','Send Poll',['class'=>activeMenu('enviar_encuesta')])!!}</li>
								<?php break; ?>
								@endif
							@endforeach
								<li>{!!HTML::link('encuestas','Review Polls',['class'=>activeMenu('encuestas')])!!}</li>
							</ul>
						</li>

						<li><a href="riesgos" class="{{ activeMenu('riesgos') }}">Risk Identification</a></li>

						<li class="dropdown">
							<a href="#" class="dropdown-toggle">
								<i class="fa fa-plus-square"></i>
								<span class="hidden-xs">Risks Assessment</span>
							</a>
							<ul class="dropdown-menu" style="{{ dropDown22() }}">
							@foreach (Session::get('roles') as $role)
								@if ($role != 6)
								<li>{!!HTML::link('evaluacion','Create Poll',['class'=>activeMenu('evaluacion')])!!}</li>
								<?php break; ?>
								@endif
							@endforeach
								<li>{!!HTML::link('evaluacion_agregadas','Polls',['class'=>activeMenu('evaluacion_agregadas')])!!}</li>
							@foreach (Session::get('roles') as $role)
								@if ($role != 6)
								<li>{!!HTML::link('evaluacion_manual','Assess Risk',['class'=>activeMenu('evaluacion_manual')])!!}</li>
								<?php break; ?>
								@endif
							@endforeach
							</ul>
						</li>

						<li class="dropdown">
							<a href="#" class="dropdown-toggle">
								<i class="fa fa-plus-square"></i>
								<span class="hidden-xs">KRI</span>
							</a>
							<ul class="dropdown-menu" style="{{ dropDown23() }}">
								<li><a href="kri" class="{{ activeMenu('kri') }}">KRI Monitor</a></li>
								<li><a href="riesgo_kri" class="{{ activeMenu('riesgo_kri') }}">Risk - KRI</a></li>
							@foreach (Session::get('roles') as $role)
								@if ($role != 6)
								<li><a href="enlazar_riesgos" class="{{ activeMenu('enlazar_riesgos') }}">Link Risks</a></li>
								<?php break; ?>
								@endif
							@endforeach
							</ul>
						</li>
					</ul>
				</li>