<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Chargement des donnees du formulaire
 *
 * @param string $page
 *     Vide pour créer une nouvelle URL
 *     Nom d'une page pour éditer son URL
 * @param string $redirect
 * @return array
 */
function formulaires_editer_url_page_charger($page = '', $redirect = '') {

	// Valeurs de base
	$valeurs    = array();
	$page       = trim($page);
	$mode       = ($page ? 'modifier' : 'creer');
	$page_loked = false;

	// Valeurs selon qu'on modifie ou crée une URL
	switch ($mode){

		case 'modifier' :
			$url = sql_getfetsel(
				'url',
				'spip_urls',
				array(
					'page = '.sql_quote($page),
			));
			$fond        = trouver_fond_page($page);
			$page_locked = true; // interdire de modifier le champ page
			if (!$fond ){
				$message_erreur = _T('urls_pages:erreur_fond_absent_page', array('page' => $page));
			}
			break;

		case 'creer' :
			$url  = '';
			$fond = '';
			break;
	}

	// Valeurs renvoyées
	if (isset($message_erreur)
		and $message_erreur
	){
		$valeurs['message_erreur'] = $message_erreur;
		$valeurs['editable'] = false;
	}
	$valeurs = array_merge($valeurs, array(
		'page'        => $page,
		'url'         => $url,
		'page_locked' => $page_locked,
		'page_fond'   => $fond,
	));

	return $valeurs;
}


/**
 * Vérifier les valeurs postées
 *
 * @param string $page
 *     Vide pour créer une nouvelle URL
 *     Nom d'une page pour éditer son URL
 * @param string $redirect
 * @return array
 */
function formulaires_editer_url_page_verifier($page = '', $redirect = '') {
	$erreurs = array();

	include_spip('action/editer_url');
	$creation = !strlen($page);
	$page     = trim(_request('page'));
	$url      = trim(_request('url'));

	// PAGE
	// ====
	// Page obligatoire
	if (!$page) {
		$erreurs['page'] = _T('info_obligatoire');
	}
	// Format de la page incorrect
	elseif (!preg_match('/^([-\.\w]+)$/', $page)) {
		$erreurs['page'] = _T('urls_pages:erreur_page_mauvais_format');
	}
	// Page correspondant à objet éditorial
	elseif ($objets = lister_objets_types()
		and $objets_split = join('|', $objets)
		and $regex_objets = "/^($objets_split)([\-=_][0-9a-zA-Z]+)?$/"
		and preg_match($regex_objets, $page)
	) {
		$erreurs['page'] = _T('urls_pages:erreur_page_fond_objet_editorial');
	}
	// Création : URL de la page déjà enregistrée
	elseif ($creation
		and $url_bdd = sql_getfetsel('url', 'spip_urls', array('page = '.sql_quote($page)))
	) {
		$erreurs['page'] = _T('urls_pages:erreur_page_doublon_url', array('url' => $url_bdd));
	}
	// Squelette de la page absent
	elseif (!$fond = trouver_fond_page($page)) {
		$erreurs['page'] = _T('urls_pages:erreur_page_fond_absent');
	}

	// URL
	// ===
	// URL obligatoire
	if (!$url) {
		$erreurs['url'] = _T('info_obligatoire');
	}
	// Format URL incorrect
	elseif ($url != ($url_clean = url_nettoyer($url, 255))) {
		set_request('url', $url_clean);
		$erreurs['url'] = _T('urls:verifier_url_nettoyee');
	}
	// URL en doublon (peut importe l'objet)
	elseif (sql_countsel('spip_urls', array('url = '.sql_quote($url), 'page != '.sql_quote($page)))){
		$erreurs['url'] = _T('urls_pages:erreur_url_doublon');
	}

	return $erreurs;
}

/**
 * Traitement
 *
 * @param string $page
 *     Vide pour créer une nouvelle URL
 *     Nom d'une page pour éditer son URL
 * @param string $redirect
 * @return array
 */
function formulaires_editer_url_page_traiter($page = '', $redirect = '') {

	// Pas d'ajax si redirection
	if ($redirect) {
		refuser_traiter_formulaire_ajax();
	}

	$res  = array();
	$page = trim(_request('page'));
	$url  = trim(_request('url'));
	$mode = (sql_countsel('spip_urls', array('page = '.sql_quote($page))) ? 'modifier' : 'creer');
	$set  = array(
		'page' => $page,
		'url'  => $url,
	);

	// Actions BDD
	switch ($mode) {

		case 'creer' :
			$set = array_merge($set, array(
				'id_objet' => 0,
				'type'     => '',
				'perma'    => 1,
				'date'     => date('Y-m-d H:i:s '),
			));
			if (($insert = sql_insertq('spip_urls', $set)) === false){
				$message_erreur = _T('erreur_technique_enregistrement_impossible');
			}
			break;

		case 'modifier' :
			if (!$update = sql_updateq('spip_urls', $set, array('page = '.sql_quote($page)))) {
				$message_erreur = _T('erreur_technique_enregistrement_impossible');
			}
			break;
	}

	// Messages de retour éventuels
	if (isset($message_erreur)
		and $message_erreur
	){
		$res['message_erreur'] = $message_erreur;
	} else {
		$message_ok = _T('info_modification_enregistree');
		$message_ok .= '<br><a href="' . generer_url_ecrire('controler_urls_pages') . '">' . _T('navigateur_pas_redirige') . '</a>';
		$res['message_ok'] = $message_ok;

	}

	// Redirection éventuelle
	if ($redirect){
		$res['redirect'] = $redirect;
	}
	$res['editable'] = false;

	return $res;
}
