<?php
/**
 * Utilisations de pipelines par le plugin Identifiants
 *
 * @plugin     Identifiants
 * @copyright  2016
 * @author     C.R
 * @licence    GNU/GPL
 * @package    SPIP\Identifiants\Pipelines
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


/**
 * Ajouter du contenu sur les formulaires d'édition des objets.
 *
 * - Ajouter la saisie identifiant sur les objets configurés
 *
 * @pipeline editer_contenu_objet
 *
 * @param  array $flux Données du pipeline
 * @return array       Données du pipeline
 */
function identifiants_editer_contenu_objet($flux) {

	include_spip('inc/config');
	include_spip('inc/autoriser');
	$objets = lire_config('identifiants/objets', array());

	// Identifiants sur les objets activés
	if (
		$objet = $flux['args']['type']
		and $table_objet_sql = table_objet_sql($objet)
		and in_array($table_objet_sql, $objets)
		and autoriser('modifier', 'identifiants')
	) {

		// récupérer le squelette de la saisie
		// la valeur de l'identifiant est donnée dans formulaire_charger
		$saisie = recuperer_fond(
			'prive/objets/editer/identifiant',
			array(
				'identifiant' => $flux['args']['contexte']['identifiant'],
				'erreurs'     => $flux['args']['contexte']['erreurs'],
			)
		);

		// ajouter la saisie au niveau des champs extras
		$balise = defined('_DIR_PLUGIN_SAISIES') ? saisie_balise_structure_formulaire('ul') : 'div';
		$cherche = '%(<!--extra-->)%is';
		$remplace = "<$balise class='editer-groupe identifiant'>$saisie</$balise>\n" . '$1';
		$flux['data'] = preg_replace($cherche, $remplace, $flux['data']);
	}

	return $flux;
}


/**
 * Ajouter du contenu dans la boîte infos d'un objet
 *
 * - Afficher l'identifiant sous le n° de l'objet pour les objets configurés
 *
 * @pipeline boite_info
 * @param array $flux Données du pipeline
 * @return array      Données du pipeline
**/
function identifiants_boite_infos($flux) {

	include_spip('inc/config');
	include_spip('inc/autoriser');
	$objets = lire_config('identifiants/objets', array());

	if (
		$objet = $flux['args']['type']
		and $id_objet = intval($flux['args']['id'])
		and $table_objet_sql = table_objet_sql($objet)
		and in_array($table_objet_sql, $objets)
		and autoriser('voir', 'identifiants')
	) {

		// récupérer la valeur de l'identifiant
		$identifiant = sql_getfetsel(
			'identifiant',
			'spip_identifiants',
			'objet='.sql_quote($objet).' AND id_objet='.intval($id_objet)
		);

		// récupérer le squelette
		$info = recuperer_fond(
			'prive/objets/infos/identifiant',
			array(
				'identifiant' => $identifiant,
			)
		);

		$cherche = "/(<div[^>]*class=('|\")numero.*?<\/div>)/is";
		$remplace = '$1' . "$info\n";
		$flux['data'] = preg_replace($cherche, $remplace, $flux['data']);

	}

	return $flux;
}


/**
 * Ajouter des valeurs au chargement des formulaires
 *
 * - Ajouter l'identifiant lors de l'édition d'un objet, pour les objets configurés
 *
 * @pipeline boite_info
 * @param array $flux Données du pipeline
 * @return array      Données du pipeline
**/
function identifiants_formulaire_charger($flux) {

	include_spip('inc/config');
	include_spip('inc/autoriser');
	$objets = lire_config('identifiants/objets', array());

	if (
		preg_match('/^editer_(.*)/', $flux['args']['form'], $matches) // formulaire editer_xxx
		and $objet = $matches[1]
		and $table_objet_sql = table_objet_sql($objet)
		and in_array($table_objet_sql, $objets)
		and autoriser('modifier', 'identifiants')
	) {

		// on suppose que id_objet est le 1er paramètre du formulaire
		$id_objet = intval($flux['args']['args'][0]);

		// récupérer la valeur de l'identifiant
		$identifiant = sql_getfetsel(
			'identifiant',
			'spip_identifiants',
			'objet='.sql_quote($objet).' AND id_objet='.intval($id_objet)
		);
		$flux['data']['identifiant'] = $identifiant;

	}

	return $flux;
}


