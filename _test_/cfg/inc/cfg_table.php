<?php
/*
 * Plugin cfg : classe cfg_table: storage naturel dans une table
 *
 * Auteur : bertrand@toggg.com
 * © 2007 - Distribue sous licence LGPL
 *
 */

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
		$this->cfg->table || ($this->cfg->message = _L('nom table manquant'));
/*
		$this->cfg->champs_id || ($this->cfg->champs_id = array(
				strpos('_', $this->cfg->table) !== false ?
					 'id' . preg_replace(',s$,', '', strrchr($this->cfg->table, '_')) :
					 'id_' . $this->cfg->table ));
*/
	}
	
// recuperer les valeurs
	function lire()
	{
 		if (!$this->cfg->cfg_id) {
			$this->cfg->message = _L('id manquant');
			return false;
		}

   		$cles = explode('/', $this->cfg->cfg_id);
    	$val = array();
//    		explode('/', $this->cfg->nom . ($this->cfg->casier ? '/' . $this->cfg->casier : ''))

		$query = '';
		$sep = 'SELECT ';
		foreach ($this->cfg->champs as $name => $def) {
			if (isset($def['id'])) {
				continue;
			}
			$query .= $sep . $name;
			$sep = ', ';
	    }
		$query .= ' FROM ' . $this->cfg->table . $this->where();
		$query = spip_query($query);
		($query = spip_fetch_array($query)) && ($val = $query);

		foreach ($this->cfg->champs_id as $i => $name) {
			$val[$name] = $cles[$i];
	    }

	    return $val;
	}
	
// fabriquer le WHERE depuis cfg_id
	function where()
	{
   		$cles = explode('/', $this->cfg->cfg_id);
		$sep = ' WHERE ';
		$where = '';
		foreach ($this->cfg->champs_id as $i => $name) {
			$where .= $sep . $name . '=' . 
				// sans doute un peu brutal ...
				(is_numeric($cles[$i]) ? intval($cles[$i]) : _q($cles[$i]));
			$sep = ' AND ';
	    }
		return $where;
	}
	
// modifier le fragment qui peut etre tout le meta
	function modifier($supprimer = false)
	{
//spip_log($val);
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
				spip_query('DELETE FROM ' . $this->cfg->table . $this->where());
		}

		if ($existe) {
			$query = 'UPDATE ' . $this->cfg->table;
			$sep = ' SET ';
			foreach ($this->cfg->champs as $name => $def) {
				if (isset($def['id'])) {
					continue;
				}
				$query .= $sep . $name .'=' . _q($this->cfg->val[$name]);
				$sep = ', ';
		    }
				spip_log($query . $this->where());
		    return spip_query($query . $this->where());
	    }

		$query = 'INSERT INTO ' . $this->cfg->table;
		$sep = ' (';
		$values = ' VALUES';
		foreach ($this->cfg->champs as $name => $def) {
			$query .= $sep . $name;
			$values .= $sep . _q($this->cfg->val[$name]);
			$sep = ', ';
	    }
				spip_log($query . ')' . $values . ')');
	    return spip_query($query . ')' . $values . ')');
	}
}
?>
