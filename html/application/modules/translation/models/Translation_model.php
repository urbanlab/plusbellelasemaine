<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
--
-- Structure de la table `_translation`
--

DROP TABLE IF EXISTS `_translation`;
CREATE TABLE `_translation` (
  `translation_object_FK` int(11) NOT NULL,
  `lang_FK` int(11) NOT NULL,
  `content` text COLLATE utf8_unicode_ci
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Structure de la table `_translation_object`
--

DROP TABLE IF EXISTS `_translation_object`;
CREATE TABLE `_translation_object` (
  `id` int(11) NOT NULL,
  `title` varchar(50) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Index pour la table `_translation`
--
ALTER TABLE `_translation`
  ADD PRIMARY KEY (`translation_object_FK`,`lang_FK`);

--
-- Index pour la table `_translation_object`
--
ALTER TABLE `_translation_object`
  ADD PRIMARY KEY (`id`);


--
-- AUTO_INCREMENT pour la table `_translation_object`
--
ALTER TABLE `_translation_object`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=89;

*/

class Translation_model extends CI_Model {

	private $itemTable = '`_translation_object`';
	private $contentTable = '`_translation`';
	
	function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }

	public function addTranslation($label) {


		$sql = 'INSERT INTO '.$this->contentTable.' (`translation_object_FK`, `lang_FK`, `content`) 
                VALUES (?, ?, ?) ';
		$this->db->query($sql, $label);
        $itemId = $this->db->insert_id();
		return $itemId;
	}

    public function addTranslationObject($label) {

        $sql = 'INSERT INTO '.$this->itemTable.' (`title`) 
				VALUES (?) ';
        $this->db->query($sql, array($label));
        $itemId = $this->db->insert_id();

        return $itemId;
    }

	public function getItemList() {
		$sql = 'SELECT `id`, `label`, `code_iso`, `i2loc_key`, `font_type`
				FROM '.$this->mainTable.'
				ORDER BY `id`';
		$query = $this->db->query($sql, array());		
		return $query;
	}
	
	public function getTranslation($itemId, $lang) {
		$sql = 'SELECT `content` 
				FROM '.$this->contentTable.' 
				WHERE `translation_object_FK` = ? AND `lang_FK` = ?';
		$item = $this->db->query($sql, array($itemId, $lang))->row();		
		
		
		return $item->content;
	}

	public function getTranslations($itemId){
        $sql = 'SELECT `content` , `lang_FK`
				FROM '.$this->contentTable.' 
				WHERE `translation_object_FK` = ?';

        $item = $this->db->query($sql, array($itemId))->result();


        return $item;
    }

	public function updateContent($itemId, $langId, $content) {
		$sql = 'UPDATE '.$this->contentTable.'
				SET `content`=?
				WHERE `translation_object_FK`=? AND `lang_FK`=?
				LIMIT 1';
		$this->db->query($sql, array($content, $itemId, $langId));
	}

	public function getLangCodeIso($langId){
        $sql = 'SELECT `code_iso` from `lang` where id = ?';

        return $this->db->query($sql, array($langId))->row();

    }

    public function getLangList(){
        $sql = 'SELECT * from `lang`';
        return $this->db->query($sql, array())->result();
    }
}