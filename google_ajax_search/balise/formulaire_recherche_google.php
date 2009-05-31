<?php

if (!defined("_ECRIRE_INC_VERSION")) return;	#securite

// il y a surement plus simple ...
function balise_FORMULAIRE_RECHERCHE_GOOGLE($p) {
	return calculer_balise_dynamique($p,'FORMULAIRE_RECHERCHE_GOOGLE', array('recherche'));
}

function balise_FORMULAIRE_RECHERCHE_GOOGLE_stat($args, $filtres) {
	return $args;
}

function balise_FORMULAIRE_RECHERCHE_GOOGLE_dyn($recherche='') {
	return array('formulaires/formulaire_recherche_google', $GLOBALS['delais'],	
  			   array('recherche' => $recherche						 
						 )); 
}

?>
