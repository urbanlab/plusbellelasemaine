<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');



class Admin_Import extends Admin_Controller {

	private $exportTplPath = "tpl/export_story_tpl.xlsx";

    private $lang;

	
	function __construct()
    {
		parent::__construct();
        
		$this->lang = Modules::run('translation/_getLangList');
	}
	
	public function index() {
		$this->view(NULL);

	}



	public function view( $error = NULL) {
		Modules::run('security/_make_sure_admin_is_connected');
		
		$this->load->helper('form');
		
		$data = array(
			'currentSection' => 'import',
			'view_file' => 'imports/import_view',
        	'page_title' => 'Import',
        	'page_meta_description' => '',
			'additionnalCssFiles' => array(),
			'additionnalJsFiles' => array(),
			//$data['additionnalJsCmd_wready'] = array('classe.init()';
        	//$data['additionnalJsCmd_wload'] = array('classe.init()';
			//$data['additionnalJsCmd_wscroll'] = array('classe.init()';
			//$data['additionnalJsCmd_wresize'] = array('classe.init()';
			
			'mainTitle' => 'Import depuis un ficher exel',
			'breadcrumb' => array(
				'Gestion des Admins' => ''
			),
			'error' => $error,
		);
        echo Modules::run('template/adm/_default', $data);
    }

    public function importValidate(){
        $config['upload_path'] = './uploads/xlsx/';
        $config['allowed_types'] = 'xlsx';

		$scenarioId = intval($this->input->post('scenarioId'));
		if(empty($scenarioId) || is_nan($scenarioId) || $scenarioId <= 0) {
			$this->session->set_flashdata("importScenarioError", "Invalid scenario ID");

            redirect(base_admin_url('import'), 'refresh');
            die();
		}

		
		
        $this->load->library('upload', $config);
        $this->load->helper('file');

        if ( ! $this->upload->do_upload('xlsxFile'))
        {
//            $data = array('error' => $this->upload->display_errors());
//            echo "error ";
//            var_dump($data);
            $this->session->set_flashdata("importFileUploadError",$this->upload->display_errors());

            redirect(base_admin_url('import'), 'refresh');
            die();
        }
        else
        {

            $uploadData = $this->upload->data();

            //echo "full path : ".$uploadData["full_path"]."<br />";
            $this->_importLdData($scenarioId, $uploadData['full_path']);

            //$this->_pushDataToDB();
        }
    }

    public function importJson(){
        $config['upload_path'] = './uploads/json/';
        $config['allowed_types'] = '*';

        $this->load->library('upload', $config);
        $this->load->helper('file');

        if ( ! $this->upload->do_upload('jsonFile'))
        {

            $this->session->set_flashdata("importFileUploadError",$this->upload->display_errors());

            redirect(base_admin_url('import'), 'refresh');
            die();
        }
        else
        {
            $uploadData = $this->upload->data();


            //echo "full path : ".$uploadData["full_path"]."<br />";
            $this->_importLdDataFromJson($uploadData['full_path']);

            //$this->_pushDataToDB();
        }
    }

    public  function _importLdDataFromJson($xlsxFilePath){
	    $data = json_decode(file_get_contents($xlsxFilePath));
        $variables = array();

        foreach ($data->Variables as $variable){
            $variables[] = array_values(get_object_vars($variable));
        }
        $this->_importVariables(1, $variables);

        $ends = array();

        foreach ($data->Ends as $end){
            $val = array_values(get_object_vars($end)) ;
            $end_row = array($val[0],$val[1]);
            for ($i = 2;$i<4+count($this->lang);$i++){
                $end_row[$i] = "";
            }
            $ends[] = $end_row;
            $end_row = array("title","",$val[2]);
            for ($i = 3;$i<4+count($this->lang);$i++){
                $end_row[$i] = "";
            }
            $ends[] = $end_row;
            $end_row = array("description","",$val[3]);
            for ($i = 3;$i<4+count($this->lang);$i++){
                $end_row[$i] = "";
            }
            $ends[] = $end_row;
        }

        $this->_importEvents(1, $this->_jsonSheetStyleEventEncode($data->Events));
        $this->_importEnds(1, $ends);

        $this->_importSummary(1, $this->_jsonSheetStyleSmmuryEncode($data->Events), true);

        redirect(base_admin_url('import'), 'refresh');
    }

    private function _jsonSheetStyleEventEncode( $events_Data ){
	    $events = array();
        foreach ($events_Data as $event){
            $val = array_values(get_object_vars($event)) ;
//            pr($val);
            $events_row = array("event",$val[0],$val[1],$val[2],$val[3],$val[4]);
            for ($i = 6;$i<8+count($this->lang);$i++){
                $events_row[$i] = "";
            }
            $events[] = $events_row;

            $events_row = array("title","","","","","",$val[5]);
            for ($i = 7;$i<8+count($this->lang);$i++){
                $events_row[$i] = "";
            }
            $events[] = $events_row;

            $events_row = array("description","","","","","",$val[6]);
            for ($i = 7;$i<8+count($this->lang);$i++){
                $events_row[$i] = "";
            }
            $events[] = $events_row;

            $index = 1;
//            pr($val[7]);

            foreach ($val[7] as $choice){
                $val_choice = array_values(get_object_vars($choice));

                $events_row = array("c".$index,"",$val_choice[2],"","","",$val_choice[1]);
                for ($i = 7;$i<8+count($this->lang);$i++){
                    $events_row[$i] = "";
                }
                $events[] = $events_row;
                $index++;
            }

        }
        return $events;
    }

