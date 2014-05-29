<?php
/*
 * Plugin messagerie
 * Licence GPL
 * (c) 2008 C.Morin Yterium
 *
 */

/**
 * Pipeline messagerie_signer_message
 * Ajout d'une signature en bas de mail
 *
 * @param unknown_type $texte
 * @return unknown
 */
function messagerie_messagerie_signer_message($texte){
	$texte .= _T('messagerie:texte_signature_email',array('nom_site'=>$GLOBALS['meta']['nom_site'],'url_site'=>$GLOBALS['meta']['adresse_site']));
	return $texte;
}

/**
 * Pipeline inserthead.
 * Ajout d'une css dans l'espace public
 *
 * @param unknown_type $texte
 * @return unknown
 */
function messagerie_insert_head($texte){
	$texte .= '<link rel="stylesheet" type="text/css" href="'.find_in_path('habillage/messagerie.css').'" media="all" />'."\n";
	return $texte;
}

function messagerie_messagerie_statuts_destinataires_possibles(){
        include_spip('inc/filtres_ecrire');
        return auteurs_lister_statuts('tous',false);
}



?>
