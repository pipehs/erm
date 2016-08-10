@extends('en.master')

@section('title', 'Control Matrix')

@section('content')

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li><a href="#">Basic Reports</a></li>
			<li><a href="heatmap">Control Matrix</a></li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-m-6">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-table"></i>
					<span>Control Matrix</span>
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
				<div class="move"></div>
			</div>
			<div class="box-content box ui-draggable ui-droppable" style="top: 0px; left: 0px; opacity: 1; z-index: 1999;">
	<p>On this section you will be able to see the control matrix for the process or bussiness risks. In case that you want to see tha control matrix for bussiness risks, you must to specify if you want to see the matrix for all organizations or for a specific one.</p>
      	{!!Form::open(['route'=>'genmatriz','method'=>'GET','class'=>'form-horizontal'])!!}

      			<div class="form-group">
                 	<div class="row">
                  		{!!Form::label('Select organization',null,['class'=>'col-sm-4 control-label'])!!}
                  		<div class="col-sm-3">
                    		{!!Form::select('organization_id',$organizations, 
                         		null, 
                         	['id' => 'org','placeholder'=>'- Select -','required'=>'true'])!!}
                  		</div>
                	</div>
                </div>

                <div class="form-group" id="tipo">
	                <div class="row">
	                  {!!Form::label('Matrix kind',null,['class'=>'col-sm-4 control-label'])!!}
	                  <div class="col-sm-3">
	                    {!!Form::select('kind',(['0'=>'Process Control','1'=>'Bussiness Control']), 
	                         null, 
	                         ['id' => 'kind','placeholder'=>'- Select -','required'=>'true'])!!}
	                  </div>
	                </div>
	            </div>

           		<div class="form-group">
	                <center>
	                {!!Form::submit('Select', ['class'=>'btn btn-primary'])!!}
	                </center>
	              </div>
				<br>
				<br>
				<hr>
		@if (isset($datos))
			<hr>
			<table id="datatable-2" class="table table-bordered table-striped table-hover table-heading table-datatable" style="font-size:11px">

			@if ($value == 0)
				<thead>
				<th>Control Id<label><input type="text" placeholder="Filtrar" /></label></th>
				<th>Description<label><input type="text" placeholder="Filtrar" /></label></th>
				<th>Responsable<label><input type="text" placeholder="Filtrar" /></label></th>
				<th>Kind<label><input type="text" placeholder="Filtrar" /></label></th>
				<th>Periodicity<label><input type="text" placeholder="Filtrar" /></label></th>
				<th>Purpose<label><input type="text" placeholder="Filtrar" /></label></th>
				<th>Expected cost<label><input type="text" placeholder="Filtrar" /></label></th>
				<th>Evidence<label><input type="text" placeholder="Filtrar" /></label></th>
				<th>Risk(s) / Subprocess(es)<label><input type="text" placeholder="Filtrar" /></label></th>
				</thead>
				
				
				@foreach ($datos as $dato)
					<tr>
						<td>{{$dato['Control']}}</td>
						<td>{{$dato['Description']}}</td>
						<td>{{$dato['Responsable']}}</td>
						<td>{{$dato['Kind']}}</td>
						<td>{{$dato['Periodicity']}}</td>
						<td>{{$dato['Purpose']}}</td>
						<td>{{$dato['Expected_cost']}}</td>
						<td>{{$dato['Evidence']}}</td>
						<td>{{$dato['Risk_Subprocess']}}</td>
					</tr>
				@endforeach
				</table>
				<div id="boton_exportar">
					{!! link_to_route('genexcel', $title = 'Export', $parameters = "0,$org_selected", $attributes = ['class'=>'btn btn-success']) !!}
				</div>

			@elseif ($value == 1)
				<thead>
				<th>Control Id<label><input type="text" placeholder="Filtrar" /></label></th>
				<th>Description<label><input type="text" placeholder="Filtrar" /></label></th>
				<th>Responsable<label><input type="text" placeholder="Filtrar" /></label></th>
				<th>Kind<label><input type="text" placeholder="Filtrar" /></label></th>
				<th>Periodicity<label><input type="text" placeholder="Filtrar" /></label></th>
				<th>Purpose<label><input type="text" placeholder="Filtrar" /></label></th>
				<th>Expected Cost<label><input type="text" placeholder="Filtrar" /></label></th>
				<th>Evidence<label><input type="text" placeholder="Filtrar" /></label></th>
				<th>Risk(s) / Objective(s)<label><input type="text" placeholder="Filtrar" /></label></th>
				</thead>
				
				
				@foreach ($datos as $dato)
					<tr>
						<td>{{$dato['Control']}}</td>
						<td>{{$dato['Description']}}</td>
						<td>{{$dato['Responsable']}}</td>
						<td>{{$dato['Kind']}}</td>
						<td>{{$dato['Periodicity']}}</td>
						<td>{{$dato['Purpose']}}</td>
						<td>{{$dato['Expected_cost']}}</td>
						<td>{{$dato['Evidence']}}</td>
						<td>{{$dato['Risk_Objective']}}</td>
					</tr>
				@endforeach
				</table>
				<div id="boton_exportar">
					{!! link_to_route('genexcel', $title = 'Export', $parameters = "1,$org_selected", $attributes = ['class'=>'btn btn-success']) !!}
				</div>
			@endif
		@endif

      </div>
		</div>
	</div>
</div>

				

@stop
@section('scripts2')
<script>
</script>
@stop