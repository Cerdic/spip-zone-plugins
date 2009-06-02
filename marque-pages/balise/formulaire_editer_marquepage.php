<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

function balise_FORMULAIRE_EDITER_MARQUEPAGE($p) {
	
    return calculer_balise_dynamique($p, 'FORMULAIRE_EDITER_MARQUEPAGE', array('id_forum', 'id_rubrique'));
    
}

function balise_FORMULAIRE_EDITER_MARQUEPAGE_stat($args, $filtres) {
	
	$id_forum = intval($args[0]) ? intval($args[0]) : 'new';
	$id_rubrique = $args[1] ? $args[1] : lire_config('marquepages/rubrique');
	
	return array($id_forum, $id_rubrique);
	
}

?>
