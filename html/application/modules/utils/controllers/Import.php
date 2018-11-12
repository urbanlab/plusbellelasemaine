<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Import extends MY_Controller {

	var $data;
    
    var $lang = array(
    'FR' => 1
    );
    
    
    

	private $filename;
	private $filetype;

	private $TAB_SHEETS = array(

		"DATA" => "progression",
		"Options" => "progression",
		"Collaborateurs" => "progression",
		"Choix" => "progression",
		"Choix Lvl7et+" => "progression",
		"Niveaux" => "progression",

		"TXT_Pages" => "translation",
		"TXT_Création" => "translation",
		"TXT_PopUp" => "translation",
		"TXT_ESP_Option" => "translation",
		"TXT_ESP_Metier" => "translation",
		"TXT_Server" => "translation",
		
	);

	private $FORCE_NB_COLUMNS= array(
		"tuto" => 7,
	);

	private $SUCCES_TYPE = array(
		
		"HISTORIC" => "h",
		"NIVEAUX" => "niv",
		"PALIERS_METIERS" => "c00",
		"EnSavoir+" => "esp",
	);

	function __construct() {
		parent::__construct();
	}
	
	
	public function index(){
		redirect(base_url());
        die();
	}
    
    public function ld() {
        Modules::run('security/_make_sure_admin_is_connected');
		
        $this->load->helper('form');
        $this->load->view('utils/import_view');
    }
	
	public function import_xlsx() {
		$config['upload_path'] = './uploads/xlsx/';
		$config['allowed_types'] = 'xlsx';
		
		$this->load->library('upload', $config);
		$this->load->helper('file');
		
		if ( ! $this->upload->do_upload('xlsxFile'))
		{
			$data = array('error' => $this->upload->display_errors());
			echo "error";
			var_dump($data);
			//$this->load->view('import_users/import_users_view', $data);
            die();
		}
		else
		{
			$uploadData = $this->upload->data();
			
			//echo "full path : ".$uploadData["full_path"]."<br />";
			$this->_importLdData($uploadData['full_path']);
			
			//$this->_pushDataToDB();
		}
	}
	
	public function _importLdData($xlsxFilePath) {
		echo '<!doctype html>
		<html>
		<head>
		<meta charset="utf-8">
		<title>Import LD</title>
		</head>

		<body>
		';

		// Load excel data
		$this->load->library('excel');
		$this->load->library('exceliofactory');

		$objPHPExcel = new PHPExcel();

		$objReader = Exceliofactory::createReader('Excel2007');
		$objReader->setReadDataOnly(true);
		
		//$sheetnames = array('DATA','Options','Collaborateurs','Choix','Niveaux'); 
		//$objReader->setLoadSheetsOnly($sheetnames); 
		$objPHPExcel = $objReader->load($xlsxFilePath);
		//echo "xlsxFilePath : ".$xlsxFilePath."<br />";
		$tab = explode("/", $xlsxFilePath);
		$this->filename = $tab[count($tab)-1];
		//echo "this->filename : ".$this->filename."<br />";



		//detect filetype


        //$sql = 'DELETE * FROM `_translation_object`; DELETE * FROM `_translation`; DELETE * FROM `_event`';
       // $this->db->query($sql);
       // $this->db->query("ALTER TABLE _translation_object AUTO_INCREMENT = 1");
      //  $this->db->error();

		
		/*$nbsheet =3;

		echo "nbsheet: ".$nbsheet."<br />";

		for($i=0; $i<$nbsheet; $i++){
			echo "import sheet ".$i."__________<br />";
			$objWorksheet = $objPHPExcel->getSheet($i);
			$title = $objWorksheet->getTitle();
			$tablename = $this->_transformStr($title);

			$this->_importSheetData($objWorksheet, $tablename, $title);

			
		}
             
*/
        $this->_importVariables($objPHPExcel->getSheet(0));
        $this->_importEvents($objPHPExcel->getSheet(1));
        $this->_importEnds($objPHPExcel->getSheet(2));
        $this->_importSummary($objPHPExcel->getSheet(3));
        $this->_checkImport();
        foreach($this->lang as $c=>$l){
            $this->_JSONencode($c, $l);
        }

        
        
		echo '</body>
		</html>';
	}
    
    public function _getSheetData($objWorksheet, $nbCols){
        $sheetData = array();
        
       // echo '<h2>TABLEAU</h2>';
		//echo '<table border="1">' . "\n";
		$cnt = 0;

		
		foreach ($objWorksheet->getRowIterator() as $row) {
            if($cnt !=0 && $objWorksheet->getCellByColumnAndRow(0, $cnt+1)->getValue() == ''){break;}
			//echo '<tr>' . "\n";
			
			$cnt++;
			$rowCnt = 0;
					
			$cellIterator = $row->getCellIterator();
			$cellIterator->setIterateOnlyExistingCells(false); 
			if($cnt !== 1) {
				
				$dataRow = array();
                $cellCnt = 0;
				$values = array();
				foreach ($cellIterator as $cell) {
					$rowCnt++;
                    $cellCnt++;
					if($cellCnt > $nbCols) {
						break;
					}
					//echo '<td>';
					/* Peut permettre de faire des traitement par cellule ******
                    if($rowCnt == 16) {
						echo PHPExcel_Style_NumberFormat::toFormattedString($cell->getValue(), "YYYY/MM/DD");
						$dataRow[] = PHPExcel_Style_NumberFormat::toFormattedString($cell->getValue(), "YYYY/MM/DD");
					}else{
						echo nl2br($cell->getValue());
						$dataRow[] = $cell->getValue();
					}
                    *********/

					$value = $cell->getValue();

                   // echo nl2br($value);
				    $dataRow[] = $value;
					//echo '</td>' . "\n";
					
				}

				$sheetData[] = $dataRow;
			}
			//echo '</tr>' . "\n";
		}
		//echo '</table>' . "\n";
		//print_r($this->data);
        return $sheetData;
    }



    public function _importVariables($objWorksheet){

            $sheetData = $this->_getSheetData($objWorksheet, 5);
			$this->db->query('TRUNCATE TABLE `_variables`');

			$sql = 'INSERT INTO `_variables`(`id_var`, `title`, `initialisation`, `control`, `control_effect`) VALUES (?, ?, ?, ?, ?)';

			//var_dump($sheetData);
			foreach($sheetData as $row) {

				$this->db->query($sql, $row);
				//$this->db->error();
			}
        }
    
    
    public function _importEvents($objWorksheet){
        
        $sheetData = $this->_getSheetData($objWorksheet, 8);
        $this->db->query('TRUNCATE TABLE `_events`');
        $this->db->query('TRUNCATE TABLE `_translation_object`');
        $this->db->query('TRUNCATE TABLE `_translation`');
        $this->db->query('TRUNCATE TABLE `_event_choice`');
        
        $sqlTranslationObject = 'INSERT INTO `_translation_object`(`title`) VALUES(?)';
        $sqlTranslation = 'INSERT INTO `_translation`(`translation_object_FK`, `lang_FK`, `content`) VALUES(?, ?, ?)';
        $sqlEvent = 'INSERT INTO `_events`(`id_event`, `condition`, `weight`, `pool`, `background`, `title_FK`, `description_FK`) VALUES (?, ?, ?, ?, ?, ?, ?)';
        $sqlChoice = 'INSERT INTO `_event_choice`(`id_choice`, `command`, `summary_weight`, `summary_gauge_target`, `content_FK`, `event_FK`, `summary_text_FK`) VALUES (?, ?, ?, ?, ?, ?, ?)';


        //var_dump($sheetData);
        for($i = 0; $i < count($sheetData); $i++){
            if($sheetData[$i][0] == "event"){
                
                // mémoriser les data de l'event(de la ligne)
                $eventRow = array();
                $eventRow[] = $sheetData[$i][1];
                $eventRow[] = $sheetData[$i][2];
                $eventRow[] = $sheetData[$i][3];
                $eventRow[] = $sheetData[$i][4];
                $eventRow[] = $sheetData[$i][5]; 
                
                
                
                $i++;
                // lire la ligne title $sheetData[$i] > ligne title
                
                // faire etape 1 et 2 pour title
                $this->db->query($sqlTranslationObject, 'event_title');
                $queryTitleID = $this->db->insert_id();
                // title FR > $sheetData[$i][5]
                $index = 0;
                foreach($this->lang as $l){
                    $this->db->query($sqlTranslation, array($queryTitleID, $l, $sheetData[$i][$index+6]));
                    $index++;
                }
                
                $i++;
                // lire la ligne description $sheetData[$i] > ligne desc
                
                // faire etape 1 et 2 pour desc
                $this->db->query($sqlTranslationObject, 'event_description');
                $queryDescID = $this->db->insert_id();
                // desc FR > $sheetData[$i][5]
                $index = 0;
                foreach($this->lang as $l){
                    $this->db->query($sqlTranslation, array($queryDescID, $l, $sheetData[$i][$index+6]));
                    $index++;
                }
                
                // insert de l'event
                $eventRow[] = $queryTitleID;
                $eventRow[] = $queryDescID;
                $this->db->query($sqlEvent, $eventRow);
                $queryEventID = $this->db->insert_id();
                
                
                do {
                    $i++;
                    // lire la ligne $sheetData[$i] > ligne choix
                    $choiceRow = array();
                    $choiceRow[] = $sheetData[$i][0];
                    $choiceRow[] = $sheetData[$i][2];
                    $choiceRow[] = '';
                    $choiceRow[] = '';
                    
                    // faire etape 1 et 2 pour desc
                    $this->db->query($sqlTranslationObject, 'event_choice');
                    $queryChoiceID = $this->db->insert_id();
                    // choix FR > $sheetData[$i][5]
                    $index = 0;
                    foreach($this->lang as $l){
                        $this->db->query($sqlTranslation, array($queryChoiceID, $l, $sheetData[$i][$index+6]));
                        $index++;
                    }
                    
                    $this->db->query($sqlTranslationObject, 'summary_choice');
                    $querySumID = $this->db->insert_id();
                    // choix FR > $sheetData[$i][5]
                    foreach($this->lang as $l){
                        $this->db->query($sqlTranslation, array($querySumID, $l, ''));
                    }
                    
                    // insert de du choix
                    $choiceRow[] = $queryChoiceID;
                    $choiceRow[] = $queryEventID;
                    $choiceRow[] = $querySumID;
                    $this->db->query($sqlChoice, $choiceRow);
                }while($i+1 < count($sheetData) && $sheetData[$i+1][0] != 'event');
                
            }
        }
    }
    
    public function _importEnds($objWorksheet){

        $sheetData = $this->_getSheetData($objWorksheet, 4);
        $this->db->query('TRUNCATE TABLE `_ends`');
        
        $sqlTranslationObject = 'INSERT INTO `_translation_object`(`title`) VALUES(?)';
        $sqlTranslation = 'INSERT INTO `_translation`(`translation_object_FK`, `lang_FK`, `content`) VALUES(?, ?, ?)';
        $sqlEnds = 'INSERT INTO `_ends`(`id_end`, `background`, `end_title_FK`, `end_description_FK`) VALUES (?, ?, ?, ?)';

        //var_dump($sheetData);
        
        for($i = 0; $i < count($sheetData); $i++){

            $endRow = array();
            $endRow[] = $sheetData[$i][0];
            $endRow[] = $sheetData[$i][1];

            $i++;
            // lire la ligne title $sheetData[$i] > ligne title
            

            // faire etape 1 et 2 pour title
            $this->db->query($sqlTranslationObject, 'end_title');
            $queryTitleID = $this->db->insert_id();
            // title  > $sheetData[$i][1,2,...]
            $index = 0;
            foreach($this->lang as $l){
                $this->db->query($sqlTranslation, array($queryTitleID, $l, $sheetData[$i][$index+2]));
                $index++;
            }

            $i++;
            // lire la ligne description $sheetData[$i] > ligne desc

            // faire etape 1 et 2 pour desc
            $this->db->query($sqlTranslationObject, 'end_description');
            $queryDescID = $this->db->insert_id();
            // desc FR > $sheetData[$i][5]
            $index = 0;
            foreach($this->lang as $l){
                $this->db->query($sqlTranslation, array($queryDescID, $l, $sheetData[$i][$index+2]));
                $index++;
            }

        

            // insert de l'end
            $endRow[] = $queryTitleID;
            $endRow[] = $queryDescID;
            $this->db->query($sqlEnds, $endRow);
        }
        
    }
    
    public function _importSummary($objWorksheet){

        $sheetData = $this->_getSheetData($objWorksheet, 6);
        
        $gaugeTab = array (
            'lien social' => 1,
            'qualité du logement' => 2,
            'budget' => 3    
        );
        
        $sqlSumSelect = "SELECT ec.`id`, ec.`summary_text_FK` FROM `_events` AS e INNER JOIN `_event_choice` AS ec ON e.`id` = ec.`event_FK` WHERE e.`id_event` = ? AND ec.`id_choice` = ?";
            
        $sqlSum = 'UPDATE `_event_choice`
            SET  `summary_weight` = ?, `summary_gauge_target` = ?
            WHERE `id` = ?';
           
        $sqlSumTrans = 'UPDATE `_translation` 
            SET  `content` = ?
            WHERE `translation_object_FK` = ? AND `lang_FK` = ?';
        
        
        for($i = 0; $i < count($sheetData); $i++){
            
            $choice = $this->db->query($sqlSumSelect, array($sheetData[$i][0], $sheetData[$i][1]))->row();
            //var_dump($choice);
            //echo '<br>'. $this->db->last_query();


            // lire la ligne title $sheetData[$i] > ligne title
            $summaryWeight = empty($sheetData[$i][2])? 0 : $sheetData[$i][2];
            $summaryGaugeTarget = empty($sheetData[$i][3])? 0 : $gaugeTab[$sheetData[$i][3]];
            // faire etape 1 et 2 pour title
            $this->db->query($sqlSum, array($summaryWeight, $summaryGaugeTarget, $choice->id));
            // title  > $sheetData[$i][1,2,...]
            
            $index = 0;
            foreach($this->lang as $l){
                $this->db->query($sqlSumTrans, array($sheetData[$i][$index+4], $choice->summary_text_FK, $l));
                $index++;
            }

        }
        
    }
    
    
    public function _checkImport(){
        
        
        /*************************************************************************************************
        *                                           VARIABLES                                            *
        **************************************************************************************************/
        
        
        
        $tabError = array();
        
        $sqlSelectVar = "SELECT `control_effect`, `id_var` FROM `_variables`";
        $sqlSelectVarEvent = "SELECT `id_event`, `condition`, `weight`FROM `_events`";
        $sqlSelectVarEventChoice = "SELECT ec.`id_choice`, ec.`command`, e.`id_event` FROM `_events` AS e INNER JOIN `_event_choice` AS ec ON e.`id` = ec.`event_FK`";
        
        $selectVar = $this->db->query($sqlSelectVar);
        $selectVarEvent = $this->db->query($sqlSelectVarEvent);
        $selectVarEventChoice = $this->db->query($sqlSelectVarEventChoice);
        
        $tabVar = array();
        
        foreach ($selectVar->result() as $row){    
            $tabVar[] = $row->id_var;
        }
        
        foreach ($selectVarEventChoice->result() as $row){    
            if(preg_match_all('/\$[\w]+/' , $row->command, $matches)){

                 foreach($matches[0] as $v){
                     
                    if(!preg_match('/var[0-9]+/' , $v)){                        
                        $tabError[] = '<br><div style="background-color: #FF9999; border: 1px solid red; border-radius: 15px; padding: 10px;"><h4">ERROR <span style="font-weight:bold">' . $v . '</span> format is not valid on command field of <span style="font-weight:bold">' . $row->id_event. '</span></h4></div>';
                    }
                     
                     else{                         
                        if(!(in_array (str_replace('$', '', $v), $tabVar))){
                            $tabError[] = '<br><div style="background-color: #FF9999; border: 1px solid red; border-radius: 15px; padding: 10px;"><h4">ERROR <span style="font-weight:bold">' . str_replace('$', '', $v) . ' </span> does not exist on command field of <span style="font-weight:bold">' . $row->id_event . '</span></h4></div>';
                        }
                    }
                }
            }
        }
        
        foreach ($selectVarEvent->result() as $row){    
             if(preg_match_all('/\$[\w]+/' , $row->weight, $matches)){

                 foreach($matches[0] as $v){
                     
                    if(!preg_match('/var[0-9]+/' , $v)){                        
                        $tabError[] = '<br><div style="background-color: #FF9999; border: 1px solid red; border-radius: 15px; padding: 10px;"><h4">ERROR <span style="font-weight:bold">' . $v . '</span> format is not valid on weight field of <span style="font-weight:bold">' . $row->id_event. '</span></h4></div>';
                    }
                     
                     else{                         
                        if(!(in_array (str_replace('$', '', $v), $tabVar))){
                            $tabError[] = '<br><div style="background-color: #FF9999; border: 1px solid red; border-radius: 15px; padding: 10px;"><h4">ERROR <span style="font-weight:bold">' . str_replace('$', '', $v) . ' </span> does not exist on weight field of <span style="font-weight:bold">' . $row->id_event . '</span></h4></div>';
                        }
                    }
                }
            }
           /* if(preg_match_all('/\$[\w]+/' , $row->condition, $matches)){

                 foreach($matches[0] as $v){
                     
                    if(!preg_match('/var[0-9]+/' , $v)){                        
                        $tabError[] = '<br><div style="background-color: #FF9999; border: 1px solid red; border-radius: 15px; padding: 10px;"><h4">ERROR <span style="font-weight:bold">' . $v . '</span> format is not valid on condition field of <span style="font-weight:bold">' . $row->id_event. '</span></h4></div>';
                    }
                     
                     else{                         
                        if(!(in_array (str_replace('$', '', $v), $tabVar))){
                            $tabError[] = '<br><div style="background-color: #FF9999; border: 1px solid red; border-radius: 15px; padding: 10px;"><h4">ERROR <span style="font-weight:bold">' . str_replace('$', '', $v) . ' </span> does not exist on condition field of <span style="font-weight:bold">' . $row->id_event . '</span></h4></div>';
                        }
                    }
                }
            }*/
        }
        
        foreach ($selectVar->result() as $row){  
            
            if(preg_match_all('/\$[\w]+/' , $row->control_effect, $matches)){
                
                 foreach($matches[0] as $v){
                     
                    if(!preg_match('/var[0-9]+/' , $v)){                        
                        $tabError[] = '<br><div style="background-color: #FF9999; border: 1px solid red; border-radius: 15px; padding: 10px;"><h4">ERROR <span style="font-weight:bold">' . $v . '</span> format is not valid on controlEffect field of <span style="font-weight:bold">' . $row->id_var. '</span></h4></div>';
                    }
                     
                     else{                         
                        if(!(in_array (str_replace('$', '', $v), $tabVar))){
                            $tabError[] = '<br><div style="background-color: #FF9999; border: 1px solid red; border-radius: 15px; padding: 10px;"><h4">ERROR <span style="font-weight:bold">' . str_replace('$', '', $v) . ' </span> does not exist on controlEffect field of <span style="font-weight:bold">' . $row->id_var . '</span></h4></div>';
                        }
                    }
                }
            }
                
        }
        
        /*************************************************************************************************
        *                                          FUNCTIONS                                             *
        **************************************************************************************************/
        
        $tabEvent = array();
        $tabPool = array();
        
        $sqlSelectFct = "SELECT `control_effect`, `id_var` FROM `_variables`";
        $sqlSelectFctEvent = "SELECT `id_event`, `condition`, `weight`, `pool` FROM `_events`";
        $sqlSelectFctEventChoice = "SELECT ec.`id_choice`, ec.`command`, e.`id_event` FROM `_events` AS e INNER JOIN `_event_choice` AS ec ON e.`id` = ec.`event_FK`";
        
        $selectFct = $this->db->query($sqlSelectFct);
        $selectFctEvent = $this->db->query($sqlSelectFctEvent);
        $selectFctEventChoice = $this->db->query($sqlSelectFctEventChoice);
        
        
        foreach($selectFctEvent->result() as $row){
            $tabEvent[] = $row->id_event;
            $tabPool[] = $row->pool;
        }
        
        $tabFct = array('compareTrigger', 'end_game', 'insert_event', 'insert_pool', 'trigger_event', 'trigger_pool');
        
        foreach ($selectFct->result() as $row){
            $control_effect = preg_replace('/\s+/', '', $row->control_effect);
            if(preg_match_all('/\b([a-zA-Z_]*)\(([a-zA-Z0-9?(?)?, $_=<>+-]*)\)/' , $control_effect, $functions)){
                foreach($functions[1] as $fct){
                   if(!in_array($fct, $tabFct)){
                       $tabError[] = '<br><div style="background-color: #FF9999; border: 1px solid red; border-radius: 15px; padding: 10px;"><h4">ERROR <span style="font-weight:bold">' . $fct . '</span>  is not a valid function on controlEffect field of <span style="font-weight:bold">' . $row->id_var. '</span></h4></div>';
                   }
                    /*foreach($functions[2] as $fcts){
                       if($fct == 'compareTrigger'){
                            $tabCompare = explode(',', $fcts);
                            if(!preg_match('/\$var[0-9]+(<[=>]?|==|>=?|\&\&|\|\|)[0-9]+/', $tabCompare[0])){
                                $tabError[] =  '<br><div style="background-color: #FF9999; border: 1px solid red; border-radius: 15px; padding: 10px;"><h4">ERROR <span style="font-weight:bold">' .$tabCompare[0] . '</span>  is not a valid comparison on controlEffect field of <span style="font-weight:bold">' . $row->id_var. '</span></h4></div>';
                            }
                        }
                    }*/
                    
                }
                foreach($functions[2] as $fcts){
                    if(preg_match_all('/\b([a-zA-Z_]*)\((\w*)\)/' , $fcts, $function)){                        
                       foreach($function[1] as $fct){
                           if(!in_array($fct, $tabFct)){
                               $tabError[] =  '<br><div style="background-color: #FF9999; border: 1px solid red; border-radius: 15px; padding: 10px;"><h4">ERROR <span style="font-weight:bold">' . $fct . '</span>  is not a valid function on controlEffect field of <span style="font-weight:bold">' . $row->id_var. '</span></h4></div>';
                           }
                           
                          foreach($function[2] as $fct2){
                                if($fct == 'trigger_event' || $fct == 'insert_event'){
                                     if(!in_array($fct2, $tabEvent)){
                                       $tabError[] =  '<br><div style="background-color: #FF9999; border: 1px solid red; border-radius: 15px; padding: 10px;"><h4">ERROR <span style="font-weight:bold">' . $fct2 . '</span>  does not exist  on controlEffect field of <span style="font-weight:bold">' . $row->id_var. '</span></h4></div>';  
                                     }
                                }
                                if($fct == 'trigger_pool' || $fct == 'insert_pool'){
                                     if(!in_array($fct2, $tabPool)){
                                       echo '<br><div style="background-color: #FF9999; border: 1px solid red; border-radius: 15px; padding: 10px;"><h4">ERROR <span style="font-weight:bold">' . $fct2 . '</span>  does not exist  on controlEffect field of <span style="font-weight:bold">' . $row->id_var. '</span></h4></div>';  
                                     }
                                }
                          }
                        }   
                    }
                }
            }
        }
        
        foreach ($selectFctEventChoice->result() as $row){
            $command = preg_replace('/\s+/', '', $row->command);
            if(preg_match_all('/\b([a-zA-Z_]*)\((\w*)\)/' , $command, $function)){                        
               foreach($function[1] as $fct){
                   if(!in_array($fct, $tabFct)){
                       $tabError[] =  '<br><div style="background-color: #FF9999; border: 1px solid red; border-radius: 15px; padding: 10px;"><h4">ERROR <span style="font-weight:bold">' . $fct . '</span>  is not a valid function on command field of <span style="font-weight:bold">' . $row->id_event. '</span></h4></div>';
                   }    
                  foreach($function[2] as $fct2){
                        if($fct == 'trigger_event' || $fct == 'insert_event'){
                             if(!in_array($fct2, $tabEvent)){
                               $tabError[] =  '<br><div style="background-color: #FF9999; border: 1px solid red; border-radius: 15px; padding: 10px;"><h4">ERROR <span style="font-weight:bold">' . $fct2 . '</span>  does not exist  on command field of <span style="font-weight:bold">' . $row->id_event. '</span></h4></div>';  
                             }
                        }
                        if($fct == 'trigger_pool' || $fct == 'insert_pool'){
                             if(!in_array($fct2, $tabPool)){
                               $tabError[] =  '<br><div style="background-color: #FF9999; border: 1px solid red; border-radius: 15px; padding: 10px;"><h4">ERROR <span style="font-weight:bold">' . $fct2 . '</span>  does not exist  on command field of <span style="font-weight:bold">' . $row->id_event. '</span></h4></div>';  
                             }
                        }
                  }
                }   
            }
        }
        
        
        
        if(count($tabError) > 0){
            echo implode($tabError);
        }
        else {
            echo '<div style="background-color: #99FF99; border: 1px solid green; border-radius: 15px; padding: 10px;"><h4>Table is valid</h4></div>';
        }
        
    }
    
    public function _JSONencode ($c, $l){
    

    
       $JSONtab = array(     
            'Variables' => array(),
            'Events' => array(),
            'Ends' => array()    
        );
            
        $sql = $this->db->get('_variables');

        foreach ($sql->result() as $row){

            array_push($JSONtab['Variables'], array(
                'id' => $row->id_var, 
                'title' => $row->title, 
                'initialisation' => $row->initialisation, 
                'control' => ($row->control != NULL ? $row->control : ''),
                'controlEffect' => ($row->control_effect != NULL ? $row->control_effect : '')
            ));
        }


        $sqlEvents = $this->db->get('_events');

        $sqlChoices = "SELECT ec.`id`, ec.`id_choice`, ec.`command`, ec.`summary_weight`,  ec.`summary_gauge_target`,  t.`content`, ec.`event_FK`
                        FROM `_event_choice` AS ec INNER JOIN
                            `_translation_object` AS tro ON tro.`id` = ec.`content_FK` INNER JOIN
                            `_translation` AS t ON t.`translation_object_FK` = tro.`id`
                        WHERE ec.`event_FK` = ? AND t.`lang_FK` = ? ";
        
        $sqlSummary = "SELECT ec.`id`, ec.`id_choice`, t.`content`, ec.`summary_text_FK`
                        FROM `_event_choice` AS ec INNER JOIN
                            `_translation_object` AS tro ON tro.`id` = ec.`summary_text_FK` INNER JOIN
                            `_translation` AS t ON t.`translation_object_FK` = tro.`id`
                        WHERE ec.`event_FK` = ? AND t.`lang_FK` = ? AND ec.`id_choice` = ? ";


        foreach ($sqlEvents->result() as $row){

            $title = $this->db->get_where('_translation', array('translation_object_FK' => $row->title_FK, 'lang_FK' => $l))->row()->content; 
            $desc = $this->db->get_where('_translation', array('translation_object_FK' => $row->description_FK, 'lang_FK' => $l))->row()->content;

            $choices = array();

            $query = $this->db->query($sqlChoices, array($row->id, $l));

            foreach($query->result() as $line){ 
                
                $summaryText = $this->db->query($sqlSummary, array($row->id, $l, $line->id_choice))->row()->content;
                    
                array_push($choices, array(
                    'id' => $row->id_event . '|' . $line->id_choice,
                    'text' => $line->content,
                    'value' => $line->command,
                    'summaryWeight' => $line->summary_weight,
                    'summaryGauge' => $line->summary_gauge_target,
                    'summaryText' => ($summaryText != NULL ? $summaryText : '')
                ));
            }

            array_push($JSONtab['Events'], array(
                'id' => $row->id_event, 
                'condition' => ($row->condition != NULL ? $row->condition : ''), 
                'weight' => ($row->weight != NULL ? $row->weight : ''),
                'pool' => ($row->pool != NULL ? $row->pool : ''),
                'background' => $row->background,
                'title' => $title,
                'textEvent' => $desc,
                'Choices' => $choices                
            ));

        }

        $sqlEnds = $this->db->get('_ends');

        foreach($sqlEnds->result() as $row){
            
           $title = $this->db->get_where('_translation', array('translation_object_FK' => $row->end_title_FK, 'lang_FK' => $l))->row()->content; 
           $desc = $this->db->get_where('_translation', array('translation_object_FK' => $row->end_description_FK, 'lang_FK' => $l))->row()->content; 
            

            array_push($JSONtab['Ends'], array(
                'id' => $row->id_end,
                'background' => $row->background,
                'title' => $title,
                'text' => $desc            
            ));

        }

       /* echo '<br><br>';
        
        echo '<h3>'.$c.'</h3>';
        echo json_encode($JSONtab, JSON_PRETTY_PRINT);   */
        if(ENVIRONMENT == 'testing'){
            $fp = fopen('app/data/eventData_'.$c.'.json', 'w');
        }
        
        else if (ENVIRONMENT == 'development'){
             $fp = fopen('../../Client/App/www/data/eventData_'.$c.'.json', 'w');
        }
        
        //$fp = fopen('data/eventData_'.$c.'.json', 'w');
        fwrite($fp, json_encode($JSONtab));
        fclose($fp);

        /*$JSONtab['Variables'] = $sql;
        
        foreach($JSONtab['Variables'] as $json){
            var_dump($json);
        }*/
 
    }
    
    


    
    
    
    /*foreach($this->lang as $l){
    echo $Variables;    
    // boucle tableau de langues
      /*  $data = array(
            'Variables' => array(),
                /*array(
                    'id' => 'var1',
                    'title' => '',
                    ...
                )
            ),*/
           /* 'Event' => array(
               /* array(
                    'id' => 'var1',
                    'title' => '',
                    ...
                    'Choices' => array(
                        array(id, title, command...),
                        array(id, title, command...),
                        array(id, title, command...),
                        array(id, title, command...),
                    )
                ),
                array(
                    'id' => 'var1',
                    'title' => '',
                    ...
                    'Choices' => array(
                        array(id, title, command...),
                        array(id, title, command...),
                        array(id, title, command...),
                        array(id, title, command...),
                    )
                )*/
          //  ),
          /*  'Ends' => array()
        
  //          */
   // }
    
    
	private function _transformStr($_str)
	{
		$_str = strtolower($_str);
		$_str = str_replace(" ", "_", $_str);
		$unwanted_array = array(    'Š'=>'S', 'š'=>'s', 'Ž'=>'Z', 'ž'=>'z', 'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E',
                            'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U',
                            'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss', 'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'a', 'ç'=>'c',
                            'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o',
                            'ö'=>'o', 'ø'=>'o', 'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ý'=>'y', 'þ'=>'b', 'ÿ'=>'y' );
		$_str = strtr($_str, $unwanted_array);
		$_str = str_replace("+", "", $_str);
		return $_str;
	}




	private function _transformLink($_str)
	{
		return preg_replace("`^=HYPERLINK\(\"(.+)\",(.+)$`", "$1", $_str);
	}





	
	

}


function trace($msg){
	echo $msg."<br />";
}