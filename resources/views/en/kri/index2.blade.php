@extends('en.master')

@section('title', 'Link Risks')

@section('content')

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li>{!!Html::link('riesgo_kri','Risk - KRI')!!}</li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-m-6">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-user"></i>
					<span>Risk - KRI</span>
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

			@if ($errors->any())
				<div class="alert alert-danger alert-dismissible" role="alert">
					<ul>
					@foreach ($errors->all() as $error)
						<li>{{ $error }}</li>
					@endforeach
					</ul>
				</div>
			@endif

			@if(Session::has('message'))
				<div class="alert alert-success alert-dismissible" role="alert">
				{{ Session::get('message') }}
				</div>
			@endif

			On this section you will be able to view, create, update or asses the indicators filtering it for the bussiness risk or process risk related.

				{!!Form::open(['route'=>'kri.guardar_enlace','method'=>'POST','class'=>'form-horizontal',
				'enctype'=>'multipart/form-data'])!!}
				<div id="cargando"></div>
				<div id="risks" style="float: center;">
					<div class="form-group">
						{!!Form::label('Select risk',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-4">
							<select name="risk_id" id="risk_id" required="true">
								<option value="" selected disabled>- Select -</option>
								<option value="" disabled>- Process risks associated -</option>
								@if ($risk_subprocess != null)
									@foreach ($risk_subprocess as $risk)
										<option value="{{ $risk['id'] }}">
											{{ $risk['name'] }}
										</option>
									@endforeach
								@else
									<option value="" disabled>No process risks associated</option>
								@endif

								@if ($objective_risk != null)
									<option value="" disabled>- Bussiness risks -</option>
									@foreach ($objective_risk as $risk)
										<option value="{{ $risk['id'] }}">
											{{ $risk['name'] }}
										</option>
									@endforeach
								@else
									<option value="" disabled>No bussiness risks</option>
								@endif
							</select>
						</div>
					</div>
				</div>

				<div id="info_kri" style="float: center;">

				</div>
				</br>

				{!!Form::close()!!}

				<center>
					{!! link_to_route('kri', $title = 'Return', $parameters = NULL,
                 		$attributes = ['class'=>'btn btn-danger'])!!}
				<center>
			</div>
		</div>
	</div>
</div>
@stop


@section('scripts2')
<script>
$("#risk_id").change(function() {
	if ($("#risk_id").val() != '') //Si es que se ha seleccionado valor válido de riesgo
	{
		//Añadimos la imagen de carga en el contenedor
		$('#cargando').html('<div><center><img src="/bgrcdemo2/assets/img/loading.gif" width="19" height="19"/></center></div>');
		//se obtienen controles asociados a los riesgos presentes en el plan de prueba seleccionado
		//primero obtenemos controles asociados a los riesgos de negocio

		//obtenemos kri del riesgo seleccionado
		$.get('get_kri.'+$("#risk_id").val(), function (result) {
				
				$("#cargando").html('<br>');
				$("#info_kri").empty();

				if (result == "null")
				{
					var info = "<center>Still have not created indicators for the risk ";
					info += $("#risk_id option:selected").text() + ".<br><br></center>";
			@foreach (Session::get('roles') as $role)
				@if ($role != 6)
					info += '<center><a href="kri.create2.'+$("#risk_id").val()+'" class="btn btn-success">Create KRI</a</center>';
				<?php break; ?>
				@endif
			@endforeach
					$("#info_kri").append(info);
				}

				else
				{

					var table_row= '<table class="table table-bordered table-striped table-hover table-heading table-datatable" style="font-size:11px">';
					table_row += '<thead>';
					table_row += '<th>KRI</th>';
					table_row += '<th >Description</th>';
					table_row += '<th>Assessment unit of measurement';
					table_row += '<th>Assessment</th>';
					table_row += '<th>Result</th>';
					table_row += '<th>Description of the assessment</th>';
					table_row += '<th>Risk</th>';
					table_row += '<th>Risk responsable</th>';
					table_row += '<th>Assessment date</th>';
			@foreach (Session::get('roles') as $role)
				@if ($role != 6)
					table_row += '<th>Action</th>';
					table_row += '<th>Action</th>';
				<?php break; ?>
				@endif
			@endforeach
					table_row += '</thead>';

					

					//parseamos datos obtenidos
					var datos = JSON.parse(result);
					
					//seteamos datos
					$(datos).each( function() {
							table_row += '<tr><td>'+this.name+'</td><td>'+this.description+'</td>';

							if (this.uni_med == 0)
								uni_med = "Percentage"
							else if (this.uni_med == 1)
								uni_med = "Amount"
							else if (this.uni_med == 2)
								uni_med = "Quantity"
							table_row += '<td>'+uni_med+'</td>';
							table_row += '<td>'+this.last_eval+'</td>';

							//mostramos evaluación
							if (this.eval == 0)
							{
								table_row += '<td><ul class="semaforo verde"><li></li><li></li><li></li></ul></td>';	
							}
							else if (this.eval == 1)
							{
								table_row += '<td><ul class="semaforo amarillo"><li></li><li></li><li></li></ul></td>';	
							}
							else if (this.eval == 2)
							{
								table_row += '<td><ul class="semaforo rojo"><li></li><li></li><li></li></ul></td>';	
							}
							else
							{
								table_row += '<td>'+this.eval+'</td>';
							}
							

							table_row += '<td>'+this.description_eval+'</td>';
							table_row += '<td>'+ $("#risk_id option:selected").text() +'</td>';

							if (this.stakeholder == null)
								resp = "None"
							else
								resp = this.stakeholder

							table_row += '<td>'+resp+'</td>';
							table_row += '<td>'+this.date_last+'</td>';
					@foreach (Session::get('roles') as $role)
						@if ($role != 6)
							table_row += '<td><a href="kri.edit.'+this.id+'" class="btn btn-primary">Edit</a></td>';
							table_row += '<td><a href="kri.evaluar.'+this.id+'" class="btn btn-success">Assess</a></td>';
							<?php break; ?>
						@endif
					@endforeach
							table_row += '</tr>';

					
					});

					table_row += '</table>';
			@foreach (Session::get('roles') as $role)
				@if ($role != 6)
					table_row += '<center><a href="kri.create2.'+$("#risk_id").val()+'" class="btn btn-success">Create new KRI</a</center>';
				<?php break; ?>
				@endif
			@endforeach
					$("#info_kri").html(table_row);

				}
		});
	}
	else
	{
		$("#info_kri").empty();
	}

});
</script>
@stop