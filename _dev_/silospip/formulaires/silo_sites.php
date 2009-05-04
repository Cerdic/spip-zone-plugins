<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *  Plugin de création de sites en libre service                           *
 *                                                                         *
 *  Copyright (c) 2009                                                     *
 *  Daniel Viñar Ulriksen                                                  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;

// charger cfg
include_spip('cfg_options');
include_spip('inc/silospip_form_fonctions');

function formulaires_silo_sites_charger_dist($id_auteur = NULL){

	$champs = array();

	if (!is_numeric($id_auteur))
		$champs['editable']=false;
	else
		$champs['id_auteur']=$id_auteur;
	if(_request('id_site'))
		$champs['id_site']=_request('id_site');

	return $champs;
}

function formulaires_silo_sites_verifier_dist($id_auteur = NULL){

	if(_request('bouton_creer_site'))
		refuser_traiter_formulaire_ajax();

	//initialise le tableau des erreurs
	$erreurs = array();
    
	return $erreurs;  
}

function formulaires_silo_sites_traiter_dist($id_auteur = NULL){
	$env = $_POST;

	if(_request('bouton_effacer_site')) {
		$env['editable'] = false;
		return $env;
	}
	elseif(_request('bouton_confirm_effacer_site')) {
		global $tables_principales;

		$id_site_del = _request('id_site');

		if (is_numeric($id_auteur) && is_numeric($id_site_del)) {
			$table = 'spip_silosites';
			if( $id_createur = sql_getfetsel('id_auteur','spip_auteurs','id_auteur='.$id_auteur)) {
				$id = sql_delete($table, "id_site=$id_site_del");
			}
		}
		$env['editable'] = true;
		unset($env['bouton_confirm_effacer_site']);
		unset($env['creer_site']);
		return $env;
	}
	elseif (_request('creer_site')) {
		unset($env['message_ok']);
		include_spip('inc/headers');
		redirige_par_entete(parametre_url(self(), 'page', 'silo_creer_site'));
		// return $env;
	}

}

?>
