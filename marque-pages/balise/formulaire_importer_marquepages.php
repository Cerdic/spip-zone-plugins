<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

function balise_FORMULAIRE_IMPORTER_MARQUEPAGES($p) {
	
    return calculer_balise_dynamique($p, 'FORMULAIRE_IMPORTER_MARQUEPAGES', array('id_rubrique'));
    
}

function balise_FORMULAIRE_IMPORTER_MARQUEPAGES_stat($args, $filtres) {
	
	$id_rubrique = $args[0] ? $args[0] : lire_config('marquepages/rubrique');
	
	return array($id_rubrique);
	
}

?>
