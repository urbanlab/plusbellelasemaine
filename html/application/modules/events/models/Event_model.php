<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

	/******************* userbo table init ********************
    --
    -- Structure de la table `_events`
    --

    DROP TABLE IF EXISTS `_events`;
    CREATE TABLE `_events` (
    `id` int(11) NOT NULL,
    `id_event` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
    `condition` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
    `weight` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `pool` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `background` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `title_FK` int(11) NOT NULL,
    `description_FK` int(11) NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

    --
    -- Déchargement des données de la table `_events`
    --

    INSERT INTO `_events` (`id`, `id_event`, `condition`, `weight`, `pool`, `background`, `title_FK`, `description_FK`) VALUES
    (1, '1_1', NULL, NULL, NULL, 'perm.jpg', 1, 2),
    (2, '1_2', NULL, NULL, 'triggered', 'watch.jpg', 7, 8),
    (3, '1_3', NULL, '10 + (var2)*20', 'help', 'money.jpg', 13, 14),
    (4, '1_4', NULL, '10 + (var2)*20', 'help', 'watch.jpg', 17, 18),
    (5, '1_5', NULL, '10', 'help', 'money.jpg', 21, 22),
    (6, '1_6', NULL, '10', 'help', 'watch.jpg', 25, 26),
    (7, '2_1', NULL, NULL, NULL, 'street.jpg', 29, 30),
    (8, '2_2', NULL, '5', 'explore', 'money.jpg', 35, 36),
    (9, '2_3', '$var3==0 && $var5==0', '10', 'explore', 'money.jpg', 41, 42),
    (10, '2_4', '$var3==1 && $var4==0 && $var5==0', '10', 'explore', 'money.jpg', 47, 48),
    (11, '2_5', '$var3==1 && $var4==1 && $var5==0', '10', 'explore', 'money.jpg', 53, 54),
    (12, '2_6', '$var2==0', '10', 'explore', 'watch.jpg', 59, 60),
    (13, '2_6_1', NULL, NULL, 'triggered', 'watch.jpg', 65, 66),
    (14, '2_7', '$var5==0', '10', 'explore', 'money.jpg', 71, 72),
    (15, '2_8', '$var5>0', NULL, 'triggered', 'money.jpg', 77, 78);

    --
    -- Index pour la table `_events`
    --
    ALTER TABLE `_events`
    ADD PRIMARY KEY (`id`),
    ADD KEY `title_FK` (`title_FK`),
    ADD KEY `description_FK` (`description_FK`);

    --
    -- AUTO_INCREMENT pour la table `_events`
    --
    ALTER TABLE `_events`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;
  
  ******************* /userbo table init ********************/


class Event_model extends CI_Model {

	private $mainTable = '_events';
	
	function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }

	public function addItem($scenarioId, $data) {
		
		$sql = 'INSERT INTO '.$this->mainTable.' (`scenario_FK`, `id_event`, `condition`, `weight`, `pool`, `background`, `title_FK`, `description_FK`)
		 VALUES (?, ?, ?, ?, ?, ?, ?, ?)';
		$this->db->query($sql, array($scenarioId, $data[0], $data[1], $data[2], $data[3], $data[4], $data[5], $data[6]));
		
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
		$sql = 'SELECT `id_event`, `condition`, `weight`, `pool`, `background`, `title_FK`, `description_FK`
				FROM '.$this->mainTable.'
				WHERE `id` = ?
				';
		$item = $this->db->query($sql, array($itemId))->row();

		return $item;
	}

	public function getItemList($scenarioId){
        $sql = 'SELECT `id`, `id_event`, `condition`, `weight`, `pool`, `background`, `title_FK`, `description_FK`
				FROM '.$this->mainTable.'
				WHERE `scenario_FK` = ? 
				ORDER BY `id` ASC';
        $item = $this->db->query($sql, array($scenarioId));

        return $item;
    }
	
	public function updateItem($data) {
		
		$sql = 'UPDATE '.$this->mainTable.'
				SET
					`id_event`= ? ,
					`condition`= ? ,
					`weight`= ? ,
					`pool`= ?,
					`background`= ?
                    `title_FK`= ?,
					`description_FK`= ?
				WHERE `id` = ?
				LIMIT 1 ';
		$this->db->query($sql, array($data['id_var'], $data['title'], $data['initialisation'], $data['control'],$data['control_effect'], $data['id']));
		
		return;
	}
}