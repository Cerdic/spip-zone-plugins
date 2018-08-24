<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


function grigri_formulaire_charger($flux) {

	include_spip('inc/config');
	include_spip('inc/autoriser');
	include_spip('base/objets');
	$objets = tables_grigri();

	if (
		preg_match('/^editer_(.*)/', $flux['args']['form'], $matches) // formulaire editer_xxx
		and $objet = $matches[1]
		and $id = id_table_objet($objet)
		and $table_objet_sql = table_objet_sql($objet)
		and in_array($table_objet_sql, $objets)
		and autoriser('modifier', 'identifiants')
	) {

		// on suppose que id_objet est le 1er paramètre du formulaire
		$id_objet = intval($flux['args']['args'][0]);

		// récupérer la valeur de l'identifiant
		$grigri = sql_getfetsel('grigri', $table_objet_sql, "$id=" .intval($id_objet) );
		$flux['data']['grigri'] = $grigri;

	}

	return $flux;
}

function grigri_formulaire_traiter($flux) {

	include_spip('inc/config');
	include_spip('inc/autoriser');
	include_spip('base/objets');
	$objets = tables_grigri();

	if (
		preg_match('/^editer_(.*)/', $flux['args']['form'], $matches) // formulaire editer_xxx
		and $objet = $matches[1]
		and $id_objet = intval($flux['args']['args'][0]) // on suppose que c'est le 1er paramètre
		and $table_objet_sql = table_objet_sql($objet)
		and in_array($table_objet_sql, $objets)
	) {

		$grigri   = _request('grigri');
		include_spip('action/editer_objet');
		$set = array ( 'grigri'    => str_replace(' ', '_', $grigri),);
		objet_modifier($objet, $id_objet, $set);
	}

	return $flux;
}
/**
 * Ajouter du contenu sur les formulaires d'édition des objets.
 *
 * - Ajouter la saisie grigri sur les objets configurés
 *
 * @pipeline editer_contenu_objet
 *
 * @param  array $flux Données du pipeline
 * @return array       Données du pipeline
 */
function grigri_editer_contenu_objet($flux) {

	include_spip('inc/config');
	include_spip('inc/autoriser');
	$objets = tables_grigri();
	
	// config public/privé: faut il afficher la boite d'édition ?
	if (test_espace_prive() AND (lire_config('grigri/grigri_prive') == 'non'))
		return $flux;
	if (!test_espace_prive() AND (lire_config('grigri/grigri_public') == 'non'))
		return $flux;

	// Identifiants sur les objets activés
	if (
		$objet = $flux['args']['type']
		and $table_objet_sql = table_objet_sql($objet)
		and in_array($table_objet_sql, $objets)
		and autoriser('modifier', 'grigri')
	) {

		// récupérer le squelette de la saisie
		// la valeur de l'grigri est donnée dans formulaire_charger
		$saisie = recuperer_fond(
			'prive/objets/editer/grigri',
			array(
				'grigri' => $flux['args']['contexte']['grigri'],
				'erreurs'     => $flux['args']['contexte']['erreurs'],
			)
		);

		// On insère la saisie après le titre si l'objet possède ce champ,
		// sinon après le premier champ (qu'on considère comme le titre),
		// sinon au niveau des champs extras.
		$cherche_titre = "/(<(?:li|div)[^>]*class=(?:'|\")editer editer_titre.*?<\/(?:li|div)>)\s*(<(?:li|div)[^>]*class=(?:'|\")editer)/is";
		$cherche_1er_champ = "/(<(?:ul|div)[^>]*?>\s*<(?:li|div)[^>]*class=(?:'|\")editer.*?<\/(?:li|div)>)\s*(<(?:li|div)[^>]*class=(?:'|\")editer)/is";
		$cherche_extra = '%(<!--extra-->)%is';

		if (preg_match($cherche_titre, $flux['data'])){
			$flux['data'] = preg_replace($cherche_titre, '$1'.$saisie.'$2', $flux['data'], 1);
		} elseif (preg_match($cherche_1er_champ, $flux['data'])){
			$flux['data'] = preg_replace($cherche_1er_champ, '$1'.$saisie.'$2', $flux['data'], 1);
		} elseif (preg_match($cherche_extra, $flux['data'])){
			$balise = (floatval(spip_version()) >= 3.1 ? 'div' : 'ul');
			$remplace_extra = "<$balise class='editer-groupe grigri'>$saisie</$balise>\n" . '$1';
			$flux['data'] = preg_replace($cherche_extra, $remplace_extra, $flux['data'], 1);
		}

	}

	return $flux;
}


/**
 * Ajouter du contenu dans la boîte infos d'un objet
 *
 * - Afficher le grigri sous le n° de l'objet pour les objets configurés
 *
 * @pipeline boite_info
 * @param array $flux Données du pipeline
 * @return array      Données du pipeline
**/
function grigri_boite_infos($flux) {

	include_spip('inc/config');
	include_spip('inc/autoriser');
	$objets = tables_grigri();

	if (
		$objet = $flux['args']['type']
		and $id = id_table_objet($objet)
		and $id_objet = intval($flux['args']['id'])
		and $table_objet_sql = table_objet_sql($objet)
		and in_array($table_objet_sql, $objets)
		and autoriser('voir', 'grigri')
	) {

		// récupérer la valeur du grigri
		$grigri = sql_getfetsel('grigri', $table_objet_sql, "$id=" .intval($id_objet) );

		// récupérer le squelette
		$info = recuperer_fond(
			'prive/objets/infos/grigri',
			array(
				'grigri' => $grigri,
			)
		);

		$cherche = "/(<div[^>]*class=('|\")numero.*?<\/div>)/is";
		$remplace = '$1' . "$info\n";
		$flux['data'] = preg_replace($cherche, $remplace, $flux['data']);

	}

	return $flux;
}


/**
 * Ajouter du contenu dans la colonne de gauche d'un objet
 *
 * - Afficher la suggestion de création d'grigri
 *
 * @pipeline affiche_gauche
 * @param array $flux Données du pipeline
 * @return array      Données du pipeline
**/
function grigri_affiche_gauche($flux) {

	include_spip('inc/config');
	include_spip('inc/autoriser');
	include_spip('base/objets');

	$objets = tables_grigri();

	if (
		$objets
		and isset($flux['args']['type-page'])
		and $exec = trouver_objet_exec($flux['args']['type-page'])
		and isset($exec['edition'])
		and !$exec['edition']
		and isset($exec['table_objet_sql'])
		and $table_objet_sql = $exec['table_objet_sql']
		and isset($exec['type'])
		and $objet = $exec['type']
		and isset($exec['id_table_objet'])
		and $id_table_objet = $exec['id_table_objet']
		and in_array($table_objet_sql, $objets)
		and isset($flux['args'][$id_table_objet])
		and $id_objet = intval($flux['args'][$id_table_objet])
		and autoriser('voir', 'grigri')
	) {

		// récupérer le squelette
		$utiles = recuperer_fond(
			'prive/squelettes/inclure/grigri_utiles',
			array(
				'objet' => $objet,
				'id_objet' => $id_objet,
			)
		);

		$flux['data'] .= $utiles;

	}
	return $flux;
}
