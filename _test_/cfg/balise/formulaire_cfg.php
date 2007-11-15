<?php

/*
 * #FORMULAIRE_CFG{nom} dans le squelette
 *
 * (c) Marcimat 2007, licence GNU/GPL
 * 
 */

if (!defined("_ECRIRE_INC_VERSION")) return;	#securite



function balise_FORMULAIRE_CFG ($p) {
	return calculer_balise_dynamique($p, 'FORMULAIRE_CFG', array());
}


// Dans $args on recupere un array des valeurs collectees par balise_FORMULAIRE_CFG
function balise_FORMULAIRE_CFG_stat($args, $filtres) {
	return array($vue = $args[0], $id = $args[1]);
}


//
function balise_FORMULAIRE_CFG_dyn($vue, $id) {

	// si appel ajax on ne renvoie que le contenu
	$squelette = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']))
		? 'formulaires/formulaire_cfg' //'inc-shoutbox' <- ajax
		: 'formulaires/formulaire_cfg'; // <- non ajax

	return
		array(
			// squelette
			$squelette,
			// delai
			3600,
			// contexte
			array(
				'nom' => $vue,
				'vue' => $vue,
				'id' => $id,
				'id_cfg' => $id
			)
		);
}


/*
 * Affiche le formulaire CFG de la vue (fond) demandee
 */
function balise_VUE_CFG($p){
	$vue = 			sinon(interprete_argument_balise(1,$p), "''"); // indispensable neanmmoins
	$id = 			sinon(interprete_argument_balise(2,$p), "''");
	$aff_titre = 	sinon(interprete_argument_balise(3,$p), "''");
	$cfg_id = 		sinon(interprete_argument_balise(4,$p), "''");
	$p->code = "calculer_VUE_CFG($vue, $id, $aff_titre, $cfg_id)";
	return $p;
}

function calculer_VUE_CFG($fond, $id, $afficher_titre, $cfg_id){
	include_spip('inc/cfg');
	$cfg = cfg_charger_classe('cfg');

	$config = & new $cfg($fond, $fond, $id); 

	$config->traiter();
	
	$sortie = ($afficher_titre)
		? "<h2>$config->titre</h2>\n"
		: "";
		
	return $sortie
		   . $config->formulaire();	
}


?>
