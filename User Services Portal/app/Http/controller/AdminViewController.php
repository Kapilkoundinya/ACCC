<?php

namespace App\Http\Controllers;

use DB;
use App\Http\Controllers\Controller;
use Carbon\Carbon;

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
		//Retrive data from the database
		$aggregatedLocations = $this->getDataFromDatabase()[0];

		//Paper Level in each Lab
		$paperLevel = $this->getDataFromDatabase()[1];
	
		//Date and time information of the data
		$dateTime = $this->getDataFromDatabase()[2];

		//Sort data based on locations
		$labLocations = $this->locationClassifier($aggregatedLocations);
	
		//Append the taskscompleted element to the labLocations array
		$labLocations = $this->tasksCompletedAppend($labLocations);
		
		//Array that specifies services for all the locations (Aggregated)
		$servicesAllLocations = $this->servicesonWhole($aggregatedLocations);

		//Array that specifies services offered at each location
		$servicesPerLocation = $this->servicePerLocation($labLocations);
	
		//PieChart data at each Location and encoded in JSON format (can be a seperate function!)
		$pieChartDataPerLocation = json_encode($this->pieChartPerLocation($servicesPerLocation));

		//PieChart data for all location, encoded in JSON format
		$pieChartDataAllLocations = json_encode($this->createPieData($servicesAllLocations));

		//Other Statisctics like toptrending, most active consultant, lab performance ratio, etc.,
		$statistics = $this->statistics($servicesAllLocations,$labLocations);
	
		//dd($aggregatedLocations);
		//dd($labLocations);
		//dd($$servicesAllLocations);
		//dd($servicesPerLocation);
		//dd($statistics);
		//dd($paperLevel);
		//dd($pieChartDataPerLocation);
		//dd($pieChartDataAllLocations);

		return view('AdminView.adminview',[	'services_all_locations'=>$servicesAllLocations,
							'services_per_location'=>$servicesPerLocation,
							'statistics'=>$statistics,
							'pieChartDataAllLocations'=>$pieChartDataAllLocations,
							'lab_locations'=>$labLocations,
							'paperLevel'=>$paperLevel,
							'pieChartDataPerLocation'=>$pieChartDataPerLocation,
							'dateTime'=>$dateTime
						]);

	}


	//Function that retrives required data from the database
	private function getDataFromDatabase(){
		
		//Carbon objects for selecting custom dates
		$startIni  = (isset($_GET['start'])) ? $_GET['start'] : null;
		$endIni = (isset($_GET['end'])) ? $_GET['end'] : null;
		$startDate = (isset($startIni)? new Carbon($startIni) : new Carbon());
		$startDate->startOfDay();
		$endDate = (isset($startIni)? new Carbon($endIni) : new Carbon());
		$endDate->endOfDay();
		$dateTime = [$startIni,$endIni,$startDate,$endDate];

		//Joint Table with input data(netid,tasks,locations)
		$aggregatedLocations = DB::table('main')
				->leftJoin('labwalk','main.id','=','labwalk.id')
				->leftJoin('printing','main.id','=','printing.id')
				->whereBetween('created_at',[$startDate->toDateTimeString(), $endDate->toDateTimeString()])	
				->get();

		//Table to get the most recent paper level grouped by lab location
		$paperLevel = DB::table('main') 
				->leftJoin('printing','main.id','=','printing.id')	
				->select('main.id',DB::raw('max(usp_main.created_at) as recent_paper_level'),'main.netid','printing.*')
				->where('paper_fill','>=','0')
				->groupBy('lab')
				->get();

		return array($aggregatedLocations,$paperLevel,$dateTime);
	}	


	//Function that groups the form data via Locations 
	private function locationClassifier($aggregatedLocations){	
		
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

		//Loop that creates an array for each location with all the consultans and therir respective tasks
		foreach($aggregatedLocations as $value){

			//locations
			$location = $value->lab_main;
			$lab = $value->lab;

			//group data based on locations
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
		}

		$labLocations = ['SCE'=>$SCE,
				'CSTOP BSB'=>$CSTOP_BSB,
				'CSTOP W'=>$CSTOP_W,
				'GH'=>$GH,
				'Library'=>$Library,
				'MLC'=>$MLC,
				'SEL'=>$SEL,
				'SRC'=>$SRC,
				'SES'=>$SES
				];

		return $labLocations;
	}

	
	//Function that appendes taskCompleted element to labLocations array
	private function tasksCompletedAppend($labLocations){
		
		foreach($labLocations as $locationKey => $locationValue){
			foreach($locationValue as $consultantKey => $consultantValue){
				if ($locationKey == 'SCE' || $locationKey == 'SEL' ){
					$tasksCompleted = $this->taskcount($consultantValue,7);
					$labLocations[$locationKey][$consultantKey]['taskcompleted'] = $tasksCompleted[0];
					$labLocations[$locationKey][$consultantKey]['taskpercentage'] = $tasksCompleted[1];
				}
				else{
					$tasksCompleted = $this->taskcount($consultantValue,6);
					$labLocations[$locationKey][$consultantKey]['taskpercentage'] = $tasksCompleted[1];
					$labLocations[$locationKey][$consultantKey]['taskcompleted'] = $tasksCompleted[0];
				}	
			}
		}

		return $labLocations;
	}


	//Function that quantifies services for all locations
	private function servicesonWhole($aggregatedLocations){
		
		//Intialize the services
		$printingCount = 0;
		$computerLabsCount = 0;
		$laptopsAndEquipmentLendingCount = 0;
		$classroomSupportCount = 0;
		$otherServicesCount = 0;

		//Services served by all locations of ACCC
		//check for empty array
		if(!empty($aggregatedLocations)){
			foreach($aggregatedLocations as $serviceCount){
				$alarm = $serviceCount->alarm;
				$supply = $serviceCount->supply;
				$clean = $serviceCount->clean;
				$loggedOff = $serviceCount->logged_off;
				$secured = $serviceCount->secured;
				$schedule = $serviceCount->schedule;
				$special = $serviceCount->special;
				$paperFill = $serviceCount->paper_fill;

			if($paperFill > 0)
					$paperFill = 1;
				else
					$paperFill = 0;

				$printingCount = $printingCount + $supply + $paperFill;
				$computerLabsCount = $computerLabsCount + $loggedOff + $clean + $special + $alarm;
				$laptopsAndEquipmentLendingCount = $laptopsAndEquipmentLendingCount + $secured;
				$classroomSupportCount = $classroomSupportCount + $schedule;
				$otherServicesCount = $otherServicesCount;
			}

			$servicesAllLocations = [	'Printing' => $printingCount,
						'Computer Labs' => $computerLabsCount,
						'Laptops and Equipment Lending' => $laptopsAndEquipmentLendingCount,
						'Classroom Support' => $classroomSupportCount,
						'Other Services' => $otherServicesCount
						];
		}
		else
			$servicesAllLocations = [];

		return $servicesAllLocations; 
	}


	//Function that quantifies services at each location - only the locations which have consultants are considered
	private function servicePerLocation($labLocations){		
		
		//Initialize the services
		$printingCount = 0;
		$computerLabsCount = 0;
		$laptopsAndEquipmentLendingCount = 0;
		$classroomSupportCount = 0;
		$otherServicesCount = 0;
		$labServices=[];

		//Services served at each locationi
		//Check for empty array(LabLocations)
		if(!empty($labLocations)){
			foreach($labLocations as $locationKey => $locationValue){				
				//Check for empty array(location_value)
				if(count($locationValue)> 0){
					foreach($locationValue as  $serviceCount){

						$alarm = $serviceCount['alarm'];
						$supply = $serviceCount['supply'];
						$clean = $serviceCount['clean'];
						$loggedOff = $serviceCount['logged_off'];
						$secured = $serviceCount['secured'];
						$schedule = $serviceCount['schedule'];
						$special = $serviceCount['special'];
						$paperFill = $serviceCount['paper_fill'];

						if($paperFill > 0)
						$paperFill = 1;
						else
						$paperFill = 0;
	
						$printingCount = $printingCount + $supply + $paperFill;
						$computerLabsCount = $computerLabsCount + $loggedOff + $clean + $special + $alarm;
						$laptopsAndEquipmentLendingCount = $laptopsAndEquipmentLendingCount + $secured;
						$classroomSupportCount = $classroomSupportCount + $schedule;
						$otherServicesCount = $otherServicesCount;
					}

					$labServices[$locationKey] =[	'Printing' => $printingCount,
									'Computer Labs' => $computerLabsCount,
									'Laptops and Equipment Lending' => $laptopsAndEquipmentLendingCount,
									'Classroom Support' => $classroomSupportCount,
									'Other Services' => $otherServicesCount
									];

				$printingCount = 0;
				$computerLabsCount = 0;
				$laptopsAndEquipmentLendingCount = 0;
				$classroomSupportCount = 0;
				$otherServicesCount = 0;

				}
			}
		}

		return $labServices;
	}


	//Function that generates pieChatData for each lab
	private function pieChartPerLocation($servicesPerLocation){
		
		$pieChartDataPerLocation =[];
		foreach($servicesPerLocation as $locationName => $locationService){
			if(count($locationService) > 0)
			$pieChartDataPerLocation[$locationName] = $this->createPieData($locationService);
		}
		
		return $pieChartDataPerLocation;
	}


	//Function that calculates taskscompleted count and percentage 
	private function taskcount($name,$total){
		
		$special = $name['special'];
		$alarm = $name['alarm'];
		$supply = $name['supply'];
		$clean = $name['clean'];
		$loggedOff = $name['logged_off'];
		$secured = $name['secured'];
		$schedule = $name['schedule'];
		$tasks = [$alarm,$supply,$clean,$loggedOff,$secured,$schedule,$special];
		$countTask = array_sum($tasks);
		$percentageCount = round(($countTask / $total)*100);
		$count = [$countTask, $percentageCount];
		
		return $count;
	}


	//Function that creates pie chart data
	private function createPieData($pieChartDataPerLocation) {
		
		$colors = array('#F2C249', '#E6772E', '#4DB3B3', '#E64A45', '#3D4C53', '#72CBDB',
				'#55134E', '#A0596B', '#FEC343', '#EF7351','#f56954', '#00a65a',
				'#f39c12','#00c0ef','#3c8dbc', '#d2d6de');
		$output = array();
		$count = 0;
		foreach ($pieChartDataPerLocation as $key =>  $value) {
			$output[] = array('value' => $value,
					'color' => ($colors[($count)%(count($colors))]),
					'highlight' => ($colors[($count)%(count($colors))]),
					'label' => $key);
			$count++;
		}

		return $output;
	}

	
	//Function that performs other statistics
	private function statistics($servicesAllLocations,$labLocations){
		
		//Top trending service
		$topTrendingService = $this->topTrending($servicesAllLocations);
		
		//Top trending location
		$topTrendingLocation = $this->labPerformanceStatistics($labLocations)[0];
		
		//Location performance score
		$locationPerformancePercentage = $this->labPerformanceStatistics($labLocations)[2];
		$locationPerformanceCount = $this->labPerformanceStatistics($labLocations)[1];
		
		return array($topTrendingService,$topTrendingLocation,$locationPerformanceCount,$locationPerformancePercentage);
	}
	
		
	
	//Function to calculate the top trending values (get a key from the value where the input array should be 1D)
	private function topTrending($data){	
		
		//Check for empty array with out keys($data)
		if(!empty($data)){
			while($topService = current($data)){
				if($topService == max($data))
					$topTrending = key($data);
				next($data);
			}

			return($topTrending);
		}
		else{
			$topTrending = 'Not Enough Data is available';
		
			return($topTrending);			
		}
	}


	//Function to calculate the Performance and other statistics of Remote Labs
	private function labPerformanceStatistics($labLocations){
		
		//Location performance count and percentage
		//Check for empty array with keys($labLocations)
		if(!empty(array_filter($labLocations))){
			foreach($labLocations as $locationName => $locationData){
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
		else{
			$topTrendingLocation = 'Not Enough Data Avialable';
			$locationPerformanceCount = 'Not Enough Data Available';
			$locationPerformancePercentage = 'Not Enough Data Avialable';
		  
			return array($topTrendingLocation,$locationPerformanceCount,$locationPerformancePercentage);
		}	
			
	}


	/*//Function to sort LabLocations data based on consultant and then again based on shift
	private function($labLocations){
		
		foreach($labLocations as locationName=>locationData){
			foreach(locationData as consultantData){
				$consultantName




			}
		}

	



	}*/



}
