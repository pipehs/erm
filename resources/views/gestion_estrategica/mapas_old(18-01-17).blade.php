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
			<td class="perspectivas">Financiera</td>
			<td>
			<table class="financiera" border="0" align="center">
			<tr>
				@foreach ($objectives as $obj)
					@if ($obj['perspective'] == 1)
						<td><center><p class="circulo-big">{{$obj['code']}}</p><p class="objectives" title="{{$obj['description']}}">{{$obj['name']}}</p><br>
						<!--<div style="display:inline-block; margin:-15px;">-->
						@foreach ($obj['impacted'] as $imp)
							<p class="circulo-impactado" title="{{$imp['description']}}">{{ $imp['code'] }}</p>
						@endforeach
						<!--</div>-->
						</center>
						</td>
					@endif
				@endforeach
			</tr>
			</table>
			</td>
		</tr>

		<tr>
			<td class="perspectivas">Clientes&nbsp;</td>
			<td>
			<table class="clientes" border="0" align="center">
			<tr>
				@foreach ($objectives as $obj)
					@if ($obj['perspective'] == 3)
						<td>
						<center><p class="circulo-big">{{$obj['code']}}</p><p class="objectives" title="{{$obj['description']}}">{{$obj['name']}}</p></br>
						<!--<div style="display:inline-block; margin:-15px;">-->
						@foreach ($obj['impacted'] as $imp)
							<p class="circulo-impactado" title="{{$imp['description']}}">{{ $imp['code'] }}</p>
						@endforeach
						<!--</div>-->
						</center>
						</td>
					@endif
				@endforeach
			</tr>
			</table>
			</td>
		</tr>

		<tr>
			<td class="perspectiva_proceso">Procesos&nbsp;&nbsp;&nbsp;&nbsp;</td>
			<td>
			<table style="width:100%;">
			<tr><td>
			<table class="procesos procesos1" border="0" align="center">
			<tr><td style="vertical-align:top;"><h4><b><center>Gesti&oacute;n Operacional</center></b></h4></td></tr>
				<tr>
					<?php $contador = 0; //contador para que sean 2 por fila ?>
					@foreach ($objectives as $obj)
						@if ($obj['perspective'] == 2 && $obj['perspective2'] == 1)
							@if ($contador > 1)
								<tr>
							@endif
							<td style="vertical-align:top;">
								<center><p class="circulo-big">{{$obj['code']}}</p><p class="objectives" title="{{$obj['description']}}">{{$obj['name']}}</p></br>
								<!--<div style="display:inline-block; margin:-15px;">-->
								@foreach ($obj['impacted'] as $imp)
									<p class="circulo-impactado" title="{{$imp['description']}}">{{ $imp['code'] }}</p>
								@endforeach
								<!--</div>-->
								</center>
							</td>

							@if ($contador > 1)
								</tr>
								<?php $contador = 0; ?>
							@endif
							<?php $contador += 1; ?>
						@endif
					@endforeach
				</tr>
			</table>
			<table class="procesos procesos2" border="0" align="center">
			<tr><td style="vertical-align:top;"><h4><b><center>Gesti&oacute;n de Clientes</center></b></h4>
				<tr>
					<?php $contador = 0; //contador para que sean 2 por fila ?>
					@foreach ($objectives as $obj)
						@if ($obj['perspective'] == 2 && $obj['perspective2'] == 2)
							@if ($contador > 1)
								<tr>
							@endif
							<td style="vertical-align:top;">
								<center><p class="circulo-big">{{$obj['code']}}</p><p class="objectives" title="{{$obj['description']}}">{{$obj['name']}}</p></br>
								<!--<div style="display:inline-block; margin:-15px;">-->
								@foreach ($obj['impacted'] as $imp)
									<p class="circulo-impactado" title="{{$imp['description']}}">{{ $imp['code'] }}</p>
								@endforeach
								<!--</div>-->
								</center>
							</td>

							@if ($contador > 1)
								</tr>
								<?php $contador = 0; ?>
							@endif
							<?php $contador += 1; ?>
						@endif
					@endforeach
				</tr>
			</tr>
			</table>
			<table class="procesos procesos3" border="0" align="center">
			<tr><td style="vertical-align:top;"><h4><b><center> Gesti&oacute;n de Innovaci&oacute;n</center></b></h4></td></tr>
				<tr>
					<?php $contador = 0; //contador para que sean 2 por fila ?>
					@foreach ($objectives as $obj)
						@if ($obj['perspective'] == 2 && $obj['perspective2'] == 3)
							@if ($contador > 1)
								<tr>
							@endif
							<td style="vertical-align:top;">
								<center><p class="circulo-big">{{$obj['code']}}</p><p class="objectives" title="{{$obj['description']}}">{{$obj['name']}}</p></br>
								<!--<div style="display:inline-block; margin:-15px;">-->
								@foreach ($obj['impacted'] as $imp)
									<p class="circulo-impactado" title="{{$imp['description']}}">{{ $imp['code'] }}</p>
								@endforeach
								<!--</div>-->
								</center>
							</td>

							@if ($contador > 1)
								</tr>
								<?php $contador = 0; ?>
							@endif
							<?php $contador += 1; ?>
						@endif
					@endforeach
				</tr>
			</table>
			<table class="procesos procesos4" border="0" align="center">
				<tr><td style="vertical-align:top;"><h4><b><center>Reguladores sociales</center></b></h4></td></tr>
				<tr>
					<?php $contador = 0; //contador para que sean 2 por fila ?>
					@foreach ($objectives as $obj)
						@if ($obj['perspective'] == 2 && $obj['perspective2'] == 4)
							@if ($contador > 1)
								<tr>
							@endif
							<td style="vertical-align:top;">
								<center><p class="circulo-big">{{$obj['code']}}</p><p class="objectives" title="{{$obj['description']}}">{{$obj['name']}}</p></br>
								<!--<div style="display:inline-block; margin:-15px;">-->
								@foreach ($obj['impacted'] as $imp)
									<p class="circulo-impactado" title="{{$imp['description']}}">{{ $imp['code'] }}</p>
								@endforeach
								<!--</div>-->
								</center>
							</td>

							@if ($contador > 1)
								</tr>
								<?php $contador = 0; ?>
							@endif
							<?php $contador += 1; ?>
						@endif
					@endforeach
				</tr>
			</table>
			</td></tr></table>
			</td>
		</tr>

		<tr>
			<td class="perspectivas2">Aprendizaje</td>
			<td>
				<table style="width:100%;">
				<tr><td>
				<table class="aprendizaje aprendizaje_box" border="0" align="center">
				<tr><td style="vertical-align:top;"><h4><b><center>Capital Humano</center></b></h4></td></tr>
				<tr>
					<?php $contador = 0; //contador para que sean 2 por fila ?>
					@foreach ($objectives as $obj)
						@if ($obj['perspective'] == 4 && $obj['perspective2'] == 1)
							@if ($contador > 2)
								<tr>
							@endif
							<td style="vertical-align:top;">
								<center><p class="circulo-big">{{$obj['code']}}</p><p class="objectives" title="{{$obj['description']}}">{{$obj['name']}}</p></br>
								<!--<div style="display:inline-block; margin:-15px;">-->
								@foreach ($obj['impacted'] as $imp)
									<p class="circulo-impactado" title="{{$imp['description']}}">{{ $imp['code'] }}</p>
								@endforeach
								<!--</div>-->
								</center>
							</td>

							@if ($contador > 1)
								</tr>
								<?php $contador = 0; ?>
							@endif
							<?php $contador += 1; ?>
						@endif
					@endforeach
				</tr>
				</table>
				<table class="aprendizaje aprendizaje_box" border="0" align="center">
				<tr><td style="vertical-align:top;"><h4><b><center>Capital de informaci&oacute;n</center></b></h4></td></tr>
					<?php $contador = 0; //contador para que sean 2 por fila ?>
					@foreach ($objectives as $obj)
						@if ($obj['perspective'] == 4 && $obj['perspective2'] == 2)
							@if ($contador > 2)
								<tr>
							@endif
							<td style="vertical-align:top;">
								<center><p class="circulo-big">{{$obj['code']}}</p><p class="objectives" title="{{$obj['description']}}">{{$obj['name']}}</p></br>
								<!--<div style="display:inline-block; margin:-15px;">-->
								@foreach ($obj['impacted'] as $imp)
									<p class="circulo-impactado" title="{{$imp['description']}}">{{ $imp['code'] }}</p>
								@endforeach
								<!--</div>-->
								</center>
							</td>

							@if ($contador > 1)
								</tr>
								<?php $contador = 0; ?>
							@endif
							<?php $contador += 1; ?>
						@endif
					@endforeach
				</tr>
				</table>
				<table class="aprendizaje aprendizaje_box" border="0" align="center">
				<tr><td style="vertical-align:top;"><h4><b><center>Capital organizativo</center></b></h4></td></tr>
					<?php $contador = 0; //contador para que sean 2 por fila ?>
					@foreach ($objectives as $obj)
						@if ($obj['perspective'] == 4 && $obj['perspective2'] == 3)
							@if ($contador > 1)
								<tr>
							@endif
							<td style="vertical-align:top;">
								<center><p class="circulo-big">{{$obj['code']}}</p><p class="objectives" title="{{$obj['description']}}">{{$obj['name']}}</p></br>
								<!--<div style="display:inline-block; margin:-15px;">-->
								@foreach ($obj['impacted'] as $imp)
									<p class="circulo-impactado" title="{{$imp['description']}}">{{ $imp['code'] }}</p>
								@endforeach
								<!--</div>-->
								</center>
							</td>

							@if ($contador > 1)
								</tr>
								<?php $contador = 0; ?>
							@endif
							<?php $contador += 1; ?>
						@endif
					@endforeach
				</table>
			</td></tr></table>
			</td>
		</tr>
	</table>

	@else
		</br>
		<center><h4><b>No existe un plan estrat&eacute;gico activo para la organizaci&oacute;n {{ $org_selected }}, o bien no se han definido objetivos para &eacute;sta.</b></h4></center>
		</br></br></br></br>
	@endif
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