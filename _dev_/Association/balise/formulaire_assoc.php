<?php

if (!defined("_ECRIRE_INC_VERSION")) return;	#securite

include_spip('base/abstract_sql');

// Balise independante du contexte



function balise_FORMULAIRE_ASSOC ($p) 
{
	return calculer_balise_dynamique($p, 'FORMULAIRE_ASSOC', array());
}
//function balise_FORMULAIRE_ASSOC_stat($args, $filtres) {
	// Si le moteur n'est pas active, pas de balise
	//if ($GLOBALS['meta']["activer_moteur"] != "oui")
		//return '';

	// filtres[0] doit etre un script (a revoir)
	//else
	  //return array($filtres[0], $args[0]);
//}
 
function balise_FORMULAIRE_ASSOC_dyn() {

	

	return array('formulaires/formulaire_assoc', 3600, 
		array(
			'assoc' => ($lien ? $lien : generer_url_public('assoc')),
			//'recherche' => _request('recherche'),
			'lang' => $lang
		));
}
//function balise_FORMULAIRE_ASSOC ($p) {
	//return calculer_balise_dynamique($p, 'FORMULAIRE_ASSOC', array('id_adherent'));
//}
?>
