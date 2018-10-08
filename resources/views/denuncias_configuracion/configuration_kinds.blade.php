@extends('master')

@section('title', 'Configuración tipos de denuncia')

@section('content')
<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li>{!!Html::link('configuration','Configuración de tipos de denuncia')!!}</li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-m-6">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-ticket"></i>
					<span>Configuración tipos de denuncia</span>
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

			@if(Session::has('error'))
				<div class="alert alert-danger alert-dismissible" role="alert">
				{{ Session::get('error') }}
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
			
			Modifique o Agregue los datos de configuración que desee actualizar.<br/><br/>

			{!!Form::open(['route'=>'cc_config_kinds_store','method'=>'POST','class'=>'form-horizontal'])!!}

			<h4><b>Responsables para cada tipo de registro</b></h4>

			@foreach ($cc_kinds as $k)
				<h5 class="bs-callout"><b>{{ $k->name }}</b></h5>

				<div class="form-group">
					<label for="email_{{$k->id}}" class="col-sm-4 control-label">Ingrese correo de responsable</label>
					<div class="col-sm-4">
						{!!Form::email('email_'.$k->id,$k->responsable_mail,['class'=>'form-control'])!!}
					</div>
				</div>
			@endforeach

			<h4><b>Estados para cada tipo de registro</b></h4>
			
			@foreach ($cc_kinds as $k)
				<h5 class="bs-callout"><b>{{ $k->name }}</b></h5>
				@foreach ($k->ccStatus as $s)
					<div class="form-group">
						<label for="status_{{$k->id}}_{{$s->id}}" class="col-sm-4 control-label">Estado id: {{$s->id}}</label>
						<div class="col-sm-4">
							{!!Form::text('status_'.$k->id.'_'.$s->id,$s->description,['class'=>'form-control'])!!}
						</div>
					</div>
				@endforeach
				<div style="cursor:hand" onclick="add_status('{{$k->id}}')">
					<button type="button" class="btn btn-primary btn-app-sm btn-circle">
						<i class="fa fa-plus"></i>
					</button>
				</div> 

				<div id="new_status_{{$k->id}}">
				</div>
			@endforeach

			<h4><b>Clasificaciones para cada tipo de registro</b></h4>
			
			@foreach ($cc_kinds as $k)
				<h5 class="bs-callout"><b>{{ $k->name }}</b></h5>
				@foreach ($k->ccClassifications as $c)
					<div class="form-group">
						<label for="name_class_{{$k->id}}_{{$c->id}}" class="col-sm-4 control-label">Nombre Clasificación id: {{$c->id}}</label>
						<div class="col-sm-4">
							{!!Form::text('name_class_'.$k->id.'_'.$c->id,$c->name,['class'=>'form-control'])!!}
						</div>
					</div>

					<div class="form-group">
						<label for="description_class_{{$k->id}}_{{$c->id}}" class="col-sm-4 control-label">Descripción Clasificación id: {{$c->id}}</label>
						<div class="col-sm-4">
							{!!Form::text('description_class_'.$k->id.'_'.$c->id,$c->description,['class'=>'form-control'])!!}
						</div>
					</div>

					<div id="role">
						<div class="form-group">
							<label for="role_class_{{$k->id}}_{{$c->id}}" class="col-sm-4 control-label">Rol responsable {{$c->id}}</label>
							<div class="col-sm-4">
								@if ($cc_roles->count() == 0)
									{!!Form::text('role_class_'.$k->id.'_'.$c->id,null,['class'=>'form-control'])!!}
								@else
									@if ($c->cc_role_id != NULL)
										{!!Form::select('role_class_'.$k->id.'_'.$c->id,$cc_roles,$c->cc_role_id)!!}
									@else
										{!!Form::select('role_class_'.$k->id.'_'.$c->id,$cc_roles,null)!!}
									@endif
								@endif
							</div>
						</div>
					</div>
					<br>
				@endforeach
				<div style="cursor:hand" onclick="add_classification('{{$k->id}}')">
					<button type="button" class="btn btn-primary btn-app-sm btn-circle">
						<i class="fa fa-plus"></i>
					</button>
				</div> 

				<div id="new_classification_{{$k->id}}">
				</div>
			@endforeach

					
			<div class="form-group">
				<center>
					{!!Form::submit('Guardar', ['class'=>'btn btn-primary'])!!}
				</center>
			</div>
			{!!Form::close()!!}

			<center>
   				{!! link_to('', $title = 'Volver', $attributes = ['class'=>'btn btn-danger', 'onclick' => 'history.back()'])!!}
   			<center>
			</div>
		</div>
	</div>
</div>

@stop

@section('scripts2')
<script type="text/javascript">
	$(document).ready(function() {
		roles = [];
		@if ($cc_roles->count() > 0)
			@foreach ($cc_roles as $id=>$name)			
				data = {id:{{$id}}, name:'{{$name}}'}
				roles.push(data)
			@endforeach
		@endif
	});
</script>
{!!Html::script('assets/js/complaint_channel.js')!!}
@stop