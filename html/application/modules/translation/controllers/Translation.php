<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Translation extends MY_Controller {
	
	private $mainModelUrl = 'translation/translation_model';
	private $mainModelName = 'translation_model';
	private $mainModel;
	
	var $ss;
	var $i2locSmokittenSpreadsheetId = '1MPMmDLdDPoyGY-15-QloBThAVjFDo-dZFK00L5q80J0';
	var $i2locSmokittenParkSpreadsheetId = '1LEduuapMofLo_RL-0M0NMyStF40M4c00bo861FqV73M';
	var $i2locSmokittenCommonSpreadsheetId = '13t0FCtDVvzAKe_2v2GxvljIEwREmMwyzbu_ojDSO310';
	
	
	function __construct()
    {
		parent::__construct();
		
		$this->load->model($this->mainModelUrl);
		$this->mainModel = $this->{$this->mainModelName};
	}
	
	public function index() {
		
	}
	
	public function _addTranslation($label) {
		$newId = $this->mainModel->addTranslation($label);
		return $newId;
	}

    public function _addTranslationObject($label) {
        $newId = $this->mainModel->addTranslationObject($label);
        return $newId;
    }

    public function _getItemList(){
	    $list = $this->mainModel->getItemList();
	    return $list;
    }

    public function _getTranslations($itemId){
        $translations = $this->mainModel->getTranslations($itemId);
        return $translations;
    }

	public function _getTranslation($itemId, $lang) {
		$translation = $this->mainModel->getTranslation($itemId, $lang);
		return $translation;
	}

	public function _updateContent($itemId, $langId, $content) {
		$this->mainModel->updateContent($itemId, $langId, $content);
		return;
	}

	public function _getLangCodeIso($langId){

	    $res = $this->mainModel->getLangCodeIso($langId)->code_iso;
	    return $res;
    }

    public function  _getLangList(){
	    $langList = array();
	    foreach ($this->mainModel->getLangList() as $lang){
	        $langList[$lang->code_iso] = $lang->id;
        }

        return $langList;
    }

}
