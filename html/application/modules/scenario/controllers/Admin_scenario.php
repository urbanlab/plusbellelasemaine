<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');



class Admin_scenario extends Admin_Controller
{
    private $exportTplPath = "tpl/export_simple_scenario_tpl.xlsx";
    private $exportComplexeTplPath = "tpl/export_story_tpl.xlsx";

    private $mainModelUrl = 'scenario_model';
    private $gaugeModelUrl = 'scenario_gauge_model';
    private $mediaModelUrl = 'scenario_media_model';

    private $lang;

	function __construct()
    {
		parent::__construct();

        $this->load->model($this->mainModelUrl);
        $this->load->model($this->gaugeModelUrl);
        $this->load->model($this->mediaModelUrl);

        $this->lang = Modules::run('translation/_getLangList');
        $this->defaultLangCode = array_keys($this->lang)[0];
		$this->defaultLang = $this->lang[$this->defaultLangCode];
        
	}
	
	public function index() {
		$this->view(NULL);

	}

	public function view($itemData = NULL, $error = NULL)
    {
		$this->load->helper('form');

		$mainScenarioComplexe = $this->scenario_model->getItem(1);
		$mainScenarioComplexe->title = Modules::run('translation/_getTranslation', $mainScenarioComplexe->title_FK, $this->defaultLang);
		
		$data = array(
			'currentSection' => 'scenarios',
			'view_file' => 'scenario/scenario_view',
        	'page_title' => 'Gestion des scénarios',
        	'page_meta_description' => '',
			'additionnalCssFiles' => array('iconpicker','scenario'),
			'additionnalJsFiles' => array('iconpicker','scenario'),
			'additionnalJsCmd_wready' => array('scenario.init();'),
        	//$data['additionnalJsCmd_wload'] = array('classe.init()';
			//$data['additionnalJsCmd_wscroll'] = array('classe.init()';
			//$data['additionnalJsCmd_wresize'] = array('classe.init()';
			
			'mainTitle' => 'Gestion des scénarios',
			'breadcrumb' => array(
				'Gestion des scénarios' => ''
			),
            'itemsData' => $this->scenario_model->getItemList($this->defaultLang),
            'itemData' => !isset($itemData->events) ? $itemData : NULL,
			'mainScenarioComplexe' => $mainScenarioComplexe,
			'error' => $error,
			
			'itemViewData' => isset($itemData->events) ? $itemData : NULL,
			
		);
		
		
        echo Modules::run('template/adm/_default', $data);
    }

    public function editItem($itemId)
    {
        // récupération du scénario
        $item = $this->scenario_model->getItem($itemId);

        // récupération des jauges
        $gauges = $this->scenario_gauge_model->getForScenario($itemId);

        $item->gauge_var = array();
        $item->gauge_label = array();
        $item->gauge_summary_title = array();
        $item->gauge_picto = array();
        $item->gauge_initial_value = array();
        $item->gauge_min_value_to_loose = array();
        $item->gauge_victory_title = array();
        $item->gauge_victory_text = array();
        $item->gauge_defeat_title = array();
        $item->gauge_defeat_text = array();

        for($i=0;$i<3;$i++)
        {
            if (isset($gauges[$i]))
            {
                $item->gauge_var[$i] = $gauges[$i]->var;
                $item->gauge_label[$i] = Modules::run('translation/_getTranslation', $gauges[$i]->label_FK, $this->defaultLang);
                $item->gauge_summary_title[$i] = Modules::run('translation/_getTranslation', $gauges[$i]->summary_title_FK, $this->defaultLang);
                $item->gauge_picto[$i] = $gauges[$i]->picto;
                $item->gauge_initial_value[$i] = $gauges[$i]->initial_value;
                $item->gauge_min_value_to_loose[$i] = $gauges[$i]->min_value_to_loose;
                $item->gauge_victory_title[$i] = Modules::run('translation/_getTranslation', $gauges[$i]->victory_title_FK, $this->defaultLang);
                $item->gauge_victory_text[$i] = Modules::run('translation/_getTranslation', $gauges[$i]->victory_text_FK, $this->defaultLang);
                $item->gauge_defeat_title[$i] = Modules::run('translation/_getTranslation', $gauges[$i]->defeat_title_FK, $this->defaultLang);
                $item->gauge_defeat_text[$i] = Modules::run('translation/_getTranslation', $gauges[$i]->defeat_text_FK, $this->defaultLang);
            }
            else
            {
                $item->gauge_var[$i] = '';
                $item->gauge_label[$i] = '';
                $item->gauge_summary_title[$i] = '';
                $item->gauge_picto[$i] = $this->config->item('default_gauge_picto');
                $item->gauge_initial_value[$i] = $this->config->item('default_gauge_initial_value');
                $item->gauge_min_value_to_loose[$i] = $this->config->item('default_gauge_min_value_to_loose');
                $item->gauge_victory_title[$i] = '';
                $item->gauge_victory_text[$i] = '';
                $item->gauge_defeat_title[$i] = '';
                $item->gauge_defeat_text[$i] = '';

            }
        }

        // récupération des médias
        $medias = $this->scenario_media_model->getForScenario($itemId);

        $item->media_id = array();
        $item->media_label = array();

        for($i=0;$i<$this->config->item('nb_medias');$i++)
        {
            if (isset($medias[$i]))
            {
                $item->media_id[$i] = $medias[$i]->id;
                $item->media_label[$i] = $medias[$i]->label;
                $item->media_image_url[$i] = $medias[$i]->image_url;
            }
            else
            {
                $item->media_id[$i] = '';
                $item->media_label[$i] = '';
                $item->media_image_url[$i] = '';
            }
        }

        // translations
        $item->title = Modules::run('translation/_getTranslation', $item->title_FK, $this->defaultLang);
        $item->intro_title = Modules::run('translation/_getTranslation', $item->intro_title_FK, $this->defaultLang);
        $item->intro_text = Modules::run('translation/_getTranslation', $item->intro_text_FK, $this->defaultLang);
        $item->about_title = Modules::run('translation/_getTranslation', $item->about_title_FK, $this->defaultLang);
        $item->about_text = Modules::run('translation/_getTranslation', $item->about_text_FK, $this->defaultLang);
        $item->temporality_labels = Modules::run('translation/_getTranslation', $item->temporality_labels_FK, $this->defaultLang);

        ////////////////////////////////////////////////////////////////
        // vérification de la cohérence du scénario
        if($item->scenario_type == 2) {
			$this->_checkEvents($item->id);
		}else{
			$this->_checkImport($item->id);
		}
		
        $this->view($item, NULL);

    }

