angular.module('intro', [
    
])

.config(function ($stateProvider) {
        $stateProvider
            .state('aperfectday.intro', {
                url: 'intro',
                views: {
                    //target the ui-view named 'content' in ROOT state (aperfectday)
                    'gauge@':{
                        template: ''
                    },
                    'content@': {
                        template: ''
                    },
                    'about@': {
                        template: ''
                    },
                    'intro@': {
                        controller: 'IntroCtrl as introCtrl',
                        templateUrl: 'app/intro/intro.tmpl.html'
                    },
                    'footer@': {                        
                        templateUrl: 'app/footer/footer.tmpl.html'
                    }
                    
                },
                data : {
                    routeClass : 'intro'
                },
				params: {
					
				}
            })
        ;
    })
.controller('IntroCtrl', function IntroCtrl($rootScope, $scope, $state, $analytics, EventService) {
    
    //********************* local variables ****************//
    
    var introCtrl = this;
	
	//************************ properties *******************//
    
	introCtrl.titleIntro = "";//Bienvenue dans <em>Plus belle la semaine</em> !";
    introCtrl.textIntro = "";//<p>Vous vous apprêtez à vous glisser dans la peau d’une personne âgée qui vit seule à domicile et qui se confronte à de nouvelles difficultés dans son quotidien.</p>\n<p>A vous de faire les bons choix tout au long de la partie pour rendre votre logement plus confortable tout en conservant une vie sociale et un budget équilibré.</p>\n<p>Réussirez vous à finir la semaine sans encombres ?</p>";
    
    $scope.leftIcon = 'images/tree.svg';
    $scope.rightIcon = 'images/house.svg';
	//************************ methods **********************//
    
    introCtrl.play = play;
	
    
	//************************** run ************************//
        
	
    // $("#introModal").css("background", "url(data/img/intro.jpg) center center / cover no-repeat");    
    $('#footerContainer').css("position","unset");
    // $('#footerContainer').css("bottom","unset");
    $('#appContainer').css("background-color","#161616");
    $('#appContainer').css("background-image","unset");
    
	EventService.getJSONData().then(
		function (data){
			introCtrl.titleIntro = data.Scenario.introTitle;
			introCtrl.textIntro = data.Scenario.introText;
		}
	);
	
	function play() {
		$state.go("aperfectday.eventDesc");
	}
    
})