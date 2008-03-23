<?php

/*
 * Plugin CFG pour SPIP
 * (c) toggg 2007, distribue sous licence GNU/GPL
 * Documentation et contact: http://www.spip-contrib.net/
 *
 * classe cfg_classic: storage a plat (classique) dans spip_meta
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

// cfg_classic retrouve et met a jour les donnees a plat dans spip_meta
class cfg_classic
{
	function cfg_classic(&$cfg, $opt = array())
	{
		$this->cfg = &$cfg;
		foreach ($opt as $o=>$v) {
			$this->$o = $v;
		}
	}
	
// recuperer les valeurs, utilise la fonction commune lire_config() de cfg_options.php
	function lire()
	{
    	$val = array();
		foreach ($this->cfg->champs as $name => $def) {
			$val[$name] = lire_config($name);
	    }
	    return $val;
	}

	// modifier chaque enregistrement de meta pour chaque champ
	function modifier($supprimer = false)
	{
		foreach ($this->cfg->champs as $name => $def) {
			if ($supprimer || !$this->cfg->val[$name]) {
			    effacer_meta($name);
			} else {
			    ecrire_meta($name, $this->cfg->val[$name]);
			}
	    }
	    if (defined('_COMPAT_CFG_192')) ecrire_metas();
	    return true;
	}
}
?>
