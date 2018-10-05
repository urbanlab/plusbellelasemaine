var stats = {
	
	dayNames : [ "Dimanche", "Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi" ],
	dayNamesMin : [ "Di", "Lu", "Ma", "Me", "Je", "Ve", "Sa" ],
	dayNamesShort : [ "Dim", "Lun", "Mar", "Mer", "Jeu", "Ven", "Sam" ],
	monthNames: [ "Janvier", "Février", "Mars", "Avril", "Mai", "Juin", "Juillet", "Août", "Septembre", "Octobre", "Novembre", "Décembre" ],
	monthNamesShort: [ "Jan", "Fév", "Mar", "Avr", "Mai", "Juin", "Juil", "Août", "Sep", "Oct", "Nov", "Déc" ],
	dateFormat : "dd/mm/yy",
	
	// chart.js configs
	defaultColors : ["#0D6FFF", "#0CE8DE", "#00FF43", "#7FE80C", "#FFE50D", "#FFAD0D", "#E8560C", "#FF000E", "#C70CE8", "#3A0DFF"],
	mainOptions: {
		legend: {
			position: 'bottom',
			labels: {
				boxWidth: 20
			}
		},
		tooltips: {
			mode: 'index',
			intersect: false,
			position: 'nearest',
			cornerRadius: 0,
			/*callbacks: {
				title: function(item, data) {
					var lbl = data.labels[item[0].index];
					return lbl;
				},
				label: function(item, data) {
					var val = data.datasets[item.datasetIndex].data[item.index];
					var lbl = data.labels[item.index];
					var tot = 0;
					$.each(data.datasets[item.datasetIndex].data, function(index, value) {
						tot += value;
					});
					//return [lbl, (Math.round(100*val/tot)) + '% ('+val+')'];
					return (Math.round(100*val/tot)) + '% ('+val+')';
				}
			}*/
		},
		maintainAspectRatio: false,
		layout: {
            padding: {
                left: 0,
                right: 0,
                top: 0,
                bottom: 30
            }
        },
		scales: {
			yAxes: [{
				stacked: true
			}],
			xAxes: [{
				type: 'time',
				time: {
					parser:'DD/MM/YYYY',
					minUnit: 'day',
					isoWeekday: true,
					displayFormats: {
						'millisecond': 'DD/MM/YYYY',
						'second': 'DD/MM/YYYY',
						'minute': 'DD/MM/YYYY',
						'hour': 'DD/MM/YYYY',
						'day': 'DD/MM/YYYY',
						'week': 'DD/MM/YYYY',
						'month': 'MM/YYYY',
						'quarter': 'MMM YYYY',
						'year': 'YYYY',
					}
				}
			}]
		}
	},
	// \chart.js configs
	
	 
	init : function() {
		
		// init datepickers
		var datepickerOptions = {
			defaultDate: null, //"+1w",
			changeMonth: true,
			numberOfMonths: 1,
			dayNames: stats.dayNames,
			dayNamesShort: stats.dayNamesShort,
			dayNamesMin: stats.dayNamesMin,
			monthNames: stats.monthNames,
			monthNamesShort: stats.monthNamesShort,
			firstDay: 1,
			dateFormat: stats.dateFormat,
			maxDate: "-1d"
		};
		
		var from = $( "#startDateInput" ).datepicker(datepickerOptions);
		var to = $( "#endDateInput" ).datepicker(datepickerOptions);
		from.on( "change", function() {
			to.datepicker( "option", "minDate", stats.getDate( this ) );
			$('#validateDatesBtn').removeClass('btn-default').addClass('btn-primary');
		});
		to.on( "change", function() {
			from.datepicker( "option", "maxDate", stats.getDate( this ) );
			$('#validateDatesBtn').removeClass('btn-default').addClass('btn-primary');
		});
		
		$('#validateDatesBtn').click(function(e) {
			e.stopImmediatePropagation();
			$('#validateDatesBtn').removeClass('btn-primary').addClass('btn-default');
			$.post(
				CI.base_url + "admin/statistiques_utilisation/setDateRange",
				{ startDate: $( "#startDateInput" ).val(), endDate: $( "#endDateInput" ).val() },
				function() {
					stats.showLoading();
					location.reload();
				}
			);
		});
		$('#dataTypeInput').change(function(e) {
			e.stopImmediatePropagation();
			$.post(
				CI.base_url + "admin/statistiques_utilisation/setDataType",
				{ dataType: $( "#dataTypeInput" ).val() },
				function() {
					stats.showLoading();
					location.reload();
				}
			);
		});
		$('#boxDetails a').click(function(e) {
			e.stopImmediatePropagation();
			$.post(
				CI.base_url + "admin/statistiques_utilisation/setDetails",
				{ details: $(this).data('value') },
				function() {
					stats.showLoading();
					location.reload();
				}
			);
		});
		$('#navPeriod button').click(function(e) {
			e.stopImmediatePropagation();
			$.post(
				CI.base_url + "admin/statistiques_utilisation/setChartPeriod",
				{ chartPeriod: $(this).val() },
				function() {
					stats.showLoading();
					location.reload();
				}
			);
		});
		
		
		
		// resize charts on window resize
		/*$(window).smartresize(function(){
			stats.resizeCharts();
		});
		
		stats.resizeCharts();
		*/
		
		// init chart
		//Chart.defaults.global.legend.position = 'bottom';
		//Chart.defaults.global.legend.labels.boxWidth = 20;
		
		
	},
	
	showLoading : function() {
		$( "body" ).append( "<div class='loader'></div>" );
	},
	

	getDate : function( element ) {
		var date;
		try {
			date = $.datepicker.parseDate( stats.dateFormat, element.value );
		} catch( error ) {
			date = null;
		}

		return date;
    },
	
	resizeCharts: function() {
		$('.canvasChart').each(function(index, element) {
			var h = $(element).width() / 4 * 3;
			h = h > 200 ? 200 : h;
			//console.log($(element).width());
			//console.log(h);
			$(element).css('height', h+'px');
		});
	}
	
};