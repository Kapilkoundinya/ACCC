/*//Paper in Printer Code
var paperLevel = Document.getElementByClassName('paper')
paperLevel.innerHTML = paperatLabs(paper)


//Function that calculates paper at each lab locations
function paperatLabs(paper){
	for(var labname in paper){
		if(labname == 'SEL' && (labname.length>index && labname[index]!= NULL) ){
			paperinprinter = paperLevelCalculator(labname);
			return paperinprinter
		}
		if(labname == 'MLC' && (labname.length>index && labname[index]!= NULL) ){
			paperinprinter = paperLevelCalculator(labname); 		
			return paperinprinter
		}
		if(labname == 'CSTOP_BSB' && (labname.length>index && labname[index]!= NULL) ){
			paperinprinter = paperLevelCalculator(labname);
			return paperinprinter
		}
		if(labname == 'CSTOP_W' && (labname.length>index && labname[index]!= NULL) ){
			paperinprinter = paperLevelCalculator(labname);
			return paperinprinter
		}
		if(labname == 'SRC' && (labname.length>index && labname[index]!= NULL) ){
			paperinprinter = paperLevelCalculator(labname);
			return paperinprinter
		}
		if(labname == 'SES' && (labname.length>index && labname[index]!= NULL) ){
			paperinprinter = paperLevelCalculator(labname);
			return paperinprinter
		}
		if(labname == 'GH' && (labname.length>index && labname[index]!= NULL) ){
			paperinprinter = paperLevelCalculator(labname);
			return paperinprinter
		}
		if(labname == 'Library' && (labname.length>index && labname[index]!= NULL) ){
			paperinprinter = paperLevelCalculator(labname); 	
			return paperinprinter
		}
		if(labname == 'SCE' && (labname.length>index && labname[index]!= NULL) ){
			paperinprinter = paperLevelCalculator(labname); 
			return paperinprinter
		}
	
	}
}

function paperLevelCalculator(labname){
	for(var consultants in labname){
		var paper_old = consultants['created_at'];
		var paper_new = paper_old;
		if(paper_new > paper_old)
			paper_new = paper_new;
		else
			paper_new = paper_old;
	}
	return paper_new
}*/

/*
//Other Related Statistics for AdminView
var topTrendLocation = document.getElementById('topTrendingLocation');
var countPerLocationInitial = 0;
var countPerLocationFinal = 0;
var locationNameFinal = null;

topTrendLocation.innerHTML = topTrendingLocation(statisticsData);

//Function to find the Top trending Location (based on the tasks completed)
function  topTrendingLocation(statisticsData){
	for(var locationTrend in statisticsData){
		var locationNameInitial = locationTrend;
		var locationvalue = statisticsData[locationTrend];	
		var size = locationvalue.length;
		if(size >0){
			for(var taskscompleted in locationvalue){	
				console.log(taskcompleted);
				countPerConsultant = statisticsData.locationTrend.taskscompleted.taskcompleted;
				countPerLocationIntial = countPerLocationInitial + countPerConsultant;
			}
		}
		else
			countPerLocationInital = 0;
	
		if(countPerLocationFinal < countPerLocationInitial){
			countPerLocationFinal = countPerLocationInitial;
			locationNameFinal = locationNameInitial;
		}
		else{
			countPerLocationFinal = countPerLocationFinal;
			locationNameFinal = locationNameFinal;
		}
	}
	return locationNameFinal
}
*/
















