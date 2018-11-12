<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Admin_Userbo extends MY_Controller {
	
	private $mainModelUrl = 'userbo_model';
	private $mainModel;
	
	
	function __construct()
    {
		parent::__construct();
		
		$this->load->model($this->mainModelUrl);
		
	}
	
	public function index() {
		$this->view(NULL);
	}



	public function view($itemData = NULL, $error = NULL) {
		Modules::run('security/_make_sure_admin_is_connected');
		
		$this->load->helper('form');
		
		$data = array(
			'currentSection' => 'userbos',
			'view_file' => 'userbo/userbo_view',
        	'page_title' => 'Gestion des Admins',
        	'page_meta_description' => '',
			'additionnalCssFiles' => array(),
			'additionnalJsFiles' => array(),
			//$data['additionnalJsCmd_wready'] = array('classe.init()';
        	//$data['additionnalJsCmd_wload'] = array('classe.init()';
			//$data['additionnalJsCmd_wscroll'] = array('classe.init()';
			//$data['additionnalJsCmd_wresize'] = array('classe.init()';
			
			'mainTitle' => 'Gestion des Admins',
			'breadcrumb' => array(
				'Gestion des Admins' => ''
			),
			
			'itemsData' => $this->userbo_model->getAdminList(),
			'itemData' => $itemData,
			'error' => $error,
		);
        echo Modules::run('template/adm/_default', $data);
    }
	
	
	
	
	public function autoConnect($token = NULL) {
		
		$this->session->unset_userdata('admin');
		
		if($token == NULL) {
			redirect(base_admin_url('identification'), 'refresh');
			return;
		}else{
			// check token
			$c = curl_init();
			curl_setopt($c, CURLOPT_CAINFO, FCPATH.'application/libraries/cacert.pem');
			curl_setopt($c, CURLOPT_FAILONERROR, false);
			curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($c, CURLOPT_URL, dowino_api_url().'app/checkToken/'.$token);

			$response = curl_exec($c);
			$responseCode = curl_getinfo($c, CURLINFO_HTTP_CODE);


			if($response === FALSE) {
				echo "\n<br>Curl error: " . curl_error($c);
				
			}else{
				if($responseCode == 200) {
					//pr(json_decode($response));
				}else{
					echo 'Error '.$responseCode."\n<br>";
					pr(json_decode($response));
				}
			}
			
			curl_close($c);
			
			
			if($responseCode !== 200) {
				redirect(base_admin_url('identification'), 'refresh');
				return;
			}

		}
		
		
		// get super admin user
		//**************
		$user = $this->userbo_model->getUserSuperAdmin();
		if($user === FALSE) {
			redirect(base_admin_url('identification'), 'refresh');
			return;
		}
		
		$data = array(
			'admin' => array(
				'id' => $user->id,
				'type' => $user->userType,
				'login' => $user->login,
				'password' => $user->password,
				'firstname' => $user->firstname,
				'lastname' => $user->lastname,
			)
		);
		$this->session->set_userdata($data);
		
		// renvoi sur la page demandée après authentification
		if($this->session->userdata('REDIRECT')) {
			redirect($this->session->userdata('REDIRECT'), 'refresh');
		}else{
			redirect(base_admin_url(), 'refresh');
		}
		
	}
	
	public function changePassword(){
        
		if (!$this->input->is_ajax_request()) {
			$this->session->unset_userdata('admin');
			redirect(base_admin_url('identification'));
			die();
		}
		
		if(Modules::run('security/_is_admin_connected') == FALSE) {
			echo -1000;
			die();
		}
		
		$currentPassword = $this->input->post('currentPassword', TRUE);
		$newPassword1 = $this->input->post('newPassword1', TRUE);
		$newPassword2 = $this->input->post('newPassword2', TRUE);
		
		
		if($newPassword1 != $newPassword2 || empty($newPassword1)) {
			echo -2;
			return;
		}
		
		$admin = $this->session->userdata('admin');
		
		if(Modules::run('security/_encodePassword', $currentPassword) == $admin['password']) {
			$admin['password'] = Modules::run('security/_encodePassword', $newPassword1);
			$this->session->set_userdata('admin', $admin);
			
			
			$this->userbo_model->changePassword($admin['id'], Modules::run('security/_encodePassword', $newPassword1));
       		
			echo 1;
		}else{
			echo -1;
		}
		 
    }
	
	
	public function deleteItem($itemId) {
		Modules::run('security/_make_sure_admin_is_connected');
		
		$item = $this->userbo_model->getItem($itemId);
		
		$this->userbo_model->deleteItem($itemId);
		
		redirect(base_admin_url(current_section()));
		
	}
	
	public function editItem($itemId) {
		Modules::run('security/_make_sure_admin_is_connected');
		
		$item = $this->userbo_model->getItem($itemId);
		
		$this->view($item, NULL);
		
	}
	
	public function forgotPassword() {
        //Modules::run("security/_redirect_if_already_logged");

		$this->load->helper('string');
        $email = $this->input->post('email', TRUE);
		
		$user = $this->userbo_model->getUserDataByEmail($email);
		if($user == FALSE) {
			$this->session->set_flashdata('forgotPassword_email', $email);
			$this->session->set_flashdata('errorMessage','Email invalide');
			redirect(base_admin_url('identification/forgotPassword'), 'refresh');
			die();
		}else{
			
			$newPassword = random_string('alnum', 8);
			
			// update password in DB
			$this->userbo_model->changePassword($user->id, Modules::run('security/_encodePassword', $newPassword));
			
			// send password to the user
			$data = array(
				'to' => $user->login,
				'newPassword' => $newPassword,
			);
			Modules::run('emailer/_sendNewAdminPassword', $data);
			
			$this->session->set_flashdata('confirmMessage','Votre nouveau mot de passe à été envoyé, consultez vos emails.');
			redirect(base_admin_url('identification/forgotPassword'), 'refresh');
			die();
		}
    }

	
	public function login() {
		
		$this->session->unset_userdata('admin');
		
		$login = $this->input->post('login', TRUE);
		$password = $this->input->post('password', TRUE);
		
		if(($login == FALSE) || ($password == FALSE)) {
			redirect(base_admin_url('identification'), "refresh");
			return;
		}
		
		$res = $this->_checkLoginPassword($login, $password);
		
		switch($res) {
		
			case 1: {
				// renvoi sur la page demandée après authentification
				if($this->session->userdata('REDIRECT')) {
					redirect($this->session->userdata('REDIRECT'), 'refresh');
				}else{
					redirect(base_admin_url(), 'refresh');
				}
				break;
			}
			
			default: {
				$this->session->set_flashdata('errorMessage','Identifiant / mot de passe invalides');
				redirect(base_admin_url('identification'), 'refresh');
				break;
			}
		}
	}
	
	
	
	
	 /**
     * Logout the current user :
     * Destroy session and redirect to home page
     */
    public function logout() {
        $this->_disconnect_user();
        redirect(base_admin_url('identification'), 'refresh');
    }



	public function saveForm() {
		Modules::run('security/_make_sure_admin_is_connected');
		
		if($this->input->post('editedItemId') == -1) {
			$this->_addItem();
		}else{
			$this->_editItem($this->input->post('editedItemId'));
		}
	}
	
	
	
	
	
	public function _addItem() {
		
		
		$check = $this->_checkLoginUnique($this->input->post('login'), NULL);
		
		if($check == FALSE) {
			$this->view(NULL, 'duplicateLoginError');
			die();
		}
		
		$data = array(
			'userType' => is_section_user_admins()?'1':'2',
			'firstname' => $this->input->post('firstname'),
			'lastname' => $this->input->post('lastname'),
			'login' => $this->input->post('login'),
			'password' => Modules::run('security/_encodePassword', $this->input->post('password')),
		);
		$itemId = $this->userbo_model->addItem($data);
		
		redirect(base_admin_url(current_section()));
	}


	public function _checkLoginUnique($login, $userId = NULL) {
		
		
		
		$check = $this->userbo_model->checkLoginUnique($login, $userId);
		
		return $check;
	}
	
	private function _checkLoginPassword($login="", $password="") {
		
		
		$ret = 0;
		
		$user = $this->userbo_model->checkLoginPassword($login, Modules::run('security/_encodePassword', $password));
		
		if(!is_numeric($user)) {
			
			$ret = 1;
			$data = array(
				'admin' => array(
					'id' => $user->id,
					'type' => $user->userType,
					'login' => $user->login,
					'password' => $user->password,
					'firstname' => $user->firstname,
					'lastname' => $user->lastname,
				)
			);
			$this->session->set_userdata($data);
			
			
		}else{
			$ret = -1;
			$this->session->unset_userdata('admin');
		}
		
		return $ret;
	}
	
    private function _disconnect_user() {
        $this->load->helper('cookie');
        delete_cookie('auth_token');

        $this->session->sess_destroy();
    }
	
	public function _editItem($itemId) {
		
		
		$item = $this->userbo_model->getItem($itemId);
		
		$check = $this->_checkLoginUnique($this->input->post('login'), $itemId);
		
		if($check == FALSE) {
			$this->view($item, 'duplicateLoginError');
			die();
		}
		
		$password = NULL;
		if($this->input->post('password')) {
			$password = Modules::run('security/_encodePassword', $this->input->post('password'));
		}
		$data = array(
			'id' => $itemId,
			'userType' => is_section_user_admins()?'1':'2',
			'firstname' => $this->input->post('firstname'),
			'lastname' => $this->input->post('lastname'),
			'login' => $this->input->post('login'),
			'password' => $password,
		);
		$this->userbo_model->updateItem($data);
		
		redirect(base_admin_url(current_section()));
	}
    
	public function _getItemsList() {
		return $this->mainModel->getItemsList();
	}
}
