<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Admin extends Admin_Controller {
	
	function __construct()
    {
		error_reporting(E_ALL);
		parent::__construct();
		
		//$this->load->model('books/book_model');
		//$this->load->helper('form');
		
	}
	
	public function index() {
		
		$data = array(
			'currentSection' => 'home',
			'view_file' => 'admin/home_view',
        	'page_title' => 'Admin',
			//'page_meta_description' => $pageDescription,
			'additionnalCssFiles' => array(),
			'additionnalJsFiles' => array(),
			//$data['additionnalJsCmd_wready'] = array('classe.init()';
        	//$data['additionnalJsCmd_wload'] = array('classe.init()';
			//$data['additionnalJsCmd_wscroll'] = array('classe.init()';
			//$data['additionnalJsCmd_wresize'] = array('classe.init()';
			
			'mainTitle' => 'Bienvenue sur l\'admin de l\'application '.$this->config->item('site_name'),
			'breadcrumb' => array(
				//'Label' => 'url'
			),
			
		);
        echo Modules::run('template/adm/_default', $data);
    }
	
}
