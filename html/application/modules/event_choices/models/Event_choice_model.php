<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

	/******************* userbo table init ********************
    --
    -- Structure de la table `_event_choice`
    --

    DROP TABLE IF EXISTS `_event_choice`;
    CREATE TABLE `_event_choice` (
    `id` int(11) NOT NULL,
    `id_choice` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `command` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `summary_weight` int(11) DEFAULT NULL,
    `summary_gauge_target` int(11) DEFAULT NULL,
    `content_FK` int(11) DEFAULT NULL,
    `event_FK` int(11) NOT NULL,
    `summary_text_FK` int(11) DEFAULT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

    --
    -- Déchargement des données de la table `_event_choice`
    --

    INSERT INTO `_event_choice` (`id`, `id_choice`, `command`, `summary_weight`, `summary_gauge_target`, `content_FK`, `event_FK`, `summary_text_FK`) VALUES
    (1, 'c1', 'trigger_event(1_2)', 10, 1, 3, 1, 4),
    (2, 'c2', 'trigger_event(2_1)', 10, 1, 5, 1, 6),
    (3, 'c1', '$var1+=-5 ; trigger_pool(help)', 5, 3, 9, 2, 10),
    (4, 'c2', 'trigger_event(1_1)', 5, 3, 11, 2, 12),
    (5, 'c1', '$var1+=10 ; $var6+=1 ; trigger_event(1_1)', 0, 0, 15, 3, 16),
    (6, 'c1', '$var6+=1 ; trigger_event(1_1)', 0, 0, 19, 4, 20),
    (7, 'c1', '$var1+=10 ; trigger_event(1_1)', 0, 0, 23, 5, 24),
    (8, 'c1', 'trigger_event(1_1)', 0, 0, 27, 6, 28),
    (9, 'c1', 'trigger_pool(explore)', 10, 2, 31, 7, 32),
    (10, 'c2', 'trigger_event(1_1)', 10, 2, 33, 7, 34),
    (11, 'c1', '$var1+=20 ; trigger_event(2_1)', 5, 3, 37, 8, 38),
    (12, 'c2', 'trigger_pool(explore)', 5, 3, 39, 8, 40),
    (13, 'c1', '$var1+=20 ; $var3+=1 ; trigger_event(2_1)', 20, 3, 43, 9, 44),
    (14, 'c2', 'trigger_event(2_1)', 20, 1, 45, 9, 46),
    (15, 'c1', '$var1+=-40 ; $var3+=-1 ; trigger_event(2_1)', 10, 3, 49, 10, 50),
    (16, 'c2', '$var4+=1 ; trigger_event(2_1)', 10, 1, 51, 10, 52),
    (17, 'c1', '$var1+=-60 ; $var3+=-1 ;  $var4 +=-1 ; trigger_event(2_1)', 20, 1, 55, 11, 56),
    (18, 'c2', 'end_game(wasted)', 20, 3, 57, 11, 58),
    (19, 'c1', 'trigger_event(2_6_1)', 10, 2, 61, 12, 62),
    (20, 'c2', 'trigger_pool(explore)', 10, 2, 63, 12, 64),
    (21, 'c1', '$var2+=1 ; trigger_event(2_1)', 5, 2, 67, 13, 68),
    (22, 'c2', 'trigger_event(2_1)', 0, 0, 69, 13, 70),
    (23, 'c1', '$var5+=5 ; trigger_event(2_1)', 10, 1, 73, 14, 74),
    (24, 'c2', 'trigger_event(2_1)', 10, 2, 75, 14, 76),
    (25, 'c1', '$var5+=4 ; end_insert', 10, 1, 79, 15, 80),
    (26, 'c2', '$var5+=-1 ; end_insert', 10, 2, 81, 15, 82);

    --
    -- Index pour la table `_event_choice`
    --
    ALTER TABLE `_event_choice`
    ADD PRIMARY KEY (`id`);

    --
    -- AUTO_INCREMENT pour la table `_event_choice`
    --
    ALTER TABLE `_event_choice`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;
  
  ******************* /userbo table init ********************/


