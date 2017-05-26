@extends('master_login')

@section('title', 'Home')

@section('content')
<div class="container-fluid">
	<div id="page-login" class="row">
		<div class="col-xs-12 col-md-4 col-md-offset-4 col-sm-6 col-sm-offset-3">
			<div class="box">
				<div class="box-content">
					<div class="text-center">
						<!--<img src="assets/img/Latam/latam.png" title="LATAM" width="300px" height="80px"></img><br>-->
						<!--<img src="assets/img/NorteGrande/logo_norte_grande.PNG" title="Norte Grande" width="300px" height="80px"></img><br>-->
						<img src="assets/img/logoCompleto.PNG" title="Bussiness Governance and Risk Compliance" width="320px" height="120px"></img>
						<h3 class="page-header">Business, Governance, Risk and Compliance</h3>
					</div>
					
				    {!! Form::open(['route' => 'log.store', 'class' => 'form', 'method' => 'POST']) !!}
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