    private function _jsonSheetStyleSmmuryEncode( $events_Data ){
        $gaugeTab = array (
            1 => 'budget',
            2 => 'lien social' ,
            3 => 'qualité du logement'
        );
        $events = array();
        foreach ($events_Data as $event){

            $val = array_values(get_object_vars($event)) ;

            $index = 1;
            foreach ($val[7] as $choice){
                $val_choice = array_values(get_object_vars($choice));

                if($val_choice[5] != ""){
                    $events_row = array($val[0],"c".$index ,$val_choice[3],$gaugeTab[$val_choice[4]],$val_choice[5]);

                    for ($i = 5;$i<6+count($this->lang);$i++){
                        $events_row[$i] = "";
                    }
                    $events[] = $events_row;
                }
                $index++;
            }
        }
        return $events;
    }

    public function _importLdData($scenarioId = 1, $xlsxFilePath) {

        // Load excel data

        $objReader = new PhpOffice\PhpSpreadsheet\Reader\Xlsx();


        $objPhpSpreadsheet = $objReader->load($xlsxFilePath);

        $tab = explode("/", $xlsxFilePath);
        $this->filename = $tab[count($tab)-1];

        $this->_importVariables($scenarioId, $this->_getSheetData($objPhpSpreadsheet->getSheet(0),5));
		$this->_importEvents($scenarioId, $this->_getSheetData($objPhpSpreadsheet->getSheet(1),8+count($this->lang)));
        $this->_importEnds($scenarioId, $this->_getSheetData($objPhpSpreadsheet->getSheet(2), 4+count($this->lang)));
        $this->_importSummary($scenarioId, $this->_getSheetData($objPhpSpreadsheet->getSheet(3), 6+count($this->lang)));
        $this->_checkImport($scenarioId);
        foreach($this->lang as $c=>$l){
            $this->_JSONencode($c, $l);
        }

        redirect(base_admin_url('import'), 'refresh');
	}

    public function _getSheetData($objWorksheet, $nbCols){
        $sheetData = array();

        $cnt = 0;


        foreach ($objWorksheet->getRowIterator() as $row) {

            if($cnt !=0 && $objWorksheet->getCellByColumnAndRow(1, $cnt+1)->getValue() == ''){break;}
            //echo '<tr>' . "\n";


            if($cnt > 0) {

                $dataRow = array();

                for($j = 0; $j< $nbCols ;$j++){
                    $dataRow[] = $objWorksheet->getCellByColumnAndRow($j+1,$cnt+1)->getValue();
                }


                $sheetData[] = $dataRow;
            }
            $cnt++;

        }

//        pr($sheetData);
//        die();

        return $sheetData;
    }

    public function _importVariables($scenarioId, $objWorksheet){

        $sheetData = $objWorksheet;

		// remove previous data
		$sql = 'DELETE FROM `_variables` 
				WHERE `scenario_FK` = ? ';
        $this->db->query($sql, array($scenarioId));

        foreach($sheetData as $row) {
			
            Modules::run('variables/Admin_variables/_addItem', $scenarioId, $row);
        }
    }

