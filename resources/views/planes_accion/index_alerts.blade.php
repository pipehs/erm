@extends('master')

@section('title', 'Alertas de Planes de Acci&oacute;n')

@section('content')
<style>
.swal-title 
{
	margin: 0px;
	font-size: 16px;
	box-shadow: 0px 1px 1px rgba(0, 0, 0, 0.21);
	margin-bottom: 28px;
}	
</style>
<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li>{!!Html::link('alert_action_plans','Alertas de Planes de Acci&oacute;n')!!}</li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-m-6">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-user"></i>
					<span>Alertas de Planes de acci&oacute;n</span>
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
@if (!isset($action_plans))
			En esta sección podrá generar alertas de forma manual para los planes de acción que se encuentran abiertos y además están vencidos o próximos a vencer.<br><br>
			<div id="cargando"><br></div>

			{!!Form::open(['route'=>'alert_action_plans2','method'=>'GET','class'=>'form-horizontal'])!!}
			<div class="form-group">
				<label for="kind" class="col-sm-4 control-label">
					Seleccione si desea ver planes de acción vencidos o próximos a vencer
				</label>
				<div class="col-sm-3">
					{!!Form::select('kind',['1'=>'Planes vencidos','2'=>'Planes próximos a vencer'],null, 
							 	   ['id' => 'kind','required'=>'true','placeholder'=>'- Seleccione -'])!!}
				</div>
			</div>

			<div class="form-group">
				<center>
					{!!Form::submit('Seleccionar', ['class'=>'btn btn-success','id'=>'guardar'])!!}
				</center>
			</div>
			{!!Form::close()!!}

			<div id="tipo" style="display:none;">
			
			</div>

@elseif (isset($action_plans))

	<h4><b>{{ $title }}</b></h4>
	{!!Form::open(['route'=>'send_alert_pa','method'=>'POST','id'=>'checkboxes'])!!}
		<table id="datatable-2" class="table table-bordered table-striped table-hover table-heading table-datatable" style="font-size:11px">
		<thead>
			<th>Organización<label><input type="text" placeholder="Filtrar" /></label></th>
			<th>Hallazgo<label><input type="text" placeholder="Filtrar" /></label></th>
			<th>Plan de acci&oacute;n<label><input type="text" placeholder="Filtrar" /></label></th>
			<th>Responsable<label><input type="text" placeholder="Filtrar" /></label></th>
			<th>Fecha final plan<label><input type="text" placeholder="Filtrar" /></label></th>
			<th>Porcentaje avance<label><input type="text" placeholder="Filtrar" /></label></th>
			<th>Comentarios avances<label><input type="text" placeholder="Filtrar" /></label></th>
			<th>Fecha avance<label><input type="text" placeholder="Filtrar" /></label></th>
			<th>Vencimiento</th>
			<th>Alerta enviada<label><input type="text" placeholder="Filtrar" /></label></th>
	@foreach (Session::get('roles') as $role)
		@if ($role != 6)
			<th>Seleccionar todos <br>
				<div class="checkbox">
					<label>
						<input type="checkbox" name="check_all" id="check_all" value="1">
						<i class="fa fa-square-o"></i>
					</label>
				</div>
			</th>
			<?php break; ?>
		@endif
	@endforeach
		</thead>

	@foreach ($action_plans as $ap)
			<tr>
				<td>{{ $ap['org_name'] }}</td>
				<td>{{ $ap['issue_name'] }} - {{ $ap['issue_description'] }}</td>
				<td>
				@if ($ap['description'] == '')
					No se ha definido descripci&oacute;n
				@else
					@if (strlen($ap['description']) > 100)
						<div id="action_plan_{{$ap['id']}}" title="{{ $ap['description'] }}">{{ $ap['short_des'] }}...
						<div style="cursor:hand" onclick="expandir3({{ $ap['id'] }},'{{ $ap['description'] }}','{{ $ap['short_des'] }}')">
						<font color="CornflowerBlue">Ver completo</font>
						</div></div>
					@else
						{{ $ap['description'] }}
					@endif
				@endif
				</td>
				<td>{{ $ap['resp_name'] }}.<br>{{ $ap['resp_mail'] }}</td>
				<td>{{ date('d-m-Y',strtotime($ap['final_date'])) }}</td>
				<td>
				@if ($ap['percentage'] == NULL)
					No se ha agregado
				@else
					{{ $ap['percentage'] }}%
				@endif
				</td>
				<td>
				@if ($ap['percentage_comments'] == '' || $ap['percentage_comments'] == NULL)
					No se han agregado
				@else
					{{ $ap['percentage_comments'] }}
				@endif
				</td>
				<td>
				@if ($ap['percentage_date'] == NULL)
					No se ha agregado
				@else
					{{ date('d-m-Y',strtotime($ap['percentage_date'])) }}
				@endif
				</td>
				<td>
				@if ($ap['dif'] < 0)
					Vencío hace {{ abs($ap['dif']) }} dias
				@else
					Vence en {{ $ap['dif'] }} dias
				@endif
				</td>
				<td>
				@if (!empty($ap['ap_alert']))
					<div style="cursor:hand" class="btn btn-info" onclick="infoAlerts({{ $ap['id'] }})">Ver Info</div>
				@else
					No se ha enviado alerta
				@endif
				</td>
		@foreach (Session::get('roles') as $role)
			@if ($role != 6)
				<td>
				<div class="checkbox">
					<label>
						<input type="checkbox" name="plans_id[]" value="{{$ap['id']}}">
						<i class="fa fa-square-o"></i> 
					</label>
				</div>
				</td>
			<?php break; ?>
			@endif
		@endforeach
			</tr>
	@endforeach
	</table>

	<div id="infoalerta" style="display:none;">
			
			</div>
	{!!Form::hidden('subject',null,['id'=>'subject'])!!}
	{!!Form::hidden('cc',null,['id'=>'cc'])!!}
	{!!Form::hidden('cco',null,['id'=>'cco'])!!}
	{!!Form::hidden('message',null,['id'=>'message'])!!}
	{!!Form::close()!!}
	<div class="form-group">
		<center>
			<button type="button" class="btn btn-success" id="generarCorreo">Generar Correo</button>
		</center>
	</div>

	<center>
		<p><a href="#" onclick="history.back()" class="btn btn-danger">Volver</a></p>
	<center>

