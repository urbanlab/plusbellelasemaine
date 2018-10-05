
<div class="row" id="stats">
	<div class="col-sm-12">

		<div class="row">
			<form class="form-inline">
				<div class="col-sm-6">
					<div class="title">Données</div>
					<select id="dataTypeInput" class="form-control">
						<option <?php echo $dataType == 2 ? 'selected' : ''; ?> value="2" >Utilisateurs</option>
						<option <?php echo $dataType == 3 ? 'selected' : ''; ?> value="3" >Sessions</option>
					</select>
				</div>
				<div class="col-sm-6 text-right">
					<div class="title">Plage de dates</div>
					du
					<input type="text" id="startDateInput" name="startDateInput" class="form-control" value="<?php echo $startDate; ?>">
					au 
					<input type="text" id="endDateInput" name="endDateInput" class="form-control" value="<?php echo $endDate; ?>">
					<input type="button" value="OK" class="btn btn-default btn-flat" id="validateDatesBtn" >
				</div>
			</form>
		</div>

		<div class="row" style="margin-top: 20px;">
			<div class="col-sm-12">
				<nav aria-label="Sélecteur de période pour le graphique" class="pull-right" id="navPeriod">
				  <ul class="btn-group" role="group">
					<button type="button" class="btn btn-flat btn-<?php echo $chartPeriod == 1 ? 'primary' : 'default'; ?>" value="1" <?php echo $this->session->userdata('stats_nbDays') > $this->config->item('statsMaxChartDays') ? 'disabled' : ''; ?>>Jour</button>
					<button type="button" class="btn btn-flat btn-<?php echo $chartPeriod == 2 ? 'primary' : 'default'; ?>" value="2" <?php echo $this->session->userdata('stats_nbDays') > $this->config->item('statsMaxChartWeeks') * 7 ? 'disabled' : ''; ?>>Semaine</button>
					<button type="button" class="btn btn-flat btn-<?php echo $chartPeriod == 3 ? 'primary' : 'default'; ?>" value="3">Mois</button>
				  </ul>
				</nav>
				<div id="graph" style="height: 400px;">
					<canvas id="mainChart" class="canvasChart" ></canvas>
				</div>
			</div>
		</div>
		
		<div class="row" style="margin-top: 20px;">
			<div class="col-sm-4 col-lg-3">
				<div class="box" id="boxDetails">
					<div class="box-header with-border">
						<h3 class="box-title">Détails</h3>
					</div>
					<div class="box-body">
						<span class="glyphicon glyphicon-triangle-right"></span> <a href="#" class="<?php echo $details == 1 ? 'active' : ''; ?>" data-value="1">Plateformes</a><br>
						<br>
						<span class="glyphicon glyphicon-triangle-bottom"></span> Origines Géographiques<br>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="glyphicon glyphicon-triangle-right"></span> <a href="#" class="<?php echo $details == 2 ? 'active' : ''; ?>" data-value="2">Pays</a><br>
						<?php if($dataType > 1) { ?>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="glyphicon glyphicon-triangle-right"></span> <a href="#" class="<?php echo $details == 3 ? 'active' : ''; ?>" data-value="3">Régions</a><br>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="glyphicon glyphicon-triangle-right"></span><a href="#" class="<?php echo $details == 4 ? 'active' : ''; ?>" data-value="4"> Villes</a><br>
						<?php } ?>
					</div>
				</div>
				
				<div class="text-center" id="downloadBlock">
					<a href="<?php echo base_admin_url('statistiques_utilisation/export'); ?>" class="btn btn-sm btn-flat btn-default"><i class="fa fa-fw fa-file-excel-o"></i> Export Excel</a>
				</div>
			  	
			</div>
			<div class="col-sm-8 col-lg-9">
				<div class="box">
					<div class="box-header with-border">
						<h3 class="box-title">Données</h3>
					</div>
					<div class="box-body">
						<div class="table-responsive">
							<table id="statsData" class="display compact table table-bordered table-hover">
								<thead>
								<?php
									$labelsDataType = array('', '', 'Utilisateurs', 'Sessions');
									$labelsDetails = array('Plateformes', 'Pays', 'Régions', 'Villes');
								?>
								<tr>
									<?php 
									echo '<th>'.$labelsDetails[$details - 1].'</th>';
									if ($details==3 || $details==4 )
										echo '<th>'.$labelsDetails[1].'</th>';

									switch($dataType)
									{
										case 2:
											echo '<th>Actifs</th>';
											echo '<th>Nouveaux</th>';
											echo '<th>Total</th>';
											echo '<th>% Actifs/Total</th>';
											break;
										case 3:
											echo '<th>Sessions</th>';
											echo '<th>Nombre moyen par utilisateur</th>';
											echo '<th>Durée totale</th>';
											echo '<th>Durée moyenne par session</th>';
											break;
									}
									?>
								</tr>		

								<tr class="total">
									<?php 
									echo '<th>'.$statsData[0]['label'].'</th>';
									if (isset($statsData[0]['label2']))
										echo '<th>'.$statsData[0]['label2'].'</th>';
									switch($dataType)
									{
										case 2:
											echo '<th>'.$statsData[0]['users'].'</th>';
											echo '<th>'.$statsData[0]['newUsers'].'</th>';
											echo '<th>'.$statsData[0]['total'].'</th>';
											echo '<th>'.$statsData[0]['percent'].'</th>';
											break;
										case 3:
											echo '<th>'.$statsData[0]['sessions'].'</th>';
											echo '<th>'.$statsData[0]['avgSession'].'</th>';
											echo '<th>'.displaySeconds($statsData[0]['sessionDuration']).'</th>';
											echo '<th>'.displaySeconds($statsData[0]['avgSessionDuration']).'</th>';
											break;
									}
									?>
								</tr>
								</thead>


								<tbody>
								<?php
									$cnt = count($statsData);
									for($i = 1; $i < $cnt; $i++) {
								?>
								<tr>
									<?php 
									echo '<td>'.$statsData[$i]['label'].'</td>';
									if (isset($statsData[$i]['label2']))
										echo '<td>'.$statsData[$i]['label2'].'</td>';
									switch($dataType)
									{
										case 2:
											echo '<td>'.$statsData[$i]['users'].'</td>';
											echo '<td>'.$statsData[$i]['newUsers'].'</td>';
											echo '<td>'.$statsData[$i]['total'].'</td>';
											echo '<td>'.$statsData[$i]['percent'].'</td>';
											break;
										case 3:
											echo '<td>'.$statsData[$i]['sessions'].'</td>';
											echo '<td>'.$statsData[$i]['avgSession'].'</td>';
											echo '<td>'.displaySeconds($statsData[$i]['sessionDuration']).'</td>';
											echo '<td>'.displaySeconds($statsData[$i]['avgSessionDuration']).'</td>';
											break;
									}
									?>
								</tr>
								<?php		
									}
								?>

								</tbody>

							</table>
						</div>
						
					</div>
				</div>
			</div>
		</div>
		
		
	
	</div>
	
	<div style="margin-top: 60px; clear: both;"></div>
	
	
