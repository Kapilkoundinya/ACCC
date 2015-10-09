@extends('layouts.master')

@section('head')
@stop

@section('content')

	<!-- Page Header -->
	<section class = "content-header">
		<h1>
			Remote Labs
			<div class="pull-right">
			<small>
				<strong>Start Date : </strong>{{$dateTime[2]}} &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp	@if(isset($dateTime[1]))<strong>End Date : </strong> {{$dateTime[3]}} @endif
			</small>
			<div class="btn-group">
				<button id="dateswitch" class="btn btn-info btn-block btn-flat btn-xs"><i class="fa fa-calendar"></i></button>
			</div><!-- /.btn-group -->
			</div>	
		</h1>
	</section>

	<!-- Main Content -->
	<section class="content">
		<div class="row">
			<div class="col-md-12">
				<div class="box box-success">
					<div class="box-header">
						<h3 class="box-title"> Services Offered by Remote Labs</h3>
						<div class="box-tools pull-right">
							<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
							<button class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
						</div><!-- /.box tools -->
					</div><!-- /.box header -->
					<div class="box-body no-padding ">
						<div class="row">
							<div class="col-md-6">
								<table id="services" class="table table-striped table-hover">
									<thead>
										<tr>
											<th>Service</th><th>No.of Tasks Completed</th>
										</tr>
									</thead>
									<tbody>
										@forelse($services_all_locations as $service_name => $service_count)
											<tr><td>{{$service_name}}</td><td>{{$service_count}}</td></tr>
										@empty
										<tr><td>N/A</td><td>N/A</td></tr>
										<tr><td>N/A</td><td>N/A</td></tr>
										<tr><td>N/A</td><td>N/A</td></tr>
										<tr><td>N/A</td><td>N/A</td></tr>
										<tr><td>N/A</td><td>N/A</td></tr>
										@endforelse
									</tbody>
								</table>
							</div><!-- .col-->
							<div class="col-md-6">
								<p class="text-center"> <strong>Services Breakdown</strong> </p>
								<div class="row">
									<div class="col-md-5">
										<ul class="chart-legend clearfix" class="text-center">
											<br></br>
											<li><i class="fa fa-circle-o text-red"></i> Classroom Support</li>
											<li><i class="fa fa-circle-o text-orange"></i> Computer Labs</li>
											<li><i class="fa fa-circle-o text-yellow"></i> Printing</li>
											<li><i class="fa fa-circle-o text-green-blue"></i> Laptops and Equipment Lending</li>
											<li><i class="fa fa-circle-o text-gray"></i> Other Services</li>
										</ul>
									</div><!-- /.col -->
									<div class="col-md-7">
										<div class="chart-responsive">
											<canvas id="servicespie" width="250" height="250"></canvas>
										</div><!-- ./chart-responsive -->
									</div><!-- /.col -->
								</div><!-- /.row -->
							</div><!-- /.col -->
						</div><!-- row-->
					</div><!-- /.box-body -->
					<div class="box-footer"><!-- box-footer -->
						<div class="row">
							<div class="col-md-6">
								<div class="description-block">
									<h5 class="description-header">TOP TRENDING SERVICE </h5>
									<span class="description"><strong>{{$statistics[0]}}</strong></span>
								</div><!-- /.description-box -->
							</div><!-- /.col -->
							<div class-"col-md-6">
								<div class="description-block">
									<h5 class="description-header">TOP TRENDING LOCATION </h5>
									<span class="description"><strong>{{$statistics[1]}}</strong></span>
								</div><!-- /.description-box -->
							</div><!-- /.col -->
						</div><!-- /.row -->
					</div><!-- /.box-footer  -->
				</div><!-- /.box .box-success -->
			</div><!-- /.col -->
		</div><!-- /.row -->

		<h3>Location Based Statistics</h3>
		@forelse($lab_locations as $location => $location_name)
			@if(count($location_name)>0)
			<div class="row">
				<div class="col-md-12">
					<div class="box box-success">
						<div class="box-header">
							<h3 class="box-title">{{$location}}</h3>
								<div class="box-tools pull-right">
									<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
									<button class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
								</div><!-- /.box tools -->
						</div><!-- /.box-header -->
						<div class="box-body no-padding">
							<div class="row">
								<div class="col-md-8">
									<table id="consultants_tasks" class="table table-stripped table-hover">
										<thead>
											<tr>
												<th>NetID</th><th>Tasks Completed</th>
											</tr>
										</thead>
										<tbody>
											@foreach($location_name as $consultant)
											<tr>
												<td><button type="button" class="btn btn-default" data-toggle="modal" data-target="#consultantStatistics" onClick="getData({{$consultant['netid']}},)">{{$consultant['netid']}}</button></td>
												@if($consultant['taskpercentage'] > 80)	
													<td><span class="badge bg-green" >{{$consultant['taskcompleted']}} ({{$consultant['taskpercentage']}}%)</span></td>
												@elseif($consultant['taskpercentage'] > 30)	
													<td><span class="badge bg-orange">{{$consultant['taskcompleted']}} ({{$consultant['taskpercentage']}}%)</span></td>
												@else	
													<td><span class="badge bg-red">{{$consultant['taskcompleted']}} ({{$consultant['taskpercentage']}}%)</span></td>
												@endif
											</tr>
											@endforeach
										</tbody>
									</table>
								</div><!-- /.col -->			
								<div class="col-md-4">
									<div class="chart-responsive">
										<p class="text-center"><strong>Services</strong></p> 
										<canvas class="locationspie" height="175"></canvas>
									</div><!-- ./chart-responsive -->
								</div><!-- /.col -->
							</div><!-- /.row --> 
						</div><!-- /.box-body -->
						<div class="box-footer">
							<div class="row">
								<div class="col-md-4">
									@foreach($statistics[3] as $labName => $labPerformanceScore)
										@if($location == $labName)
											@if($labPerformanceScore >=80)
												<div class="info-box">
													<span class="info-box-icon bg-green"><i class="fa fa-cogs"></i></span>
													<div class="info-box-content">
														<span class="info-box-text">PERFORMANCE SCORE</span>
														<span class="info-box-number">	
															<strong>{{$statistics[2][$labName]}}({{$labPerformanceScore}}%)</strong>
														</span>
													</div><!--/.info-box-content -->
												</div><!-- /.info-box -->
											@elseif($labPerformanceScore >30)
												<div class="info-box">
													<span class="info-box-icon bg-orange"><i class="fa fa-cogs"></i></span>
													<div class="info-box-content">
														<span class="info-box-text">PERFORMANCE SCORE</span>
														<span class="info-box-number">	
															<strong>{{$statistics[2][$labName]}}({{$labPerformanceScore}}%)</strong>
														</span>
													</div><!--/.info-box-content -->
												</div><!-- /.info-box -->
											@else
												<div class="info-box">
													<span class="info-box-icon bg-red"><i class="fa fa-cogs"></i></span>
													<div class="info-box-content">
														<span class="info-box-text">PERFORMANCE SCORE</span>
														<span class="info-box-number">	
															<strong>{{$statistics[2][$labName]}}({{$labPerformanceScore}}%)</strong>
														</span>
													</div><!--/.info-box-content -->
												</div><!-- /.info-box -->
											@endif
										@endif
									@endforeach
								</div><!-- /.col -->
								<div class="col-md-4">
									@foreach($statistics[4] as $labName => $labPerformanceScore)
										@if($location == $labName)
											<div class="info-box">
												<span class="info-box-icon bg-blue"><i class="fa fa-user"></i></span>
												<div class="info-box-content">
													<span class="info-box-text">MOST ACTIVE CONSULTANT</span>
													<span class="info-box-number">	
														<strong>{{$statistics[4][$labName]}}</strong>
													</span>
												</div><!--/.info-box-content -->
											</div><!-- /.info-box -->
										@endif
									@endforeach
								</div><!-- /.col -->
								<div class="col-md-4">
									@foreach($paperLevel as $paper)
										@if($location == $paper->lab)
											@if($paper->paper_fill >=80)
												<div class="info-box">
													<span class="info-box-icon bg-green"><i class="fa fa-print"></i></span>
													<div class="info-box-content">
														<span class="info-box-text">Paper Level</span>
														<span class="info-box-number">	
															<strong>{{$paper->paper_fill}}%</strong>
														</span>
													</div><!--/.info-box-content -->
												</div><!-- /.info-box -->
											@elseif($paper->paper_fill >30)
												<div class="info-box">
													<span class="info-box-icon bg-orange"><i class="fa fa-print"></i></span>
													<div class="info-box-content">
														<span class="info-box-text">Paper Level</span>
														<span class="info-box-number">	
															<strong>{{$paper->paper_fill}}%</strong>
														</span>
													</div><!--/.info-box-content -->
												</div><!-- /.info-box -->
											@else
												<div class="info-box">
													<span class="info-box-icon bg-red"><i class="fa fa-print"></i></span>
													<div class="info-box-content">
														<span class="info-box-text">Paper Level</span>
														<span class="info-box-number">	
															<strong>{{$paper->paper_fill}}%</strong>
														</span>
													</div><!--/.info-box-content -->
												</div><!-- /.info-box -->
											@endif
										@endif
									@endforeach	
								</div><!-- /.col -->
							</div><!-- /.row -->
						</div><!-- /.box-footer -->
					</div><!-- /.box -->
				</div><!-- /.col -->
			</div><!-- /.row -->
			@endif
		@empty
			<div class="row">
				<div class="col-md-12">
					<h3>Not Enough Data Available</h3>	
				</div><!-- /.col -->
			</div><!-- /.row -->
		@endforelse

		<!-- Modal Popups for Consultant specific Statisctics -->
		<div id="consultantStatistics" class="modal fade" role="dialog" aria-labelledby="myModalLabel">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h4>Consultant Analytics</h4>
					</div><!-- /.modal-header -->
					<div class="modal-body">
						<div class="container-fluid">
							<div class=row>
								<div class="col-md-12">
									<div class="box">
										<div class="box-header">
											<strong>NetID</strong>								
										</div><!-- /.box-header -->
										<div class="box-body">
										</div><!-- /.box-body -->
										<div class="box-footer">
										</div><!-- /.box-footer -->
									</div><!-- /.box -->
								</div><!-- /.col -->
							</div><!-- /.row -->
						</div><!-- /.container-fluid -->
					</div><!-- /.modal-body -->
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
					</div><!-- /.modal-footer -->
				</div><!-- /.modal-content -->
			</div> <!-- /.modal-dialog -->
		</div><!-- /.modal -->
	</section>
@stop



@section('footer')
	<script>
		//Pie Chart data for services offered by all the remote labs
		var pieChartDataAllLocations = JSON.parse(' {!!$pieChartDataAllLocations!!} ');

		//Pie Chart data for services offered at each specific location
		var pieChartDataPerLocation = JSON.parse(' {!!$pieChartDataPerLocation!!} ');

		//Consultant task completion and paper in printer data
		var statisticsData = {!!json_encode($lab_locations)!!};

	</script>

	<!-- Data Tables script for Hover Tables -->
	<script src="plugins/datatables10/media/js/jquery.dataTables.js" type="text/javascript"></script>
	<script src="plugins/datatables/dataTables.bootstrap.js" type="text/javascript"></script>

	<!-- JavaScript Code -->
	<script src="js/adminview-piechart.js" type="text/javascript"></script>
	<script src="js/adminview-datatables.js" type="text/javascript"></script>
	<script src="js/adminview-daterangepicker.js" type="text/javascript"></script>
@stop
