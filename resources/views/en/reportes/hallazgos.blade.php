@extends('en.master')

@section('title', 'Issues Report')

@section('content')

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li><a href="#">Basic Reports</a></li>
			<li><a href="planes_accion">Issues</a></li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-m-6">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-table"></i>
					<span>Issues</span>
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
	<p>On this section you will be able to view issues report of each organization</p>

      	{!!Form::open(['route'=>'genissues_report','method'=>'POST','class'=>'form-horizontal'])!!}
      			<div class="form-group">
					{!!Form::label('Select organization',null,['class'=>'col-sm-4 control-label'])!!}
					<div class="col-sm-3">
						{!!Form::select('organization_id',$organizations,null, 
								 	   ['id' => 'orgs','required'=>'true','placeholder'=>'- Select -'])!!}
					</div>
				</div>
				<div class="form-group">
				{!!Form::label('Kind',null,['class'=>'col-sm-4 control-label'])!!}
				<div class="col-sm-3">
					{!!Form::select('kind',['0'=>'Process','1'=>'Subprocess','2'=>'Organization','3'=>'Process Controls','4'=>'Bussiness Controls','5'=>'Audit Programs','6'=>'Audit'],null, 
							 	   ['id' => 'kind','required'=>'true','placeholder'=>'- Select -'])!!}
				</div>
			</div>
				<div class="form-group">
						<center>
						{!!Form::submit('Select', ['class'=>'btn btn-success','id'=>'guardar'])!!}
						</center>
				</div>
		{!!Form::close()!!}
				
			@if (isset($issues) && isset($kind))

				@if ($kind == 0)
					<h4><b>{{ $org }}: Process Issues</b></h4>
					<table id="datatable-2" class="table table-bordered table-striped table-hover table-heading table-datatable" style="font-size:11px">
					<thead>
						<th style="vertical-align:top;">Process(es)<label><input type="text" placeholder="Filtrar" /></label></th>
						<th style="vertical-align:top;">Subprocess(es)<label><input type="text" placeholder="Filtrar" /></label></th>
						<th style="vertical-align:top;">Risk(s)<label><input type="text" placeholder="Filtrar" /></label></th>
						<th style="vertical-align:top;">Control(s)<label><input type="text" placeholder="Filtrar" /></label></th>
						<th style="vertical-align:top;">Issues<label><input type="text" placeholder="Filtrar" /></label></th>
						<th style="vertical-align:top;">Classification<label><input type="text" placeholder="Filtrar" /></label></th>
						<th style="vertical-align:top;">Recommendations<label><input type="text" placeholder="Filtrar" /></label></th>
						<th style="vertical-align:top;">Action Plan<label><input type="text" placeholder="Filtrar" /></label></th>
						<th style="vertical-align:top;">Status<label><input type="text" placeholder="Filtrar" /></label></th>
						<th style="vertical-align:top;">Plan Deadline<label><input type="text" placeholder="Filtrar" /></label></th>
					</thead>

				

					@foreach ($issues as $issue)
						<tr>
							<td>{{ $issue['datos']['processes'] }}</td>
							<td>{{ $issue['datos']['subprocesses'] }}</td>
							<td>{{ $issue['datos']['risks'] }}</td>
							<td>{{ $issue['datos']['controls'] }}</td> 
							<td>{{ $issue['name'] }}</td>
							<td>{{ $issue['classification'] }}</td>
							<td>{{ $issue['recommendations'] }}</td>
							<td>{{ $issue['plan'] }}</td>
							<td>{{ $issue['status'] }}</td>
							<td>{{ $issue['final_date'] }}</td>
						</tr>
					@endforeach


				@elseif ($kind == 2)
					<h4><b>{{ $org }}: Hallazgos de organizaci&oacute;n</b></h4>
					<table id="datatable-2" class="table table-bordered table-striped table-hover table-heading table-datatable" style="font-size:11px">
					<thead>
						<th style="vertical-align:top;">Objective(s) involved<label><input type="text" placeholder="Filtrar" /></label></th>
						<th style="vertical-align:top;">Risk(s)<label><input type="text" placeholder="Filtrar" /></label></th>
						<th style="vertical-align:top;">Control(s)<label><input type="text" placeholder="Filtrar" /></label></th>
						<th style="vertical-align:top;">Issue<label><input type="text" placeholder="Filtrar" /></label></th>
						<th style="vertical-align:top;">Classification<label><input type="text" placeholder="Filtrar" /></lab style="vertical-align:top;"el></th>
						<th style="vertical-align:top;">Recommendations<label><input type="text" placeholder="Filtrar" /></label></th>
						<th style="vertical-align:top;">Action Plan<label><input type="text" placeholder="Filtrar" /></label></th>
						<th style="vertical-align:top;">Status<label><input type="text" placeholder="Filtrar" /></label></th>
						<th style="vertical-align:top;">Plan Deadline<label><input type="text" placeholder="Filtrar" /></label></th>
					</thead>

				

					@foreach ($issues as $issue)
						<tr>
							<td>{{ $issue['datos']['objectives'] }}</td>
							<td>{{ $issue['datos']['risks'] }}</td>
							<td>{{ $issue['datos']['controls'] }}</td> 
							<td>{{ $issue['name'] }}</td>
							<td>{{ $issue['classification'] }}</td>
							<td>{{ $issue['recommendations'] }}</td>
							<td>{{ $issue['plan'] }}</td>
							<td>{{ $issue['status'] }}</td>
							<td>{{ $issue['final_date'] }}</td>
						</tr>
					@endforeach

				@elseif ($kind == 3)
					<h4><b>{{ $org }}: Control Process Issues</b></h4>
					<table id="datatable-2" class="table table-bordered table-striped table-hover table-heading table-datatable" style="font-size:11px">
					<thead>
						<th style="vertical-align:top;">Process(es)<label><input type="text" placeholder="Filtrar" /></label></th>
						<th style="vertical-align:top;">Subprocess(es)<label><input type="text" placeholder="Filtrar" /></label></th>
						<th style="vertical-align:top;">Risk(s)<label><input type="text" placeholder="Filtrar" /></label></th>
						<th style="vertical-align:top;">Control(s)<label><input type="text" placeholder="Filtrar" /></label></th>
						<th style="vertical-align:top;">Issue<label><input type="text" placeholder="Filtrar" /></label></th>
						<th style="vertical-align:top;">Classification<label><input type="text" placeholder="Filtrar" /></lab style="vertical-align:top;"el></th>
						<th style="vertical-align:top;">Recommendations<label><input type="text" placeholder="Filtrar" /></label></th>
						<th style="vertical-align:top;">Action Plan<label><input type="text" placeholder="Filtrar" /></label></th>
						<th style="vertical-align:top;">Status<label><input type="text" placeholder="Filtrar" /></label></th>
						<th style="vertical-align:top;">Plan Deadline<label><input type="text" placeholder="Filtrar" /></label></th>
					</thead>

					@foreach ($issues as $issue)
						<tr>
							<td>{{ $issue['datos']['processes'] }}</td>
							<td>{{ $issue['datos']['subprocesses'] }}</td>
							<td>{{ $issue['datos']['risks'] }}</td>
							<td>{{ $issue['datos']['controls'] }}</td> 
							<td>{{ $issue['name'] }}</td>
							<td>{{ $issue['classification'] }}</td>
							<td>{{ $issue['recommendations'] }}</td>
							<td>{{ $issue['plan'] }}</td>
							<td>{{ $issue['status'] }}</td>
							<td>{{ $issue['final_date'] }}</td>
						</tr>
					@endforeach

				@elseif ($kind == 4)
					<h4><b>{{ $org }}: Bussiness Control Issues</b></h4>
					<table id="datatable-2" class="table table-bordered table-striped table-hover table-heading table-datatable" style="font-size:11px">
					<thead>
						<th style="vertical-align:top;">Objective(s)<label><input type="text" placeholder="Filtrar" /></label></th>
						<th style="vertical-align:top;">Risk(s)<label><input type="text" placeholder="Filtrar" /></label></th>
						<th style="vertical-align:top;">Control(s)<label><input type="text" placeholder="Filtrar" /></label></th>
						<th style="vertical-align:top;">Issue<label><input type="text" placeholder="Filtrar" /></label></th>
						<th style="vertical-align:top;">Classification<label><input type="text" placeholder="Filtrar" /></lab style="vertical-align:top;"el></th>
						<th style="vertical-align:top;">Recommendations<label><input type="text" placeholder="Filtrar" /></label></th>
						<th style="vertical-align:top;">Action Plan<label><input type="text" placeholder="Filtrar" /></label></th>
						<th style="vertical-align:top;">Status<label><input type="text" placeholder="Filtrar" /></label></th>
						<th style="vertical-align:top;">Plan Deadline<label><input type="text" placeholder="Filtrar" /></label></th>
					</thead>

					@foreach ($issues as $issue)
						<tr>
							<td>{{ $issue['datos']['objectives'] }}</td>
							<td>{{ $issue['datos']['risks'] }}</td>
							<td>{{ $issue['datos']['controls'] }}</td> 
							<td>{{ $issue['name'] }}</td>
							<td>{{ $issue['classification'] }}</td>
							<td>{{ $issue['recommendations'] }}</td>
							<td>{{ $issue['plan'] }}</td>
							<td>{{ $issue['status'] }}</td>
							<td>{{ $issue['final_date'] }}</td>
						</tr>
					@endforeach

				@elseif ($kind == 5)
					<h4><b>{{ $org }}: Audit Program Issues</b></h4>
					<table id="datatable-2" class="table table-bordered table-striped table-hover table-heading table-datatable" style="font-size:11px">
					<thead>
						<th style="vertical-align:top;">Audit Plan<label><input type="text" placeholder="Filtrar" /></label></th>
						<th style="vertical-align:top;">Audit<label><input type="text" placeholder="Filtrar" /></label></th>
						<th style="vertical-align:top;">Audit Program<label><input type="text" placeholder="Filtrar" /></label></th>
						<th style="vertical-align:top;">Issue<label><input type="text" placeholder="Filtrar" /></label></th>
						<th style="vertical-align:top;">Classification<label><input type="text" placeholder="Filtrar" /></lab style="vertical-align:top;"el></th>
						<th style="vertical-align:top;">Recommendations<label><input type="text" placeholder="Filtrar" /></label></th>
						<th style="vertical-align:top;">Action Plan<label><input type="text" placeholder="Filtrar" /></label></th>
						<th style="vertical-align:top;">Status<label><input type="text" placeholder="Filtrar" /></label></th>
						<th style="vertical-align:top;">Plan Deadline<label><input type="text" placeholder="Filtrar" /></label></th>
					</thead>

					@foreach ($issues as $issue)
						<tr>
							<td>{{ $issue['datos']['audit_plan'] }}</td>
							<td>{{ $issue['datos']['audit'] }}</td> 
							<td>{{ $issue['name'] }}</td>
							<td>{{ $issue['classification'] }}</td>
							<td>{{ $issue['recommendations'] }}</td>
							<td>{{ $issue['plan'] }}</td>
							<td>{{ $issue['status'] }}</td>
							<td>{{ $issue['final_date'] }}</td>
						</tr>
					@endforeach

				@elseif ($kind == 6)
					<h4><b>{{ $org }}: Audit Issues</b></h4>
					<table id="datatable-2" class="table table-bordered table-striped table-hover table-heading table-datatable" style="font-size:11px">
					<thead>
						<th style="vertical-align:top;">Audit Plan<label><input type="text" placeholder="Filtrar" /></label></th>
						<th style="vertical-align:top;">Audit<label><input type="text" placeholder="Filtrar" /></label></th>
						<th style="vertical-align:top;">Issue<label><input type="text" placeholder="Filtrar" /></label></th>
						<th style="vertical-align:top;">Classification<label><input type="text" placeholder="Filtrar" /></lab style="vertical-align:top;"el></th>
						<th style="vertical-align:top;">Recommendations<label><input type="text" placeholder="Filtrar" /></label></th>
						<th style="vertical-align:top;">Action Plans<label><input type="text" placeholder="Filtrar" /></label></th>
						<th style="vertical-align:top;">Status<label><input type="text" placeholder="Filtrar" /></label></th>
						<th style="vertical-align:top;">Plan Deadline<label><input type="text" placeholder="Filtrar" /></label></th>
					</thead>

					@foreach ($issues as $issue)
						<tr>
							<td>{{ $issue['datos']['audit_plan'] }}</td>
							<td>{{ $issue['datos']['audit'] }}</td>
							<td>{{ $issue['name'] }}</td>
							<td>{{ $issue['classification'] }}</td>
							<td>{{ $issue['recommendations'] }}</td>
							<td>{{ $issue['plan'] }}</td>
							<td>{{ $issue['status'] }}</td>
							<td>{{ $issue['final_date'] }}</td>
						</tr>
					@endforeach
				@endif
			</div>
				<div id="boton_exportar">
						<input type="image" id="btnExport" src="assets/img/excel.jpg" width="70" height="70">
				</div>
		@endif
      
		</div>
	</div>
</div>	

@stop
@section('scripts2')
<script>
@if (isset($kind))

	var value1 = {{ $kind }};
	var value2 = {{ $org_id }};
	$("#btnExport").click(function(e) {					
		window.location.href = "genexcelissues."+value1+","+value2;
		e.preventDefault();
	});
@endif
</script>
@stop