angular.module('about', [
    
])


.controller('AboutCtrl', function AboutCtrl($rootScope, $scope, $state, DataService, EventService, config) {
    
    
    $scope.modal = modal;
    $scope.titleAbout = "Plus belle la semaine";
    $scope.textAbout = "Ce jeu vise à déstigmatiser la démarche d’adaptation du domicile par les personnes âgées, en plaçant le joueur face à un enchainement de choix binaires pouvant survenir au cours de son quotidien.<br><br>Il a été développé dans le cadre du projet Bien Vivre Chez Soi à la Métropole, un projet de prévention porté par la Métropole de Lyon, dans le cadre du plan d’action 2017 de la Conférence des financeurs de la prévention de la perte d’autonomie."
    
    
    $scope.infoImg = 'data/img/info.png';
    $("#modal").css("background", "url(data/img/bilan2.jpg) center no-repeat");
    
    
    function modal() {
        if($('#modal').hasClass('transparent')){
            $('#modal').css('display', 'block');
            setTimeout(function(){$('#modal').removeClass('transparent');}, 100);
            
        }
        else {
            $('#modal').addClass('transparent'); 
            setTimeout(function(){$('#modal').css('display', 'none');}, 500);
            
        }
    }
    
})