<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Admin_event_choices extends Admin_Controller {


    private $mainModel;


    function __construct()
    {
        parent::__construct();

        $this->load->model('event_choices/event_choice_model');
        $this->mainModel = $this->event_choice_model;
    }

    public function index() {

    }

    public function _addItem($data) {
        $newId = $this->mainModel->addItem($data);
        return $newId;
    }

    public function _deleteItem($itemId) {
        $newId = $this->mainModel->deleteItem($itemId);
        return $newId;
    }

    public function _getItem($itemId) {
        $list = $this->mainModel->getItem($itemId);
        return $list;
    }

    public function _getItemList($scenarioId) {
        $item = $this->mainModel->getItemList($scenarioId);
        return $item;
    }

    public function _getItemListByEvent($id_event){
        $list = $this->mainModel->getItemListByEvent($id_event);
        return $list;
    }

    public function _getItemListByEventInLang($id_event, $id_lang){
        $list = $this->mainModel->getItemListByEventInLang($id_event, $id_lang);
        return $list;
    }

    public function _getByEventAndChoice($scenarioId, $id_event, $id_choice) {
        $item = $this->mainModel->getByEventAndChoice($scenarioId, $id_event, $id_choice);
        return $item;
    }

    public function _updateSummaryWeight($itemId,$weight) {
        $item = $this->mainModel->updateSummaryWeight($itemId,$weight);
        return $item;
    }
    public function _updateGaugeTarget($itemId,$gauge_target) {
        $item = $this->mainModel->updateGaugeTarget($itemId,$gauge_target);
        return $item;
    }
}
