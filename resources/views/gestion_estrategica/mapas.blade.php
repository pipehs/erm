@extends('master')

@section('title', 'Gesti&oacute;n Estrat&eacute;gica - KPI')

@section('content')

{!!Html::style('assets/css/mapas.css')!!}

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li>{!!Html::link('mapas','Mapa Estrat&eacute;gico')!!}</li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-m-6">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-user"></i>
					<span>Mapa Estrat&eacute;gico</span>
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

			En esta secci&oacute;n podr&aacute; ver el mapa estrat&eacute;gico de cada organizaci&oacute;n ingresada en el sistema.<br><br>

@if (isset($objectives))

	@if (!empty($objectives))
	
	<table class="table_mapa" border="0">
		<thead>
		<th colspan="2" class="thead2">
		<h3>Mapa estrat&eacute;gico {{ $org_selected }}</h3>
			<div class="objetivos">Visi&oacute;n: {{ $vision }}</div>
		</th>
		</thead>
		<tr>
			<td colspan="2" class="perspectiva">Perspectiva Financiera</td>
		</tr>
		<tr>
			<td colspan="2">
				<div class="financiera_box1">
					<h4><b><center>Productividad</center></b></h4>
					<table align="center">
						<tr><td>
						@foreach ($objectives as $obj)
							@if ($obj['perspective'] == 1 && $obj['perspective2'] == 1)
								<div class="obj_position">
									<center>
									@foreach ($obj['impacted'] as $imp)
										<p class="circulo-impactado" title="{{$imp['description']}}">{{ $imp['code'] }}</p>
									@endforeach
									<br/>
									<p class="circulo-big" title="{{$obj['description']}}">{{$obj['code']}}</p><p class="objectives">{{$obj['name']}}</p>
									</center>
								</div>
							@endif
						@endforeach
						</td></tr>
					</table>
				</div>
				<div class="financiera_box2">
					<h4><b><center>Aumento</center></b></h4>
					<table align="center">
						<tr><td>
						@foreach ($objectives as $obj)
							@if ($obj['perspective'] == 1 && $obj['perspective2'] == 2)
								<div class="obj_position">
									<center>
									@foreach ($obj['impacted'] as $imp)
										<p class="circulo-impactado" title="{{$imp['description']}}">{{ $imp['code'] }}</p>
									@endforeach
									<br/>
									<p class="circulo-big" title="{{$obj['description']}}">{{$obj['code']}}</p><p class="objectives">{{$obj['name']}}</p>
									</center>
								</div>
							@endif
						@endforeach
						</td></tr>
					</table>
				</div>
			</td>
		</tr>
		<tr>
			<td class="perspectiva">Perspectiva de Clientes&nbsp;</td>
		</tr>
		<tr>
			<td colspan="2">
				<div class="clientes">
					<table align="center">
					<tr><td>
						@foreach ($objectives as $obj)
							@if ($obj['perspective'] == 2)
								<div class="obj_position">
									<center>
									@foreach ($obj['impacted'] as $imp)
										<p class="circulo-impactado" title="{{$imp['description']}}">{{ $imp['code'] }}</p>
									@endforeach
									<br/>
									<p class="circulo-big" title="{{$obj['description']}}">{{$obj['code']}}</p><p class="objectives">{{$obj['name']}}</p>
									</center>
								</div>
							@endif
						@endforeach
					</td></tr>
					</table>
				</div>
			</td>
		</tr>
		<tr>
			<td colspan="2" class="perspectiva">Perspectiva de Procesos</td>
		</tr>
		<tr>
			<td colspan="2">
				<div class="procesos procesos1">
					<h4><b><center>Gesti&oacute;n Operacional</center></b></h4>
					<table align="center">
						<tr><td>
						@foreach ($objectives as $obj)
							@if ($obj['perspective'] == 3 && $obj['perspective2'] == 1)
								<div class="obj_position">
									<center>
									@foreach ($obj['impacted'] as $imp)
										<p class="circulo-impactado" title="{{$imp['description']}}">{{ $imp['code'] }}</p>
									@endforeach
									<br/>
									<p class="circulo-big" title="{{$obj['description']}}">{{$obj['code']}}</p><p class="objectives">{{$obj['name']}}</p>
									</center>
								</div>
							@endif
						@endforeach
						</td></tr>
					</table>
				</div>
				<div class="procesos procesos2">
					<h4><b><center>Gesti&oacute;n de Clientes</center></b></h4>
					<table align="center">
						<tr><td>
						@foreach ($objectives as $obj)
							@if ($obj['perspective'] == 3 && $obj['perspective2'] == 2)
								<div class="obj_position">
									<center>
									@foreach ($obj['impacted'] as $imp)
										<p class="circulo-impactado" title="{{$imp['description']}}">{{ $imp['code'] }}</p>
									@endforeach
									<br/>
									<p class="circulo-big" title="{{$obj['description']}}">{{$obj['code']}}</p><p class="objectives">{{$obj['name']}}</p>
									</center>
								</div>
							@endif
						@endforeach
						</td></tr>
					</table>
				</div>
				<div class="procesos procesos3">
					<h4><b><center>Gesti&oacute;n de Innovaci&oacute;n</center></b></h4>
					<table align="center">
						<tr><td>
						@foreach ($objectives as $obj)
							@if ($obj['perspective'] == 3 && $obj['perspective2'] == 3)
								<div class="obj_position">
									<center>
									@foreach ($obj['impacted'] as $imp)
										<p class="circulo-impactado" title="{{$imp['description']}}">{{ $imp['code'] }}</p>
									@endforeach
									<br/>
									<p class="circulo-big" title="{{$obj['description']}}">{{$obj['code']}}</p><p class="objectives">{{$obj['name']}}</p>
									</center>
								</div>
							@endif
						@endforeach
						</td></tr>
					</table>
				</div>
				<div class="procesos procesos4">
					<h4><b><center>Reguladores sociales</center></b></h4>
					<table align="center">
						<tr><td>
						@foreach ($objectives as $obj)
							@if ($obj['perspective'] == 3 && $obj['perspective2'] == 4)
								<div class="obj_position">
									<center>
									@foreach ($obj['impacted'] as $imp)
										<p class="circulo-impactado" title="{{$imp['description']}}">{{ $imp['code'] }}</p>
									@endforeach
									<br/>
									<p class="circulo-big" title="{{$obj['description']}}">{{$obj['code']}}</p><p class="objectives">{{$obj['name']}}</p>
									</center>
								</div>
							@endif
						@endforeach
						</td></tr>
					</table>
				</div>
			</td>
		</tr>

		<tr>
			<td colspan="2" class="perspectiva">Perspectiva de Aprendizaje</td>
		</tr>
		<tr>
			<td colspan="2">
				<div class="aprendizaje_box" >
					<h4><b><center>Capital Humano</center></b></h4>
					<table align="center">
					<tr><td>
					@foreach ($objectives as $obj)
						@if ($obj['perspective'] == 4 && $obj['perspective2'] == 1)
							<div class="obj_position">
								<center>
									@foreach ($obj['impacted'] as $imp)
										<p class="circulo-impactado" title="{{$imp['description']}}">{{ $imp['code'] }}</p>
									@endforeach
									<br/>
									<p class="circulo-big" title="{{$obj['description']}}">{{$obj['code']}}</p><p class="objectives">{{$obj['name']}}</p>
								</center>
							</div>
						@endif
					@endforeach
					</td></tr>
					</table>
				</div>
				<div class="aprendizaje_box" >
				<h4><b><center>Capital de informaci&oacute;n</center></b></h4>
					<table align="center">
					<tr><td>
					@foreach ($objectives as $obj)
						@if ($obj['perspective'] == 4 && $obj['perspective2'] == 2)
							<div class="obj_position">
								<center>
									@foreach ($obj['impacted'] as $imp)
										<p class="circulo-impactado" title="{{$imp['description']}}">{{ $imp['code'] }}</p>
									@endforeach
									<br/>
									<p class="circulo-big" title="{{$obj['description']}}">{{$obj['code']}}</p><p class="objectives">{{$obj['name']}}</p>
								</center>
							</div>
						@endif
					@endforeach
					</td></tr>
					</table>
				</div>
				<div class="aprendizaje_box">
				<h4><b><center>Capital organizativo</center></b></h4>
					@foreach ($objectives as $obj)
						@if ($obj['perspective'] == 4 && $obj['perspective2'] == 3)
							<div class="obj_position">
								<center>
									@foreach ($obj['impacted'] as $imp)
										<p class="circulo-impactado" title="{{$imp['description']}}">{{ $imp['code'] }}</p>
									@endforeach
									<br/>
									<p class="circulo-big" title="{{$obj['description']}}">{{$obj['code']}}</p><p class="objectives">{{$obj['name']}}</p>
								</center>
							</div>
						@endif
					@endforeach
				</div>
			</td>
		</tr>
	</table>

	@else
		</br>
		<center><h4><b>No existe un plan estrat&eacute;gico activo para la organizaci&oacute;n {{ $org_selected }}, o bien no se han definido objetivos para &eacute;sta.</b></h4></center>
		</br></br></br></br>
	@endif
	</br></br></br>
	<center>
		<p><a href="#" onclick="history.back()" class="btn btn-danger">Volver</a></p>
	<center>
@else
	{!!Form::open(['route'=>'mapas2','method'=>'GET','class'=>'form-horizontal'])!!}
			<div class="form-group">
				{!!Form::label('Seleccione organización',null,['class'=>'col-sm-4 control-label'])!!}
				<div class="col-sm-3">
					{!!Form::select('organization_id',$organizations,null, 
							 	   ['id' => 'orgs','required'=>'true','placeholder'=>'- Seleccione -'])!!}
				</div>
			</div>

			<div class="form-group">
						<center>
						{!!Form::submit('Seleccionar', ['class'=>'btn btn-success','id'=>'guardar'])!!}
						</center>
			</div>
	{!!Form::close()!!}
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