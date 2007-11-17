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
	return array($args[0], $args[1], $args[2], $args[3]);
}


//
function balise_FORMULAIRE_CFG_dyn($vue, $id, $cfg_form, $cfg_form_ajax) {   
	
	if (empty($cfg_form)) 
		$cfg_form = 'formulaires/formulaire_cfg';
	if (empty($cfg_form_ajax)) 
		$cfg_form_ajax = 'oui';
		
	$cfg_hash = substr(md5($vue.$id.$cfg_form.$cfg_form_ajax),0,6);
	return
		array(
			// squelette
			$cfg_form,
			// delai
			3600,
			// contexte
			array(
				'nom' => $vue,
				'vue' => $vue,
				'id' => $id,
				'cfg_hash' => $cfg_hash,
				'cfg_form' => $cfg_form,
				'cfg_form_ajax' => $cfg_form_ajax,
				'id_cfg' => $id
			)
		);
}

?>
