@extends('layouts.master')

@section('head')
  <title>ACCC Campus Map</title>
  <style>
     table,th,td {
        margin: 10px;
        border: 1px solid #050;
        width: 100%;
        padding:5px;
        }
  </style>
@stop


@section('content')
<section class="content">

<!-- Div for the maps section-->
  <section>
          <div class="mapcontainer">
                <div id="map" style="width:100%;height:500px"></div>
          </div>
  </section>

<!-- Div for the Other locations data -->
  <table id='table'>
  </table>

</section>
@stop

@section('footer')

<!--Google MapsAPi and jQuery-->
<script src="https://maps.google.com/maps/api/js?sensor=false"
         type="text/javascript"></script>
<script src="js/cso-map.js" type="text/javascript"></script>

@stop
