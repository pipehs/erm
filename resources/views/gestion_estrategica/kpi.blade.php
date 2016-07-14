@extends('master')

@section('title', 'Gesti&oacute;n Estrat&eacute;gica - KPI')

@section('content')

{!!Html::style('assets/css/mapas.css')!!}

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li>{!!Html::link('kpi','KPI')!!}</li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-m-6">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-user"></i>
					<span>KPI</span>
				</div>
				<div class="box-icons">
					<a class="collapse-link">
						<i class="fa fa-chevron-up"></i>
					</a>
					<a class="expand-link">
						<i class="fa fa-expand"></i>
					</a>
					<a class="close-link">
						<i class="fa fa-times"></i>
					</a>
				</div>
				<div class="no-move"></div>
			</div>
			<div class="box-content">

			@if(Session::has('message'))
				<div class="alert alert-success alert-dismissible" role="alert">
				{{ Session::get('message') }}
				</div>
			@endif

			@if ($errors->any())
				<div class="alert alert-danger alert-dismissible" role="alert">
					<ul>
					@foreach ($errors->all() as $error)
						<li>{{ $error }}</li>
					@endforeach
					</ul>
				</div>
			@endif

			@if(Session::has('error'))
				<div class="alert alert-danger alert-dismissible" role="alert">
				{{ Session::get('error') }}
				</div>
			@endif

			En esta secci&oacute;n podr&aacute; gestionar los KPI para cada organizaci&oacute;n ingresada en el sistema.<br><br>

			{!!Form::open(['route'=>'kpi2','method'=>'GET','class'=>'form-horizontal'])!!}
			<div class="form-group">
				{!!Form::label('Seleccione organización',null,['class'=>'col-sm-4 control-label'])!!}
				<div class="col-sm-3">
					{!!Form::select('organization_id',$organizations,null, 
							 	   ['id' => 'orgs','required'=>'true','placeholder'=>'- Seleccione -'])!!}
				</div>
			</div>

			<div class="form-group">
						<center>
						{!!Form::submit('Seleccionar', ['class'=>'btn btn-success','id'=>'guardar'])!!}
						</center>
			</div>
			{!!Form::close()!!}

