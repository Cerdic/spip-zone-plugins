<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

function qr_insert_head_css($flux){
	return $flux;
}

function qr_header_prive($flux){
	return $flux;
}

function qr_porte_plume_barre_pre_charger($barres){
	// on ajoute les boutons dans la barre d'édition seulement
	return $barres;
}

function qr_porte_plume_lien_classe_vers_icone($flux){
	return $flux;
}
?>
