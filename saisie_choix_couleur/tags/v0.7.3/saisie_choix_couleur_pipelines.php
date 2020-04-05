<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function saisie_choix_couleur_insert_head($flux){
	$flux .= "<link rel='stylesheet' href='".find_in_path("saisie_choix_couleur.css")."' type='text/css' />";
	return $flux;
	}
function saisie_choix_couleur_header_prive($flux){
	return saisie_choix_couleur_insert_head($flux);
	}
?>