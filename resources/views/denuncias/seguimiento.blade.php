@extends(Auth::user() ? 'master' : 'master2')

@section('title', 'Seguimiento de denuncias')

@section('content')
<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li><a href="seguimiento_admin">Seguimiento de denuncia</a></li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-m-6">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-ticket"></i>
					<span>Seguimiento de denuncia</span>
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

			@if(Session::has('error'))
				<div class="alert alert-danger alert-dismissible" role="alert">
					{{ Session::get('error') }}
				</div>
			@endif

		<div id="error-response" class="alert alert-danger alert-dismissible" role="alert" style="display: none;">
		</div>

		<p>Ingrese el ID y contraseña del caso que desea revisar</p>

		<div class='form-horizontal'>

		<div class="form-group">
				{!!Form::label('ID *',null,['class'=>'col-sm-4 control-label'])!!}
				<div class="col-sm-4">
					{!!Form::number('id',null,['id'=>'id','class'=>'form-control','required'=>'true'])!!}
				</div>
		</div>

		<div class="form-group">
				{!!Form::label('Contraseña',null,['class'=>'col-sm-4 control-label'])!!}
				<div class="col-sm-4">
					<input type="password" class="form-control" name="password" id="password" required />
				</div>
		</div>

		<div class="form-group">
				<center>
					{!!Form::submit('Revisar', ['class'=>'btn btn-primary','id' => 'btnsubmit','onclick' => 'getCase()'])!!}
				</center>
		</div>

		</div>
			
		<div id="case">
			<div class="row">
				<div class="col-sm-6">
					<div id="case-info">
					</div>
				</div>
				<div class="col-sm-6">
					<div id="questions">
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-12">
					<div id="messages"></div>
				</div>
				<div class="col-sm-6">
					<form id="messages2" method="POST" class="form-horizontal" enctype="multipart/form-data">
					</form>
				</div>
			</div>
		</div>
		
		<center>
			<p><a href="#" onclick="history.back()" class="btn btn-danger">Volver</a></p>
		<center>

			</div>
		</div>
	</div>
</div>

@stop

@section('scripts2')
{!!Html::script('assets/js/complaint_channel.js')!!}
@stop