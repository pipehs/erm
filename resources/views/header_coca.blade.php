
<!--Start Header-->
<div id="screensaver">
	<canvas id="canvas"></canvas>
	<i class="fa fa-lock" id="screen_unlock"></i>
</div>
<div id="modalbox">
	<div class="devoops-modal">
		<div class="devoops-modal-header">
			<div class="modal-header-name">
				<span>Basic table</span>
			</div>
			<div class="box-icons">
				<a class="close-link">
					<i class="fa fa-times"></i>
				</a>
			</div>
		</div>
		<div class="devoops-modal-inner">
		</div>
		<div class="devoops-modal-bottom">
		</div>
	</div>
</div>
<header class="navbar">
	<div class="container-fluid expanded-panel">
		<div class="row">
			<div id="logo" class="col-xs-12 col-sm-2">
				<a href="">B-GRC &nbsp;&nbsp; <img src="assets/img/CocaCola/coca_andina.png" title="Coca Cola Andina" width="90px" height="20px"></img></a>

			</div>
			<div id="top-panel" class="col-xs-12 col-sm-10">
				<div class="row">
					<div class="col-xs-8 col-sm-4">
						<a href="#" class="show-sidebar">
						  <i class="fa fa-bars"></i>
						</a>
						<!--<div id="search">
							<input type="text" placeholder="search"/>
							<i class="fa fa-search"></i>
						</div>-->
					</div>
					<div class="col-xs-4 col-sm-8 top-panel-right">
						<p align="right">
						@if (!Auth::guest())
							<b>Usuario: </b> {{ Auth::user()->name }} {{ Auth::user()->surnames }}. <b>Roles: </b> 
							@foreach (Session::get('roles_name') as $role)
								{{ $role }}.
							@endforeach
								<a href="logout">Cerrar Sesi&oacute;n</a>
						</p>
						@endif
					</div>
				</div>
			</div>
		</div>
	</div>
</header>
<!--End Header-->

	

	