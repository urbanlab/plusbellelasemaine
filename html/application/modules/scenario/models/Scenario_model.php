<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Scenario_model extends CI_Model {

    private $mainTable = '`scenario`';
    private $translationTable = '`_translation`';

	function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }


	public function addItem($data) {
		
		$sql = 'INSERT INTO '.$this->mainTable.' (`uid`, `scenario_type`, 
		        `title_FK`, `intro_title_FK`, `intro_text_FK`, `about_title_FK`, `about_text_FK`,
		        `show_temporality`, `temporality_labels_FK`, `temporality_questions_per_period`, `temporality_periods_to_win`,
		        `creation_date`, `last_update_date`)
				VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?) ';

		// only type 2
		$this->db->query($sql, array($data->uid, 2,
            $data->title_FK, $data->intro_title_FK, $data->intro_text_FK, $data->about_title_FK, $data->about_text_FK,
            $data->show_temporality, $data->temporality_labels_FK, $data->temporality_questions_per_period, $data->temporality_periods_to_win,
			time(), time()));
		
		$id = $this->db->insert_id();

        $sql = 'SELECT *
				FROM '.$this->mainTable.'
				WHERE id=? LIMIT 1';
        $query = $this->db->query($sql, array($id));

        if ($query->num_rows()==1)
            return $query->row();
        else
            return FALSE;
	}

	public function deleteItem($itemId) {
		
		$sql = 'DELETE FROM '.$this->mainTable.'
				WHERE `id` = ?
				LIMIT 1 ';
		$this->db->query($sql, array($itemId));		
		
		return;
	}

	public function getItem($itemId) {
        $sql = 'SELECT *
				FROM '.$this->mainTable.'
				WHERE id=? LIMIT 1';
        $query = $this->db->query($sql, array($itemId));

        if ($query->num_rows()==1)
            return $query->row();
        else
		    return FALSE;
	}

	public function getItemList($lang = 1) {
		$sql = 'SELECT m.id, m.uid, j1.content as title
				FROM '.$this->mainTable.' as m
				JOIN '.$this->translationTable.' as j1 ON j1.translation_object_FK=m.title_FK AND j1.lang_FK=?
				WHERE id!=1
				ORDER BY id';
		$query = $this->db->query($sql, array($lang));
		return $query;
	}

	public function getAllScenarios() {
		$sql = 'SELECT `id`, `uid`, `scenario_type`, `title_FK`, `intro_title_FK`, `intro_text_FK`, `about_title_FK`, `about_text_FK`, `show_temporality`, `temporality_labels_FK`, `temporality_questions_per_period`, `temporality_periods_to_win`, `creation_date`, `last_update_date`
				FROM '.$this->mainTable.' 
				ORDER BY `id`';
		$query = $this->db->query($sql, array());
		return $query;
	}

	public function updateItem($data) {
		
		$sql = 'UPDATE '.$this->mainTable.'
				SET
					`title_FK`= ? ,
					`intro_title_FK`= ? ,
					`intro_text_FK`= ? ,
					`about_title_FK`= ? ,
					`about_text_FK`= ? ,
					`show_temporality`= ? , 
					`temporality_labels_FK`= ?,
					`temporality_questions_per_period`= ? , 
					`temporality_periods_to_win`= ? ,
					`last_update_date`= ?
				WHERE `id` = ?
				LIMIT 1 ';

		$this->db->query($sql, array(
            $data->title_FK, $data->intro_title_FK, $data->intro_text_FK, $data->about_title_FK, $data->about_text_FK,
            $data->show_temporality,$data->temporality_labels_FK,$data->temporality_questions_per_period,$data->temporality_periods_to_win,
			time(),
            $data->id));

		return;
	}
}