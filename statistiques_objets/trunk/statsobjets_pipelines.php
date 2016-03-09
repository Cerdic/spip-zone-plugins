<?php
/**
 * Pipelines utiles au plugin Statistiques des objets éditoriaux.
 *
 * @plugin    Statistiques des objets éditoriaux
 * @copyright 2016
 * @author    tcharlss
 * @licence   GNU/GPL
 * @package   SPIP\Statistiques_objets\Pipelines
 */

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) {
	return;
}


/**
 * Affichage des formulaires.
 *
 * Configuration des statistiques : ajout d'un select pour les objets
 *
 * @param array $flux
 *     tableau
 * @return array
 */
function statsobjets_formulaire_fond ($flux){

	if ($flux['args']['form'] == 'configurer_compteur'){

		// sélecteur d'objets
		$ajouter = recuperer_fond(
			'formulaires/inc-statistiques_objets',
			array(
				'objets'  => $flux['args']['contexte']['objets'],
				'erreurs' => $flux['args']['contexte']['erreurs']
			)
		);
		if ($ajouter){
			$cherche = "/(<\/div>\s+<p[^>]*class=('|\")boutons.*<\/p>)/is";
			$remplace = "$ajouter$1";
			$flux['data'] = preg_replace($cherche, $remplace, $flux['data']);
		}

	}

	return $flux;
}


/**
 * Chargement des valeurs des formulaires.
 *
 * Configuration des statistiques : ajout des objets et checker les tables
 *
 * @param array $flux
 *     tableau
 * @return array
 */
function statsobjets_formulaire_charger ($flux){

	if ($flux['args']['form'] == 'configurer_compteur'){

		include_spip('inc_config');
		include_spip('statsobjets_administrations');
		// objets configurés
		if ($objets = lire_config('activer_statistiques_objets')) {
			$flux['data']['objets'] = $objets;
		}
		// checker les tables
		statsobjets_check_upgrade();

	}

	return $flux;
}


/**
 * Compléter les traitements des formulaires.
 *
 * Configuration des statistiques : ajout des objets et checker les tables
 *
 * @param array $flux
 *     tableau
 * @return array
 */
function statsobjets_formulaire_traiter ($flux){

	if ($flux['args']['form'] == 'configurer_compteur'){

		include_spip('inc_config');
		$objets_old = lire_config('activer_statistiques_objets');
		$objets = _request('objets');
		// objets configurés
		if ($objets != $objets_old) {
			ecrire_config('activer_statistiques_objets', $objets);
		}

	}

	return $flux;
}


/**
 * Ajoute les boutons d'administration indiquant la popularité et les visites d'un objet autre qu'un article
 *
 * @uses admin_statsobjets()
 * @pipeline formulaire_admin
 * @param array $flux Données du pipeline
 * @return array       Données du pipeline
 **/
function statsobjets_formulaire_admin($flux) {
	if (
		isset($flux['args']['contexte']['objet'])
		and $objet = $flux['args']['contexte']['objet']
		and isset($flux['args']['contexte']['id_objet'])
		and $id_objet = $flux['args']['contexte']['id_objet']
	) {
		if (
			$objet != 'article'
			and $l = admin_statsobjets($objet, $id_objet, defined('_VAR_PREVIEW') ? _VAR_PREVIEW : '')
		) {
			$btn = recuperer_fond('prive/bouton/statistiques', array(
				'visites' => $l[0],
				'popularite' => $l[1],
				'statistiques' => $l[2],
			));
			$flux['data'] = preg_replace('%(<!--extra-->)%is', $btn . '$1', $flux['data']);
		}
	}

	return $flux;
}


/**
 * Calcule les visites et popularite d'un objet éditorial (sauf les articles)
 *
 * @param string $objet
 * @param int $id_objet
 * @param string $var_preview
 *     Indique si on est en prévisualisation : pas de statistiques dans ce cas.
 * @return false|array
 *     - false : pas de statistiques disponibles
 *     - array : Tableau les stats `[visites, popularité, url]`
 **/
function admin_statsobjets($objet, $id_objet, $var_preview = "") {

	include_spip('inc/config');
	include_spip('base/objets'); // au cas où
	$tables_objets = lire_config('activer_statistiques_objets', array());
	$table_objet_sql = table_objet_sql($objet);
	$id_table_objet = id_table_objet($objet);

	if ($GLOBALS['meta']["activer_statistiques"] != "non"
		and $objet != 'article'
		and in_array($table_objet_sql, $tables_objets)
		and objet_test_si_publie($objet, $id_objet) === true
		and !$var_preview
		and autoriser('voirstats')
	) {
		$row = sql_fetsel("visites, popularite", $table_objet_sql, $id_table_objet.'='.intval($id_objet));

		if ($row) {
			return array(
				intval($row['visites']),
				ceil($row['popularite']),
				str_replace('&amp;', '&', generer_url_ecrire_statistiques($id_objet))
			);
		}
	}

	return false;
}
