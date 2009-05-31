<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *  Plugin de création de sites en libre service                           *
 *                                                                         *
 *  Copyright (c) 2009                                                     *
 *  Daniel Viñar Ulriksen, dani@rezo.net                                   *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;

// charger cfg
include_spip('cfg_options');
include_spip('inc/silospip_form_fonctions');
include_spip('inc/silospip_panel');
include_spip('formulaires/login');

function formulaires_silo_creer_site_charger_dist($id_auteur = NULL){

	$valeurs = array();
	$champs = silospip_champs_formulaire();

	if (!is_numeric($id_auteur))
		$champs['editable']=false;

	//si on est en mode création et que l'utilisateur a saisi ses valeurs on les prends en compte
	foreach($champs as $clef => $val) {
		if (_request($val)) 
			$champs[$val] = _request($val);
	}
	
	if (_request('silo_lang')) 
		$champs['silo_sel_lang'] = _request('silo_lang');
	else
		$champs['silo_sel_lang'] = $GLOBALS['spip_lang'];

	$champs['silo_langues'] = explode(',', $GLOBALS['meta']['langues_proposees']);
		sort($champs['silo_langues']);
	$champs['password'] = _request('password');
	$champs['user_alternc'] = _request('user_alternc');

	return $champs;
}

function formulaires_silo_creer_site_verifier_dist($id_auteur = NULL){

	//if(_request('creer_site') && !_request('bouton_modifier_data_site'))
		//refuser_traiter_formulaire_ajax();

	//initilise le tableau de valeurs $champ => $val
	$valeurs = array(); 

	//initialise le tableau des erreurs
	$erreurs = array();
    
	//récupere la liste des champs possible
	$champs = silospip_champs_formulaire();

	foreach($champs as $clef => $val) {
		if (_request($val)) {
			$valeurs[$val] = _request($val);
		} else {
			$valeurs[$val] = '';
		}
	}

	if (($valeurs['silo_nom']) == '' ) {
		$erreurs['silo_nom'] =  _T('silospip:champ_obligatoire');
		$erreurs['message_erreur'] = _T('silospip:gen_champ_obligatoire');
	} elseif (($res = preg_match('/^[a-zA-Z0-9_-]+$/',$valeurs['silo_nom'])) !== 1 ) {
		$erreurs['silo_nom'] =  _T('silospip:chars_invalides_nom');
		$erreurs['message_erreur'] = _T('silospip:gen_chars_invalides_nom');
	} elseif (!verifier_login($GLOBALS["visiteur_session"]['login'],_request('password'))) {
		$erreurs['password'] =  _T('silospip:password_incorrect');
		$erreurs['message_erreur'] = _T('silospip:password_incorrect');
	} else {
		global $tables_principales;
		if(sql_getfetsel('nom','spip_silosites','nom='.sql_quote($valeurs['silo_nom']).' AND domaine='.sql_quote($valeurs['silo_domaine']))) {
			$erreurs['site_existe'] =  _T('silospip:site_existe', 'http://'.$valeurs['silo_nom'].'.'.$valeurs['silo_domaine'] );
			$erreurs['silo_nom'] =  _T('silospip:changer_nom_ou_dom');
			$erreurs['silo_domaine'] =  _T('silospip:changer_nom_ou_dom');
		}
	}

	
	if (count($erreurs)) {
		spip_log($erreurs,"silospip", _SILOSPIP_DEBUG_LOG);
		//message d'erreur generalise
		// $erreurs['message_erreur'] .= _T('inscription2:formulaire_remplir_obligatoires');
	}
	
	return $erreurs;  
}

function formulaires_silo_creer_site_traiter_dist($id_auteur = NULL){
	$env = $_POST;

	if(_request('bouton_valider_data_site')) {
		$env['editable'] = false;
		unset($env['bouton_valider_data_site']);
		$env['message_ok'] = 'Donnees de site valides';
		return $env;
	}
	elseif (_request('bouton_modifier_data_site')) {
		$env['editable'] = true;
		unset($env['bouton_modifier_data_site']);
		unset($env['message_ok']);
		return $env;
	}
	elseif(_request('creer_site')) {
		global $tables_principales;

		//initilise le tableau de valeurs $champ => $val
		$valeurs = array(); 

		//récupere la liste des champs possible
		$champs = silospip_champs_formulaire();
		foreach($champs as $clef => $val)
			if (_request($val)) {
				$champ_table = preg_replace('/^silo_(.*)/', '$1', $val);
				$valeurs[$champ_table] = _request($val);
			}

		$valeurs['date'] = $valeurs['maj'] = date("Y-m-d H:i:s",time());
		$valeurs['id_createur'] = $id_auteur;

		if (is_numeric($id_auteur)) {
			$table = 'spip_silosites';
			if( $id_createur = sql_getfetsel('id_auteur','spip_auteurs','id_auteur='.$id_auteur)) {
				$id = sql_insertq(
					$table,
					$valeurs
				);
			}
		}
		$env['editable'] = false;
		if (!silospip_panel_verifier_user($GLOBALS["visiteur_session"]['login'],_request('password')))
			$affiche = silospip_panel_creer_user(lire_config('silospip_admin_panel_user/'),lire_config('silospip_admin_panel_pass/'),$GLOBALS["visiteur_session"]['login'],_request('password'),_request('nom_famille'),_request('prenom'),_request('email'),_request('silo_domaine'));
		
		$env['message_ok'] = $affiche.silospip_panel_creer_base($GLOBALS["visiteur_session"]['login'],_request('silo_nom'));
		return $env;

		//include_spip('inc/headers');
		//redirige_par_entete('http://'._request('silo_nom').'.'._request('silo_domaine'));
	}
}


?>
