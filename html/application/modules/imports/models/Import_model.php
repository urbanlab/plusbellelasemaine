<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

	/******************* userbo table init ********************
--
-- Structure de la table `userbo`
--

DROP TABLE IF EXISTS `userbo`;
CREATE TABLE `userbo` (
  `id` int(11) NOT NULL,
  `firstname` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `lastname` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `login` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `userType` tinyint(1) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `userbo` (`id`, `firstname`, `lastname`, `login`, `password`, `userType`) VALUES
(1, 'Super Admin', 'DOWiNO', 'app@dowino.com', '-', 0),
(2, 'root', '', 'a@a.com', 'aa4c9edc39ae39d269581196aac04ac7407c2fa4', 0),
(3, 'Guillaume', 'Tribut', 'g.tribut@dowino.com', 'aa4c9edc39ae39d269581196aac04ac7407c2fa4', 1);

ALTER TABLE `userbo`
  ADD PRIMARY KEY (`id`),
  ADD KEY `userType` (`userType`);

ALTER TABLE `userbo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
  
  ******************* /userbo table init ********************/


class Userbo_model extends CI_Model {

	private $mainTable = 'userbo';
	
	function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }

	public function addItem($data) {
		
		$sql = 'INSERT INTO '.$this->mainTable.' (`firstname`, `lastname`, `login`, `password`, `userType`)
				VALUES (?, ?, ?, ?, ?) ';
		$this->db->query($sql, array($data['firstname'], $data['lastname'], $data['login'], $data['password'], $data['userType'] ));		
		
		$userId = $this->db->insert_id();
		
		return $userId;
	}
	
	public function changePassword($userId, $newPassword) {
		$sql = 'UPDATE '.$this->mainTable.'
				SET `password` = ?
				WHERE `id` = ?
				LIMIT 1 ';
		$this->db->query($sql, array($newPassword, $userId));
	}
	
	public function checkLoginUnique($login, $userId) {
		if($userId == NULL) {
			$sql = 'SELECT `id`
					FROM '.$this->mainTable.'
					WHERE `login` = ? ';
			$query = $this->db->query($sql,array($login));
		}else{
			$sql = 'SELECT `id`
					FROM '.$this->mainTable.'
					WHERE `login` = ? AND `id` <> ? ';
			$query = $this->db->query($sql,array($login, $userId));
		}
		
		if($query->num_rows() != 0) {
			return FALSE;
		}else{
			return TRUE;
		}
	}
	public function checkLoginPassword($login, $password) {
		$sql = 'SELECT `id`, `firstname`, `lastname`, `login`, `password`, `userType`
				FROM '.$this->mainTable.'
				WHERE `login` = ? AND `password` = ?';
		$query = $this->db->query($sql,array($login, $password));
		
		if($query->num_rows() != 1) {
			return -1;
		}else{
			return $query->row();
		}
	}
	
	public function deleteItem($itemId) {
		
		$sql = 'DELETE FROM '.$this->mainTable.'
				WHERE `id` = ?
				LIMIT 1 ';
		$this->db->query($sql, array($itemId));		
		
		return;
	}
	
	public function getItem($itemId) {
		$sql = 'SELECT `id`, `firstname`, `lastname`, `login`, `password`, `userType`
				FROM '.$this->mainTable.'
				WHERE `id` = ?
				';
		$item = $this->db->query($sql, array($itemId))->row();		
		
		
		return $item;
	}
	
	public function getAdminList() {
		$sql = 'SELECT `id`, `firstname`, `lastname`, `login`, `password`, `userType`
				FROM '.$this->mainTable.'
				WHERE `userType` = 1
				ORDER BY `firstname`, `lastname`';
		$query = $this->db->query($sql, array());		
		return $query;
	}
	
	public function getAdminEmails() {
		$sql = 'SELECT `login` FROM '.$this->mainTable.' WHERE `userType` = 1';
		$query = $this->db->query($sql, array());
		$emails = array();
		foreach($query->result() as $superadmin) 
		{
			$emails[] = $superadmin->login;
		}

		return $emails;
	}
	
	public function getPartnerList() {
		$sql = 'SELECT `id`, `firstname`, `lastname`, `login`, `password`, `userType`
				FROM '.$this->mainTable.'
				WHERE `userType` = 2
				ORDER BY `firstname`, `lastname`';
		$query = $this->db->query($sql, array());		
		return $query;
	}
	
	public function getUserDataById($id) {
		$sql = 'SELECT `id`, `firstname`, `lastname`, `login`, `password`, `userType`
				FROM '.$this->mainTable.'
				WHERE `id` = ?
				LIMIT 1 ';
		$query = $this->db->query($sql,array($id));
		
		return $query->row();		
	}
	
	public function getUserDataByEmail($email) {
		$sql = 'SELECT `id`, `firstname`, `lastname`, `login`, `password`, `userType`
				FROM '.$this->mainTable.'
				WHERE `login` = ?
				LIMIT 1 ';
		$query = $this->db->query($sql,array($email));
		
		if($query->num_rows() > 0) {
			return $query->row();
		}else {
			return FALSE;		
		}
	}
	
	public function getUserSuperAdmin() {
		$sql = 'SELECT `id`, `firstname`, `lastname`, `login`, `password`, `userType`
				FROM '.$this->mainTable.'
				WHERE `userType` = 0
				ORDER BY `id`
				LIMIT 1 ';
		$query = $this->db->query($sql,array());
		
		if($query->num_rows() > 0) {
			return $query->row();
		}else {
			return FALSE;		
		}	
	}
	
	public function updateItem($data) {
		
		$sql = 'UPDATE '.$this->mainTable.'
				SET
					`firstname`= ? ,
					`lastname`= ? ,
					`login`= ? ,
					`userType`= ?
				WHERE `id` = ?
				LIMIT 1 ';
		$this->db->query($sql, array($data['firstname'], $data['lastname'], $data['login'], $data['userType'], $data['id']));
		
		
		if($data['password'] != NULL) {
			$this->changePassword($data['id'], $data['password']);
		}
		
		
		return;
	}
}