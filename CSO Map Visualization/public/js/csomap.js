// Javascript Section of CSO Map 


	//Shifts mapped to corresponding locations
    var location_shift_check = {
        SEL_CSO:['lan','sel','csop','csow','development','csop_train/sel','cso_phones trainee','soft'],
        SEL_labs:['Extra Help','problem','cave_project','cso_project','cave_sc',,'cave'],
        SRC_remote_lab:['src'],
        SES_labs:['ses'],
        Daley_Library_helpdesk:['lib','lib_prt','ACCCess Daley Lib Trainee','acccess_lib'],
        SCW_Cstop:['C-Stop W','Extra Help C-Stop West','C-Stop West Trainee'],
        LHS:['acccess_lhs','ACCCess LHS Trainee'],
        BSB_Cstop:['bsb','C-Stop BSB','C-Stop BSB Trainee','Extra Help BSB L.C.'],
        GH_Remote_lab:['gh'],
        SEO_remote_labs:['mlc'],
        SCE_Remote_lab:['Extra Help ACCCess East','sce','ACCCess East Trainee','acccess_east'],
        BGRC:['oaa','admin','dev','itgc', 'grc'],
        Other_locations:['ssb','supply','orient_3','project','PEL','project2','lets','training','orient']
    };
	//declaring empty netid arrays
        var SEL_CSO_netid = [];
        var SEL_labs_netid = [];
        var SRC_remote_lab_netid = [];
        var SES_labs_netid = [];
        var Daley_Library_helpdesk_netid = [];
        var SCW_Cstop_netid = [];
        var LHS_netid = [];
        var BSB_Cstop_netid = [];
        var SEO_remote_labs_netid = [];
        var GH_Remote_lab_netid = [];
        var SCE_Remote_lab_netid = [];
        var BGRC_netid = [];
        var Other_locations_info = [];
	//ACCC Locations
    var ACCC_Locations = [
        ['SCE_Remote_lab',41.872804, -87.647613],
        ['GH_Remote_lab',41.872808, -87.649400],
        ['Daley_Library_helpdesk',41.871389, -87.650373],
        ['SEO_remote_labs',41.870898, -87.650141],
        ['SEL_CSO',41.870493, -87.647743],
        ['SEL_labs',41.870311, -87.647746],
        ['SES_labs',41.869218, -87.648847],
        ['SRC_remote_lab',41.873058, -87.647390],
        ['BSB_Cstop',41.873919, -87.652113],
        ['SCW_Cstop',41.870681, -87.674143],
        ['LHS',41.871788, -87.670994],
        ['BGRC',41.869423, -87.675397]
    ];
	//Custom method to find the key (location)from the value(shift)
    location_shift_check.getKeyByValue = function( value ) {
        for( var prop in this ) {
            for( var arr in this[prop]){
                if( this.hasOwnProperty(prop) ) {
                    if( this[prop][arr] === value )
                        return prop;
                }
            }
        }
    }
	//Input JSON data from AJAX request(from the server)
    $.getJSON(location.href.replace("csomap", "/REST/v1/timeclock/mapdata"), function(data) {
        var consultant_info = data;
    })
    .done(function(consultant_info){
		console.log("JSON Loaded.");
        getNetids_otherlocations(consultant_info);
		initialize();
    });
	//Check JSON data to compare the current location with all the locations in the database
	//Create an array to store all the consultants working at the current location.
    function getNetids_otherlocations(consultant_info){
        for ( var i=0; i<consultant_info.length; i++) {
            var consultant_shift = consultant_info[i].locations[0];
            var consultant_netid = consultant_info[i].netid;
			//get location from shift
            var consultant_location = location_shift_check.getKeyByValue(consultant_shift);       
			//sort netids based on location
            if (consultant_location == 'SEL_CSO') {
                SEL_CSO_netid.push(consultant_netid);
            }
            if (consultant_location == 'SEL_labs') {
                SEL_labs_netid.push(consultant_netid);
            }
            if (consultant_location == 'SRC_remote_lab') {
                SRC_remote_lab_netid.push(consultant_netid);
            }
            if (consultant_location == 'SES_labs') {
                SES_labs_netid.push(consultant_netid);
            }
            if (consultant_location == 'Daley_Library_helpdesk') {
                Daley_Library_helpdesk_netid.push(consultant_netid);
            }
			if (consultant_location == 'SCW_Cstop') {
                SCW_Cstop_netid.push(consultant_netid);
            }
			if (consultant_location == 'LHS') {
                LHS_netid.push(consultant_netid);
			}
            if (consultant_location == 'BSB_Cstop') {
                BSB_Cstop_netid.push(consultant_netid);
             }
            if (consultant_location == 'GH_Remote_lab') {
                GH_Remote_lab_netid.push(consultant_netid);
            }
            if (consultant_location == 'SEO_remote_labs') {
                SEO_remote_labs_netid.push(consultant_netid);
            }
            if (consultant_location == 'SCE_Remote_lab') {
                SCE_Remote_lab_netid.push(consultant_netid);
            }
            if (consultant_location == 'BGRC') {
                BGRC_netid.push(consultant_netid);
            }
			if (consultant_location == 'Other_locations') {
                var temp = [consultant_netid, consultant_shift];
                Other_locations_info.push(temp);
            }
        }
        //Table displaying consultants netid and shift (other locations)
        var Other_locations_display = document.getElementById("OtherLocationTable");
        Other_locations_display.innerHTML = makeTableHTML2D(Other_locations_info);
        var header = Other_locations_display.createTHead();
        var row = header.insertRow(0);
        var cell1 = row.insertCell(0);
        var cell2 = row.insertCell(1);
        cell1.innerHTML = "<b>NetID</b>";
        cell2.innerHTML = "<b>Shift</b>";
        //Function that converts 2D arrays in to HTML Tables
		function makeTableHTML2D(myArray) {
            var result = "<table border=1>";
            for(var i=0; i<myArray.length; i++) {
                result += "<tr>";
                for(var j=0; j<myArray[i].length; j++){
                    result += "<td>"+myArray[i][j]+"</td>";
                }
                result += "</tr>";
				}
                result += "</table>";
                return result;

            }
        
		}
        //Map creation and initialization
		function initialize() {
			//Layout options of custom map
            var featureOpts = [
                {
                    stylers: [
                        { hue: '#890000' },
                        { visibility: 'simplified' },
                        { gamma: 0.5 },
                        { weight: 0.5 }
                    ]
                },
                {
                    elementType: 'labels',
					stylers: [
                        { visibility: 'off' }
                    ]
                },
                {
                    featureType: 'water',
                    stylers: [
                        { color: '#890000' }
                    ]
                }
            ];
            //custom type
            var MY_MAPTYPE_ID = 'custom_style';
            //Map object
            var map = new google.maps.Map(document.getElementById('map'), {
                zoom: 15,
                center: new google.maps.LatLng(41.872199, -87.656974),
                mapTypeControlOptions: { mapTypeIds: [google.maps.MapTypeId.ROADMAP, MY_MAPTYPE_ID]},
                mapTypeId: MY_MAPTYPE_ID
            });
            //Style Options
            var styledMapOptions = {
                name: 'Custom Style'
            };
            //Info Window 
            var infowindow_main_locations = new google.maps.InfoWindow();
            var marker, i;
			var markerBounds = new google.maps.LatLngBounds();	
			//Function that converts 1D arrays in to HTML Tables
			function makeTableHTML1D(myArray) {
				var result = "<table  class=\"table table-bordered\" border=1>";
				for(var i=0; i<myArray.length; i++) {
					result += "<tr>"+"<td>"+myArray[i]+"</td>"+"</tr>";
				}
				result += "</table>";				
				return result;
			}			
			//Returns consultants netid working at each location 
            function info_content(a) {
                if (a == 'SEL_CSO') {
                    return SEL_CSO_netid;
                }
                if (a == 'SEL_labs') {
                    return SEL_labs_netid;
                }
                if (a == 'SRC_remote_lab') {
                    return SRC_remote_lab_netid;
				}
                if (a == 'SES_labs') {
                     return SES_labs_netid;
                }
                if (a == 'Daley_Library_helpdesk') {
                    return Daley_Library_helpdesk_netid;
                }
                if (a == 'SCW_Cstop') {
                    return SCW_Cstop_netid;
                }
                if (a == 'LHS') {
                    return LHS_netid;
                }
                if (a == 'BSB_Cstop') {
                    return BSB_Cstop_netid;
                }
                if (a == 'GH_Remote_lab') {
                    return GH_Remote_lab_netid;
                }
                if (a == 'SEO_remote_labs') {
                    return SEO_remote_labs_netid;
                }
                if (a == 'SCE_Remote_lab') {
                    return SCE_Remote_lab_netid;
                }
                if (a == 'BGRC') {
                    return BGRC_netid;
                }
            }
            //Marker for each location
            for (i = 0; i < ACCC_Locations.length; i++) {                   
				var marker_position = new google.maps.LatLng(ACCC_Locations[i][1], ACCC_Locations[i][2]),
					//Marker
					marker = new google.maps.Marker({
                       	position: marker_position,
						map: map,
						animation:google.maps.Animation.DROP
					});
					var locationName = ACCC_Locations[i][0]
					var a = info_content(locationName);
                    if(typeof a == "undefined" || a == null || a.length <= 0){
						marker.setAnimation(google.maps.Animation.BOUNCE);
					}	
				//Display an info window when the user clicks the marker
                google.maps.event.addListener(marker, 'click', (function(marker, i) {
                    return function() {
						var locationName = ACCC_Locations[i][0]
						var a = info_content(locationName);
						var node = makeTableHTML1D(a);
						var contentstring = '<h4 align="center">'+locationName+'</h4>'+node;
						infowindow_main_locations.setContent(contentstring);
						infowindow_main_locations.open(map, marker);
                    }
                })(marker, i));
				markerBounds.extend(marker_position);
            }			
			//Display the custom map
			var customMapType = new google.maps.StyledMapType(featureOpts, styledMapOptions);
            map.mapTypes.set(MY_MAPTYPE_ID, customMapType);			
			//Auto-zoom based on screen bounds
			map.fitBounds(markerBounds);
		}				
		//Load the google maps api when the webpage is loaded
        google.maps.event.addDomListener(window, 'load', initialize);
		//Resize the google maps api when the screen size changes
		google.maps.event.addDomListener(window,'resize',initialize);