    public function deleteItem($itemId)
    {
        Modules::run('security/_make_sure_admin_is_connected');

        $item = $this->scenario_model->getItem($itemId);
        if ($item == FALSE)
        {
            redirect(base_admin_url(current_section()));
            return;
        }

        // suppression des translations
        $translationIds = array();
        $translationIds[] = intval($item->title_FK);
        $translationIds[] = intval($item->intro_title_FK);
        $translationIds[] = intval($item->intro_text_FK);
        $translationIds[] = intval($item->about_title_FK);
        $translationIds[] = intval($item->about_text_FK);
        $translationIds[] = intval($item->temporality_labels_FK);

        $gauges = $this->scenario_gauge_model->getForScenario($itemId);
        foreach($gauges as $gauge)
        {
            $translationIds[] = intval($gauge->label_FK);
            $translationIds[] = intval($gauge->summary_title_FK);
            $translationIds[] = intval($gauge->victory_title_FK);
            $translationIds[] = intval($gauge->victory_text_FK);
            $translationIds[] = intval($gauge->defeat_title_FK);
            $translationIds[] = intval($gauge->defeat_text_FK);
        }

        // suppression des jauges
        $this->scenario_gauge_model->deleteForScenario($itemId);

        // suppression des médias
        /*$medias = $this->scenario_media_model->getForScenario($itemId);
        $dstpath = FCPATH.'medias/'.$item->uid;

        foreach($medias as $media)
        {
            $media_path = $dstpath . '/' . $media->image_url;
            if (is_file($media_path))
                unlink($media_path);
        }
		*/
		$this->load->helper('file');
		delete_files(FCPATH.'medias/'.$item->uid, TRUE);
		rmdir(FCPATH.'medias/'.$item->uid);
        
		$this->scenario_media_model->deleteForScenario($itemId);

        // suppression de l'item
        $this->scenario_model->deleteItem($itemId);

        // suppression de toutes les anciennes données
        $eventIds = array();

        $sql = 'SELECT `id`, `title_FK`, `description_FK` 
				FROM `_events` 
				WHERE `scenario_FK` = ? ';
        $sql2 = 'SELECT `content_FK`, `summary_text_FK` 
				FROM `_event_choice` 
				WHERE `event_FK` = ? ';

        $query = $this->db->query($sql, array($item->id));
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
        $this->db->query($sql, array($item->id));
        if(count($eventIds) > 0) {
            $sql = 'DELETE FROM `_event_choice` 
					WHERE `event_FK` IN ('.implode(',', $eventIds).') ';
            $this->db->query($sql);
        }


        // suppression de toutes les traductions
        if(count($translationIds) > 0) {
            $sql = 'DELETE FROM `_translation_object` 
					WHERE `id` IN ('.implode(',', $translationIds).') ';
            $this->db->query($sql);
            $sql = 'DELETE FROM `_translation` 
					WHERE `translation_object_FK` IN ('.implode(',', $translationIds).') ';
            $this->db->query($sql);
        }

        redirect(base_admin_url(current_section()));
    }


    public function saveForm()
    {
        if($this->input->post('editedItemId') == -1) {
            $this->_addItem();
        }else{
            $this->_editItem($this->input->post('editedItemId'));
        }
    }
	
	public function viewItemEvents($itemId) {
		$this->load->model('events/event_model');
		$this->load->model('event_choices/event_choice_model');
		
		// récupération du scénario
        $item = $this->scenario_model->getItem($itemId);
		$item->title = Modules::run('translation/_getTranslation', $item->title_FK, $this->defaultLang);
		
		// récupération des events
        $item->events = array();
		$events = $this->event_model->getItemList($item->id);
		foreach($events->result() as $event) {
			$event->title = Modules::run('translation/_getTranslation', $event->title_FK, $this->defaultLang);
			$event->description = Modules::run('translation/_getTranslation', $event->description_FK, $this->defaultLang);
			
			$choices = $this->event_choice_model->getItemListByEventInLang($event->id, $this->defaultLang);
			$event->choices = array();
			foreach($choices->result() as $choice) {
				$event->choices[] = $choice;
			}
			
			$item->events[] = $event;
		}
		
		$this->view($item, NULL);
	}
	
	

