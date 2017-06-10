var casper = require("casper").create({
verbose: true,
viewportSize: {
        width: 1920,
        height: 1080
    },
// logLevel: "debug"
});
var x = require('casper').selectXPath;

var currentRoute = ~~casper.cli.get(0); // = 13; // limit - 112
var RoutsAmount = ~~casper.cli.get(0); // = 13;
// currentRoute = RoutsAmount = this.cli.get(0); //20;
var currentBusStop = 1;
var busStopsAmount = 0;
var StopDescription = '';

casper.start('http://tgt72.ru/schedule/');

casper.then(function(){
	casper.wait(1000);
});

casper.then(function(){
	this.click(x('//span[@title="Маршрут"]'));
	this.then(function(){
		this.waitForSelector(x('//li[contains(@class,"select2-results__option")]'));	
	});
});

// casper.then(function(){
// 	RoutsAmount = ~~this.cli.get(0) || this.evaluate(function(){return document.getElementsByClassName("select2-results__option").length;});
// });

casper.then(function startParsing(){
	this.click(x('//span[@title="Маршрут"]'));
	this.then(function(){
		parse.call(this, currentRoute, RoutsAmount);
	});
});

function parse(currentRoute, RoutsAmount){
	if (currentRoute <= RoutsAmount){
		this.then(function clickRoutesField(){
			this.click(x('//span[@title="Маршрут"]'));
		});
		this.then(function waitForRoutesShow(){
			this.waitForSelector(x('//li[contains(@class,"select2-results__option")]'));	
		});
		this.then(function routeNumClick(){
			this.click(x('//li[contains(@class,"select2-results__option")][' + currentRoute + ']'));
		});		
		this.then(function clickStopField(){
			this.click(x('//span[@title="Остановка"]'));
		});	
		this.then(function waitingForStopsShow(){
			this.wait(1000);
		});			
		this.then(function(){
			busStopsAmount = this.evaluate(function(){return document.getElementsByClassName("select2-results__option").length;});
		});
		this.then(function clickRoutesField(){
			this.click(x('//span[@title="Маршрут"]'));
		});
		this.then(function Loop2(){
			showtime.call(this, currentBusStop, busStopsAmount);
		});
		this.then(function increaseRouteNumber(){
			currentRoute++;
		});
		this.then(function callLoop1(){
			parse.call(this, currentRoute, RoutsAmount)
		});
	}
};

function showtime(currentBusStop){
	if (currentBusStop <= busStopsAmount){
		this.then(function clickStopField(){
			this.click(x('//span[@title="Остановка"]'));
		});	
		this.then(function waitingForStopsShow(){
			this.wait(1000);
		});			
		this.then(function(){
			StopDescription = this.evaluate(function(currentBusStop){
				return document.getElementsByClassName('select2-result-checkpoints__description')[currentBusStop-1].innerHTML;
			}, currentBusStop);
		});
		this.then(function stopLinkClick(){
			this.click(x('//li[contains(@class,"select2-results__option")][' + currentBusStop + ']'));
		});
		this.then(function ScheduleClick(){
			this.click(x('//input[@id="get-schedules"]'));
		});
		this.then(function waitForETAshow(){
			this.wait(1000);
		});
		this.then(function printAll(){
			var output = this.evaluate(getResult, StopDescription);
			require('utils').dump(output);
		});
		this.then(function increaseStopNumber(){
			currentBusStop++;
		});	
		this.then(function callLoop2(){
			showtime.call(this, currentBusStop, busStopsAmount);
		});
	}
};

function getResult(StopDescription){
	var results = [];
	var busStop = document.getElementById("select2-checkpoints-search-container").innerText.substr(1);
	var route = document.getElementById("select2-routes-search-container").innerText.substr(1);
	var timesParents = document.getElementsByClassName("schedule-results-container-row-times");
	var times = [];
	for(var i = 0; i < timesParents.length; i++){
		var thisTimes = timesParents[i].children;
		for(var j = 0; j < thisTimes.length; j++){
			times.push(thisTimes[j].innerText);
		}
	}
	results.push({weekdaysETA: times, StopDescription: StopDescription, busStopName: busStop, routeNumber: route});
	return results; 
};

casper.run();