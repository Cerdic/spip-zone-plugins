<?php

// chargement des fonctions
include_spip("tinymce_fonctions") ;

/**
 * Avant chargement de la barre du PortePlume
 */
function tinymce_porte_plume_barre_pre_charger($barres) {	
	if (true===tinymce_doitetrecharge()){
		if (true===isset($barres['edition'])){
			unset($barres['edition']);
		}
	}
	return $barres;
}

/**
 * Pendant chargement de la barre du PortePlume
 */
function tinymce_porte_plume_barre_charger($barres) {	
	if (true===tinymce_doitetrecharge()){
		if (true===isset($barres['edition'])){
			unset($barres['edition']);
		}
	}
	return $barres;
}

/**
 * Avant edition d'un objet
 */
function tinymce_pre_edition($flux) {	
	return $flux ;
}

/**
 * Header de l'espace prive
 */
function tinymce_header_prive($flux) {
	if (true===tinymce_doitetrecharge()){
		// on nettoie le header
		$flux = tinymce_nettoyerheader( $flux );
		// on ajoute TinyMCE
		$flux .= tinymce_chargerenheader();
	}
	return $flux ;
}

/**
 * Header de l'espace public
 */
function tinymce_insert_head($flux) {
	return $flux ;
}

/**
 * Avant enregistrement en BDD apres modif d'un objet SPIP
 * On force un vrai rechargement en cas d'ajax ... sinon rien n'est enregistre!
 */
function tinymce_editer_contenu_objet($flux) {
	if (true===tinymce_doitrechargerpage()){
		$flux['data'] .= tinymce_jsrechargerpage();
	}
	return $flux ;
}

?>