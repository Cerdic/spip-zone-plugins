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

if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Ajouter du contenu sur les formulaires d'édition des objets.
 *
 * - Identifiants sur les objets configurés
 *
 * @pipeline editer_contenu_objet
 *
 * @param  array $flux Données du pipeline
 * @return array       Données du pipeline
 */
function identifiants_editer_contenu_objet($flux) {

	include_spip('inc/config');
	$objets = lire_config('identifiants/objets', array());

	// Identifiants sur les objets activés
	if (
		$objet = $flux['args']['type']
		and $id_objet = intval($flux['args']['id'])
		and $table_objet_sql = table_objet_sql($objet)
		and in_array($table_objet_sql,$objets)
		and autoriser('voir','identifiant')
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
 * - Identifiants sur les objets configurés
 *
 * @pipeline boite_info
 * @param array $flux Données du pipeline
 * @return array      Données du pipeline
**/
function identifiants_boite_infos($flux){

	include_spip('inc/config');
	$objets = lire_config('identifiants/objets', array());

	if (
		$objet = $flux['args']['type']
		and $id_objet = intval($flux['args']['id'])
		and $table_objet_sql = table_objet_sql($objet)
		and in_array($table_objet_sql,$objets)
		and autoriser('voir','identifiant')
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
 * Ajouter des vérifications aux formulaires
 *
 * - Identifiants sur les objets configurés
 *
 * @pipeline boite_info
 * @param array $flux Données du pipeline
 * @return array      Données du pipeline
**/
function identifiants_formulaire_charger($flux){

	include_spip('inc/config');
	$objets = lire_config('identifiants/objets', array());
	preg_match('/^editer_(.*)/', $flux['args']['form'], $matches);

	if (
		$objet = $matches[1]
		and $id_objet = intval($flux['args']['args'][0]) // on suppose que c'est le 1er paramètre
		and $table_objet_sql = table_objet_sql($objet)
		and in_array($table_objet_sql,$objets)
		and autoriser('voir','identifiant')
	) {

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
 * - Identifiants sur les objets configurés
 *
 * @pipeline boite_info
 * @param array $flux Données du pipeline
 * @return array      Données du pipeline
**/
function identifiants_formulaire_verifier($flux){

	include_spip('inc/config');
	$objets = lire_config('identifiants/objets', array());
	preg_match('/^editer_(.*)/', $flux['args']['form'], $matches);

	if (
		$objet = $matches[1]
		and $id_objet = intval($flux['args']['args'][0]) // on suppose que c'est le 1er paramètre
		and $table_objet_sql = table_objet_sql($objet)
		and in_array($table_objet_sql,$objets)
		and autoriser('voir','identifiant')
	) {

		if ($identifiant = _request('identifiant')) {
			// nombre de charactères : 50 max
			if (($nb = strlen($identifiant)) > 50) {
				$flux['data']['identifiant'] = _T('identifiant:erreur_champ_identifiant_taille', array('nb'=>$nb));
			}
			// format : charactères alphanumériques en minuscules ou "_"
			elseif (!preg_match('/^[a-z0-9_]+$/', $identifiant)) {
				$flux['data']['identifiant'] = _T('identifiant:erreur_champ_identifiant_format');
			}
			// doublon : on n'autorise qu'un seul identifiant par type d'objet
			elseif (sql_countsel('spip_identifiants', 'identifiant='.sql_quote($identifiant).' AND objet='.sql_quote($objet).' AND id_objet!='.intval($id_objet))) {
				$flux['data']['identifiant'] = _T('identifiant:erreur_champ_identifiant_doublon');
			}
		}

	}

	return $flux;
}


/**
 * Ajouter des traitements aux formulaires
 *
 * - Enregistter les identifiants sur les objets configurés
 *
 * @pipeline boite_info
 * @param array $flux Données du pipeline
 * @return array      Données du pipeline
**/
function identifiants_formulaire_traiter($flux){

	include_spip('inc/config');
	$objets = lire_config('identifiants/objets', array());
	preg_match('/^editer_(.*)/', $flux['args']['form'], $matches);

	if (
		$objet = $matches[1]
		and $id_objet = intval($flux['args']['args'][0]) // on suppose que c'est le 1er paramètre
		and $table_objet_sql = table_objet_sql($objet)
		and in_array($table_objet_sql,$objets)
		and autoriser('voir','identifiant')
	) {

		$old_identifiant = sql_getfetsel(
			'identifiant',
			'spip_identifiants',
			'objet='.sql_quote($objet).' AND id_objet='.intval($id_objet)
		);
		$new_identifiant = _request('identifiant');
		$set = array(
			'objet'       => $objet,
			'id_objet'    => $id_objet,
			'identifiant' => $new_identifiant,
		);

		// création...
		if (
			!$old_identifiant
			and $new_identifiant
		) {
			sql_insertq('spip_identifiants', $set);
		}

		// ...ou mise à jour...
		elseif (
			$old_identifiant
			and $new_identifiant
		) {
			sql_updateq('spip_identifiants', $set);
		}

		// ... ou suppression
		elseif (
			$old_identifiant
			and !$new_identifiant
		) {
			sql_delete('spip_identifiants', 'objet='.sql_quote($objet).' AND id_objet='.intval($id_objet));
		}

	}

	return $flux;
}
