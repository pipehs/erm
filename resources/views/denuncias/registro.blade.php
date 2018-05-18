@extends('master')

@section('title', 'Registro de denuncia')

@section('content')

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li><a href="denuncias.registro">Registro de denuncia</a></li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-m-6">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-ticket"></i>
					<span>Registro de denuncia</span>
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
				<div class="alert alert-danger alert-dismissible" role="alert">
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

			En esta secci&oacute;n podr&aacute; enviar y registrar una consulta, reclamo o denuncia.
			{!!Form::open(['route'=>'updatepass','method'=>'GET','class'=>'form-horizontal'])!!}

			<div class="form-group">
				<label for="terms" class="col-sm-4 control-label">Aceptar <a href="#">términos y condiciones</a></label>
				<div class="col-sm-5">
					<div class="checkbox">
						<label>
							<input type="checkbox" name="terms" value="1" required="true">
							<i class="fa fa-square-o"></i> 
						</label>
					</div>
				</div>
			</div>

			<div class="form-group">
				<label for="kind_id" class="col-sm-4 control-label">
					Seleccione el tipo de caso<a href="#" class="popper" data-popbox="pop1">?</a>
				</label>
				<div class="col-sm-5">
					{!!Form::select('kind_id',['1' => 'Denuncia','2'=>'Reclamo','3'=>'Consulta'], null, ['id' => 'kind_id','placeholder'=>'- Seleccione -'])!!}
				</div>
			</div>

			<div class="form-group">
				<label for="description" class="col-sm-4 control-label">Describa su situación</label>
				<div class="col-sm-5">
					{!!Form::textarea('description',null,['id'=>'descripcion','class'=>'form-control','rows'=>'8','cols'=>'4','required' => 'true'])!!}
				</div>
			</div>

			<div class="form-group">
				<label for="file" class="col-sm-4 control-label">Cargar documentos asociados a la situación (para seleccionar más de uno haga click en ctrl + botón izquierdo)</label>
				<div class="col-sm-5">
					<input id="file-1" type="file" class="file" name="evidence_doc[]" multiple=true data-preview-file-type="any">
				</div>
			</div>

			<div class="form-group">
				<label for="terms" class="col-sm-4 control-label">Mantener anonimato</label>
				<div class="col-sm-5">
					<div class="radio-inline">
						<label>
							<input type="radio" name="anonymous" value="1" checked> Si
							<i class="fa fa-circle-o"></i>
						</label>
					</div>
					<div class="radio-inline">
						<label>
							<input type="radio" name="anonymous" value="2"> No
							<i class="fa fa-circle-o"></i>
						</label>
					</div>
				</div>
			</div>

			<div class="form-group" id="divpass">
				{!!Form::label('Contraseña',null,['class'=>'col-sm-4 control-label'])!!}
				<div class="col-sm-5">
					<input type="password" class="form-control" name="password" id="pass" onchange="compararPass(this.value,form.repass.value)" />
				</div>
			</div>

			<div class="form-group" id="divrepass">
				{!!Form::label('Re-ingrese Contraseña',null,['class'=>'col-sm-4 control-label'])!!}
				<div class="col-sm-5">
					<input type="password" class="form-control" name="repassword" id="repass"  onchange="compararPass(form.pass.value,this.value)"/>
					<div id="error_pass"></div>
				</div>
			</div>

				<div class="form-group">
					<center>
					{!!Form::submit('Enviar', ['class'=>'btn btn-primary','id'=>'guardar'])!!}
					</center>
				</div>
			{!!Form::close()!!}
				<center>
					<p><a href="#" onclick="history.back()" class="btn btn-danger">Volver</a></p>
				<center>

			</div>
		</div>
	</div>
</div>

<div id="pop1" class="popbox">
	<h2>Denuncia</h2>
	<p>Una denuncia consiste en una acusación anónima o no, de alguna situación o circunstancia que usted considere haya infringido las normas de conducta o las leyes del país. Por ejemplo: Acoso por parte de un compañero de trabajo.</p><br>
	<h2>Reclamo</h2>
	<p>Un reclamo implica alguna situación o hecho que no sea de su agrado, aunque no constituye necesariamente una falta a las normas y leyes. Por ejemplo, compra de bebida con poca cantidad de gas o sin gas.</p><br>
	<h2>Consulta</h2>
	<p>Corresponde a cualquier duda o comentario que quiera realizar a través del sistema. Por ejemplo, quisiera saber cuál es el procedimiento para ser un pequeño comerciante de productos ACME.</p><br>
</div>
@stop

@section('scripts2')
<script>
	function compararPass(pass,repass)
	{
		if (pass != repass)
		{
			$("#divpass").attr('class','form-group has-error has-feedback');
			$("#divrepass").attr('class','form-group has-error has-feedback');
			$("#error_pass").html('<font color="red"><b>Ambas contraseñas deben ser iguales</b></font>');
			$("#guardar").attr('disabled','true');
		}
		else
		{
			$("#divpass").attr('class','form-group');
			$("#divrepass").attr('class','form-group');
			$("#error_pass").html('');
			$("#guardar").removeAttr('disabled');
		}
	}
</script>
@stop