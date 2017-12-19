angular.module('aperfectday', [
    'ui.router',
	'LocalStorageModule',   
    'gauge',    
    'about',    
    'eventDesc',    
    'endGame'
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
            // Allow loading from "luminopolisdv.dowino.com" assets domain.
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

        $urlRouterProvider.otherwise('jeu');

    })

/*-----------------------------------------------------------------------------------
                                        Main run
-----------------------------------------------------------------------------------*/

    .run(function (config, $rootScope, $http, EventService) {
        
        //************************ properties *******************//
    
        //************************ methods **********************//
        
        $rootScope.keyPressHandler = function(keyEvent){
            if(keyEvent.which  === 178){
                $rootScope.showMap = !$rootScope.showMap;
            }
        }
        
        //************************* run *************************//
        
        $rootScope.showMap = false;
    
        EventService.getJSONData().then( // load images on run
                function(data){
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
                        new Image().src = "data/img/"+ImgToLoad[i];
                    }
                    for(var i = 1; i <= 24; i ++){
                        new Image().src = "data/img/sparkles_"+i+".png";
                    }
                }
            );
    
        //******************* public functions ******************//
        ///////////////////////////////////////////////////////////     
    
    
        //******************** private functions ****************//
    
    
    })

/*-----------------------------------------------------------------------------------
                                        Services
-----------------------------------------------------------------------------------*/
 
     .factory('EventService', function ($rootScope, $http, $q, $sce, $filter, config, localStorageService) {
    
    
        var URLS = {
                GET_EVENT: 'data/eventData_FR.json?rnd='+Math.random(),
        };
    
        var events = false,
            event = {},
            varMap = new Map(),
            varObj = {};
    
    
    
        var factory = {
            
            getJSONData: getJSONData,
            getVariables: getVariables,
            updateVariable: updateVariable,
            getMap: getMap,
            setMap: setMap
        };
    
        return factory;
    
    
    /**************** Functions***************/

    
        function getJSONData (){
            // get all the eventData.json
            
            if(events) {
               
                return $q.when(events);
                }else {
                   
                       return $http.get($sce.trustAsResourceUrl(URLS.GET_EVENT)).then(cacheEvents);

               
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
                    })                    
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
            localStorageService.set('map', JSON.stringify(varMap));
        }
    
        function getMap() {
            return varMap;
        }
    
        function setMap(map){
            varMap = new Map(map);
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


            //********************** private functions *****************************//

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
