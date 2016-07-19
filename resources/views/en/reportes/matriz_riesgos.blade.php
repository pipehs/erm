@extends('en.master')

@section('title', 'Risk Matrix')

@section('content')

<style>
.form-horizontal .form-group.text-left{
    text-align: left !important;
}
</style>
<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li><a href="#">Basic Reports</a></li>
			<li><a href="heatmap">Risks Matrix</a></li>
		</ol>
	</div>
<div class="row">
	<div class="col-sm-12 col-m-6">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-table"></i>
					<span>Risks Matrix</span>
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
      <p>On this section you will be able to view a matrix for subprocesses or bussiness risks for the different organizations on the system.</p>

      {!!Form::open(['route'=>'genmatrizriesgos','method'=>'GET','class'=>'form-horizontal'])!!}

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
	                  {!!Form::label('Select kind of matrix',null,['class'=>'col-sm-4 control-label'])!!}
	                  <div class="col-sm-3">
	                    {!!Form::select('kind',(['0'=>'Process Risks','1'=>'Bussiness Risks']), 
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
			<!--{!! link_to_route('genmatrizriesgos', $title = 'Matriz Riesgos de Proceso', $parameters = 0, $attributes = ['class'=>'btn btn-primary']) !!}
					&nbsp;&nbsp;
			{!! link_to_route('genmatrizriesgos', $title = 'Matriz Riesgos de Negocio', $parameters = 1, $attributes = ['class'=>'btn btn-success']) !!}
			-->

	{!!Form::close()!!}

		@if (isset($datos))
			<hr>
			<table id="datatable-2" class="table table-bordered table-striped table-hover table-heading table-datatable" style="font-size:11px">

			@if ($value == 0)
				<thead>
				<th>Process<label><input type="text" placeholder="Filtrar" /></label></th>
				<th>Subprocess(es)<label><input type="text" placeholder="Filtrar" /></label></th>
				<th>Risk ID<label><input type="text" placeholder="Filtrar" /></label></th>
				<th>Risk Description<label><input type="text" placeholder="Filtrar" /></label></th>
				<th>Category<label><input type="text" placeholder="Filtrar" /></label></th>
				<th>Causes<label><input type="text" placeholder="Filtrar" /></label></th>
				<th>Effects<label><input type="text" placeholder="Filtrar" /></label></th>
				<th>Expected Loss<label><input type="text" placeholder="Filtrar" /></label></th>
				<th>Probability<label><input type="text" placeholder="Filtrar" /></label></th>
				<th>Impact<label><input type="text" placeholder="Filtrar" /></label></th>
				<th>Score<label><input type="text" placeholder="Filtrar" /></label></th>
				<th>Expiration Date<label><input type="text" placeholder="Filtrar" /></label></th>
				<th>Control<label><input type="text" placeholder="Filtrar" /></label></th>
				</thead>

				@foreach ($datos as $dato)
					<tr>
					<td>{{$dato['Process']}}</td>
					<td>{{$dato['Subprocess']}}</td>
					<td>{{$dato['Risk']}}</td>
					<td>{{$dato['Description']}}</td>
					<td>{{$dato['Category']}}</td>
					<td>{{$dato['Causes']}}</td>
					<td>{{$dato['Effects']}}</td>
					<td>{{$dato['Expected_loss']}}</td>
					<td>{{$dato['Probability']}}</td>
					<td>{{$dato['Impact']}}</td>
					<td>{{$dato['Score']}}</td>
					<td>{{$dato['Expiration_date']}}</td>
					<td><ul>{{$dato['Controls']}}</ul></td>
					</tr>
				@endforeach

				</table>
		
				<div id="boton_exportar">
					{!! link_to_route('genexcel', $title = 'Exportar', $parameters = "3,$org_selected", $attributes = ['class'=>'btn btn-success']) !!}
				</div>
			@elseif ($value == 1)
				<thead>
					<th>Organization<label><input type="text" placeholder="Filtrar" /></label></th>
					<th>Objective<label><input type="text" placeholder="Filtrar" /></label></th>
					<th>Risk Id<label><input type="text" placeholder="Filtrar" /></label></th>
					<th>Risk Description<label><input type="text" placeholder="Filtrar" /></label></th>
					<th>Category<label><input type="text" placeholder="Filtrar" /></label></th>
					<th>Causes<label><input type="text" placeholder="Filtrar" /></label></th>
					<th>Effects<label><input type="text" placeholder="Filtrar" /></label></th>
					<th>Expected Loss<label><input type="text" placeholder="Filtrar" /></label></th>
					<th>Probability<label><input type="text" placeholder="Filtrar" /></label></th>
					<th>Impact<label><input type="text" placeholder="Filtrar" /></label></th>
					<th>Score<label><input type="text" placeholder="Filtrar" /></label></th>
					<th>Expiration Date<label><input type="text" placeholder="Filtrar" /></label></th>
					<th>Control<label><input type="text" placeholder="Filtrar" /></label></th>
				</thead>

				@foreach ($datos as $dato)
					<tr>
						<td>{{$dato['Organization']}}</td>
						<td>{{$dato['Objective']}}</td>
						<td>{{$dato['Risk']}}</td>
						<td>{{$dato['Description']}}</td>
						<td>{{$dato['Category']}}</td>
						<td>{{$dato['Causes']}}</td>
						<td>{{$dato['Effects']}}</td>
						<td>{{$dato['Expected_loss']}}</td>
						<td>{{$dato['Probability']}}</td>
						<td>{{$dato['Impact']}}</td>
						<td>{{$dato['Score']}}</td>
						<td>{{$dato['Expiration_date']}}</td>
						<td><ul>{{$dato['Controls']}}</ul></td>
					</tr>
				@endforeach

				</table>
		
				<div id="boton_exportar">
					{!! link_to_route('genexcel', $title = 'Export', $parameters = "4,$org_selected", $attributes = ['class'=>'btn btn-success']) !!}
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