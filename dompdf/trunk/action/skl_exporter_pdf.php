<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

// Exporte en pdf
/**
 * avec des arguments direct
 * <a href='#URL_ACTION_AUTEUR{skl_exporter_pdf,#PAGE-patate-4,#SELF}'>Exporter ce squelette</a>
 * simple '#URL_ACTION_AUTEUR{skl_exporter_pdf,#PAGE,#SELF}'
 * <a href='#URL_ACTION_AUTEUR{skl_exporter_pdf,facture-commande-1,#SELF}'>Exporter la commande N°1 en PDF</a>
 *
**/
function action_skl_exporter_pdf_dist($arg = null) {
	if (is_null($arg)) {
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
	}
	
	// On récupère les infos de l'argument
	@list($squelette, $objet, $id_objet) = explode('-', $arg);
	
	include_spip('base/objets');
	$id_table_objet = id_table_objet($objet);

	$exporter_pdf = charger_fonction('exporter_pdf', 'inc');
	
	if(intval($id_objet) AND isset($id_table_objet)){
		$exporter_pdf($squelette, array($id_table_objet =>  $id_objet));
	} else {
		$exporter_pdf($squelette);
	}
}