    public function _addItem()
    {
        $data = new stdClass();

        // uid
        $data->uid = strtoupper(random_string('alnum', 6));

        // title
        $queryTitleID = Modules::run('translation/_addTranslationObject', 'scenario_title');

        foreach($this->lang as $l){
            Modules::run('translation/_addTranslation', array($queryTitleID, $l, $this->_safePost('title')));
        }

        $data->title_FK = $queryTitleID;

        // intro_title
        $queryIntroTitleID = Modules::run('translation/_addTranslationObject', 'scenario_intro_title');

        foreach($this->lang as $l){
            Modules::run('translation/_addTranslation', array($queryIntroTitleID, $l, $this->_safePost('intro_title')));
        }

        $data->intro_title_FK = $queryIntroTitleID;

        // intro_text
        $queryIntroTextID = Modules::run('translation/_addTranslationObject', 'scenario_intro_description');

        foreach($this->lang as $l){
            Modules::run('translation/_addTranslation', array($queryIntroTextID, $l, $this->_safePost('intro_text')));
        }

        $data->intro_text_FK = $queryIntroTextID;

        // about_title
        $queryAboutTitleID = Modules::run('translation/_addTranslationObject', 'scenario_about_title');

        foreach($this->lang as $l){
            Modules::run('translation/_addTranslation', array($queryAboutTitleID, $l, $this->_safePost('about_title')));
        }

        $data->about_title_FK = $queryAboutTitleID;

        // about_text
        $queryAboutTextID = Modules::run('translation/_addTranslationObject', 'scenario_about_description');

        foreach($this->lang as $l){
            Modules::run('translation/_addTranslation', array($queryAboutTextID, $l, $this->_safePost('about_text')));
        }

        $data->about_text_FK = $queryAboutTextID;


        $data->show_temporality = empty($this->input->post('show_temporality'))?'0':'1';
        $data->temporality_periods_to_win = $this->_safePost('temporality_periods_to_win');
        $data->temporality_questions_per_period = $this->_safePost('temporality_questions_per_period');

        // temporality labels
        $queryTemporalityLabelsID = Modules::run('translation/_addTranslationObject', 'scenario_temporality_labels');

        foreach($this->lang as $l){
            Modules::run('translation/_addTranslation', array($queryTemporalityLabelsID, $l, $this->input->post('temporality_labels')));
        }
        $data->temporality_labels_FK = $queryTemporalityLabelsID;


        // création du scénario
        $data = $this->scenario_model->addItem($data);


        // puis ajout des jauges
        $position = 1;
        for($i=0;$i<3;$i++)
        {
            // initialisation des données de la jauge
            $gauge = new stdClass();
            $gauge->scenario_FK = $data->id;
            $gauge->position = $position;

            $gauge->var = $this->_safePost('gauge_var', $i);

            // une jauge sans nom est considéré comme non valide
            if (empty($gauge->var))
                continue;

            $label_FK = Modules::run('translation/_addTranslationObject', 'scenario_gauge_label');
            foreach($this->lang as $l){
                Modules::run('translation/_addTranslation', array($label_FK, $l, $this->_safePost('gauge_label',$i)));
            }
            $gauge->label_FK = $label_FK;

            $summary_title_FK = Modules::run('translation/_addTranslationObject', 'scenario_gauge_summary_title');
            foreach($this->lang as $l){
                Modules::run('translation/_addTranslation', array($summary_title_FK, $l, $this->_safePost('gauge_summary_title',$i)));
            }
            $gauge->summary_title_FK = $summary_title_FK;


            $gauge->picto = $this->input->post('gauge_picto')[$i];
            $gauge->initial_value = $this->input->post('gauge_initial_value')[$i];
            $gauge->min_value_to_loose = $this->input->post('gauge_min_value_to_loose')[$i];

            $victory_title_FK = Modules::run('translation/_addTranslationObject', 'scenario_gauge_victory_title');
            foreach($this->lang as $l){
                Modules::run('translation/_addTranslation', array($victory_title_FK, $l, $this->_safePost('gauge_victory_title',$i)));
            }
            $gauge->victory_title_FK = $victory_title_FK;

            $victory_text_FK = Modules::run('translation/_addTranslationObject', 'scenario_gauge_victory_text');
            foreach($this->lang as $l){
                Modules::run('translation/_addTranslation', array($victory_text_FK, $l, $this->_safePost('gauge_victory_text',$i)));
            }
            $gauge->victory_text_FK = $victory_text_FK;

            $defeat_title_FK = Modules::run('translation/_addTranslationObject', 'scenario_gauge_defeat_title');
            foreach($this->lang as $l){
                Modules::run('translation/_addTranslation', array($defeat_title_FK, $l, $this->_safePost('gauge_defeat_title',$i)));
            }
            $gauge->defeat_title_FK = $defeat_title_FK;

            $defeat_text_FK = Modules::run('translation/_addTranslationObject', 'scenario_gauge_defeat_text');
            foreach($this->lang as $l){
                Modules::run('translation/_addTranslation', array($defeat_text_FK, $l, $this->_safePost('gauge_defeat_text',$i)));
            }
            $gauge->defeat_text_FK = $defeat_text_FK;

            // si on arrive ici c'est que les données de la jauge sont valides
            $this->scenario_gauge_model->addItem($gauge);
            $position++;
        }

        ////////////////////////////////////////////////////////////////
        // contextes
        $ok = $this->_uploadMedias($data);

        ////////////////////////////////////////////////////////////////
        // excel
        if (!$this->_uploadSimpleExcel($data))
            $ok = false;

        ////////////////////////////////////////////////////////////////
        // vérification de la cohérence du scénario
        if (!$this->_checkEvents($data->id))
            $ok = false;

        if (!$ok)
        {
            redirect(base_admin_url(current_section() . "/editItem/" . $data->id));
        }
        else
        {
            $this->_generateScenarioSimple($data->id);
            redirect(base_admin_url(current_section()));
        }
    }

    public function _editItem($itemId)
    {
        $data = $this->scenario_model->getItem($itemId);
		
		
        ////////////////////////////////////////////////////////////////
        // title
        foreach($this->lang as $l){
            Modules::run('translation/_updateContent', $data->title_FK, $l, $this->input->post('title'));
        }

        // intro_title
        foreach($this->lang as $l){
            Modules::run('translation/_updateContent', $data->intro_title_FK, $l, $this->input->post('intro_title'));
        }

        // intro_text
        foreach($this->lang as $l){
            Modules::run('translation/_updateContent', $data->intro_text_FK, $l, $this->input->post('intro_text'));
        }

        // about_title
        foreach($this->lang as $l){
            Modules::run('translation/_updateContent', $data->about_title_FK, $l, $this->input->post('about_title'));
        }

        // about_text
        foreach($this->lang as $l){
            Modules::run('translation/_updateContent', $data->about_text_FK, $l, $this->input->post('about_text'));
        }

        ////////////////////////////////////////////////////////////////
        // jauges

        // suppression des anciennes jauges d'abord
        $gauges = $this->scenario_gauge_model->getForScenario($data->id);
        $translationIds = array();
        foreach($gauges as $gauge)
        {
            $translationIds[] = intval($gauge->label_FK);
            $translationIds[] = intval($gauge->summary_title_FK);
            $translationIds[] = intval($gauge->victory_title_FK);
            $translationIds[] = intval($gauge->victory_text_FK);
            $translationIds[] = intval($gauge->defeat_title_FK);
            $translationIds[] = intval($gauge->defeat_text_FK);
        }

        if(count($translationIds) > 0) {
            $sql = 'DELETE FROM `_translation_object` 
					WHERE `id` IN ('.implode(',', $translationIds).') ';
            $this->db->query($sql);
            $sql = 'DELETE FROM `_translation` 
					WHERE `translation_object_FK` IN ('.implode(',', $translationIds).') ';
            $this->db->query($sql);
        }

        $this->scenario_gauge_model->deleteForScenario($data->id);

        // puis création des nouvelles
        $position = 1;
        for($i=0;$i<3;$i++)
        {
            // initialisation des données de la jauge
            $gauge = new stdClass();
            $gauge->scenario_FK = $data->id;
            $gauge->position = $position;

            $gauge->var = $this->_safePost('gauge_var', $i);

            if (empty($gauge->var))
                continue;

            $label_FK = Modules::run('translation/_addTranslationObject', 'scenario_gauge_label');
            foreach($this->lang as $l){
                Modules::run('translation/_addTranslation', array($label_FK, $l, $this->_safePost('gauge_label',$i)));
            }
            $gauge->label_FK = $label_FK;

            $summary_title_FK = Modules::run('translation/_addTranslationObject', 'scenario_gauge_summary_title');
            foreach($this->lang as $l){
                Modules::run('translation/_addTranslation', array($summary_title_FK, $l, $this->_safePost('gauge_summary_title',$i)));
            }
            $gauge->summary_title_FK = $summary_title_FK;


            $gauge->picto = $this->input->post('gauge_picto')[$i];
            $gauge->initial_value = $this->input->post('gauge_initial_value')[$i];
            $gauge->min_value_to_loose = $this->input->post('gauge_min_value_to_loose')[$i];

            $victory_title_FK = Modules::run('translation/_addTranslationObject', 'scenario_gauge_victory_title');
            foreach($this->lang as $l){
                Modules::run('translation/_addTranslation', array($victory_title_FK, $l, $this->_safePost('gauge_victory_title',$i)));
            }
            $gauge->victory_title_FK = $victory_title_FK;

            $victory_text_FK = Modules::run('translation/_addTranslationObject', 'scenario_gauge_victory_text');
            foreach($this->lang as $l){
                Modules::run('translation/_addTranslation', array($victory_text_FK, $l, $this->_safePost('gauge_victory_text',$i)));
            }
            $gauge->victory_text_FK = $victory_text_FK;

            $defeat_title_FK = Modules::run('translation/_addTranslationObject', 'scenario_gauge_defeat_title');
            foreach($this->lang as $l){
                Modules::run('translation/_addTranslation', array($defeat_title_FK, $l, $this->_safePost('gauge_defeat_title',$i)));
            }
            $gauge->defeat_title_FK = $defeat_title_FK;

            $defeat_text_FK = Modules::run('translation/_addTranslationObject', 'scenario_gauge_defeat_text');
            foreach($this->lang as $l){
                Modules::run('translation/_addTranslation', array($defeat_text_FK, $l, $this->_safePost('gauge_defeat_text',$i)));
            }
            $gauge->defeat_text_FK = $defeat_text_FK;

            // si on arrive ici c'est que les données de la jauge sont valides
            $this->scenario_gauge_model->addItem($gauge);
            $position++;
        }

        ////////////////////////////////////////////////////////////////
        // temporalité

        $data->show_temporality = empty($this->input->post('show_temporality'))?'0':'1';

        foreach($this->lang as $l){
            Modules::run('translation/_updateContent', $data->temporality_labels_FK, $l, $this->input->post('temporality_labels'));
        }

        $data->temporality_periods_to_win = $this->_safePost('temporality_periods_to_win');
        $data->temporality_questions_per_period = $this->_safePost('temporality_questions_per_period');

        $this->scenario_model->updateItem($data);


        ////////////////////////////////////////////////////////////////
        // contextes
        $ok = $this->_uploadMedias($data);

        ////////////////////////////////////////////////////////////////
        // excel
		if($data->scenario_type == 2) {
			if (!$this->_uploadSimpleExcel($data))
				$ok = false;
		}else{
			if (!$this->_uploadComplexeExcel($data))
				$ok = false;
		}
        ////////////////////////////////////////////////////////////////
        // vérification de la cohérence du scénario
        if($data->scenario_type == 2) {
			if (!$this->_checkEvents($data->id))
            	$ok = false;
		}else{
			if (!$this->_checkImport($data->id))
            	$ok = false;
		}

        if (!$ok)
        {
            redirect(base_admin_url(current_section() . "/editItem/" . $data->id));
        }
        else
        {
            if($data->scenario_type == 2) {
				$this->_generateScenarioSimple($data->id);
			}else{
				foreach($this->lang as $c=>$l){
					$this->_generateScenarioComplexe($data->id, $c, $l);
				}
			}
            redirect(base_admin_url(current_section()));
        }
    }


