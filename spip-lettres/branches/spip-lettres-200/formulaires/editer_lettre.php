<?php
/**
 * SPIP-Lettres
 *
 * Copyright (c) 2006-2009
 * Agence Artégo http://www.artego.fr
 *
 * Ce programme est un logiciel libre distribue sous licence GNU/GPLv3.
 * Pour plus de details voir http://www.gnu.org/licenses/gpl-3.0.html
 *
 **/

include_spip('inc/documents');
include_spip('inc/editer');
include_spip('lettres_fonctions');

function formulaires_editer_lettre_charger_dist($id_lettre,$id_rubrique,$retour){
	$valeurs = formulaires_editer_objet_charger('lettre',$id_lettre,$id_rubrique,0,$retour,'lettres_edit_config');
	if (is_array($valeurs))
		unset($valeurs['id_rubrique']);
	return $valeurs;	
}
// compat 2.0.x / 2.1.x
if (version_compare($GLOBALS['spip_version_branche'],"2.0.99",">")  AND !function_exists('barre_typo')){
	function barre_typo(){};
}

function lettres_edit_config($row){
	global $spip_ecran, $spip_lang, $spip_display;

	$config['lignes'] = ($spip_ecran == "large")? 8 : 5;
	$config['afficher_barre'] = $spip_display != 4;
	$config['langue'] = $spip_lang;

	return $config;
}


function formulaires_editer_lettre_verifier_dist($id_lettre,$id_rubrique,$retour){
	$erreurs = formulaires_editer_objet_verifier('lettre',$id_lettre,array('titre'));
	return $erreurs;

}

function formulaires_editer_lettre_traiter_dist($id_lettre,$id_rubrique,$retour){

	$lettre = new lettre($id_lettre);
	$lettre->titre = _request('titre');
	$lettre->id_rubrique = _request('id_parent');
	$lettre->descriptif = _request('descriptif');
	$lettre->chapo = _request('chapo');
	$lettre->texte = _request('texte');
	$lettre->ps = _request('ps');
	/*
	TODO
				if ($champs_extra)
					$lettre->extra		= extra_recup_saisie("lettres");
	*/

	$lettre->enregistrer();
	if (!intval($id_lettre))
		$lettre->enregistrer_auteur($GLOBALS['visiteur_session']['id_auteur']);

	$retour = parametre_url($retour,'id_lettre',$lettre->id_lettre);

	return array('message_ok'=>'ok','redirect'=>$retour);
}
?>