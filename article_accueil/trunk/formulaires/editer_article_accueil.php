<?php
/*
 * Plugin Articel Accueil
 * (c) 2011 Cedric Morin, Joseph
 * Distribue sous licence GPL
 *
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Chargement des donnees du formulaire
 *
 * @param string $type
 * @param int $id
 * @return array
 */
function formulaires_editer_article_accueil_charger($id_rubrique) {

	$valeurs = array();
	
	// On passe au formulaire l'id de la rubrique, la liste des statuts d'article autorisés
	// et une condition where qui est initialisée à '' par défaut.
	// Ainsi, il est possible à un plugin de modifier la liste des statuts et le where pour
	// influer sur le sélecteur d'articles.
	$valeurs['id_rubrique'] = $id_rubrique;
	$valeurs['_statuts'] = array('prepa', 'prop', 'publie');
	$valeurs['_where'] = '';

	// On détermine si un article est déjà sélectionné ou pas.
	$valeurs['id_article_accueil'] = 0;
	if ($id = sql_getfetsel('id_article_accueil', 'spip_rubriques', 'id_rubrique='.intval($id_rubrique))) {
		$valeurs['id_article_accueil'] = $id;
	}

	return $valeurs;
}

/**
 * Traitement
 *
 * @param string $type
 * @param int $id
 * @return array
 */
function formulaires_editer_article_accueil_traiter($id_rubrique) {

	$retour = array(
		'message_ok' => '',
		'editable'   => true
	);
	
	if (!_request('annuler')) {
		$update = array();
		if (!is_null($id_accueil = _request('id_article_accueil'))) {
			include_spip('base/abstract_sql');
			$update['id_article_accueil'] = $id_accueil;
			sql_updateq('spip_rubriques', $update, 'id_rubrique='.intval($id_rubrique));
		}
	}

	return $retour;
}
