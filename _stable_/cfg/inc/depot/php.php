<?php

/*
 * Plugin CFG pour SPIP
 * (c) toggg 2007, distribue sous licence GNU/GPL
 * Documentation et contact: http://www.spip-contrib.net/
 *
 * classe cfg_php: storage dans un fichier php
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

// cfg_php retrouve et met a jour les donnees d'un fichier php
class cfg_depot_php
{
	var $champs = array();
	var $champs_id = array();
	var $val = array();
	var $param = array();
	
	// version du depot
	var $version = 2;
	
	
	function cfg_depot_php($params=array()) {
		foreach ($params as $o=>&$v) {
			$this->$o = &$v;
		}
	}
	
	// calcule l'emplacepent du fichier
	function get_fichier(){
		static $fichier = array();
		$cle = $this->param->nom . ' - ' . $this->param->fichier;
		if (isset($fichier[$cle])) 
			return $fichier[$cle];
		
		if (!$this->param->fichier) 
			$f = _DIR_VAR . 'cfg/' . $this->param->nom . '.php';	
		else
			$f = _DIR_RACINE . $this->param->fichier;

		include_spip('inc/flock');
		return $fichier[$cle] = sous_repertoire(dirname($f)) . basename($f);
	}
	
	
	// recuperer les valeurs.
	function lire() {
		$fichier = $this->get_fichier();

		// inclut une variable $cfg
    	if (!@include $fichier) 
    		return array(true, array()); // le fichier peut ne pas exister, ce n'est pas une erreur
    	
    	if (!$cfg OR !is_array($cfg)) 
    		return array(true, array()); 

    	$cfg = &cfg_monte_arbre($cfg, $this->param->nom);
    	$cfg = &cfg_monte_arbre($cfg, $this->param->casier);
    	$cfg = &cfg_monte_arbre($cfg, $this->param->cfg_id);
    	if ($this->param->cfg_id) {
    		$cles = explode('/', $this->param->cfg_id);
			foreach ($this->champs_id as $i => $name) {
				$cfg[$name] = $cles[$i];
		    }
    	}
	    return array(true, $cfg);
	}


	// ecrit chaque enregistrement pour chaque champ
	function ecrire() {
		$fichier = $this->get_fichier();

		// inclut une variable $cfg
    	if (!@include $fichier) {
    		$base = array();
    	} elseif (!$cfg OR !is_array($cfg)) {
    		$base = array();
    	} else {
    		$base = $cfg;	
    	}

    	$ici = &$base;
    	$ici = &cfg_monte_arbre($ici, $this->param->nom);
    	$ici = &cfg_monte_arbre($ici, $this->param->casier);
    	$ici = &cfg_monte_arbre($ici, $this->param->cfg_id);
		foreach ($this->champs as $name => $def) {
			if (isset($def['id'])) {
				continue;
			}

			// applique une fonction sur le champ, si demande ?
			// ... necessite de passer $this->types... API a revoir pour la validation de champs
			$ici[$name] = isset($def['type_verif']) && ($cnv = $this->types[$def['type_verif']][2]) ?
				$cnv($this->val[$name]) : $this->val[$name];
	    }

		if (!$this->ecrire_fichier($base)){
			return array(false, $this->val);
		}
		
		return array(true, $ici);
	
	}
	
	
	// supprime chaque enregistrement pour chaque champ
	function effacer(){
		$fichier = $this->get_fichier();

		// inclut une variable $cfg
    	if (!@include $fichier) {
    		$base = array();
    	} elseif (!$cfg OR !is_array($cfg)) {
    		$base = array();
    	} else {
    		$base = $cfg;	
    	}

    	$ici = &$base;
    	$ici = &cfg_monte_arbre($ici, $this->param->nom);
    	$ici = &cfg_monte_arbre($ici, $this->param->casier);
    	$ici = &cfg_monte_arbre($ici, $this->param->cfg_id);	
    	
    	foreach ($this->champs as $name => $def) {
			if (isset($def['id'])) {
				continue;
			}
			
			if ($supprimer) {
				unset($ici[$name]);
			}
		}
		
		return array($this->ecrire_fichier($base), $ici);
	}
	
	
	function ecrire_fichier($contenu){
		$fichier = $this->get_fichier();

		if (!$contenu) {
			return supprimer_fichier($fichier);
		}

$contenu = '<?php
/**************
* Config ecrite par cfg le ' . date('r') . '
* 
* NE PAS EDITER MANUELLEMENT !
***************/

$cfg = ' . var_export($contenu, true) . ';
?>
';
		return ecrire_fichier($fichier, $contenu);
	}
	
	// charger les arguments de 
	// - lire_config(php::nom/casier/champ)
	// - lire_config(php::adresse/fichier.php:nom/casier/champ)
	function charger_args($args){
		list($fichier, $args) = explode(':',$args);
		if (!$args) {
			$args = $fichier;
			$fichier = _DIR_VAR . 'cfg/' . $fichier . '.php';	
		}

		$arbre = explode('/',$args);
		$this->param->nom = array_shift($arbre);
		$champ = array_pop($arbre);
		$this->champs = array($champ=>true);
		$this->param->casier = implode('/',$arbre);
		return true;	
	}
}

?>
