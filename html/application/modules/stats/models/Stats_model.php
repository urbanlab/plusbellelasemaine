<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Stats_model extends CI_Model 
{
	private $progressTable = 'progress';
	private $successTable = 'success';
	private $scoreTable = 'score';
	private $userTable = 'user';
	private $cityTable = 'city';
	private $statQuestionTable = 'stat_question';
	private $statEventTable = 'stat_event';


	function __construct()
    {
        // Call the Model constructor
        parent::__construct();
		
		//$this->load->database('site', TRUE);
    }

    // mise à jour des stats concernant les questions
    public function updateQuestion($qid, $uid, $zone, $isgood)
    {
    	$sql = 'SELECT good, bad FROM '.$this->statQuestionTable.' WHERE qid_FK=? and uid_FK=? and zone=?';
    	$query = $this->db->query($sql, array($qid, $uid, $zone));

		if($query->num_rows()>0)
		{
			// mise à jour de la stat question
			if ($isgood)
				$sql = "UPDATE ".$this->statQuestionTable." SET good = good + 1 ";
			else
				$sql = "UPDATE ".$this->statQuestionTable." SET bad = bad + 1 ";
			$sql .= " WHERE qid_FK=? and uid_FK=? and zone=?";
			$query = $this->db->query($sql, array($qid, $uid, $zone));			
		}
		else
		{
			// insertion de la stat question
			$sql = "INSERT INTO ".$this->statQuestionTable."(qid_FK, uid_FK, zone, good, bad) VALUES (?, ?, ?, ?, ?)";
			$query = $this->db->query($sql, array($qid, $uid, $zone, $isgood?1:0, $isgood?0:1));					
		}
	}

    // mise à jour des stats concernant les évènements
    public function updateEvent($eid, $type)
    {
    	$o = 0;
    	$l = 0;
    	$c = 0;
		$sql = "INSERT INTO ".$this->statEventTable."(eid_FK, open, link, code) VALUES (?, ?, ?, ?) ";
    	switch($type)
    	{
    		case "o":
    		{
	    		$o++;
				$sql .= "ON DUPLICATE KEY UPDATE open = open + 1";
				break;
			}
			case "l":
			{
				$l++;
				$sql .= "ON DUPLICATE KEY UPDATE link = link + 1";
				break;
			}
			case "c":
			{
				$c++;
				$sql .= "ON DUPLICATE KEY UPDATE code = code + 1";
				break;
			}
			default:
			return;
		}

		$query = $this->db->query($sql, array($eid, $o, $l, $c));			
	}


	// statistique sur les scores	
	public function getScore() 
	{
		$sql = 'SELECT COUNT(*) as nb, SUM(score) as total, MAX(SCORE) as max FROM '.$this->progressTable;
		$query = $this->db->query($sql, array());
		return $query->row(); 
	}

	// données de progression
	public function getProgress()
	{
		$sql = 'SELECT 
		(SELECT COUNT(*) FROM '.$this->progressTable.') as auth,
		(SELECT COUNT(*) FROM '.$this->progressTable.' WHERE tutostep>=48) as tuto,
		(SELECT COUNT(*) FROM '.$this->progressTable.' WHERE tutostep=32767) as unlock1,
		(SELECT COUNT(*) FROM '.$this->progressTable.' WHERE level>=5) as unlock2,
		(SELECT COUNT(*) FROM '.$this->progressTable.' WHERE level>=10) as unlock3,
		(SELECT COUNT(*) FROM '.$this->progressTable.' WHERE level>=15) as unlock4,
		(SELECT COUNT(*) FROM '.$this->progressTable.' WHERE level>=20) as unlock5,
		(SELECT COUNT(*) FROM '.$this->progressTable.' WHERE level>=25) as finished
		';
		$query = $this->db->query($sql, array());
		return $query->row(); 
	}

	// données sur les questions
	public function getQuestions()
	{
		$questions = array();
		for($i=0;$i<9;$i++)
			$questions[] = array();

		// récupération des stats totales (requête à faire à cause du nombre moyen par utilisateur)
		$sql='SELECT '.
			'coalesce(SUM(s.good), 0) as good, '.
			'coalesce(SUM(s.bad), 0) as bad, '.
			'COUNT(DISTINCT(s.uid_FK)) as nb '.
			'FROM question q '.
			'left join stat_question s on s.qid_FK = q.id';
		$query = $this->db->query($sql, array());

		foreach($query-> result() as $row)
		{
			$good = intval($row->good);
			$bad = intval($row->bad);
			$total = $good+$bad;
			$nb = intval($row->nb);
			$questions[0][0]["label"]="Total";
			$questions[0][0]["total"]=$total;
			if ($total>0)
				$questions[0][0]["good"]=round(100*$good/$total);
			else	
				$questions[0][0]["good"]=0;
			if ($nb>0)
				$questions[0][0]["avg"]=round($total/$nb);
			else
				$questions[0][0]["avg"]=0;
			$questions[0][0]["good_d"]=0;
			$questions[0][0]["total_d"]=0;
			$questions[0][0]["good_m"]=0;
			$questions[0][0]["total_m"]=0;
			$questions[0][0]["good_f"]=0;
			$questions[0][0]["total_f"]=0;
		}

		// récupération des stats par catégorie (requête à faire à cause du nombre moyen par utilisateur)
		$sql='SELECT q.type, '.
			'coalesce(SUM(s.good), 0) as good, '.
			'coalesce(SUM(s.bad), 0) as bad, '.
			'COUNT(DISTINCT(s.uid_FK)) as nb '.
			'FROM question q '.
			'left join stat_question s on s.qid_FK = q.id '.
			'group by q.type order by q.type';
		$query = $this->db->query($sql, array());

		foreach($query-> result() as $row)
		{
			$type = intval($row->type);
			$good = intval($row->good);
			$bad = intval($row->bad);
			$total = $good+$bad;
			$nb = intval($row->nb);

			$index = $type*2-1;
			switch($type)
			{
				case 1: $questions[$index][0]["label"] = "Gaspillages électriques";break;
				case 2: $questions[$index][0]["label"] = "Pollutions aériennes";break;
				case 3: $questions[$index][0]["label"] = "Gaspillages thermiques";break;
				case 4: $questions[$index][0]["label"] = "Gestion des déchets";break;
			}
			$questions[$index][0]["total"]=$total;
			if ($total>0)
				$questions[$index][0]["good"]=round(100*$good/$total);
			else	
				$questions[$index][0]["good"]=0;
			if ($nb>0)
				$questions[$index][0]["avg"]=round($total/$nb);
			else
				$questions[$index][0]["avg"]=0;
			$questions[$index][0]["good_d"]=0;
			$questions[$index][0]["total_d"]=0;
			$questions[$index][0]["good_m"]=0;
			$questions[$index][0]["total_m"]=0;
			$questions[$index][0]["good_f"]=0;
			$questions[$index][0]["total_f"]=0;
		}


		// récupération des stats du début (zone = 1)
		$sql='SELECT q.id, q.question, q.type, q.difficulty, '.
			'coalesce(SUM(s.good), 0) as good, '.
			'coalesce(SUM(s.bad),0) as bad, '.
			'count(distinct(uid_FK)) as nb '.
			'FROM question q '.
			'left join stat_question s on s.qid_FK = q.id and (s.zone = 1) '.
			'group by q.id '.
			'order by q.type, q.difficulty, q.id';
		$query = $this->db->query($sql, array());

		foreach($query-> result() as $row)
		{
			$type = intval($row->type);
			$good = intval($row->good);
			$bad = intval($row->bad);
			$total = $good+$bad;
			$nb = intval($row->nb);

			$index_rows = $type*2;
			$questions[$index_rows][$row->id] = array();
			$questions[$index_rows][$row->id]["label"]=$row->question;
			$questions[$index_rows][$row->id]["type"]= $type;
			$questions[$index_rows][$row->id]["difficulty"]=$row->difficulty;
			$questions[$index_rows][$row->id]["total"]=$total;
			if ($total>0)
				$questions[$index_rows][$row->id]["good"]=round(100*$good/$total);
			else
				$questions[$index_rows][$row->id]["good"]=0;
			if ($nb>0)
				$questions[$index_rows][$row->id]["avg"]=round($total/$nb);
			else
				$questions[$index_rows][$row->id]["avg"]=0;
			$questions[$index_rows][$row->id]["nb_d"]=$nb;
			if ($total!=0)
				$questions[$index_rows][$row->id]["percent_d"]=round(100*$good/$total);
			else
				$questions[$index_rows][$row->id]["percent_d"]=0;

			$index_type = $type*2 - 1;
			$questions[$index_type][0]["good_d"]+=$good;
			$questions[$index_type][0]["total_d"]+=$total;
			$questions[0][0]["good_d"]+=$good;
			$questions[0][0]["total_d"]+=$total;
		}

		// récupération des stats du milieu (zone = 2 et 3)
		$sql='SELECT q.id, q.type, '.
			'coalesce(SUM(s.good), 0) as good, '.
			'coalesce(SUM(s.bad),0) as bad, '.
			'count(distinct(uid_FK)) as nb '.
			'FROM question q '.
			'left join stat_question s on s.qid_FK = q.id and (s.zone = 2 or s.zone = 3) '.
			'group by q.id '.
			'order by q.type, q.difficulty, q.id';
		$query = $this->db->query($sql, array());

		foreach($query-> result() as $row)
		{
			$type = intval($row->type);
			$good = intval($row->good);
			$bad = intval($row->bad);
			$total = $good+$bad;
			$nb = intval($row->nb);

			$index_rows = $type*2;
			$questions[$index_rows][$row->id]["total"]+=$total;
			if ($total>0)
				$questions[$index_rows][$row->id]["good"]=round(100*$good/$total);
			else
				$questions[$index_rows][$row->id]["good"]=0;
			if ($nb>0)
				$questions[$index_rows][$row->id]["avg"]=round($total/$nb);
			else
				$questions[$index_rows][$row->id]["avg"]=0;
			$questions[$index_rows][$row->id]["nb_m"]=$nb;
			if ($total!=0)
				$questions[$index_rows][$row->id]["percent_m"]=round(100*$good/$total);
			else
				$questions[$index_rows][$row->id]["percent_m"]=0;

			$index_type = $type*2 - 1;
			$questions[$index_type][0]["good_m"]+=$good;
			$questions[$index_type][0]["total_m"]+=$total;
			$questions[0][0]["good_m"]+=$good;
			$questions[0][0]["total_m"]+=$total;
		}

		// récupération des stats de la fin (zone = 4 et 5)
		$sql='SELECT q.id, q.type, '.
			'coalesce(SUM(s.good), 0) as good, '.
			'coalesce(SUM(s.bad),0) as bad, '.
			'count(distinct(uid_FK)) as nb '.
			'FROM question q '.
			'left join stat_question s on s.qid_FK = q.id and (s.zone = 4 or s.zone = 5) '.
			'group by q.id '.
			'order by q.type, q.difficulty, q.id';
		$query = $this->db->query($sql, array());

		foreach($query-> result() as $row)
		{
			$type = intval($row->type);
			$good = intval($row->good);
			$bad = intval($row->bad);
			$total = $good+$bad;
			$nb = intval($row->nb);

			$index_rows = $type*2;
			$questions[$index_rows][$row->id]["total"]+=$total;
			if ($total>0)
				$questions[$index_rows][$row->id]["good"]=round(100*$good/$total);
			else
				$questions[$index_rows][$row->id]["good"]=0;
			if ($nb>0)
				$questions[$index_rows][$row->id]["avg"]=round($total/$nb);
			else
				$questions[$index_rows][$row->id]["avg"]=0;
			$questions[$index_rows][$row->id]["nb_f"]=$nb;

			if ($total!=0)
				$questions[$index_rows][$row->id]["percent_f"]=round(100*$good/$total);
			else
				$questions[$index_rows][$row->id]["percent_f"]=0;

			$index_type = $type*2 - 1;
			$questions[$index_type][0]["good_f"]+=$good;
			$questions[$index_type][0]["total_f"]+=$total;
			$questions[0][0]["good_f"]+=$good;
			$questions[0][0]["total_f"]+=$total;
		}

		for($i=1;$i<=4;$i++)
		{
			$index_type = $i*2 - 1;
			if ($questions[$index_type][0]["total_d"]>0)
				$questions[$index_type][0]["percent_d"] = round(100*$questions[$index_type][0]["good_d"]/$questions[$index_type][0]["total_d"]);
			else 
				$questions[$index_type][0]["percent_d"] = 0;

			if ($questions[$index_type][0]["total_m"]>0)
				$questions[$index_type][0]["percent_m"] = round(100*$questions[$index_type][0]["good_m"]/$questions[$index_type][0]["total_m"]);
			else 
				$questions[$index_type][0]["percent_m"] = 0;

			if ($questions[$index_type][0]["total_f"]>0)
				$questions[$index_type][0]["percent_f"] = round(100*$questions[$index_type][0]["good_f"]/$questions[$index_type][0]["total_f"]);
			else 
				$questions[$index_type][0]["percent_f"] = 0;
		}
		if ($questions[0][0]["total_d"]>0)
			$questions[0][0]["percent_d"] = round(100*$questions[0][0]["good_d"]/$questions[0][0]["total_d"]);
		else
			$questions[0][0]["percent_d"] = 0;

		if ($questions[0][0]["total_m"]>0)
			$questions[0][0]["percent_m"] = round(100*$questions[0][0]["good_m"]/$questions[0][0]["total_m"]);
		else
			$questions[0][0]["percent_m"] = 0;

		if ($questions[0][0]["total_f"]>0)
			$questions[0][0]["percent_f"] = round(100*$questions[0][0]["good_f"]/$questions[0][0]["total_f"]);
		else
			$questions[0][0]["percent_f"] = 0;

		return $questions;
	}

	// données sur les évènements
	public function getEvents()
	{
		// évènements rassemblés par partenaires
		$list = array(array());
		$list[0][0]["label"] = "Total";
		$list[0][0]["open"] = 0;
		$list[0][0]["link"] = 0;
		$list[0][0]["code"] = 0;

		// correspondance entre identifiants de partenaire et index dans le tableau
		$indexes = array();

		// récupération des stats
		$sql='SELECT u.id as uid, e.title, u.firstname, u.lastname, '.
			'DATE_FORMAT(FROM_UNIXTIME(begin),"%d/%m/%Y") as begin, '.
			'DATE_FORMAT(FROM_UNIXTIME(end),"%d/%m/%Y") as end, '.
			's.open, s.link, s.code '.
			'FROM event e '.
			'inner join userbo u on e.owner_FK = u.id '.
			'left join stat_event s on s.eid_FK = e.id '.
			'group by e.id '.
			'order by u.lastname, e.end desc';
		$query = $this->db->query($sql, array());

		foreach($query-> result() as $row)
		{
			// récupération de l'index
			if (!isset($indexes[$row->uid]))
			{
				$index = sizeof($indexes);
				$indexes[$row->uid]=$index;

				$list[1+$index*2] = array(array());
				$list[1+$index*2+1] = array();

				// initialisation du partenaire
				$list[1+$index*2][0]["label"] = $row->firstname." ".$row->lastname;
				$list[1+$index*2][0]["open"] = 0;
				$list[1+$index*2][0]["link"] = 0;
				$list[1+$index*2][0]["code"] = 0;
			}
			else
				$index = $indexes[$row->uid];

			$open = intval($row->open);
			$link = intval($row->link);
			$code = intval($row->code);

			// puis remplissage des listes
			$event = array(
				"label"=>$row->title.'<br/>(du '.$row->begin.' au '.$row->end.')',
				"open"=>$open,
				"link"=>$link,
				"code"=>$code
				);

			$list[1+$index*2+1][] = $event;

			// total pour le partenaire
			$list[1+$index*2][0]["open"] += $open;
			$list[1+$index*2][0]["link"] += $link;
			$list[1+$index*2][0]["code"] += $code;

			// total général
			$list[0][0]["open"] += $open;
			$list[0][0]["link"] += $link;
			$list[0][0]["code"] += $code;
		}

		return $list;
	}


	// données sur les challenges
	public function getChallenges($gareporting)
	{
		// évènements rassemblés par partenaires
		$list = array(array());
		$list[0][0]["label"] = "Total";
		$list[0][0]["users"] = 0;
		$list[0][0]["sessions"] = 0;
		$list[0][0]["duration"] = 0;

		// on récupère les données sur les challenges en se basant sur les custom vars
		$challenges  = $gareporting->request(array('sessions','sessionDuration'), array('customVarValue1'), $this->config->item('googleAnalyticsStatsStart'), 'today');
		//pr($challenges);
		$sessions_ga = array();
		$duration_ga = array();
		foreach($challenges as $challenge)
		{
			$ids = explode(",", $challenge[0]);
			foreach($ids as $id)
			{
				if (!isset($sessions_ga[$id]))
				{
					$sessions_ga[$id] = 0;
					$duration_ga[$id] = 0.0;
				}
				$sessions_ga[$id] += intval($challenge[1]);
				$duration_ga[$id] += floatval($challenge[2]);
			}
		}
		//pr($sessions);
		//pr($duration);

		// correspondance entre identifiants de partenaire et index dans le tableau
		$indexes = array();

		// récupération des stats
		$sql='SELECT ch.id as cid, u.id as uid, ch.title, u.firstname, u.lastname,'.
			' DATE_FORMAT(FROM_UNIXTIME(begin),"%d/%m/%Y") as begin,'.
			' DATE_FORMAT(FROM_UNIXTIME(end),"%d/%m/%Y") as end,'.
			' city1, city2, COUNT(s.uid_FK) as users'.
			' FROM challenge ch'.
			' INNER JOIN '.$this->cityTable.' ci1 ON ch.id=ci1.cid_FK and ci1.num=0'.
			' INNER JOIN '.$this->cityTable.' ci2 ON ch.id=ci2.cid_FK and ci2.num=1'.			
			' INNER JOIN userbo u on ch.owner_FK = u.id'.
			' LEFT JOIN '.$this->scoreTable.' s ON ch.id=s.cid_FK'.
			' group by ch.id'.
			' order by u.lastname, ch.end desc';
		$query = $this->db->query($sql, array());

		foreach($query-> result() as $row)
		{
			// récupération de l'index
			if (!isset($indexes[$row->uid]))
			{
				$index = sizeof($indexes);
				$indexes[$row->uid]=$index;

				$list[1+$index*2] = array(array());
				$list[1+$index*2+1] = array();

				// initialisation du partenaire
				$list[1+$index*2][0]["label"] = $row->firstname." ".$row->lastname;
				$list[1+$index*2][0]["users"] = 0;
				$list[1+$index*2][0]["sessions"] = 0;
				$list[1+$index*2][0]["duration"] = 0;
			}
			else
				$index = $indexes[$row->uid];

			$users = intval($row->users);
			$sessions = isset($sessions_ga[$row->cid])?$sessions_ga[$row->cid]:0;
			$duration = isset($duration_ga[$row->cid])?$duration_ga[$row->cid]:0.0;

			// puis remplissage des listes
			$line = array(
				"label"=>$row->city1.' / '.$row->city2.'<br/>(du '.$row->begin.' au '.$row->end.')',
				"users"=>$users,
				"sessions"=>$sessions,
				"duration"=>$duration
				);

			$list[1+$index*2+1][] = $line;

			// total pour le partenaire
			$list[1+$index*2][0]["users"] += $users;
			$list[1+$index*2][0]["sessions"] += $sessions;
			$list[1+$index*2][0]["duration"] += $duration;

			// total général
			$list[0][0]["users"] += $users;
			$list[0][0]["sessions"] += $sessions;
			$list[0][0]["duration"] += $duration;
		}

		return $list;
	}

	// Sessions perdues à cause d'un bug avec GA sur la première session
	public function getLostUsers($begin, $end) 
	{
		$lost = "SELECT count(*) as lost FROM ".$this->userTable." WHERE created = connected and created<1498544171 and created>? and created<?";
		$query = $this->db->query($lost, array($begin, $end));
		$item = $query->row();	

		return $item->lost;
	}	

}