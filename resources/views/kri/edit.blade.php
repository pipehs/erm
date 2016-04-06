@extends('master')

@section('title', 'Editar KRI')

@stop

@section('content')

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li>{!!Html::link('kri','KRI')!!}</li>
			<li><a href="kri.edit.{{ $kri['id'] }}">Editar KRI</a></li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-m-6">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-user"></i>
					<span>Crear KRI</span>
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

			Modifique la informaci&oacute;n para el KRI: <b>{{ $kri['name'] }}</b>.

			{!!Form::model($kri,['route'=>['kri.update',$kri->id],'method'=>'PUT','class'=>'form-horizontal'])!!}
				@include('kri.form')

			{!!Form::close()!!}
				<center>
					{!! link_to_route('kri', $title = 'Volver', $parameters = NULL,
                 		$attributes = ['class'=>'btn btn-danger'])!!}
				<center>

			
			</div>
		</div>
	</div>
</div>
@stop


@section('scripts2')
{!!Html::script('assets/js/kri.js')!!}


<script>
$(document).ready(function () {
	$("#uni_med").change();
});
</script>
@stop