</div>


<script type="text/javascript">
	function setCharts() {
		//return;
		var chart,  options, labels, datasets;
		
		options = jQuery.extend(true, {}, stats.mainOptions);
		options.scales.xAxes[0].time.minUnit = '<?php
			switch($chartPeriod) {
				case 1: { echo 'day'; break; }
				case 2: { echo 'week'; break; }
				case 3: { echo 'month'; break; }
			}
		?>';
		/*options.scales.xAxes[0].time.unitStepSize = <?php
			switch($chartPeriod) {
				case 1: { echo 1; break; }
				case 2: { echo 7; break; }
				case 3: { echo 1; break; }
			}
		?>;*/
		/*
		<?php //if($chartPeriod == 1) { ?>
		options.scales.xAxes[0].time.min = '<?php echo $startDate; ?>';
		options.scales.xAxes[0].time.max = '<?php echo $endDate; ?>';
		<?php //} ?>
		*/
		chart = new Chart($('#mainChart'), {
			type: 'line',
			data: {
				labels: [<?php 
					// use of index 1 of statsData, index 0 contain only total without chartData details
					for($i = 1; $i < min($cnt, 7); $i++) { 
						if (isset($statsData[$i]['chartData'])) {
							echo '\''.implode('\',\'', array_keys($statsData[$i]['chartData'])).'\''; 
							break;
						}
					}
				?>],
				datasets: [
				<?php
					$cnt = count($statsData);
					// use of index 1+ of statsData, index 0 contain only total without chartData details
					for($i = 1; $i < min($cnt, 7); $i++) { if (isset($statsData[$i]['chartData'])) {
				?>
					{
						label: "<?php echo $statsData[$i]['label']; ?>",
						data: [<?php echo implode(',', $statsData[$i]['chartData']); ?>],
						backgroundColor: stats.defaultColors[<?php echo $i-1; ?>]
					},
				<?php
					} }
				?>
				]
			},
			options: options
		});
		
	}
	
	
	// init datatable
	function setDataTable() {
		<?php
		if($details == 3 ||$details == 4) {
			$dataOrder = '[ 2, "desc" ]';
		}else{
			$dataOrder = '[ 1, "desc" ]';
		}
		?>
		$('#statsData').DataTable({
			"paging": true,
			"lengthChange": false,
			"searching": false,
			"ordering": true,
			"info": true,
			"autoWidth": false,
			"orderCellsTop": true,
			"pageLength": 20,
			"language": {
				"info":           "Page _PAGE_ / _PAGES_",
				"infoEmpty":      "Aucun résultat",
				"infoFiltered":   "(filtré sur _MAX_ lignes)",
				"infoPostFix":    "",
				"decimal": ".",
				"thousands": " ",
				"lengthMenu":     "Afficher _MENU_ lignes par page",
				"loadingRecords": "Chargement...",
				"processing":     "Traitement...",
				"search":         "Recherche:",
				"zeroRecords":    "Aucun résultat",
				"paginate": {
					"first":      "<<",
					"last":       ">>",
					"next":       ">",
					"previous":   "<"
				},
				"aria": {
					"sortAscending":  ": activer pour ordonner croissant",
					"sortDescending": ": activer pour ordonner décroissant"
				}
			},
			"order": [<?php echo $dataOrder; ?>],
			"columnDefs": [
				{
					"render": function ( data, type, row, meta ) {
						if(type === 'display') {
							var totals = [<?php 
								echo '0,';
							  	if (isset($statsData[0]['label2']))
							  		echo '0,';
								switch($dataType)
								{
									case 2: echo $statsData[0]['users'].','.$statsData[0]['newUsers'].','.$statsData[0]['total'].',0'; break;
									case 3: echo $statsData[0]['sessions'].',0,0,0'; break;
								}
							?>];
							var _percent = 0;
							if (totals[meta.col]!=0)
								_percent =  Math.round(100 * data / totals[meta.col]);
							return data +' ('+ _percent +'%)';
						}else{
							return data;
						}
					},
					"targets": <?php
						switch($dataType)
						{
							case 2: echo $details == 3 || $details == 4 ? '[2,3,4]' : '[1,2,3]'; break;
							case 3: echo $details == 3 || $details == 4 ? '[2]' : '[1]'; break;
						}
					?>
				}
				
			]
			
		});
		
	}
</script>