    public function _safePost($name, $index=null)
    {
        if (isset($index))
        {
            if (empty($this->input->post($name)[$index])) return '';
            else return $this->input->post($name)[$index];
        }
        else
        {
            if (empty($this->input->post($name))) return '';
            else return $this->input->post($name);
        }
    }
	
	public function _updateCacheManifest() {
		// load cache manifest template
		$this->load->helper('file');
		$manifest = read_file(FCPATH.'/tpl/manifest_tpl.appcache');
		
		// update last update date
		$manifest = str_replace('[DATE]', date('Y-m-d H:i:s'), $manifest);
		
		// add medias and JSON data of each scenario
		$scenariosData = array();
		$scenarios = $this->scenario_model->getAllScenarios();
		foreach($scenarios->result() as $scenario) {
			$scenariosData[] = './data/'.$scenario->uid.'/scenarioData_FR.json';
			$medias = $this->scenario_media_model->getForScenario($scenario->id);
			foreach($medias as $media) {
				$scenariosData[] = './data/'.$scenario->uid.'/'.$media->image_url;
			}
		}
		$manifest = str_replace('[SCENARIOS_DATA]', implode("\n", $scenariosData), $manifest);
		
		if(ENVIRONMENT != 'development') {
            write_file('app/manifest.appcache', $manifest);
        }else {
            write_file('../../Client/App/www/manifest.appcache', $manifest);
        }
	}

