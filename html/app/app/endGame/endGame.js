angular.module('endGame', [
    
])
    .config(function ($stateProvider) {
        $stateProvider
            .state('aperfectday.endGame', {
                url: 'bilan',
                views: {
                    //target the ui-view named 'content' in ROOT state (LUMINOPOLIS_DV)
                    'content@': {
                        controller: 'EndGameCtrl as endGameCtrl',
                        templateUrl: 'app/endGame/endGame.tmpl.html'
                    },
                    'about@': {
                        controller: 'AboutCtrl as aboutCtrl',
                        templateUrl: 'app/about/about.tmpl.html'
                    }
                },
                data : {
                    routeClass : 'endGame'
                },
				params: {
                    endID: '',
                    summaries: []
				}
            })
        ;
    })

/*-----------------------------------------------------------------------------------
                                    Controllers
-----------------------------------------------------------------------------------*/
    .controller('EndGameCtrl', function EndGameCtrl(config, $rootScope, $scope, $sce, $state, $stateParams, EventService, localStorageService, $analytics) {
    
		//********************* local variables ****************//
    
        var endGameCtrl = this;
    	var ends = {};
        var gaugeColors = ['rgb(111, 234, 172)', 'rgb(255, 206, 68)', 'rgb(228, 90, 96)'];
	
		endGameCtrl.jsonData;
        endGameCtrl.gauges;
        endGameCtrl.restart = restart;
        endGameCtrl.continueBilan = continueBilan;
        endGameCtrl.endMsg = '';
        endGameCtrl.endMsgTitle = '';
        endGameCtrl.gauges;
        endGameCtrl.summaries = new Array();
        endGameCtrl.bilan = {};
        endGameCtrl.animeDelay = 1000;
        endGameCtrl.delay = 200;
        endGameCtrl.colorGauge = 'rgb(0, 0, 0)';
        endGameCtrl.textGauge = '';
        endGameCtrl.pictoGauge = '';
        endGameCtrl.endID = '';
        endGameCtrl.endTitle = '';
		endGameCtrl.endText = '';
		endGameCtrl.gaugeEnd = localStorageService.get('gaugeEnd') != null ? localStorageService.get('gaugeEnd') : null;
		// endGameCtrl.adviceLink = 'https://goo.gl/forms/g8T87HXzEvSiHnNB3';
		
	
		if($stateParams.endID != '') {
			endGameCtrl.endID = $stateParams.endID;
			localStorageService.set('endID', $stateParams.endID);
		}else{
			endGameCtrl.endID = localStorageService.get('endID');
		}
		
        var background = {};
        $("#appContainer").css("background", "linear-gradient(to bottom, #8ed0ff, #1483d1)");
		$('#footerContainer').css("bottom","0");

        clearTimeout($rootScope.idTimeout);
	
		
		EventService.getJSONData().then(
			function(data){
				endGameCtrl.jsonData = data;
				endGameCtrl.gauges = data.Scenario.gauges;
				
				// Get title and text of the end
				var ends = data.Ends;
				
				
				ends.forEach(function (end){
					if(end.id == endGameCtrl.endID){    
						endGameCtrl.endTitle = end.title;
						endGameCtrl.endText = end.text;
					}
				});
				
				
				if(endGameCtrl.gaugeEnd != null) {
					endGameCtrl.colorGauge = gaugeColors[endGameCtrl.gaugeEnd];//'rgb(111, 234, 172)';
					endGameCtrl.textGauge = endGameCtrl.gauges[endGameCtrl.gaugeEnd].label;//'ARGENT';
					endGameCtrl.pictoGauge = endGameCtrl.gauges[endGameCtrl.gaugeEnd].picto;//'fa-eur';
					//endGameCtrl.endText = "Vous êtes sur la paille, vous n'avez pas réussi à bien gérer vos économies !";
					$('#progressBar i').addClass(endGameCtrl.gauges[endGameCtrl.gaugeEnd].picto);

					var bar = new ProgressBar.Circle(progressBar, {
						color: endGameCtrl.colorGauge,
						// This has to be the same size as the maximum width to
						// prevent clipping
						strokeWidth: 16,
						trailWidth: 8,
						trailColor: '#FFFFFF',
						easing: 'easeInOut',
						duration: 500,
						fill: '#4be79f',
						text: {
						autoStyleContainer: false
						},
						from: { color: endGameCtrl.colorGauge, width: 10 },
						to: { color: endGameCtrl.colorGauge, width: 10 },
						// Set default step function for all animate calls
						step: function(state, circle) {
							circle.path.setAttribute('stroke', state.color);
							circle.path.setAttribute('stroke-width', state.width);
						}
					});
					bar.animate(0.1);


					$('#progressBar').css('display', 'block');
					$('#loseScreen').css('display', 'block');
					$('#continue').css('display', 'block');

					setTimeout(function(){
						$('#progressBar').css('opacity', 1);
					}, 300);

					setTimeout(function(){$('#progressBar i').css('opacity', 0);}, 1500);
					setTimeout(function(){
						 $('#progressBar i').removeClass(endGameCtrl.pictoGauge);
						 $('#progressBar i').addClass('fa-minus'); 
						 $('#progressBar i').css('left', '35%')
						 $('#progressBar i').css('font-size', '22px')
						 $('#progressBar i').css('color', '#990000') 

					}, 1800);

					 setTimeout(function(){
						bar.animate(0, {duration:1000});
					}, 2500);

					setTimeout(function(){$('#progressBar i, #progressBar #plusGauge p').css('opacity', 1);}, 2500);

					setTimeout(function(){$('#progressBar i, #progressBar #plusGauge p').css('opacity', 0);}, 4000);
					setTimeout(function(){
						 $('#progressBar i').removeClass('fa-minus');
						 $('#progressBar i').addClass(endGameCtrl.pictoGauge); 
						 $('#progressBar i').css('left', '50%')
						 $('#progressBar i').css('font-size', '40px')
						 $('#progressBar i').css('color', 'white') 

					}, 4300);

					setTimeout(function(){$('#progressBar i').css('opacity', 1);}, 4600);

					setTimeout(function(){$('#progressBar').css('top', '25%');}, 4800);

					setTimeout(function(){$('#loseScreen').css('opacity', 1);  $('#continue').css('opacity', 1);}, 5000);

				}else{

					$('#winScreen').css('display', 'block');
					$('#winScreen').css('opacity', 1);
					$('#continue').css('display', 'block');
					$('#continue').css('opacity', 1);

				}
				
				
				/*if($stateParams.summaries['summaries1'] == null || $stateParams.summaries['summaries2'] == null || $stateParams.summaries['summaries3'] == null){
					if(localStorageService.get("summaries1") != null){
						var summaries1 = JSON.parse(localStorageService.get("summaries1"));
					}
					else{
						summaries1 = [];
					}
					if(localStorageService.get("summaries2") != null){
						var summaries2 = JSON.parse(localStorageService.get("summaries2"));
					}
					else{
						summaries2 = [];
					}
					if(localStorageService.get("summaries3") != null){
						var summaries3 = JSON.parse(localStorageService.get("summaries3"));
					}
					else{
						summaries3 = [];
					}
				}

				else{    
					summaries1 = $stateParams.summaries['summaries1'];
					summaries2 = $stateParams.summaries['summaries2'];
					summaries3 = $stateParams.summaries['summaries3'];
				}*/
				_.forEach(endGameCtrl.gauges, function(gauge, index) {
					if(localStorageService.get('summaries'+gauge.var) != null) {
						endGameCtrl.summaries.push(JSON.parse(localStorageService.get('summaries'+gauge.var)));
						sortSums(endGameCtrl.summaries[endGameCtrl.summaries.length - 1]);
					}
				});
				
				

				// GA tracking
				if($rootScope.gameStartTime > 0) {
					var gameDuration = parseInt( (new Date().getTime() - $rootScope.gameStartTime) / 1000 );
					ga('set', 'dimension1', data.Scenario.uid);
					$analytics.eventTrack('end', {category:'game', label:$stateParams.endID, value:gameDuration});
				}
				
			}
		);
	
    
		

		



		
	
	
	
	
	
	

		function continueBilan (){   
			$('#loseScreen').css('opacity', 0);
			$('#winScreen').css('opacity', 0);
			$('#continue').css('opacity', 0);
			$('#progressBar').css('display', 'none');
			setTimeout(function(){
				$('#loseScreen').css('display', 'none');
				$('#winScreen').css('display', 'none');
				$('#continue').css('display', 'none'); 
			}, 600);
			
			$("#endGame .contentBlock").css("display", "block");
			$("#endGame .contentBlock").css("transform", "translateX(100px)");
			$("#reset").css("transform", "translateX(100px)");
			$('#footerContainer').css("bottom","");
			var index = 0;
			_.forEach(endGameCtrl.bilan, function(texts, key) {
				setTimeout(function(){
					$("#"+key).css("transform", "translateX(0px)");
					$("#"+key).css("opacity", "1");
				}, endGameCtrl.animeDelay+endGameCtrl.delay*index);
				index++;
			});/*
			setTimeout(function(){
				$("#lienSocial").css("transform", "translateX(0px)");
				$("#lienSocial").css("opacity", "1");
			}, endGameCtrl.animeDelay);
			setTimeout(function(){
				$("#logement").css("transform", "translateX(0px)");
				$("#logement").css("opacity", "1");
			}, endGameCtrl.animeDelay+endGameCtrl.delay);
			setTimeout(function(){
				$("#budget").css("transform", "translateX(0px)");
				$("#budget").css("opacity", "1");
			}, endGameCtrl.animeDelay+endGameCtrl.delay*2);*/
			setTimeout(function(){
				$("#reset").css("transform", "translateX(0px)");
				$("#reset").css("opacity", "1");
			}, endGameCtrl.animeDelay+endGameCtrl.delay*index);


			// GA tracking
			$analytics.pageTrack('/bilan-recapitulatif', {title:'Bilan récapitulatif'});
			
		}

		function sortSums(sums){
			if(sums.length > 0){
				sums.sort(function (a, b) {
					if(a.weight - b.weight >0){
						return 1;
					}
					else if (a.weight - b.weight < 0){
						return -1;
					}

					else {
						return Math.random()-0.5;
					}
				});       

				sums.reverse();   

				
						/*if(sums.length < 3){
							for(var j = 0; j < sums.length; j++){
								var event = _.find(data.Events, function(e) { return e.id == sums[j].id.split("|")[0]; }); 
								var choice = _.find(event.Choices, function(c) { return c.id == sums[j].id;});
								var objBilan = {"text":choice.summaryText, "gauge": choice.summaryGauge};
								endGameCtrl.bilan['sums'+objBilan.gauge].push(objBilan.text);
							}
						}
						else {*/
							for(var i = 0; i < 3 && i < sums.length; i++){
								event = _.find(endGameCtrl.jsonData.Events, function(e) { return e.id == sums[i].id.split("|")[0]; }); 
								choice = _.find(event.Choices, function(c) { return c.id == sums[i].id;});
								gauge = _.find(endGameCtrl.jsonData.Scenario.gauges, function(g) { return g.var == choice.summaryGauge;});
								objBilan = {"text":choice.summaryText, "gauge": choice.summaryGauge};
								if(typeof endGameCtrl.bilan[objBilan.gauge] === "undefined") {
									endGameCtrl.bilan[objBilan.gauge] = {};
									endGameCtrl.bilan[objBilan.gauge].title = gauge.summaryTitle;
									endGameCtrl.bilan[objBilan.gauge].picto = gauge.picto;
									endGameCtrl.bilan[objBilan.gauge].entries = new Array();
								}
								endGameCtrl.bilan[objBilan.gauge].entries.push(objBilan.text);

							}
						
							
						//}
				
			}
		}

		function restart() {
			localStorageService.clearAll();
            $state.go("aperfectday.intro");
		}


	});