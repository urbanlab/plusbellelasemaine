<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/*
  | -------------------------------------------------------------------
  | Site_template
  | -------------------------------------------------------------------
  | Controller général pour le templating du site version front office
  |
  |
 */

class Security extends MY_Controller {

    function __construct() {
        parent::__construct();
        
    }

	
	public function _encodePassword($password) {
		return sha1($password.$this->config->item('encryption_key'));
	}

  public function _userid() {
      $admin = $this->session->userdata('admin');
      if ($admin == FALSE || !isset($admin['id']))
        return -1;
      else
        return $admin['id'];
  }
	
  public function _is_super_admin() {
    $admin = $this->session->userdata('admin');
        if ($admin == FALSE || !isset($admin['id']) || $admin['type'] >=2) {
            return FALSE;
        } else {
            return TRUE;
        }
  }
  
  public function _is_admin() {
    $admin = $this->session->userdata('admin');
        if ($admin == FALSE || !isset($admin['id']) || $admin['type'] > 2) {
            return FALSE;
        } else {
            return TRUE;
        }
  }
  
	public function _is_admin_connected() {
        $admin = $this->session->userdata('admin');
        if ($admin == FALSE || !isset($admin['id'])) {
            return FALSE;
        } else {
            return TRUE;
        }
    }
	
	/**
     * Check if the current user is connected
     * If not, redirect to the login page
     */
    public function _make_sure_admin_is_connected() {
        if(!$this->_is_admin_connected()){
            
            if($this->uri->uri_string() != "autorisation-necessaire"){
                $this->session->set_userdata('REDIRECT', $this->uri->uri_string());
            }
            redirect(base_url().'admin/identification');
			die();
        }
    }
	
	public function _redirect_if_already_logged(){
        if($this->_is_admin_connected()){
            redirect(base_url().'home');
			die();
        }
    }
    
}
