@extends('master')

@section('title', 'Clasificación de casos')

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
			<li><a href="seguimiento_admin">Clasificación de casos</a></li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-m-6">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-ticket"></i>
					<span>Clasificación de casos</span>
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
				<th>Descripci&oacute;n<label><input type="text" placeholder="Filtrar" /></label></th>
				<th>Fecha caso<label><input type="text" placeholder="Filtrar" /></label></th>
				<th>Evidencia(s)<label><input type="text" placeholder="Filtrar" /></label></th>
				<th>Investigación</th>
				<th>Clasificar</th>
				<th>Cerrar</th>
			</thead>

			<tr>
				<td>Anónimo</td>
				<td>Denuncia</td>
				<td>Se presentó un caso de cohecho en las oficinas administrativas</td>
				<td>12-03-2018</td>
				<td><a href="#"><img src="assets/img/jpg.png" width="30" height="30" /></a><br/>Foto.jpg</td>
				<td><button class="btn btn-primary" onclick="">Agregar investigación</button></td>
				<td><button class="btn btn-success" onclick="">Clasificar</button></td>
				<td><a class="btn btn-danger" href="cerrar_caso">Cerrar</button></td>
			</tr>

			<tr>
				<td>Juán Perez<br>
				juanperez@ccu.cl</td>
				<td>Reclamo</td>
				<td>El día 20 de diciembre de 2017, compré una bebida de su compañia, la cual se encontraba complemtamente sin gas.</td>
				<td>10-01-2018</td>
				<td>Sin evidencia</td>
				<td><button class="btn btn-primary" onclick="">Agregar investigación</button></td>
				<td><button class="btn btn-success" onclick="">Clasificar</button></td>
				<td><button class="btn btn-danger" onclick="cerrar()">Cerrar</button></td>
			</tr>

			<tr>
				<td>Pedro Carmona<br>
				pedrocarmona@ccu.cl</td>
				<td>Consulta</td>
				<td>Buenos días. Me gustaría saber cual es el procedimiento para poder comprar sus productos de forma mayorista, para realizar venta en mi negocio local. Adjunto envío información asociada a mi negocio. Muchas gracias.</td>
				<td>15-12-2017</td>
				<td>
					<a href="#"><img src="assets/img/pdf.png" width="30" height="30" /></a><br/>Minimarket.pdf
					<a href="#"><img src="assets/img/jpg.png" width="30" height="30" /></a><br/>Ubicación.jpg
					<a href="#"><img src="assets/img/pdf.png" width="30" height="30" /></a><br/>Minimarket2.pdf
				</td>
				<td><button class="btn btn-primary" onclick="">Agregar investigación</button></td>
				<td><button class="btn btn-success" onclick="">Clasificar</button></td>
				<td><button class="btn btn-danger" onclick="">Cerrar</button></td>
			</tr>
		</table>
				<center>
					<p><a href="#" onclick="history.back()" class="btn btn-danger">Volver</a></p>
				<center>

			</div>
		</div>
	</div>
</div>

<div id="pop1" class="popbox">
	<h2>Denuncia</h2>
	<p>Una denuncia consiste en una acusación anónima o no, de alguna situación o circunstancia que usted considere haya infringido las normas de conducta o las leyes del país. Por ejemplo: Acoso por parte de un compañero de trabajo.</p><br>
	<h2>Reclamo</h2>
	<p>Un reclamo implica alguna situación o hecho que no sea de su agrado, aunque no constituye necesariamente una falta a las normas y leyes. Por ejemplo, compra de bebida con poca cantidad de gas o sin gas.</p><br>
	<h2>Consulta</h2>
	<p>Corresponde a cualquier duda o comentario que quiera realizar a través del sistema. Por ejemplo, quisiera saber cuál es el procedimiento para ser un pequeño comerciante de productos CCU.</p><br>
</div>
@stop

@section('scripts2')
<script>
function cerrar()
{
	texto = '<div class="form-group">'
	texto += '<label for="close_reason" class="col-sm-4 control-label">Seleccione motivo de cierre</label>'
	texto += '<div class="col-sm-5">'
	texto += '{!!Form::select("close_reason",["1" => "Resuelto con sanción","2"=>"Resuelto sin sanción","3"=>"No resuelto por falta de antecedentes","4"=>"No resuelto por abandono de la denuncia"], null, ["id" => "close_reason","placeholder"=>"- Seleccione -","class"=>"form-control"])!!}'
	texto += '</div></div><br>'

	texto += '<div class="form-group">'
	texto += '<label for="close_description" class="col-sm-4 control-label">Describa motivo de cierre</label>'
	texto += '<div class="col-sm-5">'
	texto += '{!!Form::textarea("close_description", null, ["id"=>"description_close", "class"=>"form-control", "rows"=>"8","cols"=>"4","required" => "true"])!!}'
	texto += '</div></div>'

	texto += '<br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>'

	swal({   title: "Cerrar caso con id: 12031801",
		   text: texto,  
		   showCancelButton: true,   
		   confirmButtonColor: "#31B404",   
		   confirmButtonText: "Cerrar",
		   cancelButtonText: "Cancelar",
		   html: true,
		   customClass: 'swal-wide',    
		   closeOnConfirm: false }, 
		   function(){
		   		//$.get(kind+'.bloquear.'+id, function (result) {
		   			swal(
		   			{   title: "",
		   			   text: "Caso con id: 12031801 cerrado exitosamente",
		   			   type: "success",   
		   			   showCancelButton: false,   
		   			   confirmButtonColor: "#31B404",   
		   			   confirmButtonText: "Aceptar",   
		   			   closeOnConfirm: false,
		   			   html: true 
		   			}, 
		   				function()
		   				{   
		   			   		location.reload();
		   			   	}
		   			);

		   		//});
		   		 
		});
}
</script>
@stop