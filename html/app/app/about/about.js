angular.module('about', [
    
])


.controller('AboutCtrl', function AboutCtrl($rootScope, $scope, $analytics, EventService) {
    
    
    $scope.modal = modal;
    $scope.titleAbout = "Plus belle la semaine";
    $scope.textAbout = "Ce jeu vise à déstigmatiser la démarche d’adaptation du domicile par les personnes âgées, en plaçant le joueur face à un enchainement de choix binaires pouvant survenir au cours de son quotidien.<br><br>Il a été développé dans le cadre du projet Bien Vivre Chez Soi à la Métropole, un projet de prévention porté par la Métropole de Lyon, dans le cadre du plan d’action 2017 de la Conférence des financeurs de la prévention de la perte d’autonomie."
    
    
    $scope.infoImg = 'data/img/info.png';
	// $("#aboutModal").css("background", "url(data/img/bilan2.jpg) center center / cover no-repeat");
	$("#aboutModal").css("background", "unset");
	$("#aboutModal").css("background-image", "linear-gradient(142deg, #105ec7, #72efaf)");
	// $("#gaugeContainer").css("display", "none");
	
	EventService.getJSONData().then(
		function (data){
			$scope.titleAbout = data.Scenario.aboutTitle;
			$scope.textAbout = data.Scenario.aboutText;
		}
	);
	
    
    function modal() {
        if($('#aboutModal').hasClass('transparent')){
			setTimeout(function(){
				$('#aboutModal').css('display', 'block');
				$('#aboutModal').removeClass('transparent');
				$('#contentContainer').addClass('transparent'); 
				$('#gaugeContainer').addClass('transparent'); 
				$('#contentContainer').css('display', 'none');
				$('#gaugeContainer').css('display', 'none');
			}, 100);            
			// GA tracking
			$analytics.pageTrack('/a-propos', {title:'A propos'});
        }
        else {
			setTimeout(function(){
				$('#aboutModal').addClass('transparent'); 
				$('#aboutModal').css('display', 'none');
				$('#contentContainer').css('display', 'block');
				$('#contentContainer').removeClass('transparent');
				$('#gaugeContainer').css('display', 'block');
				$('#gaugeContainer').removeClass('transparent'); 
			}, 500);
            
            setTimeout(function(){
			}, 100);

            setTimeout(function(){
			}, 500);
        }
    }
    
})