    public function _uploadMedias($data)
    {
        // d'abord création du dossier s'il n'existe pas
        //$dstpath = FCPATH.'medias/'.$data->uid;
		if(ENVIRONMENT != 'development') {
            $dstpath = 'app/data/'.$data->uid;
        }else {
            $dstpath = '../../Client/App/www/data/'.$data->uid;
        }

        if (!is_dir($dstpath)) {
            mkdir($dstpath, 0700);
			chmod($dstpath, 0700);
		}

        if (!is_dir($dstpath))
        {
            $this->session->set_flashdata('medias_error', 'Impossible de créer le dossier '.$dstpath);
            return false;
        }

        $errors = array();

        $this->load->library('upload');

        $config = array();
        $config['upload_path'] = $dstpath;
        $config['allowed_types'] = 'jpg|jpeg|png';

        $files = $_FILES;
        $nb = count($_FILES['media_file']['name']);
		
		for($i=0; $i<$nb; $i++)
        {
            $id = !empty($this->input->post('media_id')[$i])?$this->input->post('media_id')[$i]:'';
            $label = !empty($this->input->post('media_label')[$i])?$this->input->post('media_label')[$i]:'';
			
            // récupération de l'ancien média s'il y en a un
            if (!empty($id)) {
				$media = $this->scenario_media_model->getItem($id);
			}else{
				$media = NULL;
			}

            //echo $id." ".$label." ".$files['media_file']['type'][$i].'<br/>';
			
            // est-ce qu'il y a un upload à faire ?
            if (!empty($label) && !empty($files['media_file']['name'][$i]))
            {
                //$filename = preg_replace('/[^a-zA-Z0-9\-\._]/','', $label).'.jpg';
                $filename = random_string('md5').'.jpg';
				
				$_FILES['media_file']['name'] = $files['media_file']['name'][$i];
                $_FILES['media_file']['type'] = $files['media_file']['type'][$i];
                $_FILES['media_file']['tmp_name'] = $files['media_file']['tmp_name'][$i];
                $_FILES['media_file']['error'] = $files['media_file']['error'][$i];
                $_FILES['media_file']['size'] = $files['media_file']['size'][$i];

                $config['file_name'] = $filename;

                $this->upload->initialize($config);
                $this->upload->do_upload();

                $half_width = 512;
                $half_height = 768;
                $final_ratio = (float)$half_width / (float)$half_height;

                if ($this->upload->do_upload('media_file'))
                {
                    $uploadData = $this->upload->data();
                    $filename = $uploadData['file_name'];
                    $filetype = $uploadData['file_type'];
                    $filepath = $dstpath . '/' . $filename;

                    if ($filetype == 'image/jpeg' || $filetype == 'image/jpg')
                        $src = imagecreatefromjpeg($filepath);
                    elseif ($filetype == 'image/png')
                        $src = imagecreatefrompng($filepath);

                    list($src_width, $src_height) = getimagesize($filepath);
                    $ratio = (float)$src_width / $src_height;

                    // resize and crop
                    if ($ratio > $final_ratio)
                    {
                        $src_x = round(0.5 * ($src_width - (float)$src_height * (float)$half_width / (float)$half_height));
                        if ($src_x < 0) $src_x = 0;
                        $src_y = 0;
                        $src_width -= 2.0 * $src_x;
                    } else
                    {
                        $src_x = 0;
                        $src_y = round(0.5 * ($src_height - (float)$src_width * (float)$half_height / (float)$half_width));
                        if ($src_y < 0) $src_y = 0;
                        $src_height -= 2.0 * $src_y;
                    }

                    $tmp = imagecreatetruecolor(2 * $half_width, 2 * $half_height);
                    imagecopyresampled($tmp, $src, 0, 0, $src_x, $src_y, 2 * $half_width, 2 * $half_height, $src_width, $src_height);
                    imagejpeg($tmp, $filepath, 80);

                    // ajout ou mise à jour du media
                    if (empty($id))
                    {
                        $media = new stdClass();
                        $media->scenario_FK = $data->id;
                        $media->label = $label;
                        $media->image_url = $filename;
                        $this->scenario_media_model->addItem($media);
                    }
                    else
                    {
                        // suppression de l'ancien fichier
                        if ($media)
                        {
                            $old_media_path = $dstpath . '/' . $media->image_url;
                            //echo 'delete '.$old_media_path.'<br/>';

                            if (is_file($old_media_path))
                                unlink($old_media_path);
                        }

                        $media->label = $label;
                        $media->image_url = $filename;
                        $this->scenario_media_model->updateItem($media);
                    }
                }
                else
                    $errors[] = "Erreur à l'upload du fichier ".$_FILES['media_file']['name'].": ".$this->upload->display_errors();
            }
            // sinon on regarde s'il s'agit d'une suppression ou d'une mise à jour simple
            else if (!empty($id) && $media)
            {
                // suppression
                if (empty($label))
                {
                    $old_media_path = $dstpath . '/' . $media->image_url;
                    //echo 'delete '.$old_media_path.'<br/>';

                    if (is_file($old_media_path))
                        unlink($old_media_path);

                    $this->scenario_media_model->deleteItem($id);
                }
                else
                {
                    $media->label = $label;
                    $this->scenario_media_model->updateItem($media);
                }
            }
        }


        // aggrégation des erreurs
        if (count($errors)>0)
        {
            $this->session->set_flashdata('medias_error', implode("<br/>",$errors));
            return false;
        }
		return true;
    }

    public function _uploadComplexeExcel($data)
    {
		if (empty($_FILES['xlsxFile']['type']))
            return true;

        $this->load->library('upload');

        // upload du fichier xslx
        $config = array();
        $config['upload_path'] = './uploads/xlsx/';
        $config['allowed_types'] = 'xlsx';

        $this->upload->initialize($config);

        if ($this->upload->do_upload('xlsxFile'))
        {
            $uploadData = $this->upload->data();
            $filename = $uploadData['full_path'];
        }
        else
        {
            $this->session->set_flashdata('xls_error', 'Erreur à l\'importation du fichier excel: '.$this->upload->display_errors());
            return false;
        }

        
		// Load excel data
        $objReader = new PhpOffice\PhpSpreadsheet\Reader\Xlsx();

        $objPhpSpreadsheet = $objReader->load($filename);

        $tab = explode("/", $xlsxFilePath);
        $this->filename = $tab[count($tab)-1];

        $this->_importVariables($data->id, $this->_getSheetData($objPhpSpreadsheet->getSheet(0),5));
		$this->_importEvents($data->id, $this->_getSheetData($objPhpSpreadsheet->getSheet(1),8+count($this->lang)));
        $this->_importEnds($data->id, $this->_getSheetData($objPhpSpreadsheet->getSheet(2), 4+count($this->lang)));
        $this->_importSummary($data->id, $this->_getSheetData($objPhpSpreadsheet->getSheet(3), 6+count($this->lang)));
        /*$this->_checkImport($data->id);
        foreach($this->lang as $c=>$l){
            $this->_generateScenarioComplexe($data->id, $c, $l);
        }*/
	}
	