@if (isset($kpi))
	<div style="float: center;">
		@if (empty($kpi))
			<center><b>Aun no se han creado ning&uacute;n KPI para {{$org_selected}}.</b></center><br><br>
		@else
			<h4><b>{{ $org_selected }}</b></h4>
			<table class="table table-bordered table-striped table-hover table-heading table-datatable" style="font-size:11px">
				<thead>
				<th style="vertical-align:top;">Perspectivas</th>
				<th style="vertical-align:top;">Objetivo</th>
				<th style="vertical-align:top;">Indicador</th>
				<th style="vertical-align:top;">Descripci&oacute;n</th>
				<th style="vertical-align:top;">Responsable</th>
				<th style="vertical-align:top;">&Uacute;ltima medici&oacute;n</th>
				<th style="vertical-align:top;">Valor</th>
				<th style="vertical-align:top;">Meta</th>

				@foreach (Session::get('roles') as $role)
					@if ($role != 6)
						<th style="vertical-align:top;">Acci&oacute;n</th>
						<th style="vertical-align:top;">Acci&oacute;n</th>
						<th style="vertical-align:top;">Acci&oacute;n</th>
						<?php break; ?>
					@endif
				@endforeach
				</thead>
						
				@if ($financiera == 0)
						<tr>
							<th rowspan="1">Financiera</th>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
						</tr>
						@else
							<?php $cont = 0; ?>
							@foreach ($kpi as $k)
								@if ($k['perspective'] == 1)
									@if ($cont == 0)
									<tr>
										<th rowspan="{{ $procesos }}">Financiera</th>
										<td>{{ $k['objective'] }}</td>
										<td>{{ $k['name'] }}</td>
										<td>{{ $k['description'] }}</td>
										<td>{{ $k['stakeholder'] }}</td>
										<td>{{ $k['date_last_eval'] }}</td>
										<td>{{ $k['last_eval'] }}</td>
										<td>{{ $k['goal'] }}</td>
										<td>{!!link_to_route('kpi.edit', $title = 'Editar', $parameters=['id'=>$k['id'],'org_id'=>$org_id], $attributes = ['class'=>'btn btn-success']) !!}</td>
										<td>{!! link_to_route('kpi.evaluate', $title = 'Medir', $parameters = ['id'=>$k['id'],'org_id'=>$org_id], $attributes = ['class'=>'btn btn-primary']) !!}</td>
										@if ($k['status'] == 1 && !$k['status_validate'])
											<td>KPI validado</td>
										@elseif ($k['status_validate'])
											<td>
												<button class="btn btn-danger" onclick="validatekpi({{ $k['id'] }},
												'{{ $k['name'] }}')">Validar</button>
											</td>
										@else	
											<td>No hay medici&oacute;n para validar</td>
										@endif
									</tr>
									<?php $cont += 1; ?>
									@else
									<tr>
										<td>{{ $k['objective'] }}</td>
										<td>{{ $k['name'] }}</td>
										<td>{{ $k['description'] }}</td>
										<td>{{ $k['stakeholder'] }}</td>
										<td>{{ $k['date_last_eval'] }}</td>
										<td>{{ $k['last_eval'] }}</td>
										<td>{{ $k['goal'] }}</td>
										<td>{!!link_to_route('kpi.edit', $title = 'Editar', $parameters=['id'=>$k['id'],'org_id'=>$org_id], $attributes = ['class'=>'btn btn-success']) !!}</td>
										<td>{!! link_to_route('kpi.evaluate', $title = 'Medir', $parameters = ['id'=>$k['id'],'org_id'=>$org_id], $attributes = ['class'=>'btn btn-primary']) !!}</td>
										@if ($k['status'] == 1 && !$k['status_validate'])
											<td>KPI validado</td>
										@elseif ($k['status_validate'])
											<td>
												<button class="btn btn-danger" onclick="validatekpi({{ $k['id'] }},
												'{{ $k['name'] }}')">Validar</button>
											</td>
										@else	
											<td>No hay medici&oacute;n para validar</td>
										@endif
									</tr>
									@endif
								@endif
							@endforeach
						@endif

				@if ($procesos == 0)
						<tr>
							<th rowspan="1">Procesos</th>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
						</tr>
						@else
							<?php $cont = 0; ?>
							@foreach ($kpi as $k)
								@if ($k['perspective'] == 2)
									@if ($cont == 0)
									<tr>
										<th rowspan="{{ $procesos }}">Procesos</th>
										<td>{{ $k['objective'] }}</td>
										<td>{{ $k['name'] }}</td>
										<td>{{ $k['description'] }}</td>
										<td>{{ $k['stakeholder'] }}</td>
										<td>{{ $k['date_last_eval'] }}</td>
										<td>{{ $k['last_eval'] }}</td>
										<td>{{ $k['goal'] }}</td>
										<td>{!!link_to_route('kpi.edit', $title = 'Editar', $parameters=['id'=>$k['id'],'org_id'=>$org_id], $attributes = ['class'=>'btn btn-success']) !!}</td>
										<td>{!! link_to_route('kpi.evaluate', $title = 'Medir', $parameters = ['id'=>$k['id'],'org_id'=>$org_id], $attributes = ['class'=>'btn btn-primary']) !!}</td>
										@if ($k['status'] == 1 && !$k['status_validate'])
											<td>KPI validado</td>
										@elseif ($k['status_validate'])
											<td>
												<button class="btn btn-danger" onclick="validatekpi({{ $k['id'] }},
												'{{ $k['name'] }}')">Validar</button>
											</td>
										@else	
											<td>No hay medici&oacute;n para validar</td>
										@endif
									</tr>
									<?php $cont += 1; ?>
									@else
									<tr>
										<td>{{ $k['objective'] }}</td>
										<td>{{ $k['name'] }}</td>
										<td>{{ $k['description'] }}</td>
										<td>{{ $k['stakeholder'] }}</td>
										<td>{{ $k['date_last_eval'] }}</td>
										<td>{{ $k['last_eval'] }}</td>
										<td>{{ $k['goal'] }}</td>
										<td>{!!link_to_route('kpi.edit', $title = 'Editar', $parameters=['id'=>$k['id'],'org_id'=>$org_id], $attributes = ['class'=>'btn btn-success']) !!}</td>
										<td>{!! link_to_route('kpi.evaluate', $title = 'Medir', $parameters = ['id'=>$k['id'],'org_id'=>$org_id], $attributes = ['class'=>'btn btn-primary']) !!}</td>
										@if ($k['status'] == 1 && !$k['status_validate'])
											<td>KPI validado</td>
										@elseif ($k['status_validate'])
											<td>
												<button class="btn btn-danger" onclick="validatekpi({{ $k['id'] }},
												'{{ $k['name'] }}')">Validar</button>
											</td>
										@else	
											<td>No hay medici&oacute;n para validar</td>
										@endif
									</tr>
									@endif
								@endif
							@endforeach
						@endif
						@if ($clientes == 0)
						<tr>
							<th rowspan="1">Clientes</th>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
						</tr>
						@else
							<?php $cont = 0; ?>
							@foreach ($kpi as $k)
								@if ($k['perspective'] == 3)
									@if ($cont == 0)
									<tr>
										<th rowspan="{{ $clientes }}">Clientes</th>
										<td>{{ $k['objective'] }}</td>
										<td>{{ $k['name'] }}</td>
										<td>{{ $k['description'] }}</td>
										<td>{{ $k['stakeholder'] }}</td>
										<td>{{ $k['date_last_eval'] }}</td>
										<td>{{ $k['last_eval'] }}</td>
										<td>{{ $k['goal'] }}</td>
										<td>{!!link_to_route('kpi.edit', $title = 'Editar', $parameters=['id'=>$k['id'],'org_id'=>$org_id], $attributes = ['class'=>'btn btn-success']) !!}</td>
										<td>{!! link_to_route('kpi.evaluate', $title = 'Medir', $parameters = ['id'=>$k['id'],'org_id'=>$org_id], $attributes = ['class'=>'btn btn-primary']) !!}</td>
										@if ($k['status'] == 1 && !$k['status_validate'])
											<td>KPI validado</td>
										@elseif ($k['status_validate'])
											<td>
												<button class="btn btn-danger" onclick="validatekpi({{ $k['id'] }},
												'{{ $k['name'] }}')">Validar</button>
											</td>
										@else	
											<td>No hay medici&oacute;n para validar</td>
										@endif
									</tr>
									<?php $cont += 1; ?>
									@else
									<tr>
										<td>{{ $k['objective'] }}</td>
										<td>{{ $k['name'] }}</td>
										<td>{{ $k['description'] }}</td>
										<td>{{ $k['stakeholder'] }}</td>
										<td>{{ $k['date_last_eval'] }}</td>
										<td>{{ $k['last_eval'] }}</td>
										<td>{{ $k['goal'] }}</td>
										<td>{!!link_to_route('kpi.edit', $title = 'Editar', $parameters=['id'=>$k['id'],'org_id'=>$org_id], $attributes = ['class'=>'btn btn-success']) !!}</td>
										<td>{!! link_to_route('kpi.evaluate', $title = 'Medir', $parameters = ['id'=>$k['id'],'org_id'=>$org_id], $attributes = ['class'=>'btn btn-primary']) !!}</td>
										@if ($k['status'] == 1 && !$k['status_validate'])
											<td>KPI validado</td>
										@elseif ($k['status_validate'])
											<td>
												<button class="btn btn-danger" onclick="validatekpi({{ $k['id'] }},
												'{{ $k['name'] }}')">Validar</button>
											</td>
										@else	
											<td>No hay medici&oacute;n para validar</td>
										@endif
									</tr>
									@endif
								@endif
							@endforeach
						@endif

						@if ($aprendizaje == 0)
						<tr>
							<th rowspan="1">Aprendizaje</th>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
						</tr>
						@else
							<?php $cont = 0; ?>
							@foreach ($kpi as $k)
								@if ($k['perspective'] == 4)
									@if ($cont == 0)
									<tr>
										<th rowspan="{{ $aprendizaje }}">Aprendizaje</th>
										<td>{{ $k['objective'] }}</td>
										<td>{{ $k['name'] }}</td>
										<td>{{ $k['description'] }}</td>
										<td>{{ $k['stakeholder'] }}</td>
										<td>{{ $k['date_last_eval'] }}</td>
										<td>{{ $k['last_eval'] }}</td>
										<td>{{ $k['goal'] }}</td>
										<td>{!!link_to_route('kpi.edit', $title = 'Editar', $parameters=['id'=>$k['id'],'org_id'=>$org_id], $attributes = ['class'=>'btn btn-success']) !!}</td>
										<td>{!! link_to_route('kpi.evaluate', $title = 'Medir', $parameters = ['id'=>$k['id'],'org_id'=>$org_id], $attributes = ['class'=>'btn btn-primary']) !!}</td>
										@if ($k['status'] == 1 && !$k['status_validate'])
											<td>KPI validado</td>
										@elseif ($k['status_validate'])
											<td>
												<button class="btn btn-danger" onclick="validatekpi({{ $k['id'] }},
												'{{ $k['name'] }}')">Validar</button>
											</td>
										@else	
											<td>No hay medici&oacute;n para validar</td>
										@endif
									</tr>
									<?php $cont += 1; ?>
									@else
									<tr>
										<td>{{ $k['objective'] }}</td>
										<td>{{ $k['name'] }}</td>
										<td>{{ $k['description'] }}</td>
										<td>{{ $k['stakeholder'] }}</td>
										<td>{{ $k['date_last_eval'] }}</td>
										<td>{{ $k['last_eval'] }}</td>
										<td>{{ $k['goal'] }}</td>
										<td>{!!link_to_route('kpi.edit', $title = 'Editar', $parameters=['id'=>$k['id'],'org_id'=>$org_id], $attributes = ['class'=>'btn btn-success']) !!}</td>
										<td>{!! link_to_route('kpi.evaluate', $title = 'Medir', $parameters = ['id'=>$k['id'],'org_id'=>$org_id], $attributes = ['class'=>'btn btn-primary']) !!}</td>
										@if ($k['status'] == 1 && !$k['status_validate'])
											<td>KPI validado</td>
										@elseif ($k['status_validate'])
											<td>
												<button class="btn btn-danger" onclick="validatekpi({{ $k['id'] }},
												'{{ $k['name'] }}')">Validar</button>
											</td>
										@else	
											<td>No hay medici&oacute;n para validar</td>
										@endif
									</tr>
									@endif
								@endif
							@endforeach
						@endif
						</table>

					@endif

					@foreach (Session::get('roles') as $role)
						@if ($role != 6)
							<center><a href="kpi.create.{{$org_id}}" class="btn btn-success">Agregar nuevo KPI</a></center>
						<?php break; ?>
						@endif
					@endforeach
@endif


			</div>
		</div>
	</div>
</div>
@stop


@section('scripts2')
<script>

</script>
@stop