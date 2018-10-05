<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

	/******************* userbo table init ********************
    --
    -- Structure de la table `_variables`
    --

    DROP TABLE IF EXISTS `_variables`;
    CREATE TABLE `_variables` (
    `id` int(11) NOT NULL,
    `id_var` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
    `title` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
    `initialisation` int(11) NOT NULL,
    `control` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `control_effect` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

    --
    -- Déchargement des données de la table `_variables`
    --

    INSERT INTO `_variables` (`id`, `id_var`, `title`, `initialisation`, `control`, `control_effect`) VALUES
    (1, 'var1', 'Dollar', 20, 'eachEvent', 'compareTrigger ($var1==0, end_game(bankrupt))'),
    (2, 'var2', 'Dictionnary', 0, NULL, NULL),
    (3, 'var3', 'Loan', 0, NULL, NULL),
    (4, 'var4', 'Threatened', 0, NULL, NULL),
    (5, 'var5', 'Malabar', 0, 'eachEvent', 'compareTrigger ($var5 >1, $var1 +=-5, $var5 +=-1) ; compareTrigger ($var5==1, insert_event(2_8))'),
    (6, 'var6', 'Advancement', 0, 'eachEvent', 'compareTrigger ($var6==3, end_game(victory))'),
    (7, 'var1000', 'nbEventPerDay', 3, NULL, NULL),
    (8, 'var1001', 'nbEvent', 1, 'eachEvent', 'compareTrigger($var1001==21, end_game(wasted)) ; compareTrigger($var1001<21, $var1001+=1)');

    --
    -- Index pour la table `_variables`
    --
    ALTER TABLE `_variables`
    ADD PRIMARY KEY (`id`);

    --
    -- AUTO_INCREMENT pour la table `_variables`
    --
    ALTER TABLE `_variables`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
    COMMIT;
  
  ******************* /userbo table init ********************/


class Variable_model extends CI_Model {

	private $mainTable = '_variables';
	
	function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }

	public function addItem($scenarioId, $data) {

        $sql = 'INSERT INTO '.$this->mainTable.' (`scenario_FK`, `id_var`, `title`, `initialisation`, `control`, `control_effect`)
				VALUES (?, ?, ?, ?, ?, ?) ';
		$this->db->query($sql, array_merge([$scenarioId], $data));
		
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
		$sql = 'SELECT `id_var`, `title`, `initialisation`, `control`, `control_effect`
				FROM '.$this->mainTable.'
				WHERE `id` = ?
				';
		$item = $this->db->query($sql, array($itemId))->row();		
		
		
		return $item;
	}

	public function getItemList($scenarioId){
        $sql = 'SELECT `id_var`, `title`, `initialisation`, `control`, `control_effect`
				FROM '.$this->mainTable.'
				WHERE `scenario_FK` = ? 
				ORDER BY `id` ASC';

        $item = $this->db->query($sql, array($scenarioId));

		return $item;
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