    public function _importEvents($scenarioId, $objWorksheet){

        $sheetData = $objWorksheet;
        
		// clean translations
		$eventIds = array();
		$translationIds = array();
		
		$sql = 'SELECT `id`, `title_FK`, `description_FK` 
				FROM `_events` 
				WHERE `scenario_FK` = ? ';
		$sql2 = 'SELECT `content_FK`, `summary_text_FK` 
				FROM `_event_choice` 
				WHERE `event_FK` = ? ';
		
		$query = $this->db->query($sql, array($scenarioId));
		foreach($query->result() as $row) {
			$eventIds[] = $row->id;
			$translationIds[] = intval($row->title_FK);
			$translationIds[] = intval($row->description_FK);
			
			$query2 = $this->db->query($sql2, array($row->id));
			foreach($query2->result() as $row2) {
				$translationIds[] = intval($row2->content_FK);
				$translationIds[] = intval($row2->summary_text_FK);
			}
		}
		
        
		
		// remove previous data
		$sql = 'DELETE FROM `_events` 
				WHERE `scenario_FK` = ? ';
        $this->db->query($sql, array($scenarioId));
        if(count($eventIds) > 0) {
			$sql = 'DELETE FROM `_event_choice` 
					WHERE `event_FK` IN ('.implode(',', $eventIds).') ';
			$this->db->query($sql);
		}
		if(count($translationIds) > 0) {
			$sql = 'DELETE FROM `_translation_object` 
					WHERE `id` IN ('.implode(',', $translationIds).') ';
			$this->db->query($sql);
			$sql = 'DELETE FROM `_translation` 
					WHERE `translation_object_FK` IN ('.implode(',', $translationIds).') ';
			$this->db->query($sql);
		}
		
		
		// import new data
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
                $queryTitleID = Modules::run('translation/_addTranslationObject', 'event_title');

                // title FR > $sheetData[$i][5]
                $index = 0;
                foreach($this->lang as $l){
                    Modules::run('translation/_addTranslation', array($queryTitleID, $l, $sheetData[$i][$index+6]));
                    $index++;
                }

                $i++;
                // lire la ligne description $sheetData[$i] > ligne desc

                // faire etape 1 et 2 pour desc
                $queryDescID = Modules::run('translation/_addTranslationObject', 'event_description');

                // desc FR > $sheetData[$i][5]
                $index = 0;
                foreach($this->lang as $l){
                    Modules::run('translation/_addTranslation', array($queryDescID, $l, $sheetData[$i][$index+6]));
                    $index++;
                }

                // insert de l'event
                $eventRow[] = $queryTitleID;
                $eventRow[] = $queryDescID;

//                pr($eventRow);
//                die();
                $queryEventID = Modules::run('events/Admin_events/_addItem', $scenarioId, $eventRow);

                do {
                    $i++;
                    // lire la ligne $sheetData[$i] > ligne choix
                    $choiceRow = array();
                    $choiceRow[] = $sheetData[$i][0];
                    $choiceRow[] = $sheetData[$i][2];
                    $choiceRow[] = '';
                    $choiceRow[] = '';

                    // faire etape 1 et 2 pour desc
                    $queryChoiceID = Modules::run('translation/_addTranslationObject', 'event_choice');

                    // choix FR > $sheetData[$i][5]
                    $index = 0;
                    foreach($this->lang as $l){
                        Modules::run('translation/_addTranslation', array($queryChoiceID, $l, $sheetData[$i][$index+6]));

                        $index++;
                    }

                    $querySumID = Modules::run('translation/_addTranslationObject', 'summary_choice');

                    // choix FR > $sheetData[$i][5]
                    foreach($this->lang as $l){
                        Modules::run('translation/_addTranslation', array($querySumID, $l, ''));

                    }

                    // insert de du choix
                    $choiceRow[] = $queryChoiceID;
                    $choiceRow[] = $queryEventID;
                    $choiceRow[] = $querySumID;



                    Modules::run('event_choices/Admin_event_choices/_addItem', $choiceRow);

                }while($i+1 < count($sheetData) && $sheetData[$i+1][0] != 'event');

            }
        }
    }

    public function _importEnds($scenarioId, $objWorksheet){

        $sheetData = $objWorksheet;
        
		// clean translations
		$translationIds = array();
		
		$sql = 'SELECT `id`, `end_title_FK`, `end_description_FK` 
				FROM `_ends` 
				WHERE `scenario_FK` = ? ';
		$query = $this->db->query($sql, array($scenarioId));
		foreach($query->result() as $row) {
			$translationIds[] = intval($row->end_title_FK);
			$translationIds[] = intval($row->end_description_FK);
		}
		
        
		// remove previous data
		$sql = 'DELETE FROM `_ends` 
				WHERE `scenario_FK` = ? ';
        $this->db->query($sql, array($scenarioId));
        if(count($translationIds) > 0) {
			$sql = 'DELETE FROM `_translation_object` 
					WHERE `id` IN ('.implode(',', $translationIds).') ';
			$this->db->query($sql);
			$sql = 'DELETE FROM `_translation` 
					WHERE `translation_object_FK` IN ('.implode(',', $translationIds).') ';
			$this->db->query($sql);
		}

		
		// import new data
        for($i = 0; $i < count($sheetData); $i++){

            $endRow = array();
            $endRow[] = $sheetData[$i][0];
            $endRow[] = $sheetData[$i][1];

            $i++;
            // lire la ligne title $sheetData[$i] > ligne title


            // faire etape 1 et 2 pour title
            $queryTitleID = Modules::run('translation/_addTranslationObject', 'end_title');
            // title  > $sheetData[$i][1,2,...]
            $index = 0;
            foreach($this->lang as $l){
                Modules::run('translation/_addTranslation', array($queryTitleID, $l, $sheetData[$i][$index+2]));
                $index++;
            }

            $i++;
            // lire la ligne description $sheetData[$i] > ligne desc

            // faire etape 1 et 2 pour desc
            $queryDescID = Modules::run('translation/_addTranslationObject', 'end_description');
            // desc FR > $sheetData[$i][5]
            $index = 0;
            foreach($this->lang as $l){
                Modules::run('translation/_addTranslation', array($queryDescID, $l, $sheetData[$i][$index+2]));
                $index++;
            }



            // insert de l'end
            $endRow[] = $queryTitleID;
            $endRow[] = $queryDescID;

            Modules::run('ends/Admin_ends/_addItem', $scenarioId, $endRow);

        }

    }

    public function _importSummary($scenarioId, $objWorksheet, $fromOldJSON = false){

        $sheetData = $objWorksheet;
		
        $gaugeTab = array (
            'budget' => 1,
			'lien social' => 2,
            'qualité du logement' => 3
        );

        for($i = 0; $i < count($sheetData); $i++){
            $choice = Modules::run('event_choices/Admin_event_choices/_getByEventAndChoice', $scenarioId, $sheetData[$i][0], $sheetData[$i][1]);

            // lire la ligne title $sheetData[$i] > ligne title
            $summaryWeight = empty($sheetData[$i][2])? 0 : $sheetData[$i][2];
            //if($fromOldJSON === true) {
				$summaryGaugeTarget = empty($sheetData[$i][3])? 0 : $gaugeTab[$sheetData[$i][3]];
			//}else{
			//	$summaryGaugeTarget = empty($sheetData[$i][3])? 0 : $sheetData[$i][3];
			//}
            // faire etape 1 et 2 pour title

            Modules::run('event_choices/Admin_event_choices/_updateSummaryWeight', $choice->id, $summaryWeight);

            Modules::run('event_choices/Admin_event_choices/_updateGaugeTarget', $choice->id, $summaryGaugeTarget);



            // title  > $sheetData[$i][1,2,...]

            $index = 0;
            foreach($this->lang as $l){
                Modules::run('translation/_updateContent', $choice->summary_text_FK, $l, $sheetData[$i][$index+4]);
                $index++;
            }
        }
    }

    public function _checkImport($scenarioId){


        /*************************************************************************************************
         *                                           VARIABLES                                            *
         **************************************************************************************************/


        $tabError = array();

        $selectFct = $selectVar = Modules::run('variables/Admin_variables/_getItemList', $scenarioId);
        $selectFctEvent = $selectVarEvent = Modules::run('events/Admin_events/_getItemList', $scenarioId);
        $selectFctEventChoice = $selectVarEventChoice = Modules::run('event_choices/Admin_event_choices/_getItemList', $scenarioId);

        $tabVar = array();

        foreach ($selectVar->result() as $row){
            $tabVar[] = $row->id_var;
        }

        foreach ($selectVarEventChoice->result() as $row){
            if(preg_match_all('/\$[\w]+/' , $row->command, $matches)){

                foreach($matches[0] as $v){

                    if(!preg_match('/var[0-9]+/' , $v)){
                        $tabError[] = 'ERROR <span style="font-weight:bold">' . $v . '</span> format is not valid on command field of <span style="font-weight:bold">' . $row->id_event. '</span>';
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
                        $tabError[] = 'ERROR <span style="font-weight:bold">' . $v . '</span> format is not valid on weight field of <span style="font-weight:bold">' . $row->id_event. '</span>';
                    }

                    else{
                        if(!(in_array (str_replace('$', '', $v), $tabVar))){
                            $tabError[] = 'ERROR <span style="font-weight:bold">' . str_replace('$', '', $v) . ' </span> does not exist on weight field of <span style="font-weight:bold">' . $row->id_event . '</span>';
                        }
                    }
                }
            }
        }

        foreach ($selectVar->result() as $row){

            if(preg_match_all('/\$[\w]+/' , $row->control_effect, $matches)){

                foreach($matches[0] as $v){

                    if(!preg_match('/var[0-9]+/' , $v)){
                        $tabError[] = 'ERROR <span style="font-weight:bold">' . $v . '</span> format is not valid on controlEffect field of <span style="font-weight:bold">' . $row->id_var. '</span>';
                    }

                    else{
                        if(!(in_array (str_replace('$', '', $v), $tabVar))){
                            $tabError[] = 'ERROR <span style="font-weight:bold">' . str_replace('$', '', $v) . ' </span> does not exist on controlEffect field of <span style="font-weight:bold">' . $row->id_var . '</span>';
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
                        $tabError[] = 'ERROR <span style="font-weight:bold">' . $fct . '</span>  is not a valid function on controlEffect field of <span style="font-weight:bold">' . $row->id_var. '</span>';
                    }
                }

                foreach($functions[2] as $fcts){
                    if(preg_match_all('/\b([a-zA-Z_]*)\((\w*)\)/' , $fcts, $function)){
                        foreach($function[1] as $fct){
                            if(!in_array($fct, $tabFct)){
                                $tabError[] =  'ERROR <span style="font-weight:bold">' . $fct . '</span>  is not a valid function on controlEffect field of <span style="font-weight:bold">' . $row->id_var. '</span></h4></div>';
                            }

                            foreach($function[2] as $fct2){
                                if($fct == 'trigger_event' || $fct == 'insert_event'){
                                    if(!in_array($fct2, $tabEvent)){
                                        $tabError[] =  '<br><div style="background-color: #FF9999; border: 1px solid red; border-radius: 15px; padding: 10px;"><h4">ERROR <span style="font-weight:bold">' . $fct2 . '</span>  does not exist  on controlEffect field of <span style="font-weight:bold">' . $row->id_var. '</span>';
                                    }
                                }
                                if($fct == 'trigger_pool' || $fct == 'insert_pool'){
                                    if(!in_array($fct2, $tabPool)){
                                        echo 'ERROR <span style="font-weight:bold">' . $fct2 . '</span>  does not exist  on controlEffect field of <span style="font-weight:bold">' . $row->id_var. '</span>';
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
                        $tabError[] =  'ERROR <span style="font-weight:bold">' . $fct . '</span>  is not a valid function on command field of <span style="font-weight:bold">' . $row->id_event. '</span>';
                    }
                    foreach($function[2] as $fct2){
                        if($fct == 'trigger_event' || $fct == 'insert_event'){
                            if(!in_array($fct2, $tabEvent)){
                                $tabError[] =  'ERROR <span style="font-weight:bold">' . $fct2 . '</span>  does not exist  on command field of <span style="font-weight:bold">' . $row->id_event. '</span>';
                            }
                        }
                        if($fct == 'trigger_pool' || $fct == 'insert_pool'){
                            if(!in_array($fct2, $tabPool)){
                                $tabError[] =  'ERROR <span style="font-weight:bold">' . $fct2 . '</span>  does not exist  on command field of <span style="font-weight:bold">' . $row->id_event. '</span>';
                            }
                        }
                    }
                }
            }
        }

        if(count($tabError) > 0){
            $this->session->set_flashdata("importErrorMessages",$tabError);
        }
        else {
            $this->session->set_flashdata('importConfirmMessage',true);
        }


    }

    public function _JSONencode ($c, $l){



        $JSONtab = array(
            'Variables' => array(),
            'Events' => array(),
            'Ends' => array()
        );

        $sql = $this->db->query('SELECT * FROM `_variables` WHERE `scenario_FK`= 1');

        foreach ($sql->result() as $row){

            array_push($JSONtab['Variables'], array(
                'id' => $row->id_var,
                'title' => $row->title,
                'initialisation' => $row->initialisation,
                'control' => ($row->control != NULL ? $row->control : ''),
                'controlEffect' => ($row->control_effect != NULL ? $row->control_effect : '')
            ));
        }


        $sqlEvents = $this->db->query('SELECT * FROM `_events` WHERE `scenario_FK`= 1');

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

        $sqlEnds = $this->db->query('SELECT * FROM `_ends` WHERE `scenario_FK`= 1');

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
        if(ENVIRONMENT != 'development') {
            $fp = fopen('app/data/eventData_'.$c.'.json', 'w');
        }else {
            $fp = fopen('../../Client/App/www/data/eventData_'.$c.'.json', 'w');
        }

        //$fp = fopen('data/eventData_'.$c.'.json', 'w');
        fwrite($fp, json_encode($JSONtab));
        fclose($fp);


		// update cache manifest
		$this->load->helper('file');
		$manifest = read_file(FCPATH.'/tpl/manifest_tpl.appcache');
		$manifest = str_replace('[DATE]', date('Y-m-d H:i:s'), $manifest);
		
		if(ENVIRONMENT != 'development') {
            write_file('app/manifest.appcache', $manifest);
        }else {
            write_file('../../Client/App/www/manifest.appcache', $manifest);
        }
		
    }

    public function export(){
        // generate Excel

        $variables = Modules::run('variables/Admin_variables/_getItemList', 1);
        $ends = Modules::run('ends/Admin_ends/_getItemList', 1);
        $events = Modules::run('events/Admin_events/_getItemList', 1);
//        $eventChoices = Modules::run('event_choices/Admin_event_choices/_getItemList', 1);

        $gaugeTab = array (
            1 => 'budget',
            2 => 'lien social' ,
            3 => 'qualité du logement'
        );



        $reader = new PhpOffice\PhpSpreadsheet\Reader\Xlsx();
        $this->excel = $reader->load($this->exportTplPath);

        // VARIABLES
        if (true){
            $this->excel->setActiveSheetIndex(0);

//          as => active Sheet
            $as = $this->excel->getActiveSheet();

            $i=0;
            foreach ($variables->result() as $row){
                $j=0;
                foreach ($row as $value){
                    $as->setCellValueByColumnAndRow($j+1, $i+2, $value);
                    $j++;
                }
                $i++;
            }
        }

        // EVENTS
        if(true){
            $this->excel->setActiveSheetIndex(1);

//          as => active Sheet
            $as = $this->excel->getActiveSheet();

            $mainStyle = $as->getCell('A2')->getXfIndex();
            $secondStyle = $as->getCell('A3')->getXfIndex();
            $thirsStyle = $as->getCell('A5')->getXfIndex();
            //`id`,`id_event`, `condition`, `weight`, `pool`, `background`, `title_FK`, `description_FK`

            $summareyRow = 2;
            $CurrentRow=2;
            foreach ($events->result() as $row){
                $as->getCellByColumnAndRow(1, $CurrentRow)->setXfIndex($mainStyle)->setValue("event");


                $as->getCellByColumnAndRow(2, $CurrentRow)->setXfIndex($mainStyle)->setValue($row->id_event);

                $as->getCellByColumnAndRow(3, $CurrentRow)->setXfIndex($mainStyle)->setValue($row->condition);

                $as->getCellByColumnAndRow(4, $CurrentRow)->setXfIndex($mainStyle)->setValue($row->weight);

                $as->getCellByColumnAndRow(5, $CurrentRow)->setXfIndex($mainStyle)->setValue($row->pool);

                $as->getCellByColumnAndRow(6, $CurrentRow)->setXfIndex($mainStyle)->setValue($row->background);


                $traslations = Modules::run('translation/_getTranslations', $row->title_FK);
                $j=0;


                $CurrentRow++;
                $as->getCellByColumnAndRow(1, $CurrentRow)->setXfIndex($secondStyle)->setValue("title");

                for( $k = 0;$k<5;$k++){
                    $as->getCellByColumnAndRow(2+$k, $CurrentRow)->setXfIndex($secondStyle);
                    $as->getCellByColumnAndRow(2+$k, $CurrentRow+1)->setXfIndex($secondStyle);
                }

                foreach ($traslations as $trans){
                    $as->getCellByColumnAndRow(7+$j, $CurrentRow-1)->setXfIndex($mainStyle);
                    if($as->getCellByColumnAndRow(7+$j,1)->getValue() == ""){
                        $as->getCellByColumnAndRow(7+$j,1)->setValue(Modules::run('translation/_getLangCodeIso', $trans->lang_FK));
                    }
                    $as->getCellByColumnAndRow(7+$j, $CurrentRow)->setXfIndex($secondStyle)->setValue($trans->content);
                    $j++;
                }
                $CurrentRow++;

                $as->getCellByColumnAndRow(1, $CurrentRow)->setXfIndex($secondStyle)->setValue("description");

                $traslations = Modules::run('translation/_getTranslations', $row->description_FK);
                $j=0;

                foreach ($traslations as $trans){
                    if($as->getCellByColumnAndRow(7+$j,1)->getValue() == ""){
                        $as->getCellByColumnAndRow(7+$j,1)->setValue(Modules::run('translation/_getLangCodeIso', $trans->lang_FK));
                    }
                    $as->getCellByColumnAndRow(7+$j, $CurrentRow)->setXfIndex($secondStyle)->setValue($trans->content);

                    $j++;
                }


                //`id_choice`, `command`, `summary_weight`, `summary_gauge_target`, `content_FK`, `event_FK`, `summary_text_FK`

                $choices = Modules::run('event_choices/Admin_event_choices/_getItemListByEvent',$row->id);


                $k=1;
                foreach ($choices->result() as $choice){
                    $CurrentRow++;
                    $as->getCellByColumnAndRow(1, $CurrentRow)->setXfIndex($thirsStyle)->setValue($choice->id_choice);
                    $as->getCellByColumnAndRow(3, $CurrentRow)->setXfIndex($thirsStyle)->setValue($choice->command);


                    $traslations = Modules::run('translation/_getTranslations', $choice->content_FK);
                    $j=0;

                    foreach ($traslations as $trans){
                        if($as->getCellByColumnAndRow(7+$j,1)->getValue() == ""){
                            $as->getCellByColumnAndRow(7+$j,1)->setValue(Modules::run('translation/_getLangCodeIso', $trans->lang_FK)->code_iso);
                        }
                        $as->getCellByColumnAndRow(7+$j, $CurrentRow)->setXfIndex($thirsStyle)->setValue($trans->content);

                        $j++;
                    }

                    $this->excel->setActiveSheetIndex(3);
                    $as = $this->excel->getActiveSheet();

                    $traslations = Modules::run('translation/_getTranslations', $choice->summary_text_FK);
                    $needSummary = false;
                    $j=0;
                    foreach ($traslations as $trans){
                        if($as->getCellByColumnAndRow(4+$j,1)->getValue() == ""){
                            $as->getCellByColumnAndRow(4+$j,1)->setValue(Modules::run('translation/_getLangCodeIso', $trans->lang_FK)->code_iso);
                        }
                        $as->getCellByColumnAndRow(4+$j, $summareyRow)->setXfIndex($thirsStyle)->setValue($trans->content);

                        if($trans->content!=""){
                            $needSummary = true;
                            break;
                        }
                        $j++;
                    }

                    if($needSummary){//$needSummary

                        $as->getCellByColumnAndRow(1, $summareyRow)->setValue($row->id_event);
                        $as->getCellByColumnAndRow(2, $summareyRow)->setValue($choice->id_choice);
                        $as->getCellByColumnAndRow(3, $summareyRow)->setValue($choice->summary_weight);
                        $as->getCellByColumnAndRow(4, $summareyRow)->setValue($gaugeTab[$choice->summary_gauge_target]);

                        $j=0;

                        foreach ($traslations as $trans){
                            if($as->getCellByColumnAndRow(5+$j,1)->getValue() == ""){
                                $as->getCellByColumnAndRow(5+$j,1)->setValue(Modules::run('translation/_getLangCodeIso', $trans->lang_FK));
                            }
                            $as->getCellByColumnAndRow(5+$j, $summareyRow)->setXfIndex($thirsStyle)->setValue($trans->content);

                            $j++;
                        }
                        $summareyRow++;
                    }

                    $this->excel->setActiveSheetIndex(1);
                    $as = $this->excel->getActiveSheet();
                    $k++;

                }

                $CurrentRow++;
//                $as->getCellByColumnAndRow(1, $CurrentRow)->setXfIndex($thirsStyle)->setValue("c1");
//                $CurrentRow++;
//                $as->getCellByColumnAndRow(1, $CurrentRow)->setXfIndex($thirsStyle)->setValue("c2");
//                $CurrentRow++;
            }
        }

        // ENDS
        if(true){

            $this->excel->setActiveSheetIndex(2);

//        as => active Sheet
            $as = $this->excel->getActiveSheet();


            $mainStyle = $as->getCell('A2')->getXfIndex();
            $secondStyle = $as->getCell('A3')->getXfIndex();

            $i=0;
            foreach ($ends->result() as $row){

                $as->getCellByColumnAndRow(1, ($i*3)+2)->setXfIndex($mainStyle)->setValue($row->id_end);
                $as->getCellByColumnAndRow(2, ($i*3)+2)->setXfIndex($mainStyle)->setValue($row->background);

                $j=0;
                $as->getCellByColumnAndRow(1, ($i*3)+3)->setXfIndex($secondStyle)->setValue("title");
                $as->getCellByColumnAndRow(1, ($i*3)+4)->setXfIndex($secondStyle)->setValue("description");

                $as->getCellByColumnAndRow(2, ($i*3)+3)->setXfIndex($secondStyle);
                $as->getCellByColumnAndRow(2, ($i*3)+4)->setXfIndex($secondStyle);

                $traslations = Modules::run('translation/_getTranslations', $row->end_title_FK);

                foreach ($traslations as $trans){
                    $as->getCellByColumnAndRow(3+$j, ($i*3)+2)->setXfIndex($mainStyle);
                    $as->getCellByColumnAndRow(3+$j, ($i*3)+3)->setXfIndex($secondStyle)->setValue($trans->content);
                    $j++;
                }

                $j=0;
                $traslations = Modules::run('translation/_getTranslations', $row->end_description_FK);

                foreach ($traslations as $trans){
                    $as->getCellByColumnAndRow(3+$j, ($i*3)+4)->setXfIndex($secondStyle)->setValue($trans->content);

                    $j++;
                }

                $i++;
            }
        }

		PhpOffice\PhpSpreadsheet\Shared\File::setUseUploadTempDirectory(TRUE);
        $writer =  new PhpOffice\PhpSpreadsheet\Writer\Xlsx($this->excel);
//
//
        $filename = 'PBLS_export_'.date('Ymd-His').'.xlsx';

//        echo(FCPATH.'temp/'.$filename);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); //mime type
        header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
        header('Cache-Control: max-age=0'); //no cache
//

        $writer->save('php://output');

    }
	
	
	
	
	
	
	
	
	public function exportScenarioSimple($scenarioId = 2){
		
		$l = 1; // pas de multi langues dans les scénarios simples, on reste sur la langue 1 (FR)
		
		$sql = 'SELECT `id`, `uid`, `scenario_type`, `title_FK`, `intro_title_FK`, `intro_text_FK`, `about_title_FK`, `about_text_FK`, `show_temporality`, `temporality_labels_FK`, `temporality_questions_per_period`, `temporality_periods_to_win`, `creation_date`, `last_update_date` 
				FROM `scenario` 
				WHERE `id` = ?';
		$row = $this->db->query($sql, array($scenarioId))->row();
		$scenario = array(
			'id' => intval($row->id),
			'uid' => $row->uid,
			'type' => intval($row->scenario_type),
			'title' => Modules::run('translation/_getTranslation', $row->title_FK, $l),
			'introTitle' => Modules::run('translation/_getTranslation', $row->intro_title_FK, $l),
			'introText' => Modules::run('translation/_getTranslation', $row->intro_text_FK, $l),
			'aboutTitle' => Modules::run('translation/_getTranslation', $row->about_title_FK, $l),
			'aboutText' => Modules::run('translation/_getTranslation', $row->about_text_FK, $l),
			'showTemporality' => intval($row->show_temporality),
			'temporalityLabels' => Modules::run('translation/_getTranslation', $row->temporality_labels_FK, $l),
			'temporalityQuestionsPerPeriod' => intval($row->temporality_questions_per_period),
			'temporalityPeriodsToWin' => intval($row->temporality_periods_to_win),
			'creationDate' => intval($row->creation_date),
			'lastUpdateDate' => intval($row->last_update_date),
			'gauges' =>  array()
		);
		
        $JSONtab = array(
			'Scenario' => $scenario,
            'Variables' => array(),
            'Events' => array(),
            'Ends' => array()
        );

        // Variables : generated from scenario gauge
		$varTranspositions = array();
		
		$sql = 'SELECT `id`, `scenario_FK`, `position`, `var`, `label_FK`, `summary_title_FK`, `picto`, `initial_value`, `min_value_to_loose`, `victory_title_FK`, `victory_text_FK`, `defeat_title_FK`, `defeat_text_FK` 
				FROM `scenario_gauge` 
				WHERE `scenario_FK`= ?
				ORDER BY `position` ASC';
		$query = $this->db->query($sql, array($scenarioId));

        foreach ($query->result() as $row){
			$label = Modules::run('translation/_getTranslation', $row->label_FK, $l);
            $summaryTitle = Modules::run('translation/_getTranslation', $row->summary_title_FK, $l);
            $victoryTitle = Modules::run('translation/_getTranslation', $row->victory_title_FK, $l);
            $victoryText = Modules::run('translation/_getTranslation', $row->victory_text_FK, $l);
            $defeatTitle = Modules::run('translation/_getTranslation', $row->defeat_title_FK, $l);
            $defeatText = Modules::run('translation/_getTranslation', $row->defeat_text_FK, $l);

			$JSONtab['Scenario']['gauges'][] =  array(
				'var' => 'var'.$row->id,//$row->var,
				'label' => $label,
				'summaryTitle' => $summaryTitle,
				'picto' => $row->picto,
				'initialValue' => intval($row->initial_value),
				'minValueToLoose' => intval($row->min_value_to_loose),
				/*'victoryTitle' => $victoryTitle,
				'victoryText' => $victoryText,
				'defeatTitle' => $defeatTitle,
				'defeatText' => $defeatText,*/
			);
			
            $JSONtab['Variables'][] =  array(
                'id' => 'var'.$row->id,
                'title' => $row->var,
                'initialisation' => intval($row->initial_value),
                'control' => 'eachEvent',
                //'controlEffect' => 'compareTrigger($var'.$row->id.'<='.intval($row->min_value_to_loose).' && $var1003==0, $var1003=1, insert_event(2_'.$row->id.'))'
                'controlEffect' => 'compareTrigger($var'.$row->id.'<='.intval($row->min_value_to_loose).' && $var1003==0, $var1003=1, endGame(defaite_var'.$row->id.'))'
            );
			$varTranspositions['$'.$row->var] = '$var'.$row->id;
			
			$JSONtab['Ends'][] = array(
				'id' => 'defaite_var'.$row->id,
				'background' => 0,
				'title' => $defeatTitle,
				'text' => $defeatText
			);
			
			$JSONtab['Ends'][] = array(
				'id' => 'victoire_var'.$row->id,
				'background' => 0,
				'title' => $victoryTitle,
				'text' => $victoryText
			);
        }
		// Ajout variables fixes
		$JSONtab['Variables'][] = array(
			'id' => 'var1000',
			'title' => 'nbEventPerDay',
			'initialisation' => $scenario['temporalityQuestionsPerPeriod'],
			'control' => '',
			'controlEffect' => ''
		);
		$JSONtab['Variables'][] = array(
			'id' => 'var1001',
			'title' => 'nbEvent',
			'initialisation' => 1,
			'control' => 'eachEvent',
			'controlEffect' => 'compareTrigger($var1001<='.($scenario['temporalityQuestionsPerPeriod'] * $scenario['temporalityPeriodsToWin']).', $var1001+=1)'
		);
		$JSONtab['Variables'][] = array(
			'id' => 'var1003',
			'title' => 'gameOver',
			'initialisation' => 0,
			'control' => '',
			'controlEffect' => ''
		);
		
		// Récupération des contextes (medias)
		$sqlScenarioMedias = 'SELECT `id`, `scenario_FK`, `label`, `image_url`
			FROM `scenario_media` 
			WHERE `scenario_FK` = ?
			ORDER BY `id` ASC';
        $queryScenarioMedias = $this->db->query($sqlScenarioMedias, array($scenarioId));
		$scenarioMedias = array();
		foreach($queryScenarioMedias->result() as $row) {
			$scenarioMedias[$row->label] = $row->image_url;
		}
		
		// Ajout events
		$sqlEvents = 'SELECT * 
			FROM `_events` 
			WHERE `scenario_FK` = ?
			ORDER BY `id` ASC';
        $queryEvents = $this->db->query($sqlEvents, array($scenarioId));

        $sqlChoices = "SELECT ec.`id`, ec.`id_choice`, ec.`command`, ec.`summary_weight`,  ec.`summary_gauge_target`,  t.`content`, ec.`event_FK`
                        FROM `_event_choice` AS ec INNER JOIN
                            `_translation_object` AS tro ON tro.`id` = ec.`content_FK` INNER JOIN
                            `_translation` AS t ON t.`translation_object_FK` = tro.`id`
                        WHERE ec.`event_FK` = ? AND t.`lang_FK` = ? 
						ORDER BY ec.`id` ASC";

        $sqlSummary = "SELECT ec.`id`, ec.`id_choice`, t.`content`, ec.`summary_text_FK`
                        FROM `_event_choice` AS ec INNER JOIN
                            `_translation_object` AS tro ON tro.`id` = ec.`summary_text_FK` INNER JOIN
                            `_translation` AS t ON t.`translation_object_FK` = tro.`id`
                        WHERE ec.`event_FK` = ? AND t.`lang_FK` = ? AND ec.`id_choice` = ? ";


        foreach ($queryEvents->result() as $row){

            $title = Modules::run('translation/_getTranslation', $row->title_FK, $l);
            $desc = Modules::run('translation/_getTranslation', $row->description_FK, $l);

            $choices = array();

            $query = $this->db->query($sqlChoices, array($row->id, $l));

            foreach($query->result() as $line){

                $summaryText = $this->db->query($sqlSummary, array($row->id, $l, $line->id_choice))->row()->content;

                array_push($choices, array(
                    'id' => $row->id_event . '|' . $line->id_choice,
                    'text' => $line->content,
                    'value' => strtr($line->command, $varTranspositions),
                    'summaryWeight' => $line->summary_weight,
                    'summaryGauge' => $line->summary_gauge_target,
                    'summaryText' => ($summaryText != NULL ? $summaryText : '')
                ));
            }

            $JSONtab['Events'][] = array(
                'id' => $row->id_event,
                'condition' => ($row->condition != NULL ? $row->condition : ''),
                'weight' => ($row->weight != NULL ? $row->weight : ''),
                'pool' => ($row->pool != NULL ? $row->pool : ''),
                'background' => base_url('medias/'.$scenario['uid'].'/'.$scenarioMedias[$row->background]),
                'title' => $title,
                'textEvent' => $desc,
                'Choices' => $choices
            );

        }

		/*
        $sqlEnds = $this->db->query('SELECT * FROM `_ends` WHERE `scenario_FK`= 1');

        foreach($sqlEnds->result() as $row){

            $title = Modules::run('translation/_getTranslation', $row->end_title_FK, $l);
            $desc = Modules::run('translation/_getTranslation', $row->end_description_FK, $l);


            array_push($JSONtab['Ends'], array(
                'id' => $row->id_end,
                'background' => $row->background,
                'title' => $title,
                'text' => $desc
            ));

        }
		*/
		$this->load->helper('ajax');
		setAjaxHeaders();
		echo json_encode($JSONtab, JSON_PRETTY_PRINT);
        
		/*
		if(ENVIRONMENT != 'development') {
            $fp = fopen('app/data/eventData_'.$c.'.json', 'w');
        }else {
            $fp = fopen('../../Client/App/www/data/eventData_'.$c.'.json', 'w');
        }

        //$fp = fopen('data/eventData_'.$c.'.json', 'w');
        fwrite($fp, json_encode($JSONtab));
        fclose($fp);


		// update cache manifest
		$this->load->helper('file');
		$manifest = read_file(FCPATH.'/tpl/manifest_tpl.appcache');
		$manifest = str_replace('[DATE]', date('Y-m-d H:i:s'), $manifest);
		
		if(ENVIRONMENT != 'development') {
            write_file('app/manifest.appcache', $manifest);
        }else {
            write_file('../../Client/App/www/manifest.appcache', $manifest);
        }
		*/
		
    }
}
