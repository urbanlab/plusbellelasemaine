angular.module('gauge', [
    
])


.controller('GaugeCtrl', function GaugeCtrl($rootScope, $sce, $scope, $state, EventService, config, localStorageService) {

        var gaugeCtrl = this;
        
        gaugeCtrl.bar1 = {};
        gaugeCtrl.bar2 = {};
        gaugeCtrl.bar3 = {};
    
        gaugeCtrl.val1 = null;
        gaugeCtrl.val2 = null;
        gaugeCtrl.val3 = null;
    
        gaugeCtrl.gaugeEnd = null;
    
        gaugeCtrl.sparklesCurrentBg1 = 0;
        gaugeCtrl.sparklesCurrentBg2 = 0;
        gaugeCtrl.sparklesCurrentBg3 = 0;
        
        
        
        var bar1 = new ProgressBar.Circle('#progressBar1', {
              color: 'rgb(111, 234, 172)',
              // This has to be the same size as the maximum width to
              // prevent clipping
              strokeWidth: 10,
              trailWidth: 0,
              easing: 'easeInOut',
              duration: 3400, //1400,
              text: {
                autoStyleContainer: false
              },/*
              from: { color: 'rgb(111, 234, 172)', width: 10 },
              to: { color: 'rgb(111, 234, 172)', width: 10 },
              // Set default step function for all animate calls
              step: function(state, circle) {
                circle.path.setAttribute('stroke', state.color);
                circle.path.setAttribute('stroke-width', state.width);

              }*/
            });
        gaugeCtrl.bar1 = bar1;
		
        var bar2 = new ProgressBar.Circle('#progressBar2', {
              color: 'rgb(255, 206, 68)',
              // This has to be the same size as the maximum width to
              // prevent clipping
              strokeWidth: 10,
              trailWidth: 0,
              easing: 'easeInOut',
              duration: 3400, //1400,
              text: {
                autoStyleContainer: false
              },/*
              from: { color: 'rgb(255, 206, 68)', width: 10 },
              to: { color: 'rgb(255, 206, 68)', width: 10 },
              // Set default step function for all animate calls
              step: function(state, circle) {
                circle.path.setAttribute('stroke', state.color);
                circle.path.setAttribute('stroke-width', state.width);

              }*/
            });
        gaugeCtrl.bar2 = bar2;
        
        
       var bar3 = new ProgressBar.Circle('#progressBar3', {
              color: 'rgb(228, 90, 96)',
              // This has to be the same size as the maximum width to
              // prevent clipping
              strokeWidth: 10,
              trailWidth: 0,
              easing: 'easeInOut',
              duration: 3400, //1400,
              text: {
                autoStyleContainer: false
              },/*
              from: { color: 'rgb(228, 90, 96)', width: 10 },
              to: { color: 'rgb(228, 90, 96)', width: 10 },
              // Set default step function for all animate calls
              step: function(state, circle) {
                circle.path.setAttribute('stroke', state.color);
                circle.path.setAttribute('stroke-width', state.width);

              }*/
            });
        gaugeCtrl.bar3 = bar3;
    	
	
    // écouteurs sur les variables des jauges 

        $scope.$watch(function(){
            return EventService.getMap().get('var2');
        }, updateGauge1);  

         $scope.$watch(function(){
            return EventService.getMap().get('var4');
        }, updateGauge2); 

         $scope.$watch(function(){
            return EventService.getMap().get('var3');
        }, updateGauge3); 


         function updateGauge1(newVal, oldVal, scope){
             if(typeof newVal != 'undefined'){
                 
                 if(newVal <= 10){
                     gaugeCtrl.gaugeEnd = 1;
                     localStorageService.set('gaugeEnd', gaugeCtrl.gaugeEnd);
                 }
                 gaugeCtrl.val1 = Math.abs(oldVal-newVal);
                 
				 
                 if (newVal != oldVal){
                     
                     $('#progressBar1 svg').css('transform', 'scale(1.2)'); // animate the svg changing size
                     $('#progressBar1 i').css('opacity', 0);
                     setTimeout(function(){ 

                         if(oldVal-newVal > 0){
                             $('#progressBar1 i').removeClass('fa-eur');
                             $('#progressBar1 i').addClass('fa-minus'); 
                             $('#progressBar1 i').css('left', '35%')
                             $('#progressBar1 i').css('font-size', '15px')
                             $('#progressBar1 i, #progressBar1 #plusGauge1 p').css('color', '#990000')
                         }
                         else {
                             $('#progressBar1 i').removeClass('fa-eur');
                             $('#progressBar1 i').addClass('fa-plus'); 
                             $('#progressBar1 i').css('left', '35%')
                             $('#progressBar1 i').css('font-size', '15px')
                         }
                     }, 300);
                     
                     if(oldVal-newVal < 0){

                         setTimeout(function(){
                             sparkles(1);
                             $('#progressBar1 i, #progressBar1 #plusGauge1 p').css('opacity', 1);
                         }, 600);
                     } else {
                        setTimeout(function(){
                             $('#progressBar1 i, #progressBar1 #plusGauge1 p').css('opacity', 1);
                         }, 600); 
                     }
                     
                     setTimeout(function(){
                     	$('#progressBar1 i, #progressBar1 #plusGauge1 p').css('opacity', 0.2);
                     }, 800);
                     
                     setTimeout(function(){
                     	$('#progressBar1 i, #progressBar1 #plusGauge1 p').css('opacity', 1);
                     }, 1000);
                     


                     setTimeout(function(){
					 	$('#progressBar1 i, #progressBar1 #plusGauge1 p').css('opacity', 0);
					 }, 2500);
                     setTimeout(function(){
                          $('#progressBar1 i, #progressBar1 #plusGauge1 p').css('color', 'white')
                         
                         if(oldVal-newVal > 0){
                             $('#progressBar1 i').removeClass('fa-minus');
                             $('#progressBar1 i').addClass('fa-eur'); 
                             $('#progressBar1 i').css('left', '50%')
                             $('#progressBar1 i').css('font-size', '25px')
                             
                         }
                         else {
                             $('#progressBar1 i').removeClass('fa-plus');
                             $('#progressBar1 i').addClass('fa-eur'); 
                             $('#progressBar1 i').css('left', '50%')
                             $('#progressBar1 i').css('font-size', '25px')
                         }

                     }, 2800);

                     setTimeout(function(){
						 $('#progressBar1 i').css('opacity', 1);
					 }, 3100);
                }
                
				setTimeout(function(){
                    $('#progressBar1 svg').css('transform', 'scale(1)'); // when changing size animation is over change back size and start animation of gauge
                    $('#progressBar1 i').css('font-size', '25px');
                    gaugeCtrl.bar1.animate(newVal/100);
                }, 500); // mis à 500 au lieu de 2500 pour éviter le bug de disparition du svg sous edge...
				
				
                 
             }            
         }

        function updateGauge2(newVal, oldVal, scope){
             if(typeof newVal != 'undefined'){
                 if(newVal <= 10){
                     gaugeCtrl.gaugeEnd = 2;
                     localStorageService.set('gaugeEnd', gaugeCtrl.gaugeEnd);
                 }
                  gaugeCtrl.val2 = Math.abs(oldVal-newVal);
                 
				 
                 if (newVal != oldVal){
                     
                     $('#progressBar2 svg').css('transform', 'scale(1.2)'); // animate the svg changing size
                     $('#progressBar2 i').css('opacity', 0);
                     setTimeout(function(){ 

                         if(oldVal-newVal > 0){
                             $('#progressBar2 i').removeClass('fa-handshake-o');
                             $('#progressBar2 i').addClass('fa-minus'); 
                             $('#progressBar2 i').css('left', '35%')
                             $('#progressBar2 i').css('font-size', '15px')
                             $('#progressBar2 i, #progressBar2 #plusGauge2 p').css('color', '#990000')
                         }
                         else {
                             $('#progressBar2 i').removeClass('fa-handshake-o');
                             $('#progressBar2 i').addClass('fa-plus'); 
                             $('#progressBar2 i').css('left', '35%')
                             $('#progressBar2 i').css('font-size', '15px')
                         }
                     }, 300);
                     
                     if(oldVal-newVal < 0){
                     
                         setTimeout(function(){
                             sparkles(2);
                             $('#progressBar2 i, #progressBar2 #plusGauge2 p').css('opacity', 1);
                            
                        }, 600);
                     
                     } else {
                        setTimeout(function(){
                             $('#progressBar2 i, #progressBar2 #plusGauge2 p').css('opacity', 1);
                         }, 600); 
                     }
                     
                        setTimeout(function(){
                             $('#progressBar2 i, #progressBar2 #plusGauge2 p').css('opacity', 0.2);
                         }, 800);
                     
                        setTimeout(function(){
                             $('#progressBar2 i, #progressBar2 #plusGauge2 p').css('opacity', 1);
                         }, 1000);
                     

    
                     setTimeout(function(){$('#progressBar2 i, #progressBar2 #plusGauge2 p').css('opacity', 0);}, 2500);
                     setTimeout(function(){
                         $('#progressBar2 i, #progressBar2 #plusGauge2 p').css('color', 'white')
                        
                         
                         if(oldVal-newVal > 0){
                             $('#progressBar2 i').removeClass('fa-minus');
                             $('#progressBar2 i').addClass('fa-handshake-o'); 
                             $('#progressBar2 i').css('left', '50%')
                             $('#progressBar2 i').css('font-size', '25px')
                         }
                         else {
                             $('#progressBar2 i').removeClass('fa-plus');
                             $('#progressBar2 i').addClass('fa-handshake-o'); 
                             $('#progressBar2 i').css('left', '50%')
                             $('#progressBar2 i').css('font-size', '25px')
                         }

                     }, 2800);

                     setTimeout(function(){$('#progressBar2 i').css('opacity', 1);}, 3100);
                }
                 setTimeout(function(){
                    $('#progressBar2 svg').css('transform', 'scale(1)'); // when changing size animation is over change back size and start animation of gauge
                    $('#progressBar2 i').css('font-size', '25px');
                    gaugeCtrl.bar2.animate(newVal/100);
                 }, 500); // mis à 500 au lieu de 2500 pour éviter le bug de disparition du svg sous edge...
				 
				 
             } 
			 
			 
         }

        function updateGauge3(newVal, oldVal, scope){
             if(typeof newVal != 'undefined'){
                 if(newVal <= 10){
                     gaugeCtrl.gaugeEnd = 3;
                     localStorageService.set('gaugeEnd', gaugeCtrl.gaugeEnd);
                 }
                 gaugeCtrl.val3 = Math.abs(oldVal-newVal);
                 
                 if (newVal != oldVal){
                     
                     $('#progressBar3 svg').css('transform', 'scale(1.2)'); // animate the svg changing size
                     $('#progressBar3 i').css('opacity', 0);
                     setTimeout(function(){ 

                         if(oldVal-newVal > 0){
                             $('#progressBar3 i').removeClass('fa-home');
                             $('#progressBar3 i').addClass('fa-minus'); 
                             $('#progressBar3 i').css('left', '35%')
                             $('#progressBar3 i').css('font-size', '15px')
                             $('#progressBar3 i, #progressBar3 #plusGauge3 p').css('color', '#990000')
                         }
                         else {
                             $('#progressBar3 i').removeClass('fa-home');
                             $('#progressBar3 i').addClass('fa-plus'); 
                             $('#progressBar3 i').css('left', '35%')
                             $('#progressBar3 i').css('font-size', '15px')
                         }
                     }, 300);
                     
                     if(oldVal-newVal < 0){
                         
                         setTimeout(function(){
                             sparkles(3);
                             $('#progressBar3 i, #progressBar3 #plusGauge3 p').css('opacity', 1);
                        }, 600);
                     
                     } else {
                        setTimeout(function(){
                             $('#progressBar3 i, #progressBar3 #plusGauge3 p').css('opacity', 1);
                         }, 600); 
                     }
                     
                     setTimeout(function(){
                             $('#progressBar3 i, #progressBar3 #plusGauge3 p').css('opacity', 0.2);
                         }, 800);
                     
                        setTimeout(function(){
                             $('#progressBar3 i, #progressBar3 #plusGauge3 p').css('opacity', 1);
                         }, 1000);
                     


                     setTimeout(function(){$('#progressBar3 i, #progressBar3 #plusGauge3 p').css('opacity', 0);}, 2500);
                     setTimeout(function(){
                          $('#progressBar3 i, #progressBar3 #plusGauge3 p').css('color', 'white');
                         
                         
                         if(oldVal-newVal > 0){
                             $('#progressBar3 i').removeClass('fa-minus');
                             $('#progressBar3 i').addClass('fa-home'); 
                             $('#progressBar3 i').css('left', '50%')
                             $('#progressBar3 i').css('font-size', '25px')
                         }
                         else {
                             $('#progressBar3 i').removeClass('fa-plus');
                             $('#progressBar3 i').addClass('fa-home'); 
                             $('#progressBar3 i').css('left', '50%')
                             $('#progressBar3 i').css('font-size', '25px')
                         }

                     }, 2800);
 
                     setTimeout(function(){$('#progressBar3 i').css('opacity', 1);}, 3100);
                }
                 setTimeout(function(){
                    $('#progressBar3 svg').css('transform', 'scale(1)'); // when changing size animation is over change back size and start animation of gauge
                    $('#progressBar3 i').css('font-size', '25px');
                    gaugeCtrl.bar3.animate(newVal/100);
                 }, 500); // mis à 500 au lieu de 2500 pour éviter le bug de disparition du svg sous edge...
             }            
         }
    
        function sparkles(indexSpark) {
            gaugeCtrl['sparklesCurrentBg'+indexSpark] = 0;
            nextBackground(indexSpark);
           
        }

        function nextBackground(indexSpark) {

            if(gaugeCtrl['sparklesCurrentBg'+indexSpark] >= 24) {
                gaugeCtrl['sparklesCurrentBg'+indexSpark] = 0;
                return;
            }
            gaugeCtrl['sparklesCurrentBg'+indexSpark]++;
            $('#sparkle'+indexSpark).css("background-image", "url(data/img/sparkles_"+gaugeCtrl['sparklesCurrentBg'+indexSpark]+".png)"); 
            setTimeout(function(){nextBackground(indexSpark);}, 24);
        }

    })