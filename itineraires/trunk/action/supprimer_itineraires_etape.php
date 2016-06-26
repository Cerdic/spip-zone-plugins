<?php 

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) {
	return;
}

function action_supprimer_itineraires_etape_dist(){
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();
	$id_itineraires_etape = intval($arg);
	
	if ($id_itineraires_etape > 0) {
		// On déplace le truc à supprimer tout à la fin
		$deplacer = charger_fonction('deplacer_itineraires_etape', 'action/');
		$deplacer("$id_itineraires_etape-10000");
		
		// On supprime
		sql_delete('spip_itineraires_etapes', 'id_itineraires_etape = '.$id_itineraires_etape);
	}
}
