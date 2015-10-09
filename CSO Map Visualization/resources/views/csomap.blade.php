@extends('layouts.master')

@section('head')
@stop

@section('content')
<!-- (Page Header) Title section-->
<section class="content-header">
    <h1>ACCC Campus Map</h1>  
</section>
<!--Main Content -->
<section class="content">
<!-- Div for Google Maps-->
<div class="row">
    <div class="col-md-12">
        <div class="mapcontainer">
            <div id="map" style="width:100%;height:700px; border: 1px solid #050"></div>
        </div><!--maps -->
		<br></br>
	</div>
</div>	
<!-- Div for the Other locations-->
<div class="row">
    <div class="col-md-12">
        <div class="box box-solid box-success">
            <div class="box-header with-border">
				<h3 class="box-title">Other Locations</h3>
				<div class="box-tools pull-right">
					<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
				</div><!-- /.box-tools -->
            </div><!-- /.box-header -->
            <div class="box-body">
				<table class="table table-bordered" id="OtherLocationTable">
				</table>
			</div><!-- /.box-body -->
		</div><!-- /.box-->
	</div>
</div>
</section>
@stop

@section('footer')
<!-- Google Maps API-->
<script src="https://maps.google.com/maps/api/js?sensor=false"
  type="text/javascript"></script>
<!-- Java Script Functionality-->
<script src="js/csomap.js"type="text/javascript"></script>
@stop
