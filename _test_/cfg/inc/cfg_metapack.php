<?php
/*
 * Plugin cfg : classe cfg_metapack: storage serialise dans spip_meta
 *
 * Auteur : bertrand@toggg.com
 * © 2007 - Distribue sous licence LGPL
 *
 */

// cfg_metapack retrouve et met a jour les donnees serialisees dans spip_meta
class cfg_metapack
{
	function cfg_metapack(&$cfg, $opt = array())
	{
		$this->cfg = &$cfg;
		foreach ($opt as $o=>$v) {
			$this->$o = $v;
		}
	}
	
// recuperer les valeurs, utilise la fonction commune lire_config() de cfg_options.php
	function lire()
	{
    	$val = lire_config($this->cfg->nom_config());
    	if ($this->cfg->cfg_id) {
    		$cles = explode('/', $this->cfg->cfg_id);
			foreach ($this->cfg->champs_id as $i => $name) {
				$val[$name] = $cles[$i];
		    }
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
	    if (version_compare($GLOBALS['spip_version_code'],'1.93','<')) ecrire_metas();
	    return true;
	}
}
?>
