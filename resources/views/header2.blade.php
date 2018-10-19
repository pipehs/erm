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
			<div id="logo" class="col-xs-12 col-sm-12">
				<a href="">B-GRC &nbsp;&nbsp; 
				@if (isset($l) && !empty($l) && isset($l->side_width) && isset($l->side_height))
					<img src="{{ $l->logo }}" width="{{ $l->side_width }}" height="{{ $l->side_height }}"></img>
				@endif
				</a>
			</div>
			
		</div>
	</div>
</header>
<!--End Header-->

	

	