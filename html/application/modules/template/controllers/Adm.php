<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');


class Adm extends MY_Controller {

    function __construct() {
        parent::__construct();
        $this->load->helper('template/template');
		
    }
	
	public function _default($data) {
        $data = hydratation_variables_tpl($data);
        $this->load->view('adm/default_tpl', $data);
    }
	
	public function _blank($data) {
        $data = hydratation_variables_tpl($data);
        $this->load->view('adm/blank_tpl', $data);
    }
	
	
}
