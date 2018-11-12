

<div class="row" id="stats">

	<div class="col-md-4">
		<div class="info-box">
			<span class="info-box-icon bg-light-blue"><i class="ion ion-ios-game-controller-b-outline"></i></span>
			<div class="info-box-content">
				<span class="info-box-text">Nombre de parties jouées</span>
				<span class="info-box-number"><?php echo $statsData["gameFinished"]; ?> <small>(<?php echo $statsData["gameLaunched"]; ?> lancées)</small></span>
			</div>
		</div>
	</div>
	
	<div class="col-md-4">
		<div class="info-box">
			<span class="info-box-icon bg-light-blue"><i class="ion ion-ios-timer-outline"></i></span>
			<div class="info-box-content">
				<span class="info-box-text">Temps moyen de jeu par partie</span>
				<span class="info-box-number"><?php echo convertSecondsInReadableInterval($statsData["gameDuration"], '', FALSE); ?></span>
			</div>
		</div>
	</div>
	
	<div class="col-md-4">
		<div class="info-box">
			<span class="info-box-icon bg-light-blue"><i class="ion ion-ios-game-controller-b-outline"></i></span>
			<div class="info-box-content">
				<span class="info-box-text">Nombre moyen de parties jouées par utilisateur</span>
				<span class="info-box-number"><?php echo round($statsData["gamePlayedPerUser"], 1); ?></span>
			</div>
		</div>
	</div>
	
	<div class="col-md-4">
		<div class="info-box">
			<span class="info-box-icon bg-light-blue"><i class="ion ion-trophy"></i></span>
			<div class="info-box-content">
				<span class="info-box-text">Pourcentage de victoire</span>
				<span class="info-box-number"><?php echo round($statsData["winLooseRatio"]*100, 1); ?><small>%</small></span>
			</div>
		</div>
	</div>
	
	<div class="col-md-4">
		<div class="info-box">
			<span class="info-box-icon bg-light-blue"><i class="ion ion-ios-skipforward-outline"></i></span>
			<div class="info-box-content">
				<span class="info-box-text">Pourcentage de parties terminées</span>
				<span class="info-box-number"><?php echo round($statsData["gameEndedRatio"]*100, 1); ?><small>%</small></span>
			</div>
		</div>
	</div>
	
</div>

