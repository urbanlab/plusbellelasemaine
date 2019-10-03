angular.module('aperfectday', [
    'ui.router',
	'LocalStorageModule',   
    'gauge',    
    'about',    
    'intro',    
    'eventDesc',    
    'endGame',
	
	'angulartics', 'angulartics.google.analytics',
	'angular-cookie-law',
	'ng-appcache'
])

/*-----------------------------------------------------------------------------------
                                        Constants
-----------------------------------------------------------------------------------*/
    .constant('config', {
        gaugeAnimDuration: 3200 
    })


/*---------------------------------------------------------------------------------
                                    Configurations
-----------------------------------------------------------------------------------*/
    .config(['localStorageServiceProvider', function(localStorageServiceProvider){
        localStorageServiceProvider.setPrefix('ls');
    }])

//////////////////////////////////////////////////////////////////////////////////////

    .config(function($sceDelegateProvider) {
        $sceDelegateProvider.resourceUrlWhitelist([
            // Allow same origin resource loads.
            'self',
        ]);
    })

//////////////////////////////////////////////////////////////////////////////////////

    .config(function ($stateProvider, $urlRouterProvider) {
        $stateProvider
            //abstract state serves as a PLACEHOLDER or NAMESPACE for application states
            .state('aperfectday', {
                url: '/',
                abstract: true,
                views: {
                    'content@': {
                    
                    }
                },
                data: {
                }
            })
        ;

        $urlRouterProvider.otherwise('intro');

    })

//////////////////////////////////////////////////////////////////////////////////////

	.config(function ($analyticsProvider) {
		// turn off automatic tracking
		$analyticsProvider.virtualPageviews(false);
	})

	.config(function ($locationProvider) {
		//$locationProvider.html5Mode(true);
		//$locationProvider.hashPrefix('!');
	})

/*-----------------------------------------------------------------------------------
                                        Main run
-----------------------------------------------------------------------------------*/

    .run(function (config, $rootScope, $http, $stateParams, $location, EventService, localStorageService, appcache) {
        
        //************************ properties *******************//
    
        //************************ methods **********************//
        
        $rootScope.keyPressHandler = function(keyEvent){
            if(keyEvent.which  === 178){
                $rootScope.showMap = !$rootScope.showMap;
            }
        }
        
        //************************* run *************************//
        
        $rootScope.showMap = false;
		$rootScope.gameStartTime = 0;
    	
	
		// listen on appcache updates, and reload data 
		appcache.addEventListener('updateready', function(e) {
			
			//loadData();
			if (confirm('Une nouvelle version des données est disponible. Voulez-vous recharger la page ?')) {
				window.location.reload();
			}
		});
	
		// get scenarioUid in url or keep the default
		
		var sPageURL = window.location.search.substring(1);
		var sURLVariables = sPageURL.split('&');
		for (var i = 0; i < sURLVariables.length; i++) {
			var sParameterName = sURLVariables[i].split('=');
			if (sParameterName[0] == 'sid') {
				EventService.setScenarioUid(sParameterName[1]);
			}
		}
	
	
		// load data
        loadData();
    
	
	
        //******************* public functions ******************//
        function loadData() {
			EventService.getJSONData().then( // load images on run
                function(data){
					console.log(data);
					
					// check localstorage data is valid with loaded data
					if(localStorageService.get('scenarioUid') != data.Scenario.uid || localStorageService.get('scenarioLastUpdateDate') != data.Scenario.lastUpdateDate) {
						localStorageService.clearAll();
						localStorageService.set('scenarioUid', data.Scenario.uid);
						localStorageService.set('scenarioLastUpdateDate', data.Scenario.lastUpdateDate);
					}

					// pre-load medias (background)
                    var events = data.Events;
                    var images = [];
                    var img = '';
                    events.forEach(function(item){
                        img = item.background;
                        images.push(img);

                    });
                    var ImgToLoad = [];
                    $.each(images, function(i, el){
                        if($.inArray(el, ImgToLoad) === -1) ImgToLoad.push(el);
                    });
                    for(var i = 0; i < ImgToLoad.length; i ++){
                        new Image().src = ImgToLoad[i];
                    }
                    for(var i = 1; i <= 24; i ++){
                        new Image().src = "data/img/sparkles_"+i+".png";
                    }
					
					
					setTimeout(function(){ 

                    	$('#appContainer').removeClass('hidden');
                        
                     }, 1000);
                }
            );
		}
    
        //******************** private functions ****************//
    
    
    })

