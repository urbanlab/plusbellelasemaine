<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Emailer extends MY_Controller {

    function __construct() 
    {
        parent::__construct();
	    $this->load->library('email');
    }


    public function _sendNewAdminPassword($data)
    {
        $this->email->from($this->config->item('global_email_from'), $this->config->item('global_email_from_name'));
        $this->email->to($data['to']);
        $this->email->subject( mb_convert_encoding("Renouvellement de votre mot de passe", "UTF-8") );
        $message = mb_convert_encoding($this->load->view('emailer/forgotAdminPassword_view', $data, TRUE), "UTF-8");

        $this->email->message($message);
        $this->email->send();
    }
    
	/*
	public function showEmail() 
    {
		error_reporting(E_ALL);
		
		$data = array(
			
		);
		$this->load->view('emailer/forgotAdminPassword_view', $data);
	}
	*/

}
