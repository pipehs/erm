@extends('en.master')

@section('title', 'Strategic Management - KPI')

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

			On this section you will be able to manage the KPI's for the different organizations on the system.<br><br>

			{!!Form::open(['route'=>'kpi2','method'=>'GET','class'=>'form-horizontal'])!!}
			<div class="form-group">
				{!!Form::label('Select organization',null,['class'=>'col-sm-4 control-label'])!!}
				<div class="col-sm-3">
					{!!Form::select('organization_id',$organizations,null, 
							 	   ['id' => 'orgs','required'=>'true','placeholder'=>'- Select -'])!!}
				</div>
			</div>

			<div class="form-group">
						<center>
						{!!Form::submit('Select', ['class'=>'btn btn-success','id'=>'guardar'])!!}
						</center>
			</div>
			{!!Form::close()!!}

@if (isset($kpi))
	<div style="float: center;">
		@if (empty($kpi))
			<center><b>There hasn't been created any KPI for {{$org_selected}}</b></center><br><br>
		@else
			<h4><b>{{ $org_selected }}</b></h4>
			<table class="table table-bordered table-striped table-hover table-heading table-datatable" style="font-size:11px">
				<thead>
				<th style="vertical-align:top;">Perspective</th>
				<th style="vertical-align:top;">Objective</th>
				<th style="vertical-align:top;">Indicator</th>
				<th style="vertical-align:top;">Description</th>
				<th style="vertical-align:top;">Responsable</th>
				<th style="vertical-align:top;">Last medition</th>
				<th style="vertical-align:top;">Value</th>
				<th style="vertical-align:top;">Goal</th>

				@foreach (Session::get('roles') as $role)
					@if ($role != 6)
						<th style="vertical-align:top;">Action</th>
						<th style="vertical-align:top;">Action</th>
						<th style="vertical-align:top;">Action</th>
						<?php break; ?>
					@endif
				@endforeach
				</thead>
						
				@if ($financiera == 0)
						<tr>
							<th rowspan="1">Financial</th>
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
										<th rowspan="{{ $procesos }}">Financial</th>
										<td>{{ $k['objective'] }}</td>
										<td>{{ $k['name'] }}</td>
										<td>{{ $k['description'] }}</td>
										<td>{{ $k['stakeholder'] }}</td>
										<td>{{ $k['date_last_eval'] }}</td>
										<td>{{ $k['last_eval'] }}</td>
										<td>{{ $k['goal'] }}</td>
										<td>{!!link_to_route('kpi.edit', $title = 'Edit', $parameters=['id'=>$k['id'],'org_id'=>$org_id], $attributes = ['class'=>'btn btn-success']) !!}</td>
										<td>{!! link_to_route('kpi.evaluate', $title = 'Measure', $parameters = ['id'=>$k['id'],'org_id'=>$org_id], $attributes = ['class'=>'btn btn-primary']) !!}</td>
										@if ($k['status'] == 1 && !$k['status_validate'])
											<td>KPI validated</td>
										@elseif ($k['status_validate'])
											<td>
												<button class="btn btn-danger" onclick="validatekpi({{ $k['id'] }},
												'{{ $k['name'] }}')">Validate</button>
											</td>
										@else	
											<td>There isn't meditions to validate</td>
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
										<td>{!!link_to_route('kpi.edit', $title = 'Edit', $parameters=['id'=>$k['id'],'org_id'=>$org_id], $attributes = ['class'=>'btn btn-success']) !!}</td>
										<td>{!! link_to_route('kpi.evaluate', $title = 'Measure', $parameters = ['id'=>$k['id'],'org_id'=>$org_id], $attributes = ['class'=>'btn btn-primary']) !!}</td>
										@if ($k['status'] == 1 && !$k['status_validate'])
											<td>KPI validated</td>
										@elseif ($k['status_validate'])
											<td>
												<button class="btn btn-danger" onclick="validatekpi({{ $k['id'] }},
												'{{ $k['name'] }}')">Validate</button>
											</td>
										@else	
											<td>There isn't meditions to validate</td>
										@endif
									</tr>
									@endif
								@endif
							@endforeach
						@endif

				@if ($procesos == 0)
						<tr>
							<th rowspan="1">Processes</th>
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
										<th rowspan="{{ $procesos }}">Processes</th>
										<td>{{ $k['objective'] }}</td>
										<td>{{ $k['name'] }}</td>
										<td>{{ $k['description'] }}</td>
										<td>{{ $k['stakeholder'] }}</td>
										<td>{{ $k['date_last_eval'] }}</td>
										<td>{{ $k['last_eval'] }}</td>
										<td>{{ $k['goal'] }}</td>
										<td>{!!link_to_route('kpi.edit', $title = 'Edit', $parameters=['id'=>$k['id'],'org_id'=>$org_id], $attributes = ['class'=>'btn btn-success']) !!}</td>
										<td>{!! link_to_route('kpi.evaluate', $title = 'Measure', $parameters = ['id'=>$k['id'],'org_id'=>$org_id], $attributes = ['class'=>'btn btn-primary']) !!}</td>
										@if ($k['status'] == 1 && !$k['status_validate'])
											<td>KPI validated</td>
										@elseif ($k['status_validate'])
											<td>
												<button class="btn btn-danger" onclick="validatekpi({{ $k['id'] }},
												'{{ $k['name'] }}')">Validate</button>
											</td>
										@else	
											<td>There isn't meditions to validate</td>
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
										<td>{!!link_to_route('kpi.edit', $title = 'Edit', $parameters=['id'=>$k['id'],'org_id'=>$org_id], $attributes = ['class'=>'btn btn-success']) !!}</td>
										<td>{!! link_to_route('kpi.evaluate', $title = 'Measure', $parameters = ['id'=>$k['id'],'org_id'=>$org_id], $attributes = ['class'=>'btn btn-primary']) !!}</td>
										@if ($k['status'] == 1 && !$k['status_validate'])
											<td>KPI validated</td>
										@elseif ($k['status_validate'])
											<td>
												<button class="btn btn-danger" onclick="validatekpi({{ $k['id'] }},
												'{{ $k['name'] }}')">Validate</button>
											</td>
										@else	
											<td>There isn't meditions to validate</td>
										@endif
									</tr>
									@endif
								@endif
							@endforeach
						@endif
						@if ($clientes == 0)
						<tr>
							<th rowspan="1">Customers</th>
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
										<th rowspan="{{ $clientes }}">Customers</th>
										<td>{{ $k['objective'] }}</td>
										<td>{{ $k['name'] }}</td>
										<td>{{ $k['description'] }}</td>
										<td>{{ $k['stakeholder'] }}</td>
										<td>{{ $k['date_last_eval'] }}</td>
										<td>{{ $k['last_eval'] }}</td>
										<td>{{ $k['goal'] }}</td>
										<td>{!!link_to_route('kpi.edit', $title = 'Edit', $parameters=['id'=>$k['id'],'org_id'=>$org_id], $attributes = ['class'=>'btn btn-success']) !!}</td>
										<td>{!! link_to_route('kpi.evaluate', $title = 'Measure', $parameters = ['id'=>$k['id'],'org_id'=>$org_id], $attributes = ['class'=>'btn btn-primary']) !!}</td>
										@if ($k['status'] == 1 && !$k['status_validate'])
											<td>KPI validated</td>
										@elseif ($k['status_validate'])
											<td>
												<button class="btn btn-danger" onclick="validatekpi({{ $k['id'] }},
												'{{ $k['name'] }}')">Validate</button>
											</td>
										@else	
											<td>There isn't meditions to validate</td>
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
										<td>{!!link_to_route('kpi.edit', $title = 'Edit', $parameters=['id'=>$k['id'],'org_id'=>$org_id], $attributes = ['class'=>'btn btn-success']) !!}</td>
										<td>{!! link_to_route('kpi.evaluate', $title = 'Measure', $parameters = ['id'=>$k['id'],'org_id'=>$org_id], $attributes = ['class'=>'btn btn-primary']) !!}</td>
										@if ($k['status'] == 1 && !$k['status_validate'])
											<td>KPI validated</td>
										@elseif ($k['status_validate'])
											<td>
												<button class="btn btn-danger" onclick="validatekpi({{ $k['id'] }},
												'{{ $k['name'] }}')">Validate</button>
											</td>
										@else	
											<td>There isn't meditions to validate</td>
										@endif
									</tr>
									@endif
								@endif
							@endforeach
						@endif

						@if ($aprendizaje == 0)
						<tr>
							<th rowspan="1">Learning</th>
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
										<th rowspan="{{ $aprendizaje }}">Learning</th>
										<td>{{ $k['objective'] }}</td>
										<td>{{ $k['name'] }}</td>
										<td>{{ $k['description'] }}</td>
										<td>{{ $k['stakeholder'] }}</td>
										<td>{{ $k['date_last_eval'] }}</td>
										<td>{{ $k['last_eval'] }}</td>
										<td>{{ $k['goal'] }}</td>
										<td>{!!link_to_route('kpi.edit', $title = 'Edit', $parameters=['id'=>$k['id'],'org_id'=>$org_id], $attributes = ['class'=>'btn btn-success']) !!}</td>
										<td>{!! link_to_route('kpi.evaluate', $title = 'Measure', $parameters = ['id'=>$k['id'],'org_id'=>$org_id], $attributes = ['class'=>'btn btn-primary']) !!}</td>
										@if ($k['status'] == 1 && !$k['status_validate'])
											<td>KPI validated</td>
										@elseif ($k['status_validate'])
											<td>
												<button class="btn btn-danger" onclick="validatekpi({{ $k['id'] }},
												'{{ $k['name'] }}')">Validate</button>
											</td>
										@else	
											<td>There isn't meditions to validate</td>
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
										<td>{!!link_to_route('kpi.edit', $title = 'Edit', $parameters=['id'=>$k['id'],'org_id'=>$org_id], $attributes = ['class'=>'btn btn-success']) !!}</td>
										<td>{!! link_to_route('kpi.evaluate', $title = 'Measure', $parameters = ['id'=>$k['id'],'org_id'=>$org_id], $attributes = ['class'=>'btn btn-primary']) !!}</td>
										@if ($k['status'] == 1 && !$k['status_validate'])
											<td>KPI validated</td>
										@elseif ($k['status_validate'])
											<td>
												<button class="btn btn-danger" onclick="validatekpi({{ $k['id'] }},
												'{{ $k['name'] }}')">Validate</button>
											</td>
										@else	
											<td>There isn't meditions to validate</td>
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
							<center><a href="kpi.create.{{$org_id}}" class="btn btn-success">Create new KPI</a></center>
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