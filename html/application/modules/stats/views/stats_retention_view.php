
<div class="row" id="stats">
	<div class="col-sm-12">

		<div class="row">
			<div class="col-sm-12">
				<div class="box">
					<div class="box-header with-border">
						<h3 class="box-title">Par jour</h3>
					</div>
					<div class="box-body text-center">
						<canvas id="chart0" class="" style="width:100%;" height="200"></canvas>

						<table class="display compact table table-bordered">
						  	<tbody>
							<tr>
								<?php 
									foreach($statsData['chart0'] as $val) 
							  			echo '<td>'.$val[0].'% ('.$val[1].')</td>';
								?>
							</tr>
							</tbody>
						  </table>

					</div>

				</div>


				
			</div>
		</div>


	
		<div class="row">
			<div class="col-sm-12">
				<div class="box">
					<div class="box-header with-border">
						<h3 class="box-title">Par semaine</h3>
					</div>
					<div class="box-body text-center">
						<canvas id="chart1" class="" style="width:100%;" height="200"></canvas>

						<table class="display compact table table-bordered">
						  	<tbody>
							<tr>
								<?php 
									foreach($statsData['chart1'] as $val) 
							  			echo '<td>'.$val[0].'% ('.$val[1].')</td>';
								?>
							</tr>
							</tbody>
						  </table>			

					</div>
				</div>
				
			</div>
		</div>
	
		<div class="row">
			<div class="col-sm-12">
				<div class="box">
					<div class="box-header with-border">
						<h3 class="box-title">Par mois</h3>
					</div>
					<div class="box-body text-center">
						<canvas id="chart2" class="" style="width:100%;" height="200"></canvas>
						
						<table class="display compact table table-bordered">
						  	<tbody>
							<tr>
								<?php 
									foreach($statsData['chart2'] as $val) 
							  			echo '<td>'.$val[0].'% ('.$val[1].')</td>';
								?>
							</tr>
							</tbody>
						  </table>			

					</div>
				</div>
				
			</div>
		</div>
	
	</div>
	
	<div style="margin-top: 60px; clear: both;"></div>
	
	
</div>

 
<script type="text/javascript">
	function setCharts() 
	{
		var chart;
		var labels;
		var opts;

		//------------------
		<?php
		for($i=0;$i<3;$i++) {
		?>
			labels = [<?php foreach($statsData['chart'.$i] as $key=>$val) { echo "'".$key."',"; } ?>];
			opts = jQuery.extend(true, {}, stats.defaultOptions);

			opts.legend = { display : false };

			chart = new Chart($(<?php echo "'#chart".$i."'";?>), 
			{
				type: 'line',
				data: {
					labels: labels,
					datasets: [
					{
						fill: false,
						borderColor: "rgba(0,0,0,0.3)",
	        			data: [<?php foreach($statsData['chart'.$i] as $key=>$val) { echo "'".$val[0]."',"; } ?>]
	        		}]
				},
				options: opts
			});
		<?php }	?>
	}
</script>

