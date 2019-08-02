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

		// On insère la saisie après le titre si l'objet possède ce champ,
		// sinon après le premier champ (qu'on considère comme le titre),
		// sinon au niveau des champs extras.
		$cherche_titre = "/(<(?:li|div)[^>]*class=(?:'|\")editer editer_titre.*?<\/(?:li|div)>)\s*(<(?:li|div)[^>]*class=(?:'|\")editer)/is";
		$cherche_1er_champ = "/(<(?:ul|div)[^>]*?>\s*<(?:li|div)[^>]*class=(?:'|\")editer.*?<\/(?:li|div)>)\s*(<(?:li|div)[^>]*class=(?:'|\")editer)/is";
		$cherche_extra = '%(<!--extra-->)%is';

		if (preg_match($cherche_titre, $flux['data'])){
			$flux['data'] = preg_replace($cherche_titre, '$1'.$saisie.'$2', $flux['data']);
		} elseif (preg_match($cherche_1er_champ, $flux['data'])){
			$flux['data'] = preg_replace($cherche_1er_champ, '$1'.$saisie.'$2', $flux['data']);
		} elseif (preg_match($cherche_extra, $flux['data'])){
			$balise = (floatval(spip_version()) >= 3.1 ? 'div' : 'ul');
			$remplace_extra = "<$balise class='editer-groupe identifiant'>$saisie</$balise>\n" . '$1';
			$flux['data'] = preg_replace($cherche_extra, $remplace_extra, $flux['data']);
		}

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
	include_spip('base/objets');
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
	include_spip('base/objets');
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

			// 1) Nombre max de caractères
			$nb_max = 255;
			if (($nb = strlen($identifiant)) > $nb_max) {
				$erreur = _T('identifiant:erreur_champ_identifiant_taille', array('nb' => $nb, 'nb_max' => $nb_max));

			// 2) Format : caractères alphanumériques en minuscules ou "_"
			} elseif (!preg_match('/^[a-z0-9_]+$/', $identifiant)) {
				$erreur = _T('identifiant:erreur_champ_identifiant_format');

			// 3) Doublon : on interdit les doublons pour le type de l'objet,
			// sauf si l'objet avec l'identifiant en doublon n'existe pas,
			// car dans ce cas l'identifiant sera réattribué dans le traiter.
			} elseif (
				$where_doublon = array(
					'identifiant = ' . sql_quote($identifiant),
					'objet = ' . sql_quote($objet),
					intval($id_objet) ? 'id_objet != ' . intval($id_objet) : '1 = 1',
				)
				and $doublon = sql_fetsel(
					'objet, id_objet',
					'spip_identifiants',
					$where_doublon
				)
				and $table_doublon = table_objet_sql($doublon['objet'])
				and $id_table_doublon = id_table_objet($doublon['objet'])
				and sql_countsel($table_doublon, $id_table_doublon.' = '.intval($doublon['id_objet']))
			) {
				$erreur = _T('identifiant:erreur_champ_identifiant_doublon_objet', array('objet' => $doublon['objet'], 'id_objet' => $doublon['id_objet']));
			}

			if (isset($erreur)
				and $erreur
			) {
				$flux['data']['identifiant'] = $erreur;
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
	include_spip('base/objets');
	$objets = lire_config('identifiants/objets', array());

	if (
		preg_match('/^editer_(.*)/', $flux['args']['form'], $matches) // formulaire editer_xxx
		and $objet = $matches[1]
		and $id_objet = intval($flux['args']['args'][0]) // on suppose que c'est le 1er paramètre
		and $table_objet_sql = table_objet_sql($objet)
		and in_array($table_objet_sql, $objets)
		and autoriser('modifier', 'identifiants')
	) {
		if (!function_exists('maj_identifiant_objet')) {
			include_spip('identifiants_fonctions');
		}
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
		if (!function_exists('maj_identifiant_objet')) {
			include_spip('identifiants_fonctions');
		}
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
		and !sql_countsel('spip_identifiants', array('objet = '.sql_quote($objet), 'id_objet = '.intval($id_objet)))
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


/**
 * Supprimer les lignes obsolètes de la BDD
 *
 * On supprime les identifiants des objets inexistants
 *
 * @param array $flux
 */
function identifiants_optimiser_base_disparus($flux){

	$n = &$flux['data'];

	// Il faut transmettre une ressource SQL.
	// Ne connaissant pas d'office les tables et clés primaires des objets,
	// on ne peut pas faire directement le select.
	// Donc on cherche d'abord les identifiants orphelins, puis après on fait le select avec un IN.
	if ($objets_identifiants = sql_allfetsel('DISTINCT(objet)', 'spip_identifiants')) {
		include_spip('base/objets');
		$identifiants = array();
		foreach($objets_identifiants as $obj) {
			$objet           = objet_type($obj['objet']);
			$id_table_objet  = id_table_objet($objet);
			$table_objet_sql = table_objet_sql($objet);
			// Attention aux objets inconnus (plugins désactivés par ex)
			// table_objet_sql() renvoie un mauvais nom de table pour ceux là
			$table_ok = (ltrim($table_objet_sql, $GLOBALS['table_prefix'].'_') == $table_objet_sql);
			if ($table_ok
				and $orphelins = sql_allfetsel(
				'I.identifiant',
				'spip_identifiants AS I' .
				" LEFT JOIN $table_objet_sql AS B" .
					" ON I.id_objet = B.$id_table_objet",
				array(
					"I.objet = " . sql_quote($objet),
					"B.$id_table_objet IS NULL"
				)
			)){
				foreach ($orphelins as $orphelin) {
					$identifiants[] = $orphelin['identifiant'];
				}
			}
		}
		if (count($identifiants)) {
			$select = sql_select(
				'identifiant AS id',
				'spip_identifiants',
				sql_in('identifiant', $identifiants)
			);
			$n+= optimiser_sansref('spip_identifiants', 'identifiant', $select);
		}
	}

	return $flux;
}
