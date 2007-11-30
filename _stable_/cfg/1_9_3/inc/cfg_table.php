<?php

/*
 * Plugin CFG pour SPIP
 * (c) toggg 2007, distribue sous licence GNU/GPL
 * Documentation et contact: http://www.spip-contrib.net/
 *
 * classe cfg_table: storage naturel dans une table
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

// cfg_table retrouve et met a jour les donnees d'une table "objet" $cfg->table
// ici, $cfg->cfg_id est obligatoire
class cfg_table
{
	function cfg_table(&$cfg, $opt = array())
	{
		$this->cfg = &$cfg;
		foreach ($opt as $o=>$v) {
			$this->$o = $v;
		}
		$this->cfg->table || ($this->cfg->message = _T('cfg:nom_table_manquant'));
	}
	

	/* 
	 * Recuperer les valeurs
	 * 
	 * Le parametre 'autoriser_absence_id=oui' permet d'autoriser
	 * une requete sql d'insertion de nouveau contenu
	 * meme si l'on ne donne pas la valeur du champs cle primaire (id)
	 * ce qui permet d'executer la requete si le champ id est autoincrement.
	 * 
	 * Si un message d'erreur est retourne, on ne peut 
	 * faire aucune modification.
	 * 
	 */
	function lire()
	{
		// si cfg_id n'est pas present,
		// pas la peine de continuer
 		if (!$this->cfg->cfg_id) {
 			// ignorer cette erreur si le champ id est 'autoincrement'
 			if (!$this->cfg->autoriser_absence_id == 'oui')
				$this->cfg->message = _T('cfg:id_manquant');
			return false;
		}

   		$cles = explode('/', $this->cfg->cfg_id);
    	$val = array();
    	// selection des champs du select
		$select = array();
		foreach ($this->cfg->champs as $name => $def) {
			if (isset($def['id'])) {
				continue;
			}
			$select[] = $name;
	    }
		$query = sql_select($select, $this->cfg->table, $this->where());
		($query = sql_fetch($query)) && ($val = $query);

		foreach ($this->cfg->champs_id as $i => $name) {
			$val[$name] = $cles[$i];
	    }

	    return $val;
	}
	
// fabriquer un array WHERE depuis cfg_id
	function where()
	{
   		$cles = explode('/', $this->cfg->cfg_id);
		$where = array();
		foreach ($this->cfg->champs_id as $i => $name) {
			$where[] = $name . '=' 
					. (is_numeric($cles[$i]) ? intval($cles[$i]) : sql_quote($cles[$i]));
	    }
		return $where;
	}
	
// modifier le fragment qui peut etre tout le meta
	function modifier($supprimer = false)
	{

		$this->cfg_id = $sep = '';
		foreach ($this->cfg->champs_id as $name) {
			$this->cfg_id .= $sep . _request($name);
			$sep = '/';
	    }
    	$base = $this->lire();
		// Hmm... a revoir pour table uniquement de liaison...
    	$existe = count($base) > count($this->cfg->champs_id);

    	if ($supprimer) {
			return !$existe || 
				sql_delete($this->cfg->table, $this->where() );
		}

		$champs = array();
		foreach ($this->cfg->champs as $name => $def) {
			if ($existe && isset($def['id'])) {
				continue;
			}
			$champs[$name] = $this->cfg->val[$name];
		}	
			
		// update
		if ($existe) {	
		    return sql_updateq($this->cfg->table, $champs, $this->where() );
	    }
		
		// sinon insert
		
		// recherche de cles primaires autoincrementes
		// pour ne pas les integrer (sinon pg rale !)
		// pour l'instant, on part du principe que
		// si le champs primaire est vide, c'est qu'il est auto-increment
		// bof bof !
		foreach ($champs as $name => $def) {
			if (in_array($name, $this->cfg->champs_id) && $def == "''" ) {
				unset($champs[$name]);
			}
		}
		
	    return sql_insertq($this->cfg->table, $champs);
	}
}
?>
