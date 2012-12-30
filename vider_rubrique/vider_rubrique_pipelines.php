<?php

/***************************************************************************\
 * Plugin Vider Rubrique pour Spip 3.0
 * Licence GPL (c) 2012 - Apsulis
 * Suppression de tout le contenu d'une rubrique
 *
\***************************************************************************/


function vider_rubrique_boite_infos($flux){
	include_spip('inc/config');
	$actif = lire_config('vider_rubrique/config/activer');
	$roles = lire_config('vider_rubrique/config/auteurs_autorises');
	$restreindre = lire_config('vider_rubrique/config/restreindre');
	$restreindre_val = lire_config('vider_rubrique/config/restreindre_valeur');
	$type = $flux['args']['type'];

	/*
		TODO : faire tous les tests
		- si webmestre OU admin
		- si c'est restreint à une liste de rubriques (ou une branche ?)
		- etc.
		Là c'est du mode un peu à l'arrache, mais ça fait ce qu'on demande
	*/
	if(autoriser("webmestre") && $actif=="oui"){
		if (($id = intval($flux['args']['id'])) && ($type=='rubrique')) {
			if($restreindre=="oui" && $id!=intval($restreindre_val)) return $flux;
			 
			$contexte = array('id_rubrique'=>$id);
			$flux["data"] .= recuperer_fond("noisettes/bouton_vider_rubrique", $contexte);
		}
	}

	return $flux;
}

function vider_rubrique_jqueryui_plugins($plugins){
	$plugins[] = "jquery.ui.dialog";
	return $plugins;
}