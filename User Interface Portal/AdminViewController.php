<?php

namespace App\Http\Controllers;

use DB;
use App\Http\Controllers\Controller;

class AdminViewController extends Controller
{
    /**
     * Show a list of all of the application's users.
     *
     * @return Response
     */

	//Main function 
	public function adminview()
	{
		//Joint Table with input data(netid,tasks,locations)
		$AggregatedLocations = DB::table('main')
				->leftJoin('labwalk','main.id','=','labwalk.id')
				->leftJoin('printing','main.id','=','printing.id')
				->get();

		//Other Services Table
		$otherservices = DB::table('main')
				->leftJoin('other','main.id','=','other.id')
				->get();

		//Table to get the most recent paper level grouped by lab location
		$paperLevel = DB::table('main') 
				->leftJoin('printing','main.id','=','printing.id')	
				->select('main.id',DB::raw('max(usp_main.created_at) as recent_paper_level'),'main.netid','printing.*')
				->groupBy('lab')
				->get();

		//Sort data based on locations
		list($SCE,$CSTOP_BSB,$CSTOP_W,$GH,$Library,$MLC,$SEL,$SRC,$SES) = $this->locationClassifier($AggregatedLocations); 
		$LabLocations = ['SCE'=>$SCE,
				'CSTOP BSB'=>$CSTOP_BSB,
				'CSTOP W'=>$CSTOP_W,
				'GH'=>$GH,
				'Library'=>$Library,
				'MLC'=>$MLC,
				'SEL'=>$SEL,
				'SRC'=>$SRC,
				'SES'=>$SES,
				//'NO_LAB'=>$NO_LAB
				];

		//Append the taskscompleted element to the LabLocations array
		foreach($LabLocations as $location_key => $location_value){
			foreach($location_value as $consultant_key => $consultant_value){
				if ($location_key == 'SCE' || $location_key == 'SEL' ){
					$taskscompleted = $this->taskcount($consultant_value,7);
					$LabLocations[$location_key][$consultant_key]['taskcompleted'] = $taskscompleted[0];
					$LabLocations[$location_key][$consultant_key]['taskpercentage'] = $taskscompleted[1];
				}
				else{
					$taskscompleted = $this->taskcount($consultant_value,6);
					$LabLocations[$location_key][$consultant_key]['taskpercentage'] = $taskscompleted[1];
					$LabLocations[$location_key][$consultant_key]['taskcompleted'] = $taskscompleted[0];
					
				}	
			}
		}

		//Array that specifies services for all the locations (combined)
		$services_all_locations = $this->servicesonWhole($AggregatedLocations);
		//Array that specifies services offered at a given location
		$services_per_location = $this->servicePerLocation($LabLocations);

		//PieChart data at each Location and encoded in JSON format
		$pieChartData =[];
		foreach($services_per_location as $location_name => $location_service){
			if(count($location_service) > 0)
			$pieChartData[$location_name] = $this->createPieData($location_service);
		}
		$pieChartData = json_encode($pieChartData);

		//PieChart data for all location, encoded in JSON format
		$pieChartData_all_locations = json_encode($this->createPieData($services_all_locations));

		//Other Statisctics like toptrending, most active consultant, lab performance ratio, etc.,
		$statistics = $this->statistics($services_all_locations,$LabLocations);
	
		return view('AdminView.adminview',[	'services_all_locations'=>$services_all_locations,
							'services_per_location'=>$services_per_location,
							'statistics'=>$statistics,
							'pieChartDataAllLocations'=>$pieChartData_all_locations,
							'lab_locations'=>$LabLocations,
							'paperLevel'=>$paperLevel,
							'pieChartData'=>$pieChartData
						]);

	}

	//Task completion calculator function
	public function taskcount($name,$total){
		$alarm = $name['alarm'];
		$supply = $name['supply'];
		$clean = $name['clean'];
		$logged_off = $name['logged_off'];
		$secured = $name['secured'];
		$schedule = $name['schedule'];
		$special = $name['special'];
		$tasks = [$alarm,$supply,$clean,$logged_off,$secured,$schedule,$special];
		$count_task = array_sum($tasks);
		$percentage_count = round(($count_task / $total)*100);
		$count = [$count_task, $percentage_count];
		return $count;
	}


