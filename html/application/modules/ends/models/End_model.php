<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

	/******************* userbo table init ********************
    --
    -- Structure de la table `_ends`
    --

    DROP TABLE IF EXISTS `_ends`;
    CREATE TABLE `_ends` (
    `id` int(11) NOT NULL,
    `id_end` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
    `background` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `end_title_FK` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `end_description_FK` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

    --
    -- Déchargement des données de la table `_ends`
    --

    INSERT INTO `_ends` (`id`, `id_end`, `background`, `end_title_FK`, `end_description_FK`) VALUES
    (1, 'victory', 'sea.jpg', '83', '84'),
    (2, 'bankrupt', 'money.jpg', '85', '86'),
    (3, 'wasted', 'street.jpg', '87', '88');


    --
    -- Index pour la table `_ends`
    --
    ALTER TABLE `_ends`
    ADD PRIMARY KEY (`id`);

    --
    -- AUTO_INCREMENT pour la table `_ends`
    --
    ALTER TABLE `_ends`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

  
  ******************* /userbo table init ********************/


class End_model extends CI_Model {

	private $mainTable = '_ends';
	
	function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }

	public function addItem($scenarioId, $data) {
		
		$sql = 'INSERT INTO '.$this->mainTable.' (`scenario_FK`, `id_end`, `background`, `end_title_FK`, `end_description_FK`) 
		VALUES (?, ?, ?, ?, ?)';
		$this->db->query($sql, array($scenarioId, $data[0], $data[1], $data[2], $data[3]));
		
		$userId = $this->db->insert_id();
		
		return $userId;
	}

	public function deleteItem($itemId) {
		
		$sql = 'DELETE FROM '.$this->mainTable.'
				WHERE `id` = ?
				LIMIT 1 ';
		$this->db->query($sql, array($itemId));		
		
		return;
	}
	
	public function getItem($itemId) {
		$sql = 'SELECT `id_end`, `background`, `end_title_FK`, `end_description_FK`
				FROM '.$this->mainTable.'
				WHERE `id` = ?
				';
		$item = $this->db->query($sql, array($itemId))->row();		
		
		
		return $item;
	}

    public function getItemList() {
        $sql = 'SELECT `id_end`, `background`, `end_title_FK`, `end_description_FK`
				FROM '.$this->mainTable;
        $query = $this->db->query($sql, array());
        return $query;
    }

	public function updateItem($data) {
		
		$sql = 'UPDATE '.$this->mainTable.'
				SET
					`id_var`= ? ,
					`title`= ? ,
					`initialisation`= ? ,
					`control`= ?,
					`control_effect`= ?
				WHERE `id` = ?
				LIMIT 1 ';
		$this->db->query($sql, array($data['id_var'], $data['title'], $data['initialisation'], $data['control'],$data['control_effect'], $data['id']));
		
		return;
	}
}