<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Admin_variables extends Admin_Controller {


    private $mainModel;


    function __construct()
    {
        parent::__construct();

        $this->load->model("variables/variable_model");
        $this->mainModel = $this->variable_model;
    }
	
	public function index() {
		
	}
	
	public function _addItem($scenarioId, $data) {
        
		$newId = $this->mainModel->addItem($scenarioId, $data);
		return $newId;
	}

    public function _deleteItem($itemId) {
        $newId = $this->mainModel->deleteItem($itemId);
        return $newId;
    }

	public function _getItem($itemId) {
		$item = $this->mainModel->getItem($itemId);
		return $item;
	}

	public function _updateItem($data) {
        $item = $this->mainModel->updateItem($data);
        return $item;
    }

    public function _getItemList($scenarioId) {
        $item = $this->mainModel->getItemList($scenarioId);
        return $item;
    }

}
