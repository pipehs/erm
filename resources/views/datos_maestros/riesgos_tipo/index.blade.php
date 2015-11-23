@extends('master')

@section('title', 'Riesgos Tipo')

@stop

@section('content')

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li><a href="#">Datos Maestros</a></li>
			<li><a href="riesgos_tipo">Riesgos Tipo</a></li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-m-6">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-ticket"></i>
					<span>Riesgos Tipo</span>
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

		{!!Form::open(['url'=>'riesgos.create','method'=>'GET'])!!}
			{!!Form::submit('Agregar Riesgo', ['class'=>'btn btn-primary'])!!}
			
		{!!Form::close()!!}

	<table class="table table-bordered table-striped table-hover table-heading table-datatable">
	<thead>
	<th>Nombre</th><th>Descripci&oacute;n</th><th>Categor&iacute;a</th><th>Fecha Creaci&oacute;n</th><th>Fecha Expiraci&oacute;n</th><th>Editar</th><th>Bloquear</th>
	</thead>
	<tr>
	<td>Riesgo 1</td><td>Riesgo ejemplo 1</td><td>Financiero</td><td>23-10-2015</td><td>Ninguna</td>
	<td>
		<div>
            {!!Form::submit('Editar', ['class'=>'btn btn-success','name'=>'editar'])!!}
        </div><!-- /btn-group -->
	</td>
	<td>
		<div>
            {!!Form::submit('Bloquear', ['class'=>'btn btn-danger','name'=>'bloquear'])!!}
        </div><!-- /btn-group -->
	</td>
	</tr>
	<tr>
	<td>Riesgo 2</td><td>Riesgo ejemplo 2</td><td>Estrat&eacute;gico</td><td>23-10-2015</td><td>Ninguna</td>
	<td>
		<div>
            {!!Form::submit('Editar', ['class'=>'btn btn-success','name'=>'editar'])!!}
        </div><!-- /btn-group -->
	</td>
	<td>
		<div>
            {!!Form::submit('Bloquear', ['class'=>'btn btn-danger','name'=>'bloquear'])!!}
        </div><!-- /btn-group -->
	</td>
	</tr>
	<tr>
	<td>Riesgo 3</td><td>Riesgo ejemplo 3</td><td>Operacional</td><td>23-10-2015</td><td>Ninguna</td>
	<td>
		<div>
            {!!Form::submit('Editar', ['class'=>'btn btn-success','name'=>'editar'])!!}
        </div><!-- /btn-group -->
	</td>
	<td>
		<div>
            {!!Form::submit('Bloquear', ['class'=>'btn btn-danger','name'=>'bloquear'])!!}
        </div><!-- /btn-group -->
	</td>
	</tr>
	</table>

			</div>
		</div>
	</div>
</div>
<script>
// Run Datables plugin and create 3 variants of settings
function AllTables(){
	TestTable1();
	TestTable2();
	TestTable3();
	LoadSelect2Script(MakeSelect2);
}
function MakeSelect2(){
	$('select').select2();
	$('.dataTables_filter').each(function(){
		$(this).find('label input[type=text]').attr('placeholder', 'Search');
	});
}
$(document).ready(function() {
	// Load Datatables and run plugin on tables 
	LoadDataTablesScripts(AllTables);
	// Add Drag-n-Drop feature
	WinMove();
});
</script>
@stop