	//Function that quantifies services for all locations
	public function servicesonWhole($AggregatedLocations){
		//Intialize the services
		$printing_count = 0;
		$computerlabs_count = 0;
		$laptops_and_equipment_lending_count = 0;
		$classroom_support_count = 0;
		$other_services_count = 0;

		//Services served by all locations of ACCC
		foreach($AggregatedLocations as $serviceCount){
			$alarm = $serviceCount->alarm;
			$supply = $serviceCount->supply;
			$clean = $serviceCount->clean;
			$logged_off = $serviceCount->logged_off;
			$secured = $serviceCount->secured;
			$schedule = $serviceCount->schedule;
			$special = $serviceCount->special;
			$paper_fill = $serviceCount->paper_fill;

			if($paper_fill > 0)
			$paper_fill = 1;
			else
			$paper_fill = 0;

			$printing_count = $printing_count+$supply+$paper_fill;
			$computerlabs_count = $computerlabs_count+$logged_off+$clean+$special+$alarm;
			$laptops_and_equipment_lending_count = $laptops_and_equipment_lending_count+$secured;
			$classroom_support_count = $classroom_support_count+$schedule;
			$other_services_count = $other_services_count;
		}

		$services_all_locations = [	'Printing' => $printing_count,
						'Computer Labs' => $computerlabs_count,
						'Laptops and Equipment Lending' => $laptops_and_equipment_lending_count,
						'Classroom Support' => $classroom_support_count,
						'Other Services' => $other_services_count
						];

		return $services_all_locations; 
	}


	//Function that quantifies services at each location - only the locations which have consultants are considered
	public function servicePerLocation($LabLocations){
		
		//Initialize the services
		$printing_count = 0;
		$computerlabs_count = 0;
		$laptops_and_equipment_lending_count = 0;
		$classroom_support_count = 0;
		$other_services_count = 0;
		$LabServices=[];

		//Services served by each location
		foreach($LabLocations as $location_key => $location_value){
			if(count($location_value)> 0){
				foreach($location_value as  $serviceCount){

					$alarm = $serviceCount['alarm'];
					$supply = $serviceCount['supply'];
					$clean = $serviceCount['clean'];
					$logged_off = $serviceCount['logged_off'];
					$secured = $serviceCount['secured'];
					$schedule = $serviceCount['schedule'];
					$special = $serviceCount['special'];
					$paper_fill = $serviceCount['paper_fill'];

					if($paper_fill > 0)
					$paper_fill = 1;
					else
					$paper_fill = 0;

					$printing_count = $printing_count+$supply+$paper_fill;
					$computerlabs_count = $computerlabs_count+$logged_off+$clean+$special+$alarm;
					$laptops_and_equipment_lending_count = $laptops_and_equipment_lending_count+$secured;
					$classroom_support_count = $classroom_support_count+$schedule;
					$other_services_count = $other_services_count;
				}

				$LabServices[$location_key] =[	'Printing' => $printing_count,
								'Computer Labs' => $computerlabs_count,
								'Laptops and Equipment Lending' => $laptops_and_equipment_lending_count,
								'Classroom Support' => $classroom_support_count,
								'Other Services' => $other_services_count
								];

				$printing_count = 0;
				$computerlabs_count = 0;
				$laptops_and_equipment_lending_count = 0;
				$classroom_support_count = 0;
				$other_services_count = 0;

			}
		}

		return $LabServices;

	}


