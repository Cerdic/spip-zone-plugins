<?php

/*
 * Plugin CFG pour SPIP
 * (c) Marcimat, toggg  2007, distribue sous licence GNU/GPL
 * Documentation et contact: http://www.spip-contrib.net/
 *
 * Affiche le formulaire CFG de la vue (fond) demandee
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

function balise_CFG_VUE($p){
	$vue = 			sinon(interprete_argument_balise(1,$p), "''"); // indispensable neanmmoins
	$id = 			sinon(interprete_argument_balise(2,$p), "''");
	$messages = 	sinon(interprete_argument_balise(3,$p), "'oui'");

	$p->code = "calculer_CFG_VUE($vue, $id, $messages)";
	return $p;
}

function calculer_CFG_VUE($fond, $id, $afficher_messages = 'oui'){
	include_spip('inc/cfg');
	$cfg = cfg_charger_classe('cfg');
	$config = & new $cfg($fond, $fond, $id); 
		
	if ($config->autoriser()){
		return 
			(($afficher_messages != 'non') ? "<div class='cfg_message'>" . $config->message . "</div>" : "")
			. $config->formulaire();	
	} else
		return $config->refus;
}

?>
