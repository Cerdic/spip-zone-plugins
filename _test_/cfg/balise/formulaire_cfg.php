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
	//$squelette = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']))
	//	? 'formulaires/formulaire_cfg' //'inc-shoutbox' <- ajax
	//	: 'formulaires/formulaire_cfg'; // <- non ajax

	$cfg_hash = substr(md5($vue.$id),0,6);
	return
		array(
			// squelette
			$squelette = 'formulaires/formulaire_cfg',
			// delai
			3600,
			// contexte
			array(
				'nom' => $vue,
				'vue' => $vue,
				'id' => $id,
				'cfg_hash' => $cfg_hash,
				'id_cfg' => $id
			)
		);
}

?>
