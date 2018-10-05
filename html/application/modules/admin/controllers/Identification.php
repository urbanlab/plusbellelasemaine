<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Identification extends MY_Controller {

	function __construct()
    {
		parent::__construct();
	}

	public function index()
    {
        /*
		// show encrypted password
        echo Modules::run('security/_encodePassword','a'); die();
		*/
        
        if(Modules::run('security/_is_user_connected') == TRUE) {
			redirect('admin');
			return;
		}
		
		$data = array(
			'view_file' => 'admin/identification_view',
        	'page_title' => 'Identification',
			'additionnalCssFiles' => array('identification'),
			'additionnalJsFiles' => array('icheck'),
			//$data['additionnalJsCmd_wready'] = array('classe.init()';
        	//$data['additionnalJsCmd_wload'] = array('classe.init()';
			//$data['additionnalJsCmd_wscroll'] = array('classe.init()';
			//$data['additionnalJsCmd_wresize'] = array('classe.init()';
			
			'classes' => ['login-page'],
			
		);
        echo Modules::run('template/adm/_blank', $data);
    }
	
	public function forgotPassword()
    {
		$data = array(
			'view_file' => 'admin/forgotPassword_view',
        	'page_title' => 'Mot de passe oublié',
			'additionnalCssFiles' => array('identification'),
			'additionnalJsFiles' => array('icheck'),
			//$data['additionnalJsCmd_wready'] = array('classe.init()';
        	//$data['additionnalJsCmd_wload'] = array('classe.init()';
			//$data['additionnalJsCmd_wscroll'] = array('classe.init()';
			//$data['additionnalJsCmd_wresize'] = array('classe.init()';
			
			'classes' => ['login-page'],
		);
        echo Modules::run('template/adm/_blank', $data);
    }
	
}
