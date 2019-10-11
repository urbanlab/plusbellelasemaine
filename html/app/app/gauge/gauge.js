angular.module('gauge', [
    
])


.controller('GaugeCtrl', function GaugeCtrl($rootScope, $sce, $scope, $state, EventService, config, localStorageService) {

	var gaugeCtrl = this;
	var BAR_COLORS = [
		'rgb(75,231,159)',
		'rgb(255,108,233)',
		'rgb(255,215,130)'
	];
	
	gaugeCtrl.gauges = [];

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
        
	
	EventService.getJSONData().then(
		function (data){
			
			_.forEach(data.Scenario.gauges, function (gauge, index) {
				
				gauge.val = 0;
				gauge.bar = new ProgressBar.Circle('#progressBar'+(index+1), {
					color: BAR_COLORS[index],
					// This has to be the same size as the maximum width to
					// prevent clipping
					strokeWidth: 10,
                    trailWidth: 4,
                    trailColor: '#FFFFFF',
					easing: 'easeInOut',
					duration: 3400, //1400,
					text: {
					autoStyleContainer: false
					},
				});
				
				gaugeCtrl.gauges.push(gauge);
				
				// écouteurs sur les variables des jauges 
				$scope.$watch(function(){
					return EventService.getMap().get(gauge.var);
				}, function(newVal, oldVal, scope) {
					
					updateGauge(index, newVal, oldVal, scope);
				});
				
			});
			
		}
	);
        
   /*     
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
              },
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
              },
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
              },
            });
        gaugeCtrl.bar3 = bar3;
    */
	
    
	function updateGauge(index, newVal, oldVal, scope){
		var g = gaugeCtrl.gauges[index];
		var progressBarId = '#progressBar'+(index+1);
		var plusGaugeId = '#plusGauge'+(index+1);
		
		if(typeof newVal != 'undefined'){
			
			 if(newVal <= g.minValueToLoose){
				 gaugeCtrl.gaugeEnd = index;
				 localStorageService.set('gaugeEnd', gaugeCtrl.gaugeEnd);
			 }
			 g.val = Math.abs(oldVal-newVal);


			 if (newVal != oldVal){

				 $(progressBarId+' svg').css('transform', 'scale(1.2)'); // animate the svg changing size
				 $(progressBarId+' i').css('opacity', 0);
				 setTimeout(function(){ 

					 if(oldVal-newVal > 0){
						 $(progressBarId+' i').removeClass(g.picto);
						 $(progressBarId+' i').addClass('fa-minus'); 
						 $(progressBarId+' i').css('left', '30%')
						 $(progressBarId+' i, '+progressBarId+' '+plusGaugeId+' p').css('color', '#990000')
					 }
					 else {
						 $(progressBarId+' i').removeClass(g.picto);
						 $(progressBarId+' i').addClass('fa-plus'); 
						 $(progressBarId+' i').css('left', '30%')
					 }
				 }, 300);

				 if(oldVal-newVal < 0){

					 setTimeout(function(){
						 sparkles(index+1);
						 $(progressBarId+' i, '+progressBarId+' '+plusGaugeId+' p').css('opacity', 1);
					 }, 600);
				 } else {
					setTimeout(function(){
						 $(progressBarId+' i, '+progressBarId+' '+plusGaugeId+' p').css('opacity', 1);
					 }, 600); 
				 }

				 setTimeout(function(){
					$(progressBarId+' i, '+progressBarId+' '+plusGaugeId+' p').css('opacity', 0.2);
				 }, 800);

				 setTimeout(function(){
					$(progressBarId+' i, '+progressBarId+' '+plusGaugeId+' p').css('opacity', 1);
				 }, 1000);



				 setTimeout(function(){
					$(progressBarId+' i, '+progressBarId+' '+plusGaugeId+' p').css('opacity', 0);
				 }, 2500);
				 setTimeout(function(){
					  $(progressBarId+' i, '+progressBarId+' '+plusGaugeId+' p').css('color', 'white')

					 if(oldVal-newVal > 0){
						 $(progressBarId+' i').removeClass('fa-minus');
						 $(progressBarId+' i').addClass(g.picto); 
						 $(progressBarId+' i').css('left', '50%')

					 }
					 else {
						 $(progressBarId+' i').removeClass('fa-plus');
						 $(progressBarId+' i').addClass(g.picto); 
						 $(progressBarId+' i').css('left', '50%')
					 }

				 }, 2800);

				 setTimeout(function(){
					 $(progressBarId+' i').css('opacity', 1);
				 }, 3100);
			}

			setTimeout(function(){
				$(progressBarId+' svg').css('transform', 'scale(1)'); // when changing size animation is over change back size and start animation of gauge
				// $(progressBarId+' i').css('font-size', '25px');
				g.bar.animate(newVal/100);
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