    public function _uploadSimpleExcel($data)
    {
        if (empty($_FILES['xlsxFile']['type']))
            return true;

        $this->load->library('upload');

        // upload du fichier xslx
        $config = array();
        $config['upload_path'] = './uploads/xlsx/';
        $config['allowed_types'] = 'xlsx';

        $this->upload->initialize($config);

        if ($this->upload->do_upload('xlsxFile'))
        {
            $uploadData = $this->upload->data();
            $filename = $uploadData['full_path'];
        }
        else
        {
            $this->session->set_flashdata('xls_error', 'Erreur à l\'importation du fichier excel: '.$this->upload->display_errors());
            return false;
        }

        // suppression de toutes les anciennes données
        $eventIds = array();
        $translationIds = array();

        $sql = 'SELECT `id`, `title_FK`, `description_FK` 
				FROM `_events` 
				WHERE `scenario_FK` = ? ';
        $sql2 = 'SELECT `content_FK`, `summary_text_FK` 
				FROM `_event_choice` 
				WHERE `event_FK` = ? ';

        $query = $this->db->query($sql, array($data->id));
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

        if(count($translationIds) > 0) {
            $sql = 'DELETE FROM `_translation_object` 
                WHERE `id` IN ('.implode(',', $translationIds).') ';
            $this->db->query($sql);
            $sql = 'DELETE FROM `_translation` 
                WHERE `translation_object_FK` IN ('.implode(',', $translationIds).') ';
            $this->db->query($sql);
        }

        // remove previous data
        $sql = 'DELETE FROM `_events` 
				WHERE `scenario_FK` = ? ';
        $this->db->query($sql, array($data->id));
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


        // Puis traitement du fichier excel
        $objReader = new PhpOffice\PhpSpreadsheet\Reader\Xlsx();
        $objPhpSpreadsheet = $objReader->load($filename);
        $sheetData = $this->_getSheetData($objPhpSpreadsheet->getSheet(0),15);

        // import new data
        for($i = 0; $i < count($sheetData); $i++)
        {
            // mémoriser les data de l'event(de la ligne)
            $eventRow = array();

            // id_event: 1_x
            $eventRow[] = "1_".($i+1);
            // condition: null
            $eventRow[] = null;
            // weight: 1
            $eventRow[] = 1;
            // pool: vide
            $eventRow[] = '';
            // background
            $eventRow[] = $sheetData[$i][2];

            // faire etape 1 et 2 pour title
            $queryTitleID = Modules::run('translation/_addTranslationObject', 'event_title');

            // title FR > $sheetData[$i][0]
            foreach($this->lang as $l){
                Modules::run('translation/_addTranslation', array($queryTitleID, $l, $sheetData[$i][0]));
            }

            // faire etape 1 et 2 pour desc
            $queryDescID = Modules::run('translation/_addTranslationObject', 'event_description');

            // desc FR > $sheetData[$i][1]
            foreach($this->lang as $l){
                Modules::run('translation/_addTranslation', array($queryDescID, $l, $sheetData[$i][1]));
            }

            // insert de l'event
            $eventRow[] = $queryTitleID;
            $eventRow[] = $queryDescID;

            $queryEventID = Modules::run('events/Admin_events/_addItem', $data->id, $eventRow);

            // pour chacun des 3 choix (min 1 , max 3)
            for($j = 0; $j < 3; $j++)
            {
                $col = 3 + $j*4;
                
				$choiceRow = array();

                // ignore choix vide
                if (empty($sheetData[$i][$col]))
                    continue;

                // id_choice: c1, c2, c3
                $choiceRow[] = 'c'.($j+1);
                // command: col+1
                $choiceRow[] = $sheetData[$i][$col+1];
                // summary_weight: 1
                $choiceRow[] = 1;

                /*$col2 = explode('|', $sheetData[$i][$col+2]);
                if (count($col2)!=2)
                    continue;
				*/
                // summary_gauge_target: col+2
                $choiceRow[] = $sheetData[$i][$col+2];

                // faire etape 1 et 2 pour desc
                $queryChoiceID = Modules::run('translation/_addTranslationObject', 'event_choice');

                // choix FR > col+0
                foreach($this->lang as $l){
                    Modules::run('translation/_addTranslation', array($queryChoiceID, $l, $sheetData[$i][$col+0]));
                }

                $querySumID = Modules::run('translation/_addTranslationObject', 'summary_choice');

                // choix FR col+3
                foreach($this->lang as $l){
                    Modules::run('translation/_addTranslation', array($querySumID, $l, $sheetData[$i][$col+3]));

                }

                // insert de du choix
                $choiceRow[] = $queryChoiceID;
                $choiceRow[] = $queryEventID;
                $choiceRow[] = $querySumID;

				Modules::run('event_choices/Admin_event_choices/_addItem', $choiceRow);
            }
        }
		
		return true;
    }

    public function _getSheetData($objWorksheet, $nbCols)
    {
        $sheetData = array();
        $cnt = 0;
        foreach ($objWorksheet->getRowIterator() as $row)
        {
            if($cnt !=0 && $objWorksheet->getCellByColumnAndRow(1, $cnt+1)->getValue() == ''){break;}
            if($cnt > 0) {

                $dataRow = array();

                for($j = 0; $j< $nbCols ;$j++){
                    $dataRow[] = $objWorksheet->getCellByColumnAndRow($j+1,$cnt+1)->getValue();
                }


                $sheetData[] = $dataRow;
            }
            $cnt++;
        }
        return $sheetData;
    }

    // vérifie si le scenario ne contient pas des références à des jauges ou contexte qui ne sont pas déclarés
    public function _checkEvents($scenarioId)
    {
        // vérification des références sur les jauges
        $errors = array();

        $gauge_names = array();
        $gauges = $this->scenario_gauge_model->getForScenario($scenarioId);
        foreach($gauges as $gauge)
            $gauge_names[] = $gauge->var;

        $sql = "SELECT ec.command, ec.summary_gauge_target, ec.id_choice,
                t.content
				FROM _events AS e 
				INNER JOIN _translation AS t ON (t.translation_object_FK = e.title_FK AND t.lang_FK = ?)
				INNER JOIN _event_choice AS ec ON ec.event_FK = e.id
				WHERE e.`scenario_FK` = ?";
        $choices = $this->db->query($sql, array($this->defaultLang, $scenarioId));

        foreach ($choices->result() as $row)
        {
            $num = substr($row->id_choice, 1);
            $title = $row->content;
            if(preg_match_all('/\$[\w]+/' , $row->command, $matches))
            {
                foreach($matches[0] as $v)
                {
                    $v = str_replace('$', '', $v);
                    if(!(in_array ($v, $gauge_names)))
                        $errors[] = "Mauvais nom de jauge pour les <strong>effets $num</strong> de \"$title\": <strong>$v</strong>";
                }
            }
            $v = $row->summary_gauge_target;
            if(!empty($v) && !(in_array ($v, $gauge_names)))
                $errors[] = "Mauvais nom de jauge pour la <strong>synthèse $num</strong> de \"$title\": <strong>$v</strong>";
        }

        // vérification des références sur les medias
        $media_names = array();
        $medias = $this->scenario_media_model->getForScenario($scenarioId);
        foreach($medias as $media)
            $media_names[] = $media->label;

        $sql = "SELECT e.background, t.content
				FROM _events AS e
				INNER JOIN _translation AS t ON (t.translation_object_FK = e.title_FK AND t.lang_FK = ?)
				WHERE scenario_FK = ?";
        $events = $this->db->query($sql, array($this->defaultLang, $scenarioId));

        foreach ($events->result() as $row)
        {
            $v = $row->background;
            $title = $row->content;
            if(!empty($v) && !(in_array ($v, $media_names)))
                $errors[] = "Mauvais <strong>nom de contexte</strong> pour \"$title\": <strong>$v</strong>";
        }

        // aggrégation des erreurs
        if (count($errors)>0)
        {
            $this->session->set_flashdata('check_errors', implode("<br/>",$errors));
            return false;
        }

        return true;
    }

    public function exportItem($scenarioId)
    {
		$data = $this->scenario_model->getItem($scenarioId);
		
		if($data->scenario_type == 2) {
			$this->exportItemSimple($data->id);
		}else{
			$this->exportItemComplexe($data->id);
		}
		
	}
    
