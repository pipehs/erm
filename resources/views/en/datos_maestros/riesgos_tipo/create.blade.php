@extends('en.master')

@section('title', 'Create Risk')

@section('content')

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li>{!!Html::link('#','Master data')!!}</li>
			<li>{!!Html::link('riskstype','Template risk')!!}</li>
			<li>{!!Html::link('riskstype.create','Create risk')!!}</li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-m-10">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-folder"></i>
					<span>Create risk</span>
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
			Input template risk data.
				{!!Form::open(['route'=>'riskstype.store','method'=>'POST','class'=>'form-horizontal'])!!}
					@include('en.datos_maestros.riesgos_tipo.form')
				{!!Form::close()!!}

				<center>
				{!!Form::open(['url'=>'riskstype','method'=>'GET'])!!}
					{!!Form::submit('return', ['class'=>'btn btn-danger'])!!}
				{!!Form::close()!!}
				<center>
			</div>
		</div>
	</div>
</div>
@stop

@section('scripts2')
{!!Html::script('assets/js/en/create_edit_risks.js')!!}
@stop

