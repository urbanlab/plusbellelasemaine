<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Scenario_gauge_model extends CI_Model {

    private $mainTable = 'scenario_gauge';

	function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }


	public function addItem($data) {
		
		$sql = 'INSERT INTO '.$this->mainTable.' (`scenario_FK`, `position`, 
                `var`, `label_FK`, `summary_title_FK`, `picto`, 
                `initial_value`, `min_value_to_loose`, 
                `victory_title_FK`, `victory_text_FK`, 
                `defeat_title_FK`, `defeat_text_FK`)
				VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?) ';

		$this->db->query($sql, array($data->scenario_FK, $data->position,
            $data->var, $data->label_FK, $data->summary_title_FK, $data->picto, 
            $data->initial_value, $data->min_value_to_loose,
            $data->victory_title_FK, $data->victory_text_FK, 
            $data->defeat_title_FK, $data->defeat_text_FK
        ));
		
		$id = $this->db->insert_id();
		
		return $id;
	}

	public function deleteForScenario($scenarioId) {

		$sql = 'DELETE FROM '.$this->mainTable.'
				WHERE `scenario_FK` = ?';
		$this->db->query($sql, array($scenarioId));		
		
		return;
	}

	public function getForScenario($scenarioId) {
        $sql = 'SELECT *
				FROM '.$this->mainTable.'
				WHERE scenario_FK=?
				ORDER BY position';
        $query = $this->db->query($sql, array($scenarioId));

        $data = array();
        foreach($query->result() as $row)
        {
            array_push($data, $row);
        }
        return $data;
	}

	public function updateItem($data) {
		
		$sql = 'UPDATE '.$this->mainTable.'
                SET
                `scenario_FK`= ?, 
                `position`= ?, 
                `var`= ?, 
                `label_FK`= ?, 
                `summary_title_FK`= ?, 
                `picto`= ?, 
                `initial_value`= ?, 
                `min_value_to_loose`= ?, 
                `victory_title_FK`= ?, 
                `victory_text_FK`= ?, 
                `defeat_title_FK`= ?, 
                `defeat_text_FK`= ?                
				WHERE `id` = ?
				LIMIT 1 ';

        $this->db->query($sql, array($data->scenario_FK, $data->position,
            $data->var, $data->label_FK, $data->summary_title_FK, $data->picto, 
            $data->initial_value, $data->min_value_to_loose,
            $data->victory_title_FK, $data->victory_text_FK, 
            $data->defeat_title_FK, $data->defeat_text_FK,
            $data->id
        ));
    
		return;
	}
}