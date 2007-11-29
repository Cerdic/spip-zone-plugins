<?php

/*
 * #FORMULAIRE_CFG{nom} dans le squelette
 *
 * (c) Marcimat, toggg  2007, licence GNU/GPL
 * Documentation et contact: http://www.spip-contrib.net/
 */

if (!defined("_ECRIRE_INC_VERSION")) return;	#securite


function balise_FORMULAIRE_CFG ($p) {
	return calculer_balise_dynamique($p, 'FORMULAIRE_CFG', array());
}


// Dans $args on recupere un array des valeurs collectees par balise_FORMULAIRE_CFG
function balise_FORMULAIRE_CFG_stat($args, $filtres) {
	return array($args[0], $args[1], $args[2], $args[3], $args[4]);
}


//
function balise_FORMULAIRE_CFG_dyn($cfg_vue, $cfg_id, $cfg_form, $cfg_form_ajax, $cfg_afficher_messages) {   
	if (empty($cfg_form)) 
		$cfg_form = 'formulaires/formulaire_cfg_vue';
	if (empty($cfg_form_ajax)) 
		$cfg_form_ajax = 'oui';
		
	$cfg_hash = substr(md5($cfg_vue . $cfg_id . $cfg_form . $cfg_form_ajax . $cfg_afficher_messages),0,6);
	return
		array(
			// squelette
			'formulaires/formulaire_cfg',
			// delai
			3600,
			// contexte
			array(
				'cfg_nom' => $cfg_vue,
				'cfg_vue' => $cfg_vue,
				'cfg_hash' => $cfg_hash,
				'cfg_form' => $cfg_form,
				'cfg_form_ajax' => $cfg_form_ajax,
				'cfg_id' => $cfg_id,
				'cfg_afficher_messages' => $cfg_afficher_messages
			)
		);
}




/*
function balise_FORMULAIRE_CFG ($p) {
	$cfg_vue 				= interprete_argument_balise(1, $p);
	$cfg_id 				= sinon(interprete_argument_balise(2, $p),"''");
	$cfg_form 				= sinon(interprete_argument_balise(3, $p),"'formulaires/formulaire_cfg_vue'");
	$cfg_form_ajax 			= sinon(interprete_argument_balise(4, $p),"'oui'");
	$cfg_afficher_messages 	= sinon(interprete_argument_balise(5, $p),"'oui'");
	$p->code = "calculer_balise_formulaire_cfg($cfg_vue, $cfg_id, $cfg_form, $cfg_form_ajax, $cfg_afficher_messages)";
	return $p;
}
*/


/* facilites pour les pipelines */
function calculer_balise_formulaire_cfg(
			$cfg_vue, 
			$cfg_id = '', 
			$cfg_form = 'formulaires/formulaire_cfg_vue', 
			$cfg_form_ajax = 'oui', 
			$cfg_afficher_messages = 'oui')
{

	if (empty($cfg_form)) 				$cfg_form = 'formulaires/formulaire_cfg_vue';
	if (empty($cfg_form_ajax)) 			$cfg_form_ajax = 'oui';
	if (empty($cfg_afficher_messages)) 	$cfg_afficher_messages = 'oui';
				
	$cfg_hash = substr(md5($cfg_vue . $cfg_id . $cfg_form . $cfg_form_ajax),0,6);
	
	include_spip('public/assembler');
	$contexte = array(
			'cfg_nom' => $cfg_vue,
			'cfg_vue' => $cfg_vue,
			'cfg_hash' => $cfg_hash,
			'cfg_form' => $cfg_form,
			'cfg_form_ajax' => $cfg_form_ajax,
			'cfg_id' => $cfg_id,
			'cfg_afficher_messages' => $cfg_afficher_messages
	);
	return recuperer_fond('formulaires/formulaire_cfg', $contexte);	
}

?>