@endif

<div id="pop2" class="popbox">
	<p>Para ingresar varios correos, sepárelos por punto y coma (;).</p><br>
</div>
			</div>
		</div>
	</div>
</div>
@stop


@section('scripts2')
<script>
$('#generarCorreo').click(function(){
	if(jQuery('#checkboxes input[type=checkbox]:checked').length)
	{
		var title = '<b>Ingrese la información del correo</b>';
		var text = '<form id="formEnvio" name="formEnvio">'
		text += '<div class="form-group">'
		text += '<label for="subject" class="col-sm-3 control-label">Asunto *</label>'
		text += '<div class="col-sm-8">'
		text += '{!!Form::textarea("subject", null, ["id"=>"subject", "class"=>"form-control", "rows"=>"1","cols"=>"3","required" => "true","placeholder"=>"Ingrese asunto"])!!}'
		text += '</div></div>'

		text += '<div class="form-group">'
		text += '<label for="cc" class="col-sm-3 control-label" title="Para ingresar varios correos, sepárelos por punto y coma (;)">CC <a href="#" class="popper" data-popbox="pop2">?</a></label>'
		text += '<div class="col-sm-8">'
		text += '{!!Form::textarea("cc", null, ["id"=>"cc", "class"=>"form-control", "rows"=>"1","cols"=>"3","placeholder"=>"Ingrese copia a correo opcionalmente. Para ingresar varios correos, sepárelos por punto y coma (;)"])!!}'
		text += '</div></div>'

		text += '<div class="form-group">'
		text += '<label for="cco" class="col-sm-3 control-label" title="Para ingresar varios correos, sepárelos por punto y coma (;)">CCO <a href="#" class="popper" data-popbox="pop2">?</a></label>'
		text += '<div class="col-sm-8">'
		text += '{!!Form::textarea("cco", null, ["id"=>"cco", "class"=>"form-control", "rows"=>"1","cols"=>"3","placeholder"=>"Ingrese copia oculta a correo opcionalmente. Para ingresar varios correos, sepárelos por punto y coma (;)"])!!}'
		text += '</div></div>'

		text += '<div class="form-group">'
		text += '<label for="message" class="col-sm-3 control-label" title="No borre las palabras entre {{}}. Para agregar saltos de línea en el correo, ingrese los símbolos // en el lugar que quiere que estos aparezcan">Mensaje * <a href="#" class="popper" data-popbox="pop1">?</a></label>'
		text += '<div class="col-sm-8">'
	@if (isset($message))
		
		text += '{!!Form::textarea("message", $message, ["id"=>"message", "class"=>"form-control", "rows"=>"10","cols"=>"4","required" => "true"])!!}'
	@else
		text += '{!!Form::textarea("message", null, ["id"=>"message", "class"=>"form-control", "rows"=>"6","cols"=>"4","required" => "true"])!!}'
	@endif
		text += '</div></div></form>'

		text += '<br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>'
		text += '<p id="pasunto" style="display:none; color:#FF0000;"><b>Debe ingresar asunto</b></p>'
		text += '<p id="pcc" style="display:none; color:#FF0000;"><b>Debe ingresar un correo en copia válido</b></p>'
		text += '<p id="pmensaje" style="display:none; color:red;"><b>Debe ingresar mensaje</b></p>'
		text += '<div id="pop1" class="popbox">'
		text += '<p>No borre las palabras entre \{\{ \}\}. Para agregar saltos de línea en el correo, ingrese los símbolos // en el lugar que quiere que estos aparezcan.</p><br></div>'
		swal({   
			title: title,
			text: text,
			customClass: 'swal-wide3',   
		   	confirmButtonText: "Enviar",
		   	cancelButtonText: "Cancelar",   
		   	closeOnConfirm: false,
		   	html: true }, 
		   	function()
		   	{
		   		if (document.formEnvio.message.value == "")
		   		{
		   			$('#pmensaje').show();
		   		}
		   		else
		   		{
		   			$('#pmensaje').hide();
		   		}
		   		if (document.formEnvio.subject.value == "")
		   		{
		   			$('#pasunto').show();
		   		}
		   		else
		   		{
		   			$('#pasunto').hide();
		   		}
		   		if (document.formEnvio.cc.value != "")
		   		{
		   			//validamos
		   			if (!validateEmail(document.formEnvio.cc.value))
		   			{
		   				//$('#pcc').show();
		   				document.getElementById('subject').value = document.formEnvio.subject.value;
						document.getElementById('cc').value = document.formEnvio.cc.value;
						document.getElementById('cco').value = document.formEnvio.cco.value;
						document.getElementById('message').value = document.formEnvio.message.value;
		   				$('#checkboxes').submit();
		   			}
		   			else
		   			{
		   				$('#pcc').hide();
		   			}
		   		}
		   		else
		   		{
		   			$('#pcc').hide();
		   		}

		   		if (document.formEnvio.subject.value != "" && document.formEnvio.message.value != "")
		   		{
		   			if (document.formEnvio.cc.value != "")
		   			{
		   				//validamos
			   			if (!validateEmail(document.formEnvio.cc.value))
			   			{
			   				//$('#pcc').show();
			   				document.getElementById('subject').value = document.formEnvio.subject.value;
							document.getElementById('cc').value = document.formEnvio.cc.value;
							document.getElementById('cco').value = document.formEnvio.cco.value;
							document.getElementById('message').value = document.formEnvio.message.value;
			   				$('#checkboxes').submit();
			   			}
			   			else
			   			{
			   				$('#pcc').hide();
			   				$('#pmensaje').hide();
							$('#pasunto').hide();

							document.getElementById('subject').value = document.formEnvio.subject.value;
							document.getElementById('cc').value = document.formEnvio.cc.value;
							document.getElementById('cco').value = document.formEnvio.cco.value;
							document.getElementById('message').value = document.formEnvio.message.value;

							$('#checkboxes').submit();
			   			}
			   		}
			   		else
			   		{
			   			$('#pcc').hide();
			   			$('#pmensaje').hide();
						$('#pasunto').hide();

						document.getElementById('subject').value = document.formEnvio.subject.value;
						document.getElementById('cc').value = document.formEnvio.cc.value;
						document.getElementById('cco').value = document.formEnvio.cco.value;
						document.getElementById('message').value = document.formEnvio.message.value;

						$('#checkboxes').submit();
			   		}
		   		}
		   		 
		   	});
	}
	else
	{
		swal({   
			title: 'Atención',
			text: 'Debe seleccionar al menos un plan de acción',
			type: "warning",
			confirmButtonColor: "#22C023",   
			confirmButtonText: "Enviar"
		});
	}
});

