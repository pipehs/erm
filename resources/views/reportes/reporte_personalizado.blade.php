@extends('master')

@section('title', 'Reporte personalizado')

@section('content')

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li><a href="#">Reportes</a></li>
			<li><a href="planes_accion">Reporte Personalizado</a></li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-m-6">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-table"></i>
					<span>Reporte Personalizado</span>
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
			@if(Session::has('message'))
				<div class="alert alert-danger alert-dismissible" role="alert">
				{{ Session::get('message') }}
				</div>
			@endif
      <p>En esta secci&oacute;n podr&aacute; generar reportes personalizados en formato de documento de texto. Para esto, seleccione organizaci&oacute;n, adem&aacute;s de los elementos que desea que aparezcan en el reporte y un t&iacute;tulo para el mismo.</p>

    {!!Form::open(['route'=>'gentotalreport','method'=>'POST','class'=>'form-horizontal','name' => 'f1'])!!}
				<div class="form-group">
				{!!Form::label('Seleccione organización',null,['class'=>'col-sm-4 control-label'])!!}
					<div class="col-sm-3">
						{!!Form::select('organization_id',$organizations,null,['id' => 'organization_id','placeholder'=>'- Seleccione -','required' => 'true'])!!}
					</div>
				</div>

				<div class="form-group">
				<label for="name" class="col-sm-4 control-label">Ingrese t&iacute;tulo del reporte</label>
					<div class="col-sm-3">
						{!!Form::text('name',null,['id'=>'name','class'=>'form-control','required'=>'true'])!!}
					</div>
				</div>

				<div class="form-group">
				<label class="col-sm-4 control-label">Seleccione elementos</label>
					<div class="col-sm-3">
						<div class="checkbox" id="todos">
							<label>
								<input type="checkbox" name="todo" value="todo" onclick="seleccionar_todo()">
								<i class="fa fa-square-o"></i> Seleccionar todos
							</label>
						</div>
					</div>
				</div>

				<div class="form-group">
				<label class="col-sm-4 control-label"></label>
					<div class="col-sm-3">
						<div class="checkbox">
							<label>
								<input type="checkbox" name="strategic_plans">
								<i class="fa fa-square-o"></i> Planes estrat&eacute;gicos
							</label>
						</div>
					</div>
				</div>

				<div class="form-group">
				<label class="col-sm-4 control-label"></label>
					<div class="col-sm-3">
						<div class="checkbox">
							<label>
								<input type="checkbox" name="processes">
								<i class="fa fa-square-o"></i> Procesos
						</div>
					</div>
				</div>

				<div class="form-group">
				<label class="col-sm-4 control-label"></label>
					<div class="col-sm-3">
						<div class="checkbox">
							<label>
								<input type="checkbox" name="risks">
								<i class="fa fa-square-o"></i> Riesgos
						</div>
					</div>
				</div>

				<div class="form-group">
				<label class="col-sm-4 control-label"></label>
					<div class="col-sm-3">
						<div class="checkbox">
							<label>
								<input type="checkbox" name="controls">
								<i class="fa fa-square-o"></i> Controles
						</div>
					</div>
				</div>

				<div class="form-group">
				<label class="col-sm-4 control-label"></label>
					<div class="col-sm-3">
						<div class="checkbox">
							<label>
								<input type="checkbox" name="audit_plans">
								<i class="fa fa-square-o"></i> Planes de auditor&iacute;a
						</div>
					</div>
				</div>

				<div class="form-group">
				<label class="col-sm-4 control-label"></label>
					<div class="col-sm-3">
						<div class="checkbox">
							<label>
								<input type="checkbox" name="issues">
								<i class="fa fa-square-o"></i> Hallazgos y planes de acci&oacute;n
						</div>
					</div>
				</div>

				<br>
				<div class="form-group">
	                <center>
	                {!!Form::submit('Seleccionar', ['class'=>'btn btn-success'])!!}
	                </center>
	            </div>

	{!!Form::close()!!}

      		</div>
		</div>
	</div>
</div>

				

@stop
@section('scripts2')
<script>
function seleccionar_todo(){ 
   for (i=0;i<document.f1.elements.length;i++)
   {
      if(document.f1.elements[i].type == "checkbox")	
         document.f1.elements[i].checked=1 
   }

   //cambiamos seleccionar todo
	newcampo = '<label>'
	newcampo += '<input type="checkbox" name="todo" value="todo" onclick="deseleccionar_todo()" checked>'
	newcampo += '<i class="fa fa-square-o"></i> Deseleccionar todos'
	newcampo += '</label>'

   $('#todos').html(newcampo)
}

function deseleccionar_todo(){ 
   for (i=0;i<document.f1.elements.length;i++)
   {
      if(document.f1.elements[i].type == "checkbox")	
         document.f1.elements[i].checked=0 
   }

   //cambiamos seleccionar todos
	newcampo = '<label>'
	newcampo += '<input type="checkbox" name="todo" value="todo" onclick="seleccionar_todo()">'
	newcampo += '<i class="fa fa-square-o"></i> Seleccionar todos'
	newcampo += '</label>'

   $('#todos').html(newcampo)
}  
</script>
@stop