	//Function that groups the form data via Locations 
	public function locationClassifier($AggregatedLocations){
		
		//Empty location arrays
		$SCE = [];
		$CSTOP_BSB = [];
		$CSTOP_W = [];
		$GH = [];
		$Library = [];
		$MLC = [];
		$SEL = [];
		$SRC = [];
		$SES = [];
		//$NO_LAB = [];

		//Loop that creates an array for each location with all the consultans and their respective tasks
		foreach($AggregatedLocations as $value){

			//locations
			$location = $value->lab_main;
			$lab = $value->lab;

			//group the data based on locations
			if($location == 'SCE'|| $lab == 'SCE'){
				$SCE[] = get_object_vars($value);
			}
			if($location == 'CSTOP BSB'|| $lab == 'CSTOP BSB'){
				$CSTOP_BSB[] = get_object_vars($value);
			}
			if($location == 'CSTOP W'|| $lab == 'CSTOP W'){
				$CSTOP_W[] = get_object_vars($value);
			}
			if($location == 'GH'|| $lab == 'GH'){
				$GH[] = get_object_vars($value);
			}
			if($location == 'Library'|| $lab == 'Library'){
				$Library[] = get_object_vars($value);
			}
			if($location == 'MLC'|| $lab == 'MLC'){
				$MLC[] = get_object_vars($value);
			}
			if($location == 'SEL'|| $lab == 'SEL'){
				$SEL[] = get_object_vars($value);
			}
			if($location == 'SRC'|| $lab == 'SRC'){
				$SRC[] = get_object_vars($value);
			}
			if($location == 'SES'|| $lab == 'SES'){
				$SES[] = get_object_vars($value);
			}
			/*if($location == NULL && $lab == NULL){
				$NO_LAB[] = get_object_vars($value);
			}*/
		}

		return array($SCE,$CSTOP_BSB,$CSTOP_W,$GH,$Library,$MLC,$SEL,$SRC,$SES);
	}


	//Function that creates pie chart data
	private function createPieData($pieChartData) {
		$colors = array('#F2C249', '#E6772E', '#4DB3B3', '#E64A45', '#3D4C53', '#72CBDB',
				'#55134E', '#A0596B', '#FEC343', '#EF7351','#f56954', '#00a65a',
				'#f39c12','#00c0ef','#3c8dbc', '#d2d6de');
		$output = array();
		$count = 0;
		foreach ($pieChartData as $key =>  $value) {
			$output[] = array('value' => $value,
					'color' => ($colors[($count)%(count($colors))]),
					'highlight' => ($colors[($count)%(count($colors))]),
					'label' => $key);
			$count++;
		}

		return $output;
	}

	//Function that performs other statistics
	private function statistics($services_all_locations,$LabLocations){
		
		//Top trending service
		$topTrendingService = $this->topTrending($services_all_locations);
		
		//Top trending location
		$topTrendingLocation = $this->labPerformanceStatistics($LabLocations)[0];
		
		//Location performance score
		$locationPerformancePercentage = $this->labPerformanceStatistics($LabLocations)[2];
		$locationPerformanceCount = $this->labPerformanceStatistics($LabLocations)[1];
		
		return array($topTrendingService,$topTrendingLocation,$locationPerformanceCount,$locationPerformancePercentage);
	}
		
	//Function to calculate the top trending values (get a key from the value where the input array should be 1D)
	private function topTrending($data){
		while($topService = current($data)){
			if($topService == max($data))
				$topTrending = key($data);
			next($data);
		}

		return ($topTrending);
	}	

	//Function to calculate the Performance and other statistics of Remote Labs
	private function labPerformanceStatistics($LabLocations){
		
		//Location performance count and percentage
		foreach($LabLocations as $locationName => $locationData){
			$finalTaskCount = 0;
			$consultantsCount = count($locationData);
			$consultantsCountPerLocation[$locationName] = $consultantsCount;
			if(count($locationData)>0){
				foreach($locationData as $taskCount){
					$initialTaskCount = $taskCount['taskcompleted'];
					$finalTaskCount = $initialTaskCount + $finalTaskCount;
				}
				$locationPerformanceCount[$locationName] = $finalTaskCount;
			
			if( ($locationName == 'SCE') || ($locationName == 'SEL') )
				$locationPerformancePercentage[$locationName] = round(($locationPerformanceCount[$locationName]*100)/($consultantsCountPerLocation[$locationName]*7));
			else
				$locationPerformancePercentage[$locationName] = round(($locationPerformanceCount[$locationName]*100)/($consultantsCountPerLocation[$locationName]*6));
			}
		}

		$topTrendingLocation = $this->topTrending($locationPerformanceCount);
		return array($topTrendingLocation,$locationPerformanceCount,$locationPerformancePercentage);
	}


}