	public function exportItemComplexe($scenarioId) {
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
        $this->excel = $reader->load($this->exportComplexeTplPath);

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
                        $as->getCellByColumnAndRow(4, $summareyRow)->setValue($choice->summary_gauge_target);

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
	
	public function exportItemSimple($scenarioId) {
		// generate Excel
        $sql = "SELECT e.id,
                t1.content as title,
                t2.content as description,
                e.background
				FROM _events AS e 
				INNER JOIN _translation AS t1 ON (t1.translation_object_FK = e.title_FK AND t1.lang_FK = ?)
				INNER JOIN _translation AS t2 ON (t2.translation_object_FK = e.description_FK AND t2.lang_FK = ?)
				WHERE e.scenario_FK = ? ORDER BY id";
        $events = $this->db->query($sql, array($this->defaultLang,$this->defaultLang,$scenarioId));

        $reader = new PhpOffice\PhpSpreadsheet\Reader\Xlsx();
        $this->excel = $reader->load($this->exportTplPath);

        $this->excel->setActiveSheetIndex(0);
        $as = $this->excel->getActiveSheet();

        $row = 2;
        foreach ($events->result() as $event)
        {
            $as->setCellValueByColumnAndRow(1, $row, $event->title);
            $as->setCellValueByColumnAndRow(2, $row, $event->description);
            $as->setCellValueByColumnAndRow(3, $row, $event->background);

            $col=4;
            for($i=1;$i<=3;$i++)
            {
                $sql = "SELECT 
                    ec.command,
                    ec.summary_gauge_target,
                    t1.content as content,
                    t2.content as summary_text
                    FROM _event_choice AS ec 
                    INNER JOIN _translation AS t1 ON (t1.translation_object_FK = ec.content_FK AND t1.lang_FK = ?)
                    INNER JOIN _translation AS t2 ON (t2.translation_object_FK = ec.summary_text_FK AND t2.lang_FK = ?)
                    WHERE ec.event_FK = ? AND ec.id_choice = ? LIMIT 1";
                $choices = $this->db->query($sql, array($this->defaultLang, $this->defaultLang, $event->id, 'c'.$i));

                foreach ($choices->result() as $choice)
                {
                    $as->setCellValueByColumnAndRow($col+0, $row, $choice->content);
                    $as->setCellValueByColumnAndRow($col+1, $row, $choice->command);
                    $as->setCellValueByColumnAndRow($col+2, $row, $choice->summary_gauge_target);
                    $as->setCellValueByColumnAndRow($col+3, $row, $choice->summary_text);
                    $col+=4;
                }
            }

            $row++;
        }

        PhpOffice\PhpSpreadsheet\Shared\File::setUseUploadTempDirectory(TRUE);
        $writer =  new PhpOffice\PhpSpreadsheet\Writer\Xlsx($this->excel);

        $filename = 'PBLS_export_'.date('Ymd-His').'.xlsx';

//        echo(FCPATH.'temp/'.$filename);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); //mime type
        header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
        header('Cache-Control: max-age=0'); //no cache

        $writer->save('php://output');
    }

	public function _generateScenarioComplexe($scenarioId, $c, $l) {

        $row = $this->scenario_model->getItem($scenarioId);
		$scenario = array(
			'type' => 2,
			//'id' => intval($row->id),
			'uid' => $row->uid,
			'type' => intval($row->scenario_type),
			'title' => Modules::run('translation/_getTranslation', $row->title_FK, $l),
			'introTitle' => Modules::run('translation/_getTranslation', $row->intro_title_FK, $l),
			'introText' => nl2br(Modules::run('translation/_getTranslation', $row->intro_text_FK, $l)),
			'aboutTitle' => Modules::run('translation/_getTranslation', $row->about_title_FK, $l),
			'aboutText' => nl2br(Modules::run('translation/_getTranslation', $row->about_text_FK, $l)),
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
		
		// Creation de jauges
		$varTranspositions = array();
		$gauges = $this->scenario_gauge_model->getForScenario($scenarioId);
		foreach ($gauges as $row){
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
				'minValueToLoose' => intval($row->min_value_to_loose)
			);
			
			$varTranspositions[$row->var] = 'var'.$row->id;
			
		}
		
		// Récupération des contextes (medias)
		$scenarioMedias = array();
		$medias = $this->scenario_media_model->getForScenario($scenarioId);
		foreach($medias as $row) {
			$scenarioMedias[$row->label] = $row->image_url;
		}
		

        $sql = $this->db->query('SELECT * FROM `_variables` WHERE `scenario_FK`= 1');

        foreach ($sql->result() as $row){

            array_push($JSONtab['Variables'], array(
                'id' => strtr($row->id_var, $varTranspositions),//$row->id_var,
                'title' => $row->title,
                'initialisation' => $row->initialisation,
                'control' => ($row->control != NULL ? $row->control : ''),
                'controlEffect' => ($row->control_effect != NULL ? strtr($row->control_effect, $varTranspositions) : '')
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
                    'value' => strtr($line->command, $varTranspositions),
                    'summaryWeight' => $line->summary_weight,
                    'summaryGauge' => strtr($line->summary_gauge_target, $varTranspositions),
                    'summaryText' => ($summaryText != NULL ? $summaryText : '')
                ));
            }

            array_push($JSONtab['Events'], array(
                'id' => $row->id_event,
                'condition' => ($row->condition != NULL ? strtr($row->condition, $varTranspositions) : ''),
                'weight' => ($row->weight != NULL ? $row->weight : ''),
                'pool' => ($row->pool != NULL ? $row->pool : ''),
                'background' => 'data/'.$scenario['uid'].'/'.$scenarioMedias[$row->background],
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

        // Write data to json
        if(ENVIRONMENT != 'development') {
            $fp = fopen('app/data/'.$scenario['uid'].'/scenarioData_'.$c.'.json', 'w');
        }else {
            $fp = fopen('../../Client/App/www/data/'.$scenario['uid'].'/scenarioData_'.$c.'.json', 'w');
        }
        fwrite($fp, json_encode($JSONtab));
        fclose($fp);

		
		// Update cache manifest to be up to date with last modifications
		$this->_updateCacheManifest();
		
    }

    function _generateScenarioSimple($scenarioId) 
    { 
        $l = $this->defaultLang; // pas de multi langues dans les scénarios simples, on reste sur la langue 1 (FR)
		
		$row = $this->scenario_model->getItem($scenarioId);
		$scenario = array(
			'type' => 2,
			//'id' => intval($row->id),
			'uid' => $row->uid,
			'type' => intval($row->scenario_type),
			'title' => Modules::run('translation/_getTranslation', $row->title_FK, $l),
			'introTitle' => Modules::run('translation/_getTranslation', $row->intro_title_FK, $l),
			'introText' => nl2br(Modules::run('translation/_getTranslation', $row->intro_text_FK, $l)),
			'aboutTitle' => Modules::run('translation/_getTranslation', $row->about_title_FK, $l),
			'aboutText' => nl2br(Modules::run('translation/_getTranslation', $row->about_text_FK, $l)),
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
		
		$gauges = $this->scenario_gauge_model->getForScenario($scenarioId);
		foreach ($gauges as $row){
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
                'controlEffect' => 'compareTrigger($var'.$row->id.'<='.intval($row->min_value_to_loose).' && $var1003==0, $var1003=1, end_game(defaite_'.$row->id.'))'
            );
			//$varTranspositions['$'.$row->var] = '$var'.$row->id;
			$varTranspositions[$row->var] = 'var'.$row->id;
			
			$JSONtab['Ends'][] = array(
				'id' => 'defaite_'.$row->id,
				'background' => 0,
				'title' => $defeatTitle,
				'text' => $defeatText
			);
			
			$JSONtab['Ends'][] = array(
				'id' => 'victoire_'.$row->id,
				'background' => 0,
				'title' => $victoryTitle,
				'text' => $victoryText
			);
        }
		
		// Ajout des conditions de victoire
		if(count($gauges) == 1) {
			$JSONtab['Variables'][0]['controlEffect'] .= '; compareTrigger($var1001>='.($scenario['temporalityQuestionsPerPeriod'] * $scenario['temporalityPeriodsToWin']).', $var1003=1, end_game(victoire_'.$gauges[0]->id.'))';
		}else if(count($gauges) == 2) {
			$JSONtab['Variables'][0]['controlEffect'] .= '; compareTrigger($var1001>='.($scenario['temporalityQuestionsPerPeriod'] * $scenario['temporalityPeriodsToWin']).' && $var'.$gauges[0]->id.'>=$var'.$gauges[1]->id.', $var1003=1, end_game(victoire_'.$gauges[0]->id.'))';
			$JSONtab['Variables'][1]['controlEffect'] .= '; compareTrigger($var1001>='.($scenario['temporalityQuestionsPerPeriod'] * $scenario['temporalityPeriodsToWin']).' && $var'.$gauges[1]->id.'>=$var'.$gauges[0]->id.', $var1003=1, end_game(victoire_'.$gauges[1]->id.'))';
		}else if(count($gauges) == 3) {
			$JSONtab['Variables'][0]['controlEffect'] .= '; compareTrigger($var1001>='.($scenario['temporalityQuestionsPerPeriod'] * $scenario['temporalityPeriodsToWin']).' && $var'.$gauges[0]->id.'>=$var'.$gauges[1]->id.' && $var'.$gauges[0]->id.'>=$var'.$gauges[2]->id.', $var1003=1, end_game(victoire_'.$gauges[0]->id.'))';
			$JSONtab['Variables'][1]['controlEffect'] .= '; compareTrigger($var1001>='.($scenario['temporalityQuestionsPerPeriod'] * $scenario['temporalityPeriodsToWin']).' && $var'.$gauges[1]->id.'>=$var'.$gauges[0]->id.' && $var'.$gauges[1]->id.'>=$var'.$gauges[2]->id.', $var1003=1, end_game(victoire_'.$gauges[1]->id.'))';
			$JSONtab['Variables'][2]['controlEffect'] .= '; compareTrigger($var1001>='.($scenario['temporalityQuestionsPerPeriod'] * $scenario['temporalityPeriodsToWin']).' && $var'.$gauges[2]->id.'>=$var'.$gauges[0]->id.' && $var'.$gauges[2]->id.'>=$var'.$gauges[1]->id.', $var1003=1, end_game(victoire_'.$gauges[2]->id.'))';
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
		$scenarioMedias = array();
		$medias = $this->scenario_media_model->getForScenario($scenarioId);
		foreach($medias as $row) {
			$scenarioMedias[$row->label] = $row->image_url;
		}
		
		// Ajout events
		$queryEvents = Modules::run('events/admin_events/_getItemList', $scenarioId);
		$eventIndex = 1;
		foreach ($queryEvents->result() as $row){

            $title = Modules::run('translation/_getTranslation', $row->title_FK, $l);
            $desc = Modules::run('translation/_getTranslation', $row->description_FK, $l);

            $choices = array();

            $query = Modules::run('event_choices/admin_event_choices/_getItemListByEventInLang', $row->id, $l);
			
            foreach($query->result() as $line){

                array_push($choices, array(
                    'id' => $row->id_event . '|' . $line->id_choice,
                    'text' => $line->content,
                    //'value' => strtr($line->command, $varTranspositions).($queryEvents->num_rows() > $eventIndex ? '; trigger_event(1_'.($eventIndex+1).')' : ''),
                    'value' => strtr($line->command, $varTranspositions),
                    'summaryWeight' => $line->summary_weight,
                    'summaryGauge' => strtr($line->summary_gauge_target, $varTranspositions),
                    'summaryText' => $line->summary_text
                ));
            }

            $JSONtab['Events'][] = array(
                'id' => $row->id_event,
                'condition' => ($row->condition != NULL ? $row->condition : ''),
                'weight' => ($row->weight != NULL ? $row->weight : ''),
                'pool' => '',//($eventIndex > 1 ? 'triggered' : ''),
                'background' => 'data/'.$scenario['uid'].'/'.$scenarioMedias[$row->background],
                'title' => $title,
                'textEvent' => $desc,
                'Choices' => $choices
            );
			
			$eventIndex++;
        }
		
		
		// Write data to json
        if(ENVIRONMENT != 'development') {
            $fp = fopen('app/data/'.$scenario['uid'].'/scenarioData_'.$this->defaultLangCode.'.json', 'w');
        }else {
            $fp = fopen('../../Client/App/www/data/'.$scenario['uid'].'/scenarioData_'.$this->defaultLangCode.'.json', 'w');
        }
        fwrite($fp, json_encode($JSONtab));
        fclose($fp);
		
		
		// Update cache manifest to be up to date with last modifications
		$this->_updateCacheManifest();
    } 
	
	
	
	
	
	
	
	//**********************************
	//		Scenario complexe import data functions
	
	
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
		
        for($i = 0; $i < count($sheetData); $i++){
            $choice = Modules::run('event_choices/Admin_event_choices/_getByEventAndChoice', $scenarioId, $sheetData[$i][0], $sheetData[$i][1]);

            // lire la ligne title $sheetData[$i] > ligne title
            $summaryWeight = empty($sheetData[$i][2])? 0 : $sheetData[$i][2];
            $summaryGaugeTarget = empty($sheetData[$i][3])? '' : $sheetData[$i][3];
			Modules::run('event_choices/Admin_event_choices/_updateSummaryWeight', $choice->id, $summaryWeight);
			Modules::run('event_choices/Admin_event_choices/_updateGaugeTarget', $choice->id, $summaryGaugeTarget);
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
            $this->session->set_flashdata('check_errors', implode("<br/>",$tabError));
            return FALSE;
        }else {
            return TRUE;
        }


    }
}