/*-----------------------------------------------------------------------------------
                                        Services
-----------------------------------------------------------------------------------*/
 
     .factory('EventService', function ($rootScope, $http, $q, $sce, $filter, config, localStorageService) {
    
    	var DEFAULT_SCENARIO_UID = "9CR73H"; // scenario complexe de base
        var URLS = {
                GET_EVENT: 'data/eventData_FR.json',
                //GET_EVENT: 'data/eventData_FR.json?rnd='+Math.random(),
        };
    
        var events = false,
            event = {},
            varMap = new Map(),
            varObj = {},
			scenario,
			scenarioUid;
    
    
		scenarioUid = DEFAULT_SCENARIO_UID;
    
        var factory = {
            
            getJSONData: getJSONData,
            getVariables: getVariables,
            updateVariable: updateVariable,
            getMap: getMap,
            setMap: setMap,
			setScenarioUid: setScenarioUid
        };
    
        return factory;
    
    
    /**************** Functions***************/

    
        function getJSONData (){
            // get all the eventData.json
            
            if(events) {
               
                return $q.when(events);
                }else {
                   
                       //return $http.get($sce.trustAsResourceUrl(URLS.GET_EVENT)).then(cacheEvents);
                       return $http.get($sce.trustAsResourceUrl('data/'+scenarioUid+'/scenarioData_FR.json')).then(cacheEvents);
                }
    
            function cacheEvents(result) {
                events = result.data;
                _.forEach(events.Variables, function (item) {
                   if(item.controlEffect !== ''){
                    item.controlEffect = item.controlEffect.replace(/\s/gm, '');
                   }
                });
                return events;
            }
        }
    
        function getVariables(){ 
            // get only the variables from the eventData.json and put them into a Map('nom_variable', 'valeur_variable')
            getJSONData().then(
                function (event) {
                    varObj = event.Variables;
                    angular.forEach(varObj, function (item){
                        varMap.set(item.id, parseInt(item.initialisation));
                    });
					localStorageService.set('map', JSON.stringify(Array.from(varMap)));
                }
            );
        }
    
        function updateVariable (buttonValue){
            
			// Update the variable in the Map
            var key = buttonValue.replace(/([\$]|[-+=]+[0-9]+|\(([^)]+)\)|\s)/gm, '');
            if(buttonValue.match(/(\$?var[0-9]+[+=]{2}|\(([^)]+)\))/gm)){
                 var value = buttonValue.replace(/(\$?var[0-9]+[+=]{2}|\(([^)]+)\)|\s)/gm, '')
                 value = parseInt(value);
                 if(varMap.get(key)+value < 0){
                    varMap.set(key, 0);
                }
                else if (varMap.get(key)+value > 100){
                    varMap.set(key, 100);            
                }
                else {
                   varMap.set(key, varMap.get(key)+value); 
                }
            }
            else if(buttonValue.match(/(\$?var[0-9]+[-=]{2}|\(([^)]+)\))/gm)){
                value = buttonValue.replace(/(\$?var[0-9]+[-=]{2}|\(([^)]+)\)|\s)/gm, '')
                value = parseInt(value);
                if(varMap.get(key)-value < 0){
                    varMap.set(key, 0);
                }
                else if (varMap.get(key)-value > 100){
                    varMap.set(key, 100);            
                }
                else {
                   varMap.set(key, varMap.get(key)-value); 
                }
                
            }
            else if(buttonValue.match(/(\$?var[0-9]+[=]{1}|\(([^)]+)\))/gm)){
                value = buttonValue.replace(/(\$?var[0-9]+[=]{1}|\(([^)]+)\)|\s)/gm, '')
                value = parseInt(value);
                varMap.set(key, value);
            }
			localStorageService.set('map', JSON.stringify(Array.from(varMap)));
        }
    
        function getMap() {
            return varMap;
        }
    
        function setMap(map){
            varMap = new Map(map);
			localStorageService.set('map', JSON.stringify(Array.from(varMap)));
        } 
	
		function setScenarioUid(uid) {
			scenarioUid = uid;
		}
        
    })

//////////////////////////////////////////////////////////////////////////////////////

    .factory ('DataService', function ($rootScope, $http, $q, config) {
    
        var URLS = {
                    GET_DATA: 'data/data.json',
                };

            var data = false;

            var factory = {
                getData: getData,
                update: update
            };

            return factory;


            // *********** public functions **********************

            function getData() { 
                if(data) {
                    return $q.when(data);
                }else{

                        return $http.get(URLS.GET_DATA).then(cacheData);
 
                }
            }   

            function update() {
                data = false;
            }


            //********************** private functions ***************************** //

            function cacheData(result) {
                data = result.data;
                
                return data;
            }
            

    })

/*-----------------------------------------------------------------------------------
                                        filters
-----------------------------------------------------------------------------------*/

.filter('to_trusted', ['$sce', function($sce){
        return function(text) {
            return $sce.trustAsHtml(text);
        };
    }])
