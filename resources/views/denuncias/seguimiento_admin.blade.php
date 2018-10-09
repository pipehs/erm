@extends('master')

@section('title', 'Seguimiento de denuncias')

@section('content')
<style>
.popper {
    border-radius: 100%;
    padding: 2px 6px;
    background: #4132bc;
    color: white !important;
    margin-left: 10px;
}

.popbox {
    display: none;
    position: absolute;
    z-index: 99999;
    width: 400px;
    padding: 10px;
    background: #4132bc;
    color: white;
    border: 1px solid #4D4F53;
    border-radius:3px;
    margin: 0px;
    -webkit-box-shadow: 0px 0px 5px 0px rgba(164, 164, 164, 1);
    box-shadow: 0px 0px 5px 0px rgba(164, 164, 164, 1);
}

.popbox p{
	margin:0;
}

.popbox h2
{
    background-color: #070664;
    font-weight: bold;
    color:  #E3E5DD;
    font-size: 14px;
    display: block;
    width: 100%;
    margin: -10px 0px 8px -10px;
    padding: 5px 10px;
}
</style>
<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li><a href="seguimiento_admin">Seguimiento de denuncias</a></li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-m-6">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-ticket"></i>
					<span>Seguimiento de denuncias</span>
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

			En esta secci&oacute;n podr&aacute; dar seguimiento a los casos ingresados por los usuarios
			
		<table class="table table-bordered table-striped table-hover table-heading table-datatable" id="datatable-2" style="font-size:11px">
			<thead>
				<th>Autor<label><input type="text" placeholder="Filtrar" /></label></th>
				<th>Tipo de caso<label><input type="text" placeholder="Filtrar" /></label></th>
			@foreach ($questions as $q)
				<th>{{ $q->question }}<label><input type="text" placeholder="Filtrar" /></label></th>
			@endforeach
				<th>Fecha caso<label><input type="text" placeholder="Filtrar" /></label></th>
				<th>Revisar</th>
				<th>Investigar</th>
				<th>Clasificar</th>
				<th>Cerrar</th>
			</thead>

			@foreach ($cases as $c)
			<tr>
				<td>{{ $c->complainant }}</td>
				<td>{{ $c->kind }}</td>
				@foreach ($c->questions as $q)
				<td>
					@if ($q->answer == NULL)
						Sin respuesta
					@else
						{{ $q->answer }}
					@endif
				</td>
				@endforeach
				<td>{{ $c->created_at }}</td>
				<td><a class="btn btn-primary" href="seguimiento_admin2.{{$c->id}}">Revisar</button></td>
				<td><a class="btn btn-default" href="cerrar_caso">Investigar</button></td>
				<td><button class="btn btn-success" onclick="">Clasificar</button></td>
				<td><button class="btn btn-danger" onclick="cerrar();">Cerrar</button></td>
			</tr>
			@endforeach
		</table>
				<center>
					<p><a href="#" onclick="history.back()" class="btn btn-danger">Volver</a></p>
				<center>

			</div>
		</div>
	</div>
</div>
@stop

@section('scripts2')

@stop