function infoAlerts(id)
{
	title = '<b>Información de alertas enviadas previamente</b>'

	$.get('info_alerts.'+id, function (result) {
		//alert(text1);
		text = ''
		datos = JSON.parse(result);
		$(datos).each( function() {
			
			text +='<table class="table table-striped table-datatable">'
			text += '<thead><th colspan="2">Alerta enviada el: '+this.fecha+'</th></thead>'
			if (this.email == null)
			{
				text += '<tr><td>Enviada a:</td><td>No definido</td></tr>'
			}
			else
			{
				text += '<tr><td>Enviada a:</td><td>'+this.email+'</td></tr>'
			}

			if (this.cc != null && this.cc != "")
			{
				text += '<tr><td>CC:</td><td>'+this.cc+'</td></tr>'
			}
			else
			{
				text += '<tr><td>CC:</td><td>Sin copia</td></tr>'
			}

			if (this.cco != null && this.cco != "")
			{
				text += '<tr><td>CCO:</td><td>'+this.cco+'</td></tr>'
			}
			else
			{
				text += '<tr><td>CC:</td><td>Sin copia oculta</td></tr>'
			}

			text += '<tr><td>Mensaje:</td><td>'+this.message+'</td></tr>'
			text += '</table><br>'
		});
		
		//alert(text)
		document.getElementById('infoalerta').value = text
	});

	//alert(document.getElementById('infoalerta').value)
	
	setTimeout(function(){
		swal({   
				title: title,   
				text: $('#infoalerta').val(),
				customClass: 'swal-wide',   
				html: true 
			});
	},800);
}
</script>
@stop