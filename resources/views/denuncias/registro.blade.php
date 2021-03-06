@extends(Auth::user() ? 'master' : 'master2')

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
	<div id="register-form">
			En esta secci&oacute;n podr&aacute; enviar y registrar una consulta, reclamo o denuncia.
			{!!Form::open(['route'=>'registro_denuncia2','method'=>'POST','class'=>'form-horizontal','enctype'=>'multipart/form-data','id'=>'registercomplaint'])!!}

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
					{!!Form::select('cc_kind_id',$cc_kinds, null, ['id' => 'kind_id','placeholder'=>'- Seleccione -'])!!}
				</div>
			</div>

			@foreach ($questions as $q)
				<div class="form-group">
					<label for="answer_{{$q->id}}" class="col-sm-4 control-label">{{$q->question}}</label>
					<div class="col-sm-5">
						@if ($q->cc_kind_answer_id == 1)
							{!!Form::textarea('answer_'.$q->id,null,['class'=>'form-control','rows'=>'8','cols'=>'4',$q->required2])!!}
						@elseif ($q->cc_kind_answer_id == 2)
							@foreach ($q->p_answers as $ans)
								<div class="radio-inline">
									<label>
										<input type="radio" {{$q->required2}} name="answer_{{$q->id}}" value="{{$ans->id}}"> {{ $ans->description }}
										<i class="fa fa-circle-o"></i>
									</label>
								</div>
							@endforeach
						@elseif ($q->cc_kind_answer_id == 3)
							@foreach ($q->p_answers as $ans)
								<div class="checkbox">
									<label>
										<input type="checkbox" name="answer_{{$q->id}}[]" value="{{$ans->id}}"> {{ $ans->description }}
										<i class="fa fa-square-o"></i>
									</label>
								</div>
							@endforeach
						@elseif ($q->cc_kind_answer_id == 4)
							{!!Form::date('answer_'.$q->id,null,['class'=>'form-control',$q->required2])!!}
						@endif
					</div>
				</div>
			@endforeach

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

			<div id="anonymous2" style="display: none;">
				<div class="form-group">
					{!!Form::label('Nombre *',null,['class'=>'col-sm-4 control-label'])!!}
					<div class="col-sm-5">
						<input type="text" class="form-control" name="name" id="name"/>
					</div>
				</div>

				<div class="form-group">
					{!!Form::label('Apellidos *',null,['class'=>'col-sm-4 control-label'])!!}
					<div class="col-sm-5">
						<input type="text" class="form-control" name="surnames" id="surnames"/>
					</div>
				</div>

				<div class="form-group">
					{!!Form::label('E-mail *',null,['class'=>'col-sm-4 control-label'])!!}
					<div class="col-sm-5">
						<input type="text" class="form-control" name="email" id="email"/>
					</div>
				</div>

				<div class="form-group">
					{!!Form::label('Teléfono',null,['class'=>'col-sm-4 control-label'])!!}
					<div class="col-sm-5">
						<input type="text" class="form-control" name="telephone" id="telephone"/>
					</div>
				</div>
			</div>

			<div class="form-group" id="divpass">
				{!!Form::label('Contraseña *',null,['class'=>'col-sm-4 control-label'])!!}
				<div class="col-sm-5">
					<input type="password" class="form-control" name="password" id="pass" required="true" onchange="compararPass(this.value,form.repass.value)" />
				</div>
			</div>

			<div class="form-group" id="divrepass">
				{!!Form::label('Re-ingrese Contraseña *',null,['class'=>'col-sm-4 control-label'])!!}
				<div class="col-sm-5">
					<input type="password" class="form-control" name="repassword" id="repass" required="true" onchange="compararPass(form.pass.value,this.value)"/>
					<div id="error_pass"></div>
				</div>
			</div>

			<div class="form-group" style="text-align: center;">
					{!!Form::submit('Enviar', ['class'=>'btn btn-primary','id'=>'guardar'])!!}
			</div>
			{!!Form::close()!!}
			
				<div style="text-align: center;">
					<p><a href="#" onclick="history.back()" class="btn btn-danger">Volver</a></p>
				</div>
	</div>
	<div id="register-response">
	</div>
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
{!!Html::script('assets/js/complaint_channel.js')!!}
@stop