class Event_choice_model extends CI_Model {

	private $mainTable = '_event_choice';
	
	function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }

	public function addItem($data) {
		
		$sql = 'INSERT INTO '.$this->mainTable.' (`id_choice`, `command`, `summary_weight`, `summary_gauge_target`, `content_FK`, `event_FK`, `summary_text_FK`)
		 VALUES (?, ?, ?, ?, ?, ?, ?)';
		$this->db->query($sql, array($data[0], $data[1], $data[2], $data[3], $data[4], $data[5], $data[6]));
		
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
		$sql = 'SELECT `id_choice`, `command`, `summary_weight`, `summary_gauge_target`, `content_FK`, `event_FK`, `summary_text_FK`
				FROM '.$this->mainTable.'
				WHERE `id` = ?
				';
		$item = $this->db->query($sql, array($itemId))->row();		
		
		
		return $item;
	}

    public function getItemList($scenarioId){
        $sql = "SELECT ec.`id_choice`, ec.`command`, ec.`summary_weight`, ec.`summary_gauge_target`, e.`id_event` 
				FROM `_events` AS e INNER JOIN 
					`_event_choice` AS ec ON e.`id` = ec.`event_FK`
				WHERE e.`scenario_FK` = ? 
				ORDER BY ec.`id` ASC";
        $query = $this->db->query($sql, array($scenarioId));

        return $query;
    }

    public function getItemListByEvent($id_event){
        $sql = 'SELECT ec.`id`, ec.`id_choice`, ec.`command`, ec.`summary_weight`, ec.`summary_gauge_target`, ec.`content_FK`, ec.`event_FK`, ec.`summary_text_FK`
                FROM `_events` AS e INNER JOIN `_event_choice` AS ec ON e.`id` = ec.`event_FK`
				WHERE ec.`event_FK` = ? 
				ORDER BY ec.`id` ASC';
        $query = $this->db->query($sql, array($id_event));

        return $query;
    }

    public function getItemListByEventInLang($id_event, $id_lang){
        $sql = 'SELECT ec.`id`, ec.`id_choice`, ec.`command`, ec.`summary_weight`,  ec.`summary_gauge_target`, t1.`content` AS content, ec.`event_FK`, t2.`content` AS summary_text
				FROM `_event_choice` AS ec INNER JOIN
					`_translation` AS t1 ON t1.`translation_object_FK` = ec.`content_FK` AND t1.`lang_FK` = ? INNER JOIN
					`_translation` AS t2 ON t2.`translation_object_FK` = ec.`summary_text_FK` AND t2.`lang_FK` = ? 
				WHERE ec.`event_FK` = ? 
				ORDER BY ec.`id` ASC';
        $query = $this->db->query($sql, array($id_lang, $id_lang, $id_event));

        return $query;
    }

	public function getByEventAndChoice($scenarioId, $id_event, $id_choice){
        $sql = 'SELECT ec.`id`, ec.`id_choice`, ec.`command`, ec.`summary_weight`, ec.`summary_gauge_target`, ec.`content_FK`, ec.`event_FK`, ec.`summary_text_FK`
				FROM `_events` AS e INNER JOIN '.$this->mainTable.' AS ec ON e.`id` = ec.`event_FK` 
				WHERE e.`scenario_FK` = ? AND e.`id_event` = ? AND ec.`id_choice` = ?';

        $item = $this->db->query($sql, array($scenarioId, $id_event, $id_choice))->row();

        return $item;
    }

    public function updateSummaryWeight($itemId,$weight){
        $sql = 'UPDATE '.$this->mainTable.'
            SET  `summary_weight` = ?
            WHERE `id` = ?';
        $this->db->query($sql,array($weight,$itemId));
    }

    public function updateGaugeTarget($itemId,$gauge_target){
        $sql = 'UPDATE '.$this->mainTable.'
            SET  `summary_gauge_target` = ?
            WHERE `id` = ?';
        $this->db->query($sql,array($gauge_target,$itemId));
    }
}