@extends('master')

@section('title', 'Categor&iacute;as de Riesgos')

@stop

@section('content')

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li><a href="#">Reportes B&aacute;sicos</a></li>
			<li><a href="heatmap">Mapa de Calor</a></li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-m-6">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-table"></i>
					<span>Mapa de Calor</span>
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
				   <div id="container">
				   	
				   </div>
				    
			</div>
		</div>
	</div>
</div>
@stop


@section('scripts')
<script>
$(function () {

    $('#container').highcharts({
        
        chart: {
            type: 'heatmap',
            marginTop: 40,
            marginBottom: 40
        },


        title: {
            text: 'Mapa de calor para Riesgos Ejemplo'
        },

        xAxis: {
            categories: ['1', '2', '3', '4', '5'],
            title: 'Probabilidad'
        },

        yAxis: {
            categories: ['1', '2', '3', '4', '5'],
            title: 'Criticidad'
        },

        colorAxis: {
            min: 0,
            minColor: '#F3F781',
            maxColor: '#FF0000'
        },

        legend: {
            align: 'right',
            layout: 'vertical',
            margin: 0,
            verticalAlign: 'top',
            y: 25,
            symbolHeight: 320
        },

        tooltip: {
            formatter: function () {
                return '<b>Riesgos de criticidad ' + this.series.xAxis.categories[this.point.x] + ' y probabilidad ' + 
                this.series.yAxis.categories[this.point.y] + '</b>';
            }
        },

        series: [{
            name: 'Niveles',
            borderWidth: 0,
            data: [[0,0,0,"holi"],[0,1,1],[0,2,2],[0,3,3],[0,4,4],[1,0,1],[1,1,2],[1,2,3],[1,3,4],[1,4,5],[2,0,2],[2,1,3],[2,2,4],[2,3,5],[2,4,6],[3,0,3],[3,1,4],[3,2,5],[3,3,6],[3,4,7],[4,0,4],[4,1,5],[4,2,6],[4,3,7],[4,4,8]],
            dataLabels: {
                enabled: true,
                color: 'black',
                style: {
                    textShadow: 'none'
                }
            }
        }]

    });
});
</script>


@stop
