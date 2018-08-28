@extends('master_login')

@section('title', 'Home')

@section('content')
<div class="container-fluid">
	<div id="page-login" class="row">
		<div class="col-xs-12 col-md-4 col-md-offset-4 col-sm-6 col-sm-offset-3">
			<div class="box">
				<div class="box-content">
					<div class="text-center">
					@if (isset($data['logo']) && !empty($data['logo']) && $data['logo']->l != '' &&  isset($data['logo_width']) && !empty($data['logo_width']) && isset($data['logo_height']) && !empty($data['logo_height']))
						<img src="{{ $data['logo']->l }}" title="{{$data['organization']->o}}" width="{{ $data['logo_width']->w }}" height="{{ $data['logo_height']->h }}"></img><br>
					@endif
						<img src="assets/img/Logobgrcfinal.png" title="Bussiness Governance and Risk Compliance" width="180px" height="120px"></img>
						<h3 class="page-header"><style="font-family: Arial, Helvetica, sans-serif; color: #2E2E2E;"> Business, Governance, Risk and Compliance</style></h3>

					@if (isset($data['version']) && !empty($data['version']))
						<h6 class="page-header" style="color: darkblue;">
							<b>Versión {{ $data['version']->v }}</b>
						</h6>
					@endif
					</div>
					
				    {!! Form::open(['route' => 'log.store', 'class' => 'form', 'method' => 'POST']) !!}
				    <!--
				    <div class="form-group">
						<label class="control-label">Choose Languaje</label><br>
							<div class="radio-inline">
								<label>Español
									<input type="radio" name="languaje" value="es" checked>
									<i class="fa fa-circle-o"></i>
								</label>
							</div>
							<div class="radio-inline">
								<label>English
									<input type="radio" name="languaje" value="en">
									<i class="fa fa-circle-o"></i>
								</label>
							</div>
					</div>
					-->
					{!!Form::hidden('languaje','es')!!}
					<div class="form-group">
						<label class="control-label">E-mail</label>
						<input type="email" class="form-control" name="email" />
					</div>
					<div class="form-group">
						<label class="control-label">Password</label>
						<input type="password" class="form-control" name="password" />
					</div>
					<div class="text-center">
						{!!Form::submit('Log on', ['class'=>'btn btn-primary'])!!}
					</div>

					{!! Form::close() !!}
				</div>
			</div>
		</div>
	</div>
</div>

@stop