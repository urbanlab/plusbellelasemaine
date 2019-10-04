<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Scenario_media_model extends CI_Model {

    private $mainTable = 'scenario_media';

	function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }


	public function addItem($data) {
		
		$sql = 'INSERT INTO '.$this->mainTable.' (`scenario_FK`, `label`, `image_url`)
				VALUES (?, ?, ?) ';

		$this->db->query($sql, array($data->scenario_FK, $data->label, $data->image_url));
		
		$id = $this->db->insert_id();
		
		return $id;
	}

    public function deleteItem($id) {

        $sql = 'DELETE FROM '.$this->mainTable.'
				WHERE `id` = ? LIMIT 1';
        $this->db->query($sql, array($id));

        return;
    }

    public function deleteForScenario($scenarioId) {

		$sql = 'DELETE FROM '.$this->mainTable.'
				WHERE `scenario_FK` = ?';
		$this->db->query($sql, array($scenarioId));		
		
		return;
	}

    public function getItem($id) {
        $sql = 'SELECT *
				FROM '.$this->mainTable.'
				WHERE id=? LIMIT 1';
        $query = $this->db->query($sql, array($id));

        if ($query->num_rows()==1)
            return $query->row();
        else
            return '';
    }

	public function getForScenario($scenarioId) {
        $sql = 'SELECT *
				FROM '.$this->mainTable.'
				WHERE scenario_FK=?
				ORDER BY id';
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
                `label`= ?, 
                `image_url`= ?                
				WHERE `id` = ?
				LIMIT 1 ';

        $this->db->query($sql, array($data->scenario_FK, $data->label, $data->image_url,
            $data->id
        ));
    
		return;
	}
}