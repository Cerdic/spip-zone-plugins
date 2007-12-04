<?php

/*
 * Plugin CFG pour SPIP
 * (c) Marcimat, toggg  2007, distribue sous licence GNU/GPL
 * Documentation et contact: http://www.spip-contrib.net/
 *
 * Affiche le formulaire CFG de la vue (fond) demandee
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

function balise_CFG_TRAITER($p){
	$vue = 			sinon(interprete_argument_balise(1,$p), "''"); // indispensable neanmmoins
	$id = 			sinon(interprete_argument_balise(2,$p), "''");

	$p->code = "calculer_CFG_TRAITER($vue, $id)";
	return $p;
}

function calculer_CFG_TRAITER($fond, $id){
	include_spip('inc/cfg');
	$cfg = cfg_charger_classe('cfg');
	$config = & new $cfg($fond, $fond, $id); 
		
	if ($config->autoriser()){
		$config->traiter();	
		return $config->message;	
	} else
		return $config->refus;
}

?>
