angular.module('eventDesc', ['ngSanitize'
    
])
    .config(function ($stateProvider) {
        $stateProvider
            .state('aperfectday.eventDesc', {
                url: 'jeu',
                views: {
                    //target the ui-view named 'content' in ROOT state (aperfectday)
                    'gauge@':{
                        controller: 'GaugeCtrl as gaugeCtrl',
                        templateUrl: 'app/gauge/gauge.tmpl.html'
                    },
                    'content@': {
                        controller: 'EventDescCtrl as eventDescCtrl',
                        templateUrl: 'app/eventDesc/eventDesc.tmpl.html'
                    },
                    'about@': {
                        controller: 'AboutCtrl as aboutCtrl',
                        templateUrl: 'app/about/about.tmpl.html'
                    },
                    'intro@': {
                        template: ''
                    }
                    
                },
                data : {
                    routeClass : 'eventDesc'
                },
				params: {
					endID : '',
                    summaries : [],
                    from: ''
				}
            })
        ;
    })


/*-----------------------------------------------------------------------------------
                                    Controllers
-----------------------------------------------------------------------------------*/
    .controller('EventDescCtrl', function EventDescCtrl($rootScope, $sce, $scope, $state, DataService, EventService, localStorageService, config, $stateParams, $analytics) {
    
        //********************* local variables ****************//
    
        var eventDescCtrl = this;
        var animDelay = 0; // use to wait for the animation to end before changing data
    	var jsonData;
        //************************ properties *******************//
    
        eventDescCtrl.variable = {};
    
        eventDescCtrl.eventData = '';
        eventDescCtrl.eventChoices = [];
        eventDescCtrl.event = {};    
        eventDescCtrl.map = {};
        eventDescCtrl.isInInsert = false;
        eventDescCtrl.endInsertTrigger = {};
        eventDescCtrl.summaries = new Array(); // tab of summaries, one for each gauge
        //eventDescCtrl.summaries['summaries1'] = new Array();
        //eventDescCtrl.summaries['summaries2'] = new Array();
        //eventDescCtrl.summaries['summaries3'] = new Array();
        eventDescCtrl.days = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'];
        eventDescCtrl.timeOut = 1000; // time of an animation 
        eventDescCtrl.day = eventDescCtrl.days[0];
        eventDescCtrl.cntDays = 1;
        eventDescCtrl.showTemporality = 0;
        eventDescCtrl.cntDays = 1;
        
    
        //************************ methods **********************//
    
        eventDescCtrl.triggerEvent = triggerEvent;
        eventDescCtrl.triggerPool = triggerPool;
        eventDescCtrl.buttonClick = buttonClick;
        eventDescCtrl.resetData = resetData;
    
        //************************** run ************************//
            
    
    //écouteur sur la variable nbEvent    
       $scope.$watch(function(){
            return EventService.getMap().get('var1001');
        }, function(newVal, oldVal, scope){
		   
		   
           if(newVal != oldVal){
               setTimeout(function(){updateDay();}, animDelay);
            }
           else if(newVal == 1) {
                updateDay();
            
				// GA tracking
			   	ga('set', 'dimension1', jsonData.Scenario.uid);
				$analytics.eventTrack('start', {category:'game', label:''});
				$rootScope.gameStartTime = new Date().getTime();
           }
       });


        $('#startScreen').show();
        setTimeout(function(){$('#startScreen').hide();}, eventDescCtrl.timeOut+200);
        $('#dayTransition').css("opacity", "0");
        $('#footerContainer').css("position","absolute");
        // $('#footerContainer').css("bottom","0");        
	    $("#gaugeContainer").css("display", "inherit");
        $(".background").show();
        EventService.getJSONData().then(
			function (data){
				jsonData = data;
				
				// init
				eventDescCtrl.showTemporality = jsonData.Scenario.showTemporality; 
				setEventData();
			}
		);
    
        function displayData(event){
			if(typeof event === 'undefined') {
				return;
			}
			
			// memorize events viewed in this game
			var eventsViewed = localStorageService.get('eventsViewed') !== null ? JSON.parse(localStorageService.get('eventsViewed')) : [];
			eventsViewed.push(event.id);
			localStorageService.set('eventsViewed', JSON.stringify(eventsViewed));
			
			
			// GA tracking
			ga('set', 'dimension1', jsonData.Scenario.uid);
			$analytics.eventTrack('event', {category:'game', label:event.id});
			
			
            // display the updated data
            eventDescCtrl.eventChoices = [];
            eventDescCtrl.eventData = event;
            $(".background").css("background", "url("+event.background+') center no-repeat');
            $(".background").css("background-size", "cover");
            
            localStorageService.set('event', event.id);
            printMap();
            
            event.Choices.forEach(function (item){
                eventDescCtrl.eventChoices.push(item);
            });
            
        }
    
        function containsObj (obj, tab){ // check if the summary already exist in summaries
            if(eventDescCtrl.summaries['summaries'+tab] && eventDescCtrl.summaries['summaries'+tab].length > 0){
                for (var i = 0; i < eventDescCtrl.summaries['summaries'+tab].length; i++) {
                    if (eventDescCtrl.summaries['summaries'+tab][i].id === obj.id) {
                        return true;
                    }
                }
                eventDescCtrl.summaries['summaries'+tab].push(obj);
                 var strSum = JSON.stringify(eventDescCtrl.summaries['summaries'+tab]);
                localStorageService.set('summaries'+tab, strSum);
            }
            else{
				eventDescCtrl.summaries['summaries'+tab] = new Array();
                eventDescCtrl.summaries['summaries'+tab].push(obj);
                 strSum = JSON.stringify(eventDescCtrl.summaries['summaries'+tab]);
                localStorageService.set('summaries'+tab, strSum);
            }

        }
    
        function updateDay() { // animate the changes : event -> new event AND event -> next day -> new event 
            if(eventDescCtrl.cntDays < jsonData.Scenario.temporalityQuestionsPerPeriod && EventService.getMap().get('var1001') != 1){
              eventDescCtrl.cntDays ++;  
            }            
            else {
                eventDescCtrl.cntDays = 1;
            }
             localStorageService.set('cntDays',  eventDescCtrl.cntDays );
            
            if(eventDescCtrl.showTemporality == 1 && (EventService.getMap().get('var1001')-1)%eventDescCtrl.nbEventPerDay == 0){ // check if a day has passed
                if((EventService.getMap().get('var1001')-1) == 0){ // if first day then set to 'Lundi'
                    $('.currentDay p').html('');
                    $('.nextDay p').html(eventDescCtrl.days[0]);
                    eventDescCtrl.day = eventDescCtrl.days[0];
                }
                else {
                    $('.currentDay p').html(eventDescCtrl.days[((EventService.getMap().get('var1001')-1)/eventDescCtrl.nbEventPerDay)%7-1]); // determine the day
                    $('.nextDay p').html(eventDescCtrl.days[((EventService.getMap().get('var1001')-1)/eventDescCtrl.nbEventPerDay)%7]);
                    eventDescCtrl.day = eventDescCtrl.days[((EventService.getMap().get('var1001')-1)/eventDescCtrl.nbEventPerDay)%7];
                }

                $('#dayTransition').css('display', 'block'); 
                setTimeout(function(){$('#dayTransition').css("opacity", "1");}, 100); // fade to black
                setTimeout(function (){
                    $('.currentDay').toggleClass('transparent');
                    $('.currentDay').css('transform', 'translate(-50%, -150px)');   // anime the current day to go up                
                }, eventDescCtrl.timeOut);
                setTimeout(function (){
                    $('.nextDay').css('transform', 'translate(-50%, -150px)'); // anime the next day to go up 
                    $('.nextDay').toggleClass('transparent');                    
                }, eventDescCtrl.timeOut+250);
                setTimeout(function (){$('#dayTransition').css("opacity", "0");}, eventDescCtrl.timeOut*3); // fade out
                setTimeout(function (){
                    $('#dayTransition').css('display', 'none');
                    $('.nextDay').css('transform', 'translate(-50%, -50%)'); // rest the postion of the days
                    $('.currentDay').css('transform', 'translate(-50%, -50%)');
                    $('.currentDay, .nextDay').toggleClass('transparent');
                    
                }, eventDescCtrl.timeOut*4);
                setTimeout(function(){ $('#noclick').removeClass('no-click');}, eventDescCtrl.timeOut*4); // add transparent div to prevent from click spam
            }
           else {
                $('.day').addClass('transparent'); // if no day has passed then just change to the next event
                $('#toggle-anime').addClass('transparent');
                setTimeout(function(){ $('#toggle-anime').removeClass('transparent'); $('.day').removeClass('transparent');}, 500);
                setTimeout(function(){ $('#noclick').removeClass('no-click');}, 1000);
            }
        }
    

		function buttonClick(value, sumID, sumWeight, sumGauge) { 
           
			
			
			// GA tracking
			ga('set', 'dimension1', jsonData.Scenario.uid);
			$analytics.eventTrack('event-choice', {category:'game', label:sumID});
			
			animDelay = 0;
            $('#noclick').addClass('no-click');
            var sum = {"id":sumID, "weight": sumWeight};            
            containsObj(sum, sumGauge);

            
            var endInsert = false;
            var trigger = '';
            var values = value.split(";");
            var isGameOver = false;
			
			// check variable after the click
            EventService.getJSONData().then(
                function(events){
            
					
					angular.forEach(values, function (item) {

						 if(item.match(/([$var][0-9]+([-+=][\=]?[-+]?|[<>])[0-9]+)/gm)){ 
							// check if gauge variable change
							var key = item.match(/(var[0-9]+)/gm);
							// si la var qui change est une des 3 jauge > animDelay = config.gaugeAnimDuration;
							 var keyInGauges = _.find(events.Scenario.gauges, function(gauge) {
								return gauge.var == key;
							});
							if(typeof keyInGauges !== undefined){
								animDelay = config.gaugeAnimDuration;
							}


							// check if a variable is in the list of command and update the variable
							EventService.updateVariable(item);
						}

						else if (item.match(/end_game/gm)){ // if endGame is in the choice go to the endGame route

							isGameOver = true;
							var strEnd = item.replace(/end_game\(|\)/gm, '');                    
							$('.nextDay, .currentDay').css('display', 'none');
							$('#dayTransition').css('display', 'block');
							setTimeout(function(){$('#dayTransition').css("opacity", "1");}, 100);
							setTimeout(function(){ $state.go("aperfectday.endGame", {endID : strEnd, summaries : eventDescCtrl.summaries});
							}, eventDescCtrl.timeOut+200);
						}

						else if(item.match(/\btrigger_event\b/gm)){ // fill var trigger with a trigger_event
							trigger = item;
						}

						else if(item.match(/\btrigger_pool\b/gm)){ // fill var trigger with a trigger_pool
							trigger = item;
						}

						else if(item.match(/\bend_insert\b/gm)){ // check if insert is over
							endInsert = true;
						}



					}); 
					if(isGameOver){
						return;
					}
            
			
					var variables = {};
                    var variablesToCheck = [];
                    variables = events.Variables;
                    variables.forEach(function (variable){
                        
                        if(variable.control !== ''){    // check if there are variables that have to be checked at each event
                            variablesToCheck.push(variable);
                        }
                    });
					
					
                    var mapTemp = new Map(EventService.getMap());
                    var insertTemp = '';                    
                    variablesToCheck.forEach(function (variableToCheck){ // find the function bound to the variables
                        
                       var compareTriggers = variableToCheck.controlEffect.split(";");
						
                       for (var k = 0; k < compareTriggers.length; k++){ // go through all the functions
                           
                           compareTriggers[k] = compareTriggers[k].replace(/^compareTrigger\(|\)$/gm, '');
                           var commands = compareTriggers[k].split(","); // split the parameters of the function into commands []
                           var varCondition = commands[0].match(/(var[0-9]+)/gm); // the first parameters is the condition
                           var conditionString = commands[0];
                           for(i = 0; i < varCondition.length; i ++) {
                                var valueCondition = EventService.getMap().get(varCondition[i]);
                               
                                conditionString = conditionString.replace('$'+varCondition[i], valueCondition);
                           }
						   
                           if(eval(conditionString)){ // check if condition is valid
							   for(var l = 1; l < commands.length; l ++){ // go through the other parameters
                                   
								
                                   if(commands[l].match(/(var[0-9]+)/gm)){ 
                                       
                               			// check if there are variables to update and if so put them into a temporary Map
                                        var key = commands[l].match(/(var[0-9]+)/gm);
                                        key = String(key);
									    
                                        if(commands[l].match(/(\$?var[0-9]+[+=]{2}|\(([^)]+)\)|\s)/gm)){
                                             var value = commands[l].replace(/(\$?var[0-9]+[+=]{2}|\(([^)]+)\)|\s)/gm, '')
                                             value = parseInt(value);
                                             if(mapTemp.get(key)+value < 0){
                                                mapTemp.set(key, 0);
                                            }
                                            else if (mapTemp.get(key)+value > 100){
                                                mapTemp.set(key, 100);            
                                            }
                                            else {
                                               mapTemp.set(key, mapTemp.get(key)+value); 
                                            }
											
                                        }
                                        else if(commands[l].match(/(\$?var[0-9]+[-=]{2}|\(([^)]+)\)|\s)/gm)){
                                            value = commands[l].replace(/(\$?var[0-9]+[-=]{2}|\(([^)]+)\)|\s)/gm, '')
                                            value = parseInt(value);
                                            if(mapTemp.get(key)-value < 0){
                                                mapTemp.set(key, 0);
                                            }
                                            else if (mapTemp.get(key)-value > 100){
                                                mapTemp.set(key, 100);            
                                            }
                                            else {
                                               mapTemp.set(key, mapTemp.get(key)-value); 
                                            }
											
                                        }
                                        else if(commands[l].match(/(\$?var[0-9]+[=]{1}|\(([^)]+)\)|\s)/gm)){
                                            value = commands[l].replace(/(\$?var[0-9]+[=]{1}|\(([^)]+)\)|\s)/gm, '')
                                            value = parseInt(value);
                                            mapTemp.set(key, value);
											
                                        }
                                        // si la var qui change est une des 3 jauge > animDelay = config.gaugeAnimDuration;
                                        var keyInGauges = _.find(events.Scenario.gauges, function(gauge) {
											return gauge.var == key;
										});
										if(typeof keyInGauges !== undefined){
											animDelay = config.gaugeAnimDuration;
										}
                                       

                                    }                                   
                                    else if(commands[l].match(/^(insert_event|insert_pool)/gm)){                                        
                                        insertTemp = commands[l]; // if there is an inser_event in the commands put it in a variable
                                   }
                                   else if (commands[l].match(/^end_game/gm)){ // if endGame is in the choice go to the endGame route
                                       var strEnd = commands[l].replace(/end_game\(|\)/gm, '');
                                      	
                                        $('.nextDay, .currentDay').css('display', 'none');
                                        $('#dayTransition').css('display', 'block');
                                       // wait for animation of gauges to end before going to bilan
                                        setTimeout(function(){$('#dayTransition').css("opacity", "1");}, config.gaugeAnimDuration+100);
                                        setTimeout(function(){ $state.go("aperfectday.endGame", {endID : strEnd, summaries : eventDescCtrl.summaries});
                                        }, config.gaugeAnimDuration+eventDescCtrl.timeOut+200);
                                    }
                               }
                           }                                   
                       } 
                    });                    
                    
                    
					
                    EventService.setMap(mapTemp); // replace the old map with the temp map
                    
                    if(eventDescCtrl.isInInsert && endInsert){ // condition to escape the insert 
                        insertTemp = eventDescCtrl.endInsertTrigger;
                        eventDescCtrl.isInInsert = false;
                        localStorageService.set('isInInsert', eventDescCtrl.isInInsert);
                    }
                    
                    else {
                    
                        if(insertTemp == ''){
                            
                            insertTemp = trigger;
                        }
                        else {
                            eventDescCtrl.isInInsert = true;
                            localStorageService.set('isInInsert', eventDescCtrl.isInInsert);
                            eventDescCtrl.endInsertTrigger = trigger;
                            localStorageService.set('endInsertTrigger', eventDescCtrl.endInsertTrigger);
                        }
                    }
                    
                    //call the right function and pass the ID of the event/pool
					if(events.Scenario.type == 2) {
						setTimeout(eventDescCtrl.triggerPool(''), animDelay);
					}else{
						if(insertTemp.match(/(insert_event|trigger_event)/gm)){
							var eventID = insertTemp.replace(/(insert_event|trigger_event)\(|\)/gm, '');
							setTimeout(eventDescCtrl.triggerEvent(eventID), animDelay);

						}else if(insertTemp.match(/(insert_pool|trigger_pool)/gm)){
							var poolID = insertTemp.replace(/(insert_pool|trigger_pool)\(|\)/gm, '');
							setTimeout(eventDescCtrl.triggerPool(poolID), animDelay);
						}
					}
                }
                
            );
            
        }

        //*********************** functions *********************//
    
        function setEventData (){ // initData
            EventService.getJSONData().then(
                function (events){
                   
                    if(localStorageService.get('map') == ''|| 	localStorageService.get("map") === null){
                        EventService.getVariables();
                    }else {
						var mapLS = localStorageService.get('map');
                        
						/*
						var initMap = new Map();
                        mapLS = mapLS.split(";");
                        mapLS.splice(mapLS.length-1, 1);
                        mapLS.forEach(function (item){
                            var key = item.match(/(var[0-9]+)/gm);
                            key = String(key);
                            var value = item.match(/[:]+[-]?[0-9]+/gm);
                            value = String(value).replace(/\:[-]?/gm, '');
                            value = parseInt(value);
                            initMap.set(key, value);
                        });
						*/
						var initMap = new Map(JSON.parse(mapLS));
                        EventService.setMap(initMap);
                        
                    }
                    
                     if((localStorageService.get('summaries1') == '' || localStorageService.get("summaries1") === null) || (localStorageService.get('summaries2') == '' || localStorageService.get("summaries2") === null) || (localStorageService.get('summaries3') == '' || localStorageService.get("summaries3") === null)){ 
                        eventDescCtrl.summaries = new Array();
                        eventDescCtrl.summaries['summaries1'] = new Array();
                        eventDescCtrl.summaries['summaries2'] = new Array();
                        eventDescCtrl.summaries['summaries3'] = new Array();
                     }
                    else {
                        eventDescCtrl.summaries['summaries1']= JSON.parse(localStorageService.get("summaries1"));
                        eventDescCtrl.summaries['summaries2'] = JSON.parse(localStorageService.get("summaries2"));
                        eventDescCtrl.summaries['summaries3'] = JSON.parse(localStorageService.get("summaries3"));
                    }

					
					
                     if(localStorageService.get('event') == '' || localStorageService.get("event") === null){
                        var eventTab = [];
                        for (var i = 0; i < events.Events.length; i++) {                          
                            if(checkConditions(events.Events[i]) && events.Events[i].pool == '') { // check if pool exist
                                eventTab.push(events.Events[i]);
                            }                               
                        }
						
						
						
                        getRandomEvent(eventTab);
                        displayData(eventDescCtrl.event);
                    }
                    else {
                        var eventID = localStorageService.get('event');
                        for (var j = 0; j < events.Events.length; j++) {
                            if (events.Events[j].id == eventID) {
                                displayData(events.Events[j]);
                            }
                        }
                    }
                    
                    eventDescCtrl.cntDays = localStorageService.get('cntDays') != null ? localStorageService.get('cntDays') : 1;

                    
                    EventService.getJSONData().then(
                        function (data){
							
                            //var nbEventPerDayFind = _.find(data.Variables, ['title', 'nbEventPerDay']);
                            var nbEventPerDayFind = _.find(data.Variables, ['id', 'var1000']);
                            eventDescCtrl.nbEventPerDay = nbEventPerDayFind.initialisation;
                            eventDescCtrl.day = eventDescCtrl.days[((EventService.getMap().get('var1001')-1)/eventDescCtrl.nbEventPerDay)%7];
                        }
                    );
                }
            );
        }

    
        function triggerEvent(choiceValue){ // find the ID of the event and change the data
            var eventID = choiceValue.replace(/\btrigger_event\(\b|\)|\s/gm, '' );
            EventService.getJSONData().then(
                function(events){
                    for (var i = 0; i < events.Events.length; i++) {
                        if (events.Events[i].id == eventID) {
                            eventDescCtrl.event = events.Events[i];
                            if(eventDescCtrl.showTemporality == 1 && (EventService.getMap().get('var1001')-1)%eventDescCtrl.nbEventPerDay == 0){ // check if a day has passed to wait the correct time 
                                $rootScope.idTimeout = setTimeout(function(){displayData(eventDescCtrl.event);}, eventDescCtrl.timeOut+100+animDelay);
                            }
                            else {
                               $rootScope.idTimeout = setTimeout(function(){displayData(eventDescCtrl.event);}, 500+animDelay);
                            }
                        }                
                    }
                }

            );
        }
    
        function triggerPool(choiceValue){ // find the ID of the pool and change the data
            var poolID = choiceValue.replace(/\btrigger_pool\(\b|\)|\s/gm, '' );
            
			EventService.getJSONData().then(
                function(events){                    
                    var poolTab = [];
                    if(events.Scenario.type == 2) {
						var eventsViewed = localStorageService.get('eventsViewed') !== null ? JSON.parse(localStorageService.get('eventsViewed')) : [];
						for (var i = 0; i < events.Events.length; i++) {
							if(_.findIndex(eventsViewed, function(eventId){ return eventId == events.Events[i].id; }) == -1) {
								poolTab.push(events.Events[i]);                 
							}
						}
					}else{
						for (var i = 0; i < events.Events.length; i++) {
							if (events.Events[i].pool == poolID) {                           
								if(checkConditions(events.Events[i])) {
									if(eventDescCtrl.eventData.id != events.Events[i].id){
										poolTab.push(events.Events[i]); 
									}

								}
							}                
						}
					}
					
					

                    eventDescCtrl.event = getRandomEvent(poolTab); // random on all the event of the pool passed

                    if(eventDescCtrl.showTemporality == 1 && (EventService.getMap().get('var1001')-1)%eventDescCtrl.nbEventPerDay == 0){ // check if a day has passed to wait the correct time 
                       $rootScope.idTimeout = setTimeout(function(){displayData(eventDescCtrl.event);}, eventDescCtrl.timeOut+100+animDelay);
                    }
                    else {
                       $rootScope.idTimeout = setTimeout(function(){displayData(eventDescCtrl.event);}, 500+animDelay);
                    }
                }

            );
        }
    
        function checkConditions(event){
            if(event.condition == ''){ // check if condition exist
                return true;
            }
            else {
                var conditionString = event.condition;
                
                
                var varArray = conditionString.match(/([var]+[0-9]+)/gm);

                for (var i = 0; i < varArray.length; i++) {
                       
                    var value = EventService.getMap().get(varArray[i]);
                    value = parseInt(value);                     
                    conditionString = conditionString.replace('$'+varArray[i], value);
                }
                
                return eval(conditionString);
            }
            
        }
    
        function getRandomEvent (tab){ // return a random event in the tab passed (with weight)
            var weightTab = [];
            for(var i = 0; i < tab.length; i++ ){
                if(tab[i].weight !== ''){
                    if(tab[i].weight.match(/([var]+[0-9]+)/gm)){
                        var str = tab[i].weight.match(/([var]+[0-9]+)/gm);
                        var value = EventService.getMap().get(String(str));
                        tab[i].weight = tab[i].weight.replace(/([var]+[0-9]+)/gm, value);
                    }
                    for(var j = 0; j < eval(tab[i].weight); j++){
                        weightTab.push(tab[i]);
                    }
                }
                else {
                    weightTab.push(tab[i]);
                }
            }  
            eventDescCtrl.event = weightTab[Math.floor(Math.random() *weightTab.length)]
           return eventDescCtrl.event;
        }
    
        function printMap() { // displays the map after the update of the variables
			
              EventService.getJSONData().then(
                function (data){
                    
					var str = '';
                    var variable = '';
                    var variables = {};
                    variables = data.Variables;
                    EventService.getMap().forEach(function (value, key){
                        variable = _.find(variables, ['id', key]);
                        str +=  variable.title + " : " + value + "<br>\n";
                    });
                    eventDescCtrl.map = $sce.trustAsHtml(str);
					
					
                }
            );        
        }
    
        function resetData(){
			
			// GA tracking
			if($rootScope.gameStartTime > 0) {
				var gameDuration = parseInt( (new Date().getTime() - $rootScope.gameStartTime) / 1000 );
				ga('set', 'dimension1', jsonData.Scenario.uid);
				$analytics.eventTrack('end', {category:'game', label:'resetAvantFin', value:gameDuration});
			}


            //$('#startScreen').show();
            //setTimeout(function(){$('#startScreen').hide();}, eventDescCtrl.timeOut+200);
            $('#dayTransition').css("opacity", "0");
            $(".background").show();
            localStorageService.clearAll();
            eventDescCtrl.eventChoices = [];
            eventDescCtrl.event = null;
            eventDescCtrl.day = eventDescCtrl.days[0];
            //setEventData();
			
			$state.go("aperfectday.intro");
			
        }
        
        
    });
    