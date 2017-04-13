<?php
/**
 * Utilisations de pipelines par Objets virtuels
 *
 * @plugin     Objets virtuels
 * @copyright  2017
 * @author     Matthieu Marcillaud
 * @licence    GNU/GPL
 * @package    SPIP\Objets_virtuels\Pipelines
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


/**
 * Déclare les champs virtuels utilisés
 *
 * @pipeline declarer_tables_objets_sql
 * @param array $tables
 * @return array
 */
function objets_virtuels_declarer_tables_objets_sql($tables) {
	include_spip('objets_virtuels_fonctions');
	$tables_actives = objets_virtuels_tables_actives();
	foreach ($tables_actives as $table) {
		if (isset($tables[$table])) {
			if (empty($tables[$table]['field']['virtuel'])) {
				$tables[$table]['field']['virtuel'] = 'text DEFAULT \'\' NOT NULL';
				$tables[$table]['champs_editables'][] = 'virtuel';
				$tables[$table]['champs_versionnes'][] = 'virtuel';
				$tables[$table]['rechercher_champs']['virtuel'] = 3;
			}
		}
	}
	return $tables;
}


/**
 * Ajoute le formulaire de redirection sur les objets activés
 *
 * @pipeline declarer_tables_objets_sql
 * @param array $flux
 * @return array
 */
function objets_virtuels_afficher_config_objet($flux) {
	include_spip('objets_virtuels_fonctions');
	$tables = objets_virtuels_tables_actives();
	$type = $flux['args']['type'];
	// ! Articles déjà gérés par le Core
	if ($type != 'article' and in_array(table_objet_sql($type), $tables)) {
		$flux['data'] .= recuperer_fond('prive/objets/editer/redirection_objet_virtuel', [
			'objet' => $type,
			'id_objet' => $flux['args']['id']
		]);
	}
	return $flux;
}


/**
 * Utilisation du pipeline affiche milieu
 *
 *  Ajoute un bloc montrant que l'objet a une redirection, si tel est le cas.
 *
 * @pipeline affiche_milieu
 *
 * @param array $flux
 *     Données du pipeline
 * @return array
 *     Données du pipeline
 */
function objets_virtuels_affiche_milieu($flux) {
	include_spip('objets_virtuels_fonctions');

	// si on est sur une page ou il faut inserer les mots cles...
	if ($desc = trouver_objet_exec($flux['args']['exec'])
		and $desc['edition'] !== true // page visu
		and $type = $desc['type']
		and $type != 'article' // ! Articles déjà gérés par le Core
		and $id_table_objet = $desc['id_table_objet']
		and isset($flux['args'][$id_table_objet])
		and ($id = intval($flux['args'][$id_table_objet]))
		and (in_array($desc['table_objet_sql'], objets_virtuels_tables_actives()))
	) {
		$virtuel = quete_objet_virtuel($desc['table'], $id);
		$texte = recuperer_fond(
			'prive/squelettes/inclure/redirection_objet_virtuel',
			['virtuel' => $virtuel],
			['ajax' => true]
		);
		if ($p = strpos($flux['data'], '<div id="wys')) {
			$flux['data'] = substr_replace($flux['data'], $texte, $p, 0);
		} elseif ($p = strpos($flux['data'], '<!--affiche_milieu-->')) {
			$flux['data'] = substr_replace($flux['data'], $texte, $p, 0);
		} else {
			$flux['data'] .= $texte;
		}
	}

	return $flux;
}


/**
 * Insertion dans le pipeline objet_compte_enfants (SPIP)
 *
 * Une objet est considérée comme vide lorsqu'il n'a pas d'objets liés (articles, rubriques, documents).
 * Ici on impose que le champ "virtuel" doit être vide pour que l'objet soit considéré comme vide.
 *
 * @pipeline objet_compte_enfants
 * @param array $flux
 * @return array
 */
function objets_virtuels_objet_compte_enfants($flux) {
	include_spip('objets_virtuels_fonctions');

	if (
		$objet = $flux['args']['objet']
		and $id_objet = $flux['args']['id_objet']
		and $table = table_objet_sql($objet)
		and in_array($table, objets_virtuels_tables_actives())
	) {
		$virtuel = quete_objet_virtuel($objet, $id_objet);
		if (strlen(trim($virtuel)) > 0) {
			$flux['data']['redirection'] = 1;
		}
	}
	return $flux;
}


/**
 * Insertion dans le pipeline calculer_rubriques (SPIP)
 * (cf calculer_rubriques_publiees() dans inc/rubriques)
 *
 * Évite de dépublier une rubrique avec une redirection
 *
 * @pipeline calculer_rubriques
 * @param array $flux
 * @return array
 */
function objets_virtuels_calculer_rubriques($flux) {
	include_spip('objets_virtuels_fonctions');
	if (in_array('spip_rubriques', objets_virtuels_tables_actives())) {
		$rubriques_virtuelles_non_publiees = sql_allfetsel(
			'id_rubrique, statut, id_parent',
			'spip_rubriques',
			'statut_tmp != "publie" AND virtuel != ""'
		);
		foreach ($rubriques_virtuelles_non_publiees as $rub) {
			sql_updateq('spip_rubriques', array('statut_tmp' => 'publie'), 'id_rubrique=' . intval($rub['id_rubrique']));
		}
	}
	return $flux;
}

/**
 * Pipeline d'autorisation
 * @pipeline autoriser
 */
function objets_virtuels_autoriser() {}

if (!function_exists('autoriser_rubrique_supprimer')) {
	/**
	 * Ne pas pouvoir supprimer une rubrique si elle a un champ de redirection actif
	 * @param string $faire
	 * @param string $type
	 * @param int $id
	 * @param array $qui
	 * @param array $opt
	 * @return bool
	 */
	function autoriser_rubrique_supprimer($faire, $type, $id, $qui, $opt) {
		include_spip('objets_virtuels_fonctions');
		if (in_array('spip_rubriques', objets_virtuels_tables_actives())) {
			$virtuel = quete_objet_virtuel('rubrique', intval($id));
			if (strlen($virtuel) > 0) {
				return false;
			}
		}
		return autoriser_rubrique_supprimer_dist($faire, $type, $id, $qui, $opt);
	}
}
