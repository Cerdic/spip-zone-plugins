<?php
/*
 * Plugin cfg : classe cfg_extrapack: storage serialise dans extra de spip_auteurs ou autre
 *
 * Auteur : bertrand@toggg.com
 * © 2007 - Distribue sous licence LGPL
 *
 */

// cfg_extrapack retrouve et met a jour les donnees serialisees dans la colonne extra
// d'une table "objet" $cfg->table, par défaut spip_auteurs
// ici, $cfg->cfg_id est obligatoire ... peut-être mappé sur l'auteur courant (a voir)
class cfg_extrapack
{
	function cfg_extrapack(&$cfg, $opt = array())
	{
		$this->cfg = &$cfg;
		foreach ($opt as $o=>$v) {
			$this->$o = $v;
		}
		$this->cfg->table || ($this->cfg->table = 'spip_auteurs');
/*
		$this->cfg->champs_id || ($this->cfg->champs_id = array(
				strpos('_', $this->cfg->table) !== false ?
					 'id' . preg_replace(',s$,', '', strrchr($this->cfg->table, '_')) :
					 'id_' . $this->cfg->table ));
*/
	}
	
// recuperer les valeurs, utilise la fonction commune lire_config() de cfg_options.php
	function lire()
	{
 		if (!$this->cfg->cfg_id) {
			$this->cfg->message = _L('id manquant');
			return false;
		}

    	$val = lire_config(array(
    		$this->cfg->table,
    		'',
    		$cles = explode('/', $this->cfg->cfg_id),
    		explode('/', $this->cfg->nom . ($this->cfg->casier ? '/' . $this->cfg->casier : ''))
    	));

		foreach ($this->cfg->champs_id as $i => $name) {
			$val[$name] = $cles[$i];
	    }
	    return $val;
	}
	
// se positionner dans le tableau arborescent
	function & monte_arbre(&$base, $chemin)
	{
		if (!$chemin) {
			return $base;
		}
		foreach (explode('/', $chemin) as $chunk) {
			if (!isset($base[$chunk])) {
				$base[$chunk] = array();
			}
	    	$this->_report[] = array(&$base, $chunk);
	    	$base = &$base[$chunk];
		}
		return $base;
	}
	
// modifier le fragment qui peut etre tout le meta
	function modifier($supprimer = false)
	{
/*
    	($base = lire_config($this->cfg->nom)) || ($base = array());
    	$ici = &$base;
    	$this->_report = array();
    	$ici = &$this->monte_arbre($ici, $this->cfg->casier);
    	$ici = &$this->monte_arbre($ici, $this->cfg->cfg_id);
		foreach ($this->cfg->champs as $name => $def) {
			if (isset($def['id'])) {
				continue;
			}
			if ($supprimer) {
				unset($ici[$name]);
			} else {
				$ici[$name] = $this->cfg->val[$name];
			}
	    }
		if ($supprimer) {
			for ($i = count($this->_report); $i--; ) {
				if ($this->_report[$i][0][$this->_report[$i][1]]) {
					break;
				}
				unset($this->_report[$i][0][$this->_report[$i][1]]);
			}
		}
		if ($supprimer && !$base) {
		    effacer_meta($this->cfg->nom);
		} else {
		    ecrire_meta($this->cfg->nom, serialize($base));
	    }
	    ecrire_metas();
*/
	    return true;
	}
}
?>