/**
 * Ajouter des vérifications aux formulaires
 *
 * - Vérifier le format et l'unicité de l'identifiant lors de l'édition d'un objet,
 * pour les objets configurés.
 *
 * @pipeline boite_info
 * @param array $flux Données du pipeline
 * @return array      Données du pipeline
**/
function identifiants_formulaire_verifier($flux) {

	include_spip('inc/config');
	include_spip('inc/autoriser');
	$objets = lire_config('identifiants/objets', array());

	if (
		preg_match('/^editer_(.*)/', $flux['args']['form'], $matches) // formulaire editer_xxx
		and $objet = $matches[1]
		and $id_objet = $flux['args']['args'][0] // on suppose que l'id est le 1er paramètre
		and $table_objet_sql = table_objet_sql($objet)
		and in_array($table_objet_sql, $objets)
		and autoriser('modifier', 'identifiants')
	) {

		if ($identifiant = _request('identifiant')) {
			// nombre max de caractères (on ne sait jamais)
			$nb_max = 255;
			if (($nb = strlen($identifiant)) > $nb_max) {
				$flux['data']['identifiant'] = _T('identifiant:erreur_champ_identifiant_taille', array('nb' => $nb, 'nb_max' => $nb_max));
			// format : caractères alphanumériques en minuscules ou "_"
			} elseif (!preg_match('/^[a-z0-9_]+$/', $identifiant)) {
				$flux['data']['identifiant'] = _T('identifiant:erreur_champ_identifiant_format');
					// doublon : on n'autorise qu'un seul identifiant par type d'objet
			} elseif (
				// objet existant
				(
					intval($id_objet)
					and sql_countsel(
						'spip_identifiants',
						'identifiant = '.sql_quote($identifiant).
							' AND objet = '.sql_quote($objet).
							' AND id_objet != '.intval($id_objet)
					)
				)
				// nouvel objet
				or (
					!intval($id_objet)
					and sql_countsel(
						'spip_identifiants',
						'identifiant = '.sql_quote($identifiant).' AND objet = '.sql_quote($objet)
					)
				)
			) {
				$flux['data']['identifiant'] = _T('identifiant:erreur_champ_identifiant_doublon');
			}
		}

	}

	return $flux;
}


/**
 * Ajouter des traitements aux formulaires
 *
 * - Mettre à jour l'identifiant lors de l'édition d'un objet, pour les objets configurés
 *
 * @note
 * On ne peut pas utiliser les pipelines pre_edition et post_edition,
 * car il ne s'agit pas d'un champ de la table de l'objet édité.
 *
 * @pipeline boite_info
 * @param array $flux Données du pipeline
 * @return array      Données du pipeline
**/
function identifiants_formulaire_traiter($flux) {

	include_spip('inc/config');
	include_spip('inc/autoriser');
	$objets = lire_config('identifiants/objets', array());

	if (
		preg_match('/^editer_(.*)/', $flux['args']['form'], $matches) // formulaire editer_xxx
		and $objet = $matches[1]
		and $id_objet = intval($flux['args']['args'][0]) // on suppose que c'est le 1er paramètre
		and $table_objet_sql = table_objet_sql($objet)
		and in_array($table_objet_sql, $objets)
		and autoriser('modifier', 'identifiants')
	) {

		maj_identifiant_objet($objet, $id_objet, _request('identifiant'));

	}

	return $flux;
}


/**
 * Intervenir après la création d'un objet.
 *
 * - Créer l'identifiant lors de la création d'un objet, pour les objets configurés.
 *
 * @note
 * L'identifiant n'est pas transmis dans $flux['data'], il faut le récupérer avec _request().
 * Il ne s'agit pas d'un champ de la table de l'objet édité.
 *
 * @pipeline post_insertion
 * @param  array $flux Données du pipeline
 * @return array       Données du pipeline
 */
function identifiants_post_insertion($flux) {

	include_spip('inc/config');
	include_spip('inc/autoriser');
	$objets = lire_config('identifiants/objets', array());

	if (
		$table_objet = $flux['args']['table']
		and $objet = objet_type($table_objet)
		and $id_objet = $flux['args']['id_objet']
		and in_array($table_objet, $objets)
		and autoriser('modifier', 'identifiants')
	) {

		maj_identifiant_objet($objet, $id_objet, _request('identifiant'));

	}

	return $flux;
}


/**
 * Ajouter du contenu dans la colonne de gauche d'un objet
 *
 * - Afficher la suggestion de création d'identifiants
 *
 * @pipeline affiche_gauche
 * @param array $flux Données du pipeline
 * @return array      Données du pipeline
**/
function identifiants_affiche_gauche($flux) {

	include_spip('inc/config');
	include_spip('inc/autoriser');
	include_spip('base/objets');
	$objets = lire_config('identifiants/objets', array());

	// prendre en compte le pipeline identifiant_utiles
	if (
		is_array($identifiants_utiles = identifiants_utiles())
		and $objets_utiles = array_map('table_objet_sql', array_keys($identifiants_utiles))
	) {
		$objets = array_merge($objets, $objets_utiles);
	}

	if (
		isset($flux['args']['type-page'])
		and $objet = $flux['args']['type-page']
		and $table_objet_sql = table_objet_sql($objet)
		and in_array($table_objet_sql, $objets)
		and $id_table_objet = id_table_objet($objet)
		and isset($flux['args'][$id_table_objet])
		and $id_objet = intval($flux['args'][$id_table_objet])
		and !sql_countsel('spip_identifiants', 'objet = '.sql_quote($objet).' AND id_objet = '.intval($id_objet))
		and autoriser('voir', 'identifiants')
		and !empty($identifiants_utiles[$objet])
	) {

		// récupérer le squelette
		$utiles = recuperer_fond(
			'prive/squelettes/inclure/identifiants_utiles',
			array(
				'objet' => $objet,
				'id_objet' => $id_objet,
				'identifiants_utiles' => $identifiants_utiles[$objet]
			)
		);

		$flux['data'] .= $utiles;

	}

	return $flux;
}
