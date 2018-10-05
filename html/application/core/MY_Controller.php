<?php defined('BASEPATH') OR exit('No direct script access.');

class MY_Controller extends MX_Controller {

    public $user;
	
	public function __construct()
    {
        parent::__construct();

        //header('X-UA-Compatible: IE=edge,chrome=1');

    }



}

class Admin_Controller extends MY_Controller {
	
	public $admin;
	
    public function __construct()
    {
        parent::__construct();
        
		if(!Modules::run('security/_is_admin')) {
			redirect('admin/identification');
		}
	
		$this->admin = $this->session->userdata('admin');
    }

}
