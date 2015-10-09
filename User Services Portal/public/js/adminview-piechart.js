$(
function () {

  //-----------------------
  //- PIE CHART -
  //-----------------------

  var pieOptions = {
    //Boolean - Whether we should show a stroke on each segment
    segmentShowStroke: true,

    //String - The colour of each segment stroke
    segmentStrokeColor: "#fff",

    //Number - The width of each segment stroke
    segmentStrokeWidth: 2,

    //Number - The percentage of the chart that we cut out of the middle
    percentageInnerCutout: 50, // This is 0 for Pie charts

    //Number - Amount of animation steps
    animationSteps: 100,

    //String - Animation easing effect
    animationEasing: "easeOutBounce",

    //Boolean - Whether we animate the rotation of the Doughnut
    animateRotate: true,

    //Boolean - Whether we animate scaling the Doughnut from the centre
    animateScale: true,

    //Boolean - whether to make the chart responsive to window resizing
    responsive: true,

    // Boolean - whether to maintain the starting aspect ratio or not when responsive, if set to false, will take up entire container
    maintainAspectRatio: false,

    //String - A legend template
    legendTemplate: "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<segments.length; i++){%><li><span style=\"background-color:<%=segments[i].fillColor%>\"></span><%if(segments[i].label){%><%=segments[i].label%><%}%></li><%}%></ul>"
  };

  //Create pie or doughnut chart for services_all_locations
  // Get context with jQuery - using jQuery's .get() method.
  var servicesAll = $("#servicespie").get(0).getContext("2d");
  var servicesChart = new Chart(servicesAll);
  
  //Create doughnut pie chart
  servicesChart.Doughnut(pieChartDataAllLocations, pieOptions);


  //Create pie or doughnut chart for services_per_location
  var i=0;
  for(var location_name in pieChartDataPerLocation){
  	var servicesLocations = $(".locationspie").get(i);
	var context = servicesLocations.getContext("2d");
  	var servicesCharts = new Chart(context);
	var locationData = pieChartDataPerLocation[location_name];
	servicesCharts.Doughnut(locationData, pieOptions);
	i++;
  }

  //-----------------
  //- END PIE CHART -
  //-----------------


}

);
