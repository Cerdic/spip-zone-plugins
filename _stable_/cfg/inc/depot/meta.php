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
class cfg_depot_meta
{
	var $champs = array();
	var $champs_id = array();
	var $val = array();
	var $param = array();
	
	function cfg_depot_meta(&$params=array())
	{
		$this->cfg = &$cfg;
		foreach ($params as $o=>&$v) {
			$this->$o = &$v;
		}	
	}
	
	// recuperer les valeurs.
	function lire()
	{
    	$val = array();
		foreach ($this->champs as $name => $def) {
			$val[$name] = lire_meta($name);
	    }
	    return $val;
	}


	// ecrit chaque enregistrement de meta pour chaque champ
	function ecrire()
	{
		foreach ($this->champs as $name => $def) {
			ecrire_meta($name, $this->val[$name]);
	    }
	    if (defined('_COMPAT_CFG_192')) ecrire_metas();
	    return true;
	}
	
	
	// supprime chaque enregistrement de meta pour chaque champ
	function effacer(){
		foreach ($this->champs as $name => $def) {
			if (!$this->val[$name]) {
			    effacer_meta($name);
			}
	    }
	    if (defined('_COMPAT_CFG_192')) ecrire_metas();
	    return true;			
	}
	
	
	
	function charger_args($args){
		$this->champs = array($args=>true);
		return true;	
	}
}
?>
