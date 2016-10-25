@extends('en.master')

@section('title', 'KRI Monitor')

@section('content')

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li>{!!Html::link('kri','Manage KRI')!!}</li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-m-6">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-user"></i>
					<span>KRI Monitor</span>
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

			On this section you will be able to monitor, create or edit the indicators for the relevant bussiness risks. Also you can assess them. <br><br>

			<div id="risks" style="float: center;">
					
				</div>

				<div id="info_kri" style="float: center;">
					@if ($kri == null)
						<center><b>Still have not created any KRI</b></center><br><br>
					@else
						<table class="table table-bordered table-striped table-hover table-heading table-datatable" style="font-size:11px">
						<thead>
						<th style="vertical-align:top;">KRI</th>
						<th style="vertical-align:top;">Description</th>
						<th style="vertical-align:top;">Periodicity</th>
						<th style="vertical-align:top;">Assessment unit of measurement</th>
						<th style="vertical-align:top;">Assessment</th>
						<th style="vertical-align:top;">Results</th>
						<th style="vertical-align:top;">Assessment description</th>
						<th style="vertical-align:top;">Risk</th>
						<th style="vertical-align:top;">Risk responsable</th>
						<th style="vertical-align:top;">Created date</th>
						<th style="vertical-align:top;">Assessment interval</th>
			@foreach (Session::get('roles') as $role)
				@if ($role != 6)
						<th style="vertical-align:top;">Action</th>
						<th style="vertical-align:top;">Action</th>
						<th style="vertical-align:top;">Action</th>
				<?php break; ?>
				@endif
			@endforeach
						</thead>

						@foreach ($kri as $k)

							<tr>
							<td>{{ $k['name'] }} </td>
							<td>{{ $k['description'] }}</td>

							<td>
							@if ($k['periodicity'] == 0)
								Diary
							@elseif ($k['periodicity'] == 1)
								Weekly
							@elseif ($k['periodicity'] == 2)
								Monthly
							@elseif ($k['periodicity'] == 3)
								Biannual
							@elseif ($k['periodicity'] == 4)
								Annual
							@elseif ($k['periodicity'] == 5)
								Each time it occurs
							@else
								Not defined
							@endif
							</td>
							<td>
							@if ($k['uni_med'] == 0)
								Percentage
							@elseif ($k['uni_med'] == 1)
								Amount
							@elseif ($k['uni_med'] == 2)
								Quantity
							@endif
							</td>
							<td>{{ $k['last_eval'] }}</td>
							<td>
							@if ($k['eval'] == 0)
								<ul class="semaforo verde"><li></li><li></li><li></li></ul>
							@elseif ($k['eval'] == 1)
								<ul class="semaforo amarillo"><li></li><li></li><li></li></ul>
							@elseif ($k['eval'] == 2)
								<ul class="semaforo rojo"><li></li><li></li><li></li></ul>
							@elseif ($k['eval'] == 3)
								None
							@endif
							</td>
							<td>{{ $k['description_eval'] }}</td>
							<td>{{ $k['risk'] }}</td>
							<td>
							@if ($k['risk_stakeholder'] == NULL)
								Not specified
							@else
								{{ $k['risk_stakeholder'] }}
							@endif
							</td>
							<td>{{ $k['created_at'] }}</td>
							<td>
							@if ($k['date_min'] != null)
								{{ $k['date_min'] }} al {{ $k['date_max'] }}
							@else
								None
							@endif
							</td>
			@foreach (Session::get('roles') as $role)
				@if ($role != 6)
							<td><a href="kri.edit.{{ $k['id'] }}" class="btn btn-primary">Edit</a></td>
							<td><a href="kri.evaluar.{{ $k['id'] }}" class="btn btn-success">Assess</a></td>
							<td><button class="btn btn-danger" onclick="eliminar2({{ $k['id'] }},'{{ $k['name'] }}','kri','The KRI')">Delete</button></td>
				<?php break; ?>
				@endif
			@endforeach
							</tr>
						@endforeach
						</table>
					@endif
			@foreach (Session::get('roles') as $role)
				@if ($role != 6)
					<center><a href="kri.create" class="btn btn-success">Create new KRI</a></center>
				<?php break; ?>
				@endif
			@endforeach
				</div>
				</br>

			</div>
		</div>
	</div>
</div>
@stop


@section('scripts2')

@stop