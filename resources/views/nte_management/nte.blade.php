@extends('layouts.front-app')
@section('title')
{{Auth::user()->access[Route::current()->action["as"]]["user_type"]}} - Dashboard
@stop
@section("styles")
<style>
	th{
		text-align: center;
	}
    .btn-check{
       display:none;
    }
    .dz-success-mark{
        display: none;
    }
    .dz-error-mark{
        display: none;
    }
   
</style>
@stop
@section("content")
@if(preg_match("/R/i", Auth::user()->access[Route::current()->action["as"]]["access"])=="0")
                            
	{{Auth::user()->access[Route::current()->action["as"]]["access"]}}
	<div class="page-wrapper">
		<div class="content container-fluid">
			<div class="row">
				<div class="col-xl-12 col-sm-12 col-12 mb-4">
					<div class="row">
						<div class="col-xl-10 col-sm-8 col-12 ">
							<label >YOU HAVE NO PRIVILEDGE ON THIS PAGE </label>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
@else
<div class="page-wrapper" id="dashboard_page">
    <div class="content container-fluid">
        <div class="page-name 	mb-4">
            <h4 class="m-0">NTE Management</h4>
            <label> {{date('D, d M Y')}}</label>
            
            
        </div>

        <div class="row mb-4">
            <div class="col-xl-9 col-sm-12 col-12" id="statistics_container">

        <div class="row" id="graph_container">
            <div class="col-md-6 ">
                
                <div id="container"></div>
                
            </div>
             <div class="col-md-6">
                
                <div id="container_2"></div>
                
            </div> 
        </div>
        
    </div>
</div>
@endif
@stop
@section("scripts")
<script src="{{asset_with_env('plugins/highcharts/highcharts.js')}}"></script>
<script src="{{asset_with_env('plugins/highcharts/variable-pie.js')}}"></script>
<script src="{{asset_with_env('plugins/highcharts/exporting.js')}}"></script>
<script src="{{asset_with_env('plugins/highcharts/export-data.js')}}"></script>
<script src="{{asset_with_env('plugins/highcharts/accessibility.js')}}"></script>
    <script>

    </script>
@stop