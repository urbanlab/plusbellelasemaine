<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Misc extends MY_Controller {

	function __construct()
    {
		parent::__construct();
	}

	public function index() {
		
		
    }
	
	public function missing() {
		
		$this->output->set_status_header('404');
		
		$data = array(
			'view_file' => 'misc/missing_view',
        	'page_title' => 'Page manquante',
			'additionnalCssFiles' => array('missing'),
			//'additionnalJsFiles' => array('tarifs'),
			//$data['additionnalJsCmd_wready'] = array('classe.init()';
        	//$data['additionnalJsCmd_wload'] = array('classe.init()';
			//$data['additionnalJsCmd_wscroll'] = array('classe.init()';
			//$data['additionnalJsCmd_wresize'] = array('classe.init()',
			
		);
        echo Modules::run('template/front/_blank', $data);
	}
}
