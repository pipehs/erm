@extends('en.master')

@section('title', 'Strategic Management - KPI')

@section('content')

{!!Html::style('assets/css/mapas.css')!!}

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li>{!!Html::link('mapas','Strategic Map')!!}</li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-m-6">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-user"></i>
					<span>Strategic Map</span>
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

			On this section you will be able to see the strategic map for each organization on the system.<br><br>

			{!!Form::open(['route'=>'mapas2','method'=>'GET','class'=>'form-horizontal'])!!}
			<div class="form-group">
				{!!Form::label('Select organization',null,['class'=>'col-sm-4 control-label'])!!}
				<div class="col-sm-3">
					{!!Form::select('organization_id',$organizations,null, 
							 	   ['id' => 'orgs','required'=>'true','placeholder'=>'- Select -'])!!}
				</div>
			</div>

			<div class="form-group">
						<center>
						{!!Form::submit('Select', ['class'=>'btn btn-success','id'=>'guardar'])!!}
						</center>
			</div>
			{!!Form::close()!!}

@if (isset($objectives))
	
	
	<h3>{{ $org_selected }}</h3>
		<div class="objetivos">Vision: {{ $vision }}</div>
		<div class="perspectiva">Perspective</div>
		<div class="financiera">
		<table style="margin:10px;" width="100%" height="100%">
		<tr>
			<td width="20%">Financial</td>

			@foreach ($objectives as $obj)
				@if ($obj->perspective == 1)
				<td><p class="objectives" title="{{$obj->description}}">{{$obj->name}}</td>
				@endif
			@endforeach
		</tr>
		</table>
		</div>

		<div class="procesos">
		<table style="margin:15px;" width="100%" height="100%">
		<tr>
			<td width="20%">Processes</td>
			@foreach ($objectives as $obj)
				@if ($obj->perspective == 2)
				<td><p class="objectives" title="{{$obj->description}}">{{$obj->name}}</td>
				@endif
			@endforeach
		</tr>
		</table>
		</div>

		<div class="procesos">
		<table style="margin:15px;" width="100%" height="100%">
		<tr>
			<td width="20%">Customers</td>
			@foreach ($objectives as $obj)
				@if ($obj->perspective == 3)
				<td><p class="objectives" title="{{$obj->description}}">{{$obj->name}}</td>
				@endif
			@endforeach
		</tr>
		</table>
		</div>

		<div class="procesos">
		<table style="margin:15px; vertical-align:middle;" width="100%" height="100%">
		<tr>
			<td width="20%">Learning</td>
			@foreach ($objectives as $obj)
				@if ($obj->perspective == 4)
				<td><p class="objectives" title="{{$obj->description}}">{{$obj->name}}</td>
				@endif
			@endforeach
		</tr>
		</table>

		</div>
	</div>
	
@endif


			</div>
		</div>
	</div>
</div>
@stop


@section('scripts2')
<script>
$(function() {
    $( document ).tooltip();
  })
</script>
@stop