<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Admin_stats extends Admin_Controller {
	
	private $mainModel;
	
	
	function __construct()
    {
		parent::__construct();
		
		$this->load->model('stats/stats_model');
		$this->mainModel = $this->stats_model;
	}
	
	public function index() {
		/*if($this->session->userdata('stats_subscriptionType') === FALSE) {
			$this->session->set_userdata('stats_subscriptionType', NULL);
		}
		if($this->session->userdata('stats_userType') === FALSE) {
			$this->session->set_userdata('stats_userType', NULL);
		}*/
		
		redirect(base_admin_url('statistiques_utilisation'));
	}
	
	public function retention() {
		$this->viewRetention();
	}
	
	public function specific() {
		$this->viewSpecific();
	}
	
	public function usage() {
		$this->viewUsage();
	}
	
	
	public function usage_export() {
		$this->load->helper('date');
		
		// get data
		$filters = $this->_getUsageFilters();
		$statsData = $this->_getUsageData($filters['dataType'], $filters['startDate'], $filters['endDate'], $filters['chartPeriod'], $filters['details']);
		
		// generate Excel
		//$this->load->library('excel');
		//$this->load->library('exceliofactory');
		//$this->excel = new PHPExcel();
		//$this->excel = Exceliofactory::createReader('Excel2007');
		//$this->excel = $this->excel->load('tpl/export_usage_downloads_tpl.xlsx');  
		$this->excel = PhpOffice\PhpSpreadsheet\IOFactory::load('tpl/export_usage_downloads_tpl.xlsx');
		
		
		// ## fisrt sheet > donnees
		$this->excel->setActiveSheetIndex(0);  
		$as = $this->excel->getActiveSheet();
		
		// export date
		$as->setCellValueByColumnAndRow(1, 3, 'Export du '.date('d/m/Y'));
		
		// write filters
		switch($filters['dataType']) {
			case 2: $dataTypeLabel = 'Utilisateurs'; break;
			case 3: $dataTypeLabel = 'Sessions'; break;
		}
		switch($filters['details']) {
			case 1: $detailsLabel = 'Plateformes'; break;
			case 2: $detailsLabel = 'Pays'; break;
			case 3: $detailsLabel = 'Régions'; break;
			case 4: $detailsLabel = 'Villes'; break;
		}
		$as->setCellValueByColumnAndRow(1, 5, 'Données : '.$dataTypeLabel)
			->setCellValueByColumnAndRow(1, 6, 'Plage de dates : '.$filters['startDate'].' au '.$filters['endDate'])
			->setCellValueByColumnAndRow(1, 7, 'Détails : '.$detailsLabel);
		
		// data pos
		$col = 1;
		$row = 10;
		$offset = 0;

		// sub details
		$label2 = $filters['details']==3 || $filters['details']==4;
	  	if ($label2)
	  	{
	  		$as->insertNewColumnBeforeByIndex($col+2, 1); 
	  		$as->setCellValueByColumnAndRow($col+1, $row-1, "Pays");
			$as->getColumnDimensionByColumn($col+1)->setAutoSize(true);
	  		$offset=1;
	  	}


		// write table header
		$as->setCellValueByColumnAndRow(1, $row-1, $detailsLabel);
		switch($filters['dataType']) {
			case 2: 
				// colonnes utilisateurs 
				$as->insertNewColumnBeforeByIndex($col+$offset+2, 2); 
				$as->setCellValueByColumnAndRow($col+$offset+1, $row-1, "Actifs");
				$as->getColumnDimensionByColumn($col+$offset+1)->setAutoSize(true);
				$as->setCellValueByColumnAndRow($col+$offset+2, $row-1, "Nouveaux");
				$as->getColumnDimensionByColumn($col+$offset+2)->setAutoSize(true);
				$as->setCellValueByColumnAndRow($col+$offset+3, $row-1, "Total");
				$as->getColumnDimensionByColumn($col+$offset+3)->setAutoSize(true);
				$as->setCellValueByColumnAndRow($col+$offset+4, $row-1, "% Actifs/Total");
				$as->getColumnDimensionByColumn($col+$offset+4)->setAutoSize(true);
				break;
			case 3: 
				// colonnes sessions
				$as->insertNewColumnBeforeByIndex($col+$offset+2, 2); 
				$as->setCellValueByColumnAndRow($col+$offset+1, $row-1, "Sessions");
				$as->getColumnDimensionByColumn($col+$offset+1)->setAutoSize(true);
				$as->setCellValueByColumnAndRow($col+$offset+2, $row-1, "Nombre moyen par utilisateur");
				$as->getColumnDimensionByColumn($col+$offset+2)->setAutoSize(true);
				$as->setCellValueByColumnAndRow($col+$offset+3, $row-1, "Durée totale");
				$as->getColumnDimensionByColumn($col+$offset+3)->setAutoSize(true);
				$as->setCellValueByColumnAndRow($col+$offset+4, $row-1, "Durée moyenne par session");
				$as->getColumnDimensionByColumn($col+$offset+4)->setAutoSize(true);
				break;
		}
		
		
		// write data
		
		//$style1 = $as->getStyleByColumnAndRow(0, $row+1);
		//$style2 = $as->getStyleByColumnAndRow(1, $row+1);
		$as->insertNewRowBefore($row+2, count($statsData) - 2); 
		//$as->duplicateStyle($style1, 'A'.($row + 2).':A'.($row + count($statsData) - 2));
		//$as->duplicateStyle($style2, 'B'.($row + 2).':C'.($row + count($statsData) - 2));
		
		switch($filters['dataType']) {
			case 2: 
				// colonnes utilisateurs 
				foreach($statsData as $entry) {
					$as->setCellValueByColumnAndRow($col, $row, $entry['label'])
						->setCellValueByColumnAndRow($col+$offset+1, $row, $entry['users'],null,\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC)
						->setCellValueByColumnAndRow($col+$offset+2, $row, $entry['newUsers'],null,\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC)
						->setCellValueByColumnAndRow($col+$offset+3, $row, $entry['total'])
						->setCellValueByColumnAndRow($col+$offset+4, $row, $entry['percent']);

					if ($label2)
						$as->setCellValueByColumnAndRow($col+1, $row, $entry['label2']);
					$row++;
				}
				break;
			case 3: 
				// colonnes sessions
				foreach($statsData as $entry) {
					$as->getStyleByColumnAndRow($col+$offset+2, $row)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_00);

					$as->setCellValueByColumnAndRow($col, $row, $entry['label'])
						->setCellValueByColumnAndRow($col+$offset+1, $row, $entry['sessions'],null,\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC)
						->setCellValueByColumnAndRow($col+$offset+2, $row, $entry['avgSession'],null,\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC)
						->setCellValueByColumnAndRow($col+$offset+3, $row, displaySeconds($entry['sessionDuration']))
						->setCellValueByColumnAndRow($col+$offset+4, $row, displaySeconds($entry['avgSessionDuration']),\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);

					if ($label2)
						$as->setCellValueByColumnAndRow($col+1, $row, $entry['label2']);
					$row++;
				}
				break;
		}
		
		
		// ## 2nd sheet > donnees_graph
		$this->excel->setActiveSheetIndex(1);    
		$as = $this->excel->getActiveSheet();
				
		// write filter
		switch($filters['chartPeriod']) {
			case 1: $chartPeriodLabel = 'Jour'; break;
			case 2: $chartPeriodLabel = 'Semaine'; break;
			case 3: $chartPeriodLabel = 'Mois'; break;
		}
		$as->setCellValueByColumnAndRow(1, 1, 'Périodicité : '.$chartPeriodLabel);

		
		$cnt = count($statsData);
		$col = 1;
		$row = 4;
		$as->insertNewRowBefore($row+1, count($statsData[1]['chartData']) - 1);
		foreach($statsData[1]['chartData'] as $key => $value) {
			$as->setCellValueByColumnAndRow($col, $row, $key);
			$row++;
		}
		$col++;
		for($i = 1; $i < $cnt; $i++) {
			$row = 4;
			$as->setCellValueByColumnAndRow($col, $row-1, $statsData[$i]['label']);
			
			foreach($statsData[$i]['chartData'] as $key => $value) {
				$as->setCellValueByColumnAndRow($col, $row, $value);
				$row++;
			}
			
			$colLetter = $as->getHighestColumn(4);
			$as->getColumnDimension($colLetter)->setAutoSize(true);
			
			if($i < $cnt - 1) {
				$colLetter++;
				$as->insertNewColumnBefore($colLetter, 1);
			}
			
			$col++;
		}
				
		
		// set file to first sheet
		$this->excel->setActiveSheetIndex(0);  
		
		
		// export file
		$filename = '';
		switch($filters['dataType']) {
			case 2: $filename .= 'utilisateurs_'; break;
			case 3: $filename .= 'sessions_'; break;
		}
		switch($filters['details']) {
			case 1: $filename .= 'plateformes_'; break;
			case 2: $filename .= 'pays_'; break;
			case 3: $filename .= 'regions_'; break;
			case 4: $filename .= 'villes_'; break;
		}
		$filename .= date('Ymd-His').'.xlsx';
		
		
		//force user to download the Excel file without writing it to server's HD
		//header('Content-Type: application/vnd.ms-excel'); //mime type
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); //mime type
		header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
		header('Cache-Control: max-age=0'); //no cache
		$writer = PhpOffice\PhpSpreadsheet\IOFactory::createWriter($this->excel, 'Xlsx');
		$writer->save('php://output');
	}
	
	public function usage_setChartPeriod() {
		$this->session->set_userdata('stats_chartPeriod', intval($this->input->post('chartPeriod')));
	}
	public function usage_setDataType() {
		$this->session->set_userdata('stats_dataType', intval($this->input->post('dataType')));
	}
	public function usage_setDateRange() {
		$this->session->set_userdata('stats_startDate', $this->input->post('startDate'));
		$this->session->set_userdata('stats_endDate', $this->input->post('endDate'));
		
		$this->_validateChartPeriod();
	}
	public function usage_setDetails() {
		$this->session->set_userdata('stats_details', intval($this->input->post('details')));
	}
	
	
	public function viewRetention($error = NULL) {
		

		$statsData = $this->_getRetentionData();
		
		$data = array(
			'currentSection' => 'stats',
			'currentSubSection' => 'retention',
			'view_file' => 'stats/stats_retention_view',
        	'page_title' => 'Taux de rétention',
			//'page_meta_description' => $pageDescription,
			'additionnalCssFiles' => array('jqueryui', 'datatables', 'stats'),
			'additionnalJsFiles' => array('jqueryui', 'chartjs', 'datatables', 'stats'),
			'additionnalJsCmd_wready' => array('stats.init();', 'setCharts();'),
        	//$data['additionnalJsCmd_wload'] = array('classe.init()';
			//$data['additionnalJsCmd_wscroll'] = array('classe.init()';
			//$data['additionnalJsCmd_wresize'] = array('classe.init()';
			
			'mainTitle' => 'Taux de rétention',
			'breadcrumb' => array(
				'Statistiques ' => '',
				'taux de rétention' => ''
			),
			
			'statsData' => $statsData,
			//'subscriptionType' => $subscriptionType,
			//'userType' => $userType,
			
		);
        echo Modules::run('template/adm/_default', $data);
    }
	
	public function viewSpecific($error = NULL) {
		$this->load->helper('date');
		
		$statsData = $this->_getSpecificData();

		$data = array(
			'currentSection' => 'stats',
			'currentSubSection' => 'specific',
			'view_file' => 'stats/stats_specific_view',
        	'page_title' => 'Statistiques spécifiques',
			//'page_meta_description' => $pageDescription,
			'additionnalCssFiles' => array('stats'),
			'additionnalJsFiles' => array('stats'),
			'additionnalJsCmd_wready' => array('stats.init();'),
        	//$data['additionnalJsCmd_wload'] = array('classe.init()';
			//$data['additionnalJsCmd_wscroll'] = array('classe.init()';
			//$data['additionnalJsCmd_wresize'] = array('classe.init()';
			
			'mainTitle' => 'Statistiques spécifiques',
			'breadcrumb' => array(
				'Statistiques ' => '',
				'spécifiques' => ''
			),
			
			'statsData' => $statsData,
			
		);
        echo Modules::run('template/adm/_default', $data);
    }
	
	public function viewUsage($error = NULL) {
		$this->_validateChartPeriod();
		$filters = $this->_getUsageFilters();
		
		$statsData = $this->_getUsageData($filters['dataType'], $filters['startDate'], $filters['endDate'], $filters['chartPeriod'], $filters['details']);
		
		$data = array(
			'currentSection' => 'stats',
			'currentSubSection' => 'usage',
			'view_file' => 'stats/stats_usage_view',
        	'page_title' => 'Statistiques d\'utilisation',
			//'page_meta_description' => $pageDescription,
			'additionnalCssFiles' => array('jqueryui', 'datatables', 'stats'),
			'additionnalJsFiles' => array('jqueryui', 'chartjs', 'datatables', 'underscore', 'stats'),
			'additionnalJsCmd_wready' => array('stats.init();', 'setCharts();', 'setDataTable();'),
        	//$data['additionnalJsCmd_wload'] = array('classe.init()';
			//$data['additionnalJsCmd_wscroll'] = array('classe.init()';
			//$data['additionnalJsCmd_wresize'] = array('classe.init()';
			
			'mainTitle' => 'Statistiques d\'utilisation',
			'breadcrumb' => array(
				'Statistiques ' => '',
				'd\'utilisation' => ''
			),
			
			'statsData' => $statsData,
			//'subscriptionType' => $subscriptionType,
			//'userType' => $userType,
			
			'dataType' => $filters['dataType'],
			'startDate' => $filters['startDate'],
			'endDate' => $filters['endDate'],
			'chartPeriod' => $filters['chartPeriod'],
			'details' => $filters['details'],
			
		);
        echo Modules::run('template/adm/_default', $data);
    }
	
	
	

	// récupération des infos Google Analytics
	public function _getAppUsersData($startDate, $endDate, $chartPeriod, $details, $section) 
	{
		// Chargement de la library Google Analytics Reporting
		$this->load->library('GAReporting');
		if ($section==2)
			$this->gareporting->init($this->config->item('googleAnalyticsViewId2'));
		else
			$this->gareporting->init($this->config->item('googleAnalyticsViewId'));
		$this->load->helper('date');

		$startDate = DateTime::createFromFormat('d/m/Y', $startDate);
		$endDate = DateTime::createFromFormat('d/m/Y', $endDate);

		// conversion des dates au format Google Analytics pour les requêtes
		$startGa = $startDate->format('Y-m-d');
		$endGa = $endDate->format('Y-m-d');

		// dimension choisie pour le filtrage 
		// on peut filtrer sur une dimension secondaire pour différencier villes/régions de pays différents par ex
		$dimensions = array();
		if ($details==2)
		{
			$dimensions[] = "country";
		}
		else if ($details==3)
		{
			$dimensions[] = "region";
			$dimensions[] = "country";
		}
		else if ($details==4)
		{
			$dimensions[] = "city";
			$dimensions[] = "country";
		}
		else
		{
			// plateform
			$dimensions[] = "operatingSystem";
		}
		$filter_end = sizeof($dimensions);


		// si besoin, récupération du nombre total d'utilisateurs depuis le début du jeu pour afficher les totaux
		// les données sont réparties en fonction de la dimension de filtrage choisie
		if ($section==0)
		{
			$totals = array();
			$results = $this->gareporting->request(array('users'), $dimensions, $this->config->item('googleAnalyticsStatsStart'), $endGa);

			// on gère les eventuels compléments sur le filtrage (Régions/Pays)
			foreach($results as $result) 
			{
				$key = $result[0];
				for($i=1;$i<$filter_end;$i++)
					$key.="-".$result[$i];
				$totals[$key]=$result[$filter_end];
			}
			//pr($totals);
		}


		// toutes les données seront un array temporaire indexé par la dimension choisie
		$tmp = array();


		////////////////////////////////////////////////////////////////////////////
		// d'abord on fait une requête pour les données du graph
		// ces données sont découpées sur la dimension choisie mais aussi sur une dimension temps

		// init chartdata array
		// il s'agit d'un tableau initialisé à 0 pour toutes les tranches de temps souhaités
		$start = clone $startDate;
		$end = clone $endDate;
		$chartData = array();
		switch($chartPeriod) {
			case 1: { // by days
				while($start <= $end) {
					$chartData[$start->format('d/m/Y')] = 0;
					$start->add(new DateInterval('P1D'));
				}
				break;
			}
			case 2: { // by weeks
				while($start <= $end) {
					$chartData[getFirstdayOfWeekFromDateTime($start, 'd/m/Y')] = 0;
					$start->add(new DateInterval('P1D'));
				}
				break;
			}
			case 3: { // by months
				while($start <= $end) {
					$chartData[$start->format('01/m/Y')] = 0;
					$start->add(new DateInterval('P1D'));
				}
				
				break;
			}
		}


		// on rajoute la dimension lié à l'échelle de temps pour avoir les bonnes courbes
		if ($chartPeriod==3)
			$dimensions[] = "nthMonth"; // by months
		else if ($chartPeriod==2)
			$dimensions[] = "nthWeek"; // by weeks
		else
			$dimensions[] = "nthDay"; // by days
		$time_start = sizeof($dimensions)-1;
		$metrics_start = sizeof($dimensions);


		// récupérations des données souhaitées sur Google Analytics
		$metrics = array();
		if ($section==2)
		{
			// tous les joueurs ayant répondu à une notification
			$metrics = array('totalEvents');
			$metricsfilter = array(array('eventCategory', 'EXACT', array('notification')));
			$results = $this->gareporting->request($metrics, $dimensions, $startGa, $endGa, $metricsfilter);
		}
		else
		{
			if ($section==1)
				$metrics = array('sessions');
			else
				$metrics = array('users');
			$results = $this->gareporting->request($metrics, $dimensions, $startGa, $endGa);
		}

		//pr($results);

		// on récupère et structure les données dans le tableau temporaire
		foreach($results as $result) 
		{
			// l'index correspond à la dimension choisie (ou combinaison de deux dimensions, comme ville/pays)
			$key = $result[0];
			for($i=1;$i<$filter_end;$i++)
				$key.="-".$result[$i];

			if(array_key_exists($key, $tmp) === FALSE) 
			{
				$tmp[$key] = array(
					'chartData' => array_merge(array(), $chartData)
				);

				// mise en place du label à l'initialisation de cette courbe
				// label = dimension pour le filtrage
				$tmp[$key]['label'] = $result[0];

				// cas particulier: plateforme (not set) = ios
				//if ($details==1 && $result[0]=="(not set)")
				//	$tmp[$key]['label'] = "iOS";

				for($i=1;$i<$filter_end;$i++)
					$tmp[$key]['label'.($i+1)] = $result[$i];
			}

			// ajout des données issues de Google Analytics
			$date = array_keys($chartData)[intval($result[$time_start])];
			$tmp[$key]['chartData'][$date] += floatval($result[$metrics_start]);
		}
		
		//pr($tmp);


		////////////////////////////////////////////////////////////////////////////
		// puis on fait une requête pour le tableau de données
		// ces données sont découpées sur la dimension choisie mais globales sur la période
		
		// on retire le filtrage sur le temps
		array_pop($dimensions);
		$metrics_start = sizeof($dimensions);


		// récupérations des données souhaitées sur Google Analytics
		$metrics = array();
		if ($section==2)
		{
			// d'abord on récupère tous les joueurs sur cette période
			$metrics = array('users');
			$result1 = $this->gareporting->request($metrics, $dimensions, $startGa, $endGa);

			// puis tous les joueurs ayant répondu à une notification
			$metrics = array('totalEvents', 'users');
			$metricsfilter = array(array('eventCategory', 'EXACT', array('notification')));
			$result2 = $this->gareporting->request($metrics, $dimensions, $startGa, $endGa, $metricsfilter);

			// et on rassemble les deux, attention il faut mettre totalEvents en premier pour le graph
			$metrics = array('totalEvents', 'users', 'eventUsers');
			$results = array();
			foreach($result1 as $result) 
			{
				$key = $result[0];
				for($i=1;$i<$filter_end;$i++)
					$key.="-".$result[$i];
				$results[$key] = $result;

				// on initialise totalEvents et eventUsers
				$results[$key][$metrics_start+0] = 0;
				$results[$key][$metrics_start+1] = $result[$metrics_start+0];
				$results[$key][$metrics_start+2] = 0;
			}
			foreach($result2 as $result) 
			{
				$key = $result[0];
				for($i=1;$i<$filter_end;$i++)
					$key.="-".$result[$i];

				$results[$key][$metrics_start+0] += $result[$metrics_start+0];
				$results[$key][$metrics_start+2] += $result[$metrics_start+1];
			}
		}
		else
		{
			if ($section==1)
				$metrics = array('sessions','sessionDuration','avgSessionDuration', 'users');
			else
				$metrics = array('users','newUsers');
			$results = $this->gareporting->request($metrics, $dimensions, $startGa, $endGa);
		}


		// si on a pas de donnée, on rempli le graph de données et le tableau pour éviter un problème d'affichage
		if (sizeof($tmp)==0)
		{
			$key = "pas de données";
			$tmp[$key] = array(
				'chartData' => array_merge(array(), $chartData)
			);

			// label = dimension pour le filtrage
			$tmp[$key]['label'] = $key;
			for($i=1;$i<$filter_end;$i++)
				$tmp[$key]['label'.($i+1)] = "";

			// données nulles
			for($i=0;$i<sizeof($metrics);$i++)
				$tmp[$key][$metrics[$i]] = 0;
		}


		//pr($results);

		// on rajoute les données dans le tableau temporaire indexé par la dimension
		foreach($results as $result) 
		{
			// l'index correspond à la dimension choisie (ou combinaison de deux dimensions, comme ville/pays
			$key = $result[0];
			for($i=1;$i<$filter_end;$i++)
				$key.="-".$result[$i];

			// création d'une nouvelle ligne dans le tableau si nécessaire
			if(array_key_exists($key, $tmp) === FALSE)
			{
				$tmp[$key] = array();

				// mise en place du label à l'initialisation de cette courbe
				// label = dimension pour le filtrage
				$tmp[$key]['label'] = $result[0];

				// cas particulier: plateforme (not set) = ios
				//if ($details==1 && $result[0]=="(not set)")
				//	$tmp[$key]['label'] = "iOS";

				for($i=1;$i<$filter_end;$i++)
					$tmp[$key]['label'.($i+1)] = $result[$i];				
			}

			if(array_key_exists($metrics[0], $tmp[$key]) === FALSE) 
			{
				// les données issues de Google Analytics sont indexées par leur identifiant
				for($i=0;$i<sizeof($metrics);$i++)
					$tmp[$key][$metrics[$i]] = 0;
			}

			// ajout des données issues de Google Analytics
			for($i=0;$i<sizeof($metrics);$i++)
			{
				$tmp[$key][$metrics[$i]] += floatval($result[$metrics_start+$i]);
			}
		}

		//pr($tmp);

		// puis on remet les données dans un tableau non associatif en rajoutant les données calculées
		$ret = array();
		foreach($tmp as $key => $value) 
		{
			// parties calculées spécifiques à chaque section
			switch($section)
			{
				case 0:
					// récupération du nombre total d'utilisateur depuis le début du jeu si néssaire
					$total = 0;
					if (isset($totals[$key]))
						$total = $totals[$key];
					$value["total"] = $total;

					$percent = ($total>0)?(Round(100*$value["users"]/$total)):0;
					$value["percent"] = "".$percent."%";
				break;
				case 1:
					$value["avgSession"] = ($value["users"]>0)?(0.01*Round(100*$value["sessions"]/$value["users"])):0;
					$value["avgSessionDuration"] = 0.01*Round(100*$value["avgSessionDuration"]);
				break;
				case 2:
					$value["percent"] = ($value["users"]>0)?(Round(100*$value["eventUsers"]/$value["users"])):0;
					$value["avg"] = ($value["eventUsers"]>0)?(0.01*Round(100*$value["totalEvents"]/$value["eventUsers"])):0;
				break;
			}

			$ret[] = $value;
		}

		// tri
		switch($section)
		{
			case 2:
				function cmp($a, $b) {
					return $a["totalEvents"] < $b["totalEvents"];
				}
			break;
			case 1:
				function cmp($a, $b) {
					return $a["sessions"] < $b["sessions"];
				}
			break;
			case 0:
				function cmp($a, $b) {
					return $a["users"] < $b["users"];
				}
			break;
		}
		usort($ret, "cmp");
		

		////////////////////////////////////////////////////////////////////////////
		// puis on fait une requête pour la première ligne du tableau de données
		// on ne peut pas faire la somme des lignes, les données seront fausses
		
		$dimensions = array();
		$metrics_start = sizeof($dimensions);

		// si besoin, récupération du nombre total d'utilisateurs depuis le début du jeu pour afficher les totaux
		// là non plus on ne peut pas faire de somme des lignes
		if ($section==0)
		{
			$results = $this->gareporting->request(array('users'), $dimensions, $this->config->item('googleAnalyticsStatsStart'), $endGa);
			$total = 0;
			foreach($results as $result)
				$total = $results[0][0];
		}


		if ($section==2)
		{
			// d'abord on récupère tous les joueurs sur cette période
			$metrics = array('users');
			$result1 = $this->gareporting->request($metrics, $dimensions, $startGa, $endGa);

			// puis tous les joueurs ayant répondu à une notification
			$metrics = array('totalEvents', 'users');
			$metricsfilter = array(array('eventCategory', 'EXACT', array('notification')));
			$result2 = $this->gareporting->request($metrics, $dimensions, $startGa, $endGa, $metricsfilter);

			// et on rassemble les deux, attention il faut mettre totalEvents en premier pour le graph
			$metrics = array('totalEvents', 'users', 'eventUsers');
			$results = array();
			foreach($result1 as $result) 
			{
				$results[] = $result;

				// on initialise totalEvents et eventUsers
				$results[0][$metrics_start+0] = 0;
				$results[0][$metrics_start+1] = $result[$metrics_start+0];
				$results[0][$metrics_start+2] = 0;
			}
			foreach($result2 as $result) 
			{
				$results[0][$metrics_start+0] += $result[$metrics_start+0];
				$results[0][$metrics_start+2] += $result[$metrics_start+1];
			}			
		}
		else
		{
			$results = $this->gareporting->request($metrics, $dimensions, $startGa, $endGa);
		}


		// la première ligne contient les totaux
		$firstrow = array('total'=>0);

		// label = dimension pour le filtrage
		$firstrow['label'] = "Total";
		for($i=1;$i<$filter_end;$i++)
			$firstrow['label'.($i+1)] = "";

		// init des données au cas où on en récupère pas du tout
		for($i=0;$i<sizeof($metrics);$i++)
			$firstrow[$metrics[$i]] = 0;

		// récupération des données metrics
		foreach($results as $result) 
		{
			for($i=0;$i<sizeof($metrics);$i++)
				$firstrow[$metrics[$i]] = floatval($result[$i]);
		}

		// parties calculées spécifiques à chaque section
		switch($section)
		{
			case 0:
				$firstrow["total"] = $total;
				$percent = ($firstrow["total"]>0)?(Round(100*$firstrow["users"]/$firstrow["total"])):0;
				$firstrow["percent"] = "".$percent."%";
			break;
			case 1:
				$firstrow["avgSession"] = ($firstrow["users"]>0)?(0.01*Round(100*$firstrow["sessions"]/$firstrow["users"])):0;
				$firstrow["avgSessionDuration"] = 0.01*Round(100*$firstrow["avgSessionDuration"]);
			break;
			case 2:
				$percent = ($firstrow["users"]>0)?(Round(100*$firstrow["eventUsers"]/$firstrow["users"])):0;
				$firstrow["percent"] = $percent;
				$avg = ($firstrow["eventUsers"]>0)?(0.01*Round(100*$firstrow["totalEvents"]/$firstrow["eventUsers"])):0;
				$firstrow["avg"] = $avg;
			break;
		}

		array_unshift($ret, $firstrow);

		//pr($ret);
		return $ret;
	}
	
	public function _getUsageData($dataType, $startDate, $endDate, $chartPeriod, $details) {
		
		switch($dataType) {
			case 2: { // app users via google analytics
				$data = $this->_getAppUsersData($startDate, $endDate, $chartPeriod, $details, 0);
				break;
			}
			case 3: { // app sessions via google analytics
				$data = $this->_getAppUsersData($startDate, $endDate, $chartPeriod, $details, 1);
				break;
			}
		}
		
		return $data;
	}
	
	public function _getUsageFilters() {
		if($this->session->userdata('stats_dataType')) {
			$dataType = intval($this->session->userdata('stats_dataType'));
		}else{
			$dataType = 2;
			$this->session->set_userdata('stats_dataType', $dataType);
		}
		
		if($this->session->userdata('stats_startDate')) {
			$startDate = $this->session->userdata('stats_startDate');
		}else{
			$startDate = (new DateTime('yesterday'))
			  ->modify('first day of this month')
			  ->format('d/m/Y');
			$this->session->set_userdata('stats_startDate', $startDate);
		}
		
		if($this->session->userdata('stats_endDate')) {
			$endDate = $this->session->userdata('stats_endDate');
		}else{
			$endDate = (new DateTime('yesterday'))
			  ->modify('last day of this month')
			  ->format('d/m/Y');
			$this->session->set_userdata('stats_endDate', $endDate);
		}
		
		if($this->session->userdata('stats_chartPeriod')) {
			$chartPeriod = intval($this->session->userdata('stats_chartPeriod'));
		}else{
			$chartPeriod = 1;
			$this->session->set_userdata('stats_chartPeriod', $chartPeriod);
		}
		
		if($this->session->userdata('stats_details') ) {
			$details = intval($this->session->userdata('stats_details'));
		}else{
			$details = 1;
			$this->session->set_userdata('stats_details', $details);
		}
		
		return array(
			'dataType' => $dataType,
			'startDate' => $startDate,
			'endDate' => $endDate,
			'chartPeriod' => $chartPeriod,
			'details' => $details
		);
	}

	public function _getRetentionData() 
	{
		$data = array();

		/////////////////////////////////////////////////////////////////
		// Données issues de Google Analytics
		$this->load->library('GAReporting');
		$this->gareporting->init($this->config->item('googleAnalyticsViewId'));

		// rétention à 7 jours
		$data["chart0"] = $this->gareporting->getRetention(0, 7);

		// rétention à 6 semaines
		$data["chart1"] = $this->gareporting->getRetention(1, 6);

		// rétention à 3 mois
		$data["chart2"] = $this->gareporting->getRetention(2, 3);

		return $data;
	}

	
	public function _getSpecificData() 
	{
        $this->load->library('GAReporting');
        $this->gareporting->init($this->config->item('googleAnalyticsViewId'));

        $endGa = (new DateTime('today'))->format("Y-m-d");

        $result1 = $this->gareporting->request(array("eventValue","totalEvents"), array("eventAction","eventLabel"),
            $this->config->item('googleAnalyticsStatsStart'), $endGa, [["eventAction","EXACT","end"]]);

		$eventsTotals = array();
		$defeats = 0;
        foreach ($result1 as $event){
            if(array_key_exists($event[0],$eventsTotals)){
                $eventsTotals[$event[0]]["value"] += $event[2];
                $eventsTotals[$event[0]]["count"] += $event[3];
                for($i=0;$i<$event[3];$i++){
                    $eventsTotals[$event[0]]["label"][] = $event[1];
                }
            }
            else{
                $eventsTotals[$event[0]] = array("label"=>array($event[1]),"value"=>$event[2],"count"=>$event[3]);
            }
			
			if($event[0] == 'end' && strpos($event[1], 'victoire') === FALSE) {
				$defeats += $event[3];
			}
        }
		
		
        $results = $this->gareporting->request(array('users'), array("country"), $this->config->item('googleAnalyticsStatsStart'), $endGa);
        $usersCount = $results[0][1];

		$result2 = $this->gareporting->request(array("eventValue","totalEvents"), array("eventAction"),
            $this->config->item('googleAnalyticsStatsStart'), $endGa, [["eventAction","EXACT","start"]]);
		$startsCount = $result2[0][2];

		$data = array(
            "gameLaunched" => $startsCount,
            "gameFinished" => $eventsTotals["end"]['count'],
            "gameDuration" => round($eventsTotals["end"]['value']/$eventsTotals["end"]['count']),
            "gamePlayedPerUser" => $eventsTotals["end"]['count']/$usersCount,
            "winLooseRatio" => ($eventsTotals['end']['count'] - $defeats) / $eventsTotals['end']['count'],
            "gameEndedRatio" => $eventsTotals["end"]['count']/$startsCount
        );
//
//		/////////////////////////////////////////////////////////////////
//		// Données issues de AppAnnie
//		$endDate = (new DateTime('today'))->format('d/m/Y');
//		$download_data = $this->_getAppDownloadsData($this->config->item('appAnnieStatsStart'), $endDate, 0, 1);
//
//
//		/////////////////////////////////////////////////////////////////
//		// Données issues de Google Analytics
//		$this->load->library('GAReporting');
//		$this->gareporting->init($this->config->item('googleAnalyticsViewId2'));
//
//		// on récupère le nombre de sessions depuis le début, ce qui correspond au nombres d'App lancées
//		$sessions  = $this->gareporting->request(array('users'), array(), $this->config->item('googleAnalyticsStatsStart'), 'today');
//		if ($sessions)
//		{
//			$sessions = intval($sessions[0][0]);
//			if ($sessions>$download_data[0]["downloads"])
//				$sessions=$download_data[0]["downloads"];
//		}
//		else
//			$sessions = 0;
//
//		// du fait d'un bug avec GA toutes les premières session avant 1498544171 ne sont pas comptabilisées
//		$begin = (new DateTime($this->config->item('googleAnalyticsStatsStart')))->getTimestamp();
//		$end = (new DateTime('today'))->getTimestamp();
//		$lostSessions = intval($this->mainModel->getLostUsers($begin, $end));
//		$sessions += $lostSessions;
//
//		/////////////////////////////////////////////////////////////////
//		// Données issues de la base de donnée du jeu
//		$score = $this->mainModel->getScore();
//		$data["score_avg"] = round($score->total/$score->nb);
//		$data["score_max"] = $score->max;
//		$data["user_nb"] = $score->nb;
//
//		$progress = $this->mainModel->getProgress();
//
//		$data["questions"] = $this->mainModel->getQuestions();
//		$data["events"] = $this->mainModel->getEvents();
//		$data["challenges"] = $this->mainModel->getChallenges($this->gareporting);
//
//
//		$data["progress"] = array(
//			'Téléchargements' => $download_data[0]["downloads"],
//			'App lancée' => $progress->auth,
//			'Identification passée' => $sessions,
//			'Tutorial passé' => $progress->tuto,
//			'Zone 1 débloquée' => $progress->unlock1,
//			'Zone 2 débloquée' => $progress->unlock2,
//			'Zone 3 débloquée' => $progress->unlock3,
//			'Zone 4 débloquée' => $progress->unlock4,
//			'Zone 5 débloquée' => $progress->unlock5,
//			'Jeu fini' => $progress->finished
//			);

		return $data;
	}
	
	
	public function _validateChartPeriod() {
		if($this->session->userdata('stats_startDate') && $this->session->userdata('stats_endDate')) {
			$start = DateTime::createFromFormat('d/m/Y', $this->session->userdata('stats_startDate'));
			$end = DateTime::createFromFormat('d/m/Y', $this->session->userdata('stats_endDate'));
			$interval = $start->diff($end, true);
			$nbDays = intval($interval->format('%a'));
			//echo $nbDays;

			$this->session->set_userdata('stats_nbDays', $nbDays);

			if($nbDays > $this->config->item('statsMaxChartWeeks') * 7 && $this->session->userdata('stats_chartPeriod') != 3) { // plus de 1 an > affichage en mois
				$this->session->set_userdata('stats_chartPeriod', 3);
			}else if($nbDays > $this->config->item('statsMaxChartDays') && $this->session->userdata('stats_chartPeriod') == 1) { // plus de 6 semaines > affichage à minima en semaine
				$this->session->set_userdata('stats_chartPeriod', 2);
			}
		}
	}


}
