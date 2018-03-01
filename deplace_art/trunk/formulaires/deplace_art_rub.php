<?php
/**
 * Formulaire de déplacement des articles d'une rubrique à une autre
 *
 * @plugin     Déplacer des articles par lot
 * @copyright  2018
 * @author     Teddy Payet
 * @licence    GNU/GPL
 * @package    SPIP/Formulaires/deplace_artRub
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function formulaires_deplace_art_rub_charger_dist($id_rubrique) {
	// Contexte du formulaire.
	$contexte = array();

	return $contexte;
}

/*
*   Fonction de vérification, cela fonction avec un tableau d'erreur.
*   Le tableau est formater de la sorte:
*   if (!_request('NomErreur')) {
*       $erreurs['message_erreur'] = '';
*       $erreurs['NomErreur'] = '';
*   }
*   Pensez à utiliser _T('info_obligatoire'); pour les éléments obligatoire.
*/
function formulaires_deplace_art_rub_verifier_dist($id_rubrique) {
	include_spip('inc/utils');
	$erreurs = array();

	// On vérifie qu'on a bien sélectionné des articles et une rubrique de destination
	$obligatoires = array('rubrique_dest');
	foreach ($obligatoires as $obligatoire) {
		$values = _request($obligatoire);
		if (!is_array($values) or count($values) == 0) {
			$erreurs[$obligatoire] = _T('info_obligatoire');
		}
	}
	include_spip('inc/autoriser');
	// ****
	// Vérifions les autorisations sur la rubrique de destination
	// ****
	$rubrique_dest = _request('rubrique_dest');
	$rubrique_dest = explode('|', $rubrique_dest[0]);
	$rubrique_dest = $rubrique_dest[1];
	$autoriser = autoriser('modifier', 'rubrique', $rubrique_dest);

	if ($autoriser == false) {
		$erreurs['rubrique_dest'] = _T('deplace_art:rubrique_dest_interdite');
	}

	if ($rubrique_dest == $id_rubrique) {
		$erreurs['rubrique_dest'] = _T('deplace_art:rubrique_source_identique_destination');
	}

	return $erreurs;
}

function formulaires_deplace_art_rub_traiter_dist($id_rubrique) {
	include_spip('inc/utils');
	include_spip('base/abstract_sql');

	$retour = array(
		'editable' => true,
		'message_ok' => '',
		'redirect' => '',
	);
	// Traitement du formulaire.
	$rubrique_dest = _request('rubrique_dest');
	$rubrique_dest = explode('|', $rubrique_dest[0]);
	$rubrique_dest = $rubrique_dest[1];
	$_rubrique_info = sql_fetsel('id_secteur, titre', "spip_rubriques", "id_rubrique=$rubrique_dest");
	$resultat = sql_updateq('spip_articles', array(
		'id_secteur' => $_rubrique_info['id_secteur'],
		'id_rubrique' => $rubrique_dest,
	), 'id_rubrique=' . $id_rubrique);

	// On met à jour les rubriques.
	include_spip('inc/rubriques');
	calculer_rubriques();

	if ($resultat) {
		include_spip('inc/urls');
		$retour['message_ok'] = _T('deplace_art:deplacement_art_reussi', array(
			'titre' => $_rubrique_info['titre'],
			'url' => generer_url_ecrire('rubrique', "id_rubrique=" . $rubrique_dest),
		));
	} else {
		$retour['message_ko'] = _T('erreur_technique_ajaxform');
	}

	// Donnée de retour.
	return $retour;
}
