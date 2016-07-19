<li class="dropdown">
					<a href="#" class="dropdown-toggle">
						<i class="fa fa-check-circle"></i>
						<span class="hidden-xs">Audit Management</span>
					</a>
					<ul class="dropdown-menu" style="{{ dropDown6() }}">
						<!--<li><a href="auditorias" class="{{ activeMenu('auditorias') }}">Auditor&iacute;as</a></li>-->
						<li><a href="plan_auditoria" class="{{ activeMenu('plan_auditoria') }}">Audit Plans</a></li>
						<!--<li><a href="crear_pruebas" class="{{ activeMenu('crear_pruebas') }}">Generar programa de <br>auditor&iacute;a</a></li>-->
						<li><a href="programas_auditoria" class="{{ activeMenu('programas_auditoria') }}">Audit Programs</a></li>
						<!--<li><a href="pruebas" class="{{ activeMenu('pruebas') }}">Reporte pruebas de <br>auditor&iacute;a</a></li>-->
				@foreach (Session::get('roles') as $role)
					@if ($role != 6)
						<li><a href="ejecutar_pruebas" class="{{ activeMenu('ejecutar_pruebas') }}">Audit Ejecution</a></li>
						<li><a href="supervisar" class="{{ activeMenu('supervisar') }}">Audit Supervision</a></li>
						<li><a href="notas" class="{{ activeMenu('notas') }}">Notes Revision</a></li>
						<li><a href="planes_accion" class="{{ activeMenu('planes_accion') }}">Action Plans</a></li>
					<?php break; ?>
					@endif
				@endforeach
					</ul>
				</li>