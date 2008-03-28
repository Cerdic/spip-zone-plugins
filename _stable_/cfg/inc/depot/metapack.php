<?php

/*
 * Plugin CFG pour SPIP
 * (c) toggg 2007, distribue sous licence GNU/GPL
 * Documentation et contact: http://www.spip-contrib.net/
 *
 * classe cfg_metapack: storage serialise dans spip_meta
 */

if (!defined("_ECRIRE_INC_VERSION")) return;



// cfg_metapack retrouve et met a jour les donnees dans spip_meta
class cfg_depot_metapack
{
	var $champs = array();
	var $champs_id = array();
	var $val = array();
	var $param = array();
	
	var $_arbre = array();
	
	// version du depot
	var $version = 2;
	
	function cfg_depot_metapack($params=array())
	{
		foreach ($params as $o=>&$v) {
			$this->$o = &$v;
		}	
	}
	
	// recuperer les valeurs.
	function lire()
	{
	    $val = unserialize($GLOBALS['meta'][$this->param->nom]);
    	$val = &cfg_monte_arbre($val, $this->param->casier);
    	$val = &cfg_monte_arbre($val, $this->param->cfg_id);
    	
        // utile ??
    	if ($this->param->cfg_id) {
    		$cles = explode('/', $this->param->cfg_id);
			foreach ($this->champs_id as $i => $name) {
				$val[$name] = $cles[$i];
		    }
    	}
    	
    	// s'il y a des champs demandes, les retourner... sinon, retourner la base
    	// (cas de lire_config('metapack::nom') tout court)
    	if (count($this->champs)){
    		$_val = array();
			foreach ($this->champs as $name => $def) {
				$_val[$name] = $val[$name];
			}
			$val = $_val;
    	}
	    return array(true, $val);
	}


	// ecrit chaque enregistrement de meta pour chaque champ
	// pour ecrire une meta normale, on peut passer serialize a false
	function ecrire($serialize=true)
	{
  		// si pas de champs : on ecrit directement (ecrire_meta(metapack::nom,$val))...
  		if (!$this->champs){
  			ecrire_meta($this->param->nom, serialize($this->val));
  			if (defined('_COMPAT_CFG_192')) ecrire_metas();
  			return array(true, $this->val);
  		}
  		
	    $base = unserialize($GLOBALS['meta'][$this->param->nom]);
		$ici = &$base;
		$ici = &$this->monte_arbre($ici, $this->param->casier);
		$ici = &$this->monte_arbre($ici, $this->param->cfg_id);
		
		foreach ($this->champs as $name => $def) {
			if (isset($def['id'])) continue;
			$ici[$name] = $this->val[$name];
		}

		ecrire_meta($this->param->nom, $serialize ? serialize($base) : $base);
		if (defined('_COMPAT_CFG_192')) ecrire_metas();
		return array(true, $ici);
	}
	
	
	// supprime chaque enregistrement de meta pour chaque champ
	function effacer(){
  		// si pas de champs : on supprime directement (effacer_meta(metapack::nom))...
  		if (!$this->champs){
  			effacer_meta($this->param->nom);
  			if (defined('_COMPAT_CFG_192')) ecrire_metas();
  			return array(true, array());
  		}
  		
	    $base = unserialize($GLOBALS['meta'][$this->param->nom]);
	    $this->_arbre = array();
		$ici = &$base;
		$ici = &$this->monte_arbre($ici, $this->param->casier);
		$ici = &$this->monte_arbre($ici, $this->param->cfg_id);

		// supprimer les champs
		foreach ($this->champs as $name => $def) {
			if (isset($def['id'])) continue;
			unset($ici[$name]);
		}

		// supprimer les dossiers vides
		for ($i = count($this->_arbre); $i--; ) {
			if ($this->_arbre[$i][0][$this->_arbre[$i][1]]) {
				break;
			}
			unset($this->_arbre[$i][0][$this->_arbre[$i][1]]);
		}
		
		if (!$base) {
		    effacer_meta($this->param->nom);
		} else {
		    ecrire_meta($this->param->nom, serialize($base));
	    }		
		if (defined('_COMPAT_CFG_192')) ecrire_metas();
		
		return array(true, array());
	}
	
	
	// charger les arguments de lire_config(metapack::nom/casier/champ)
	// $args = 'nom'; ici
	// il se peut qu'il n'y ait pas de champs si : lire_config(metapack::nom);
	function charger_args($args){
		$args = explode('/',$args);
		$this->param->nom = array_shift($args);
		$champ = array_pop($args);
		if ($champ) {
			$this->champs = array($champ=>true);
		}
		$this->param->casier = implode('/',$args);
		return true;	
	}
	
	
	// se positionner dans le tableau arborescent
	function & monte_arbre(&$base, $chemin){
		if (!$chemin) {
			return $base;
		}
		if (!is_array($chemin)) {
			$chemin = explode('/', $chemin);
		}
		foreach ($chemin as $dossier) {
			if (!isset($base[$dossier])) {
				$base[$dossier] = array();
			}
	    	$this->_arbre[] = array(&$base, $dossier);
	    	$base = &$base[$dossier];
		}
		return $base;
	}
}



?>
