<?php
/**
 * Déclaration de filtres pour les squelettes
 *
 * @package SPIP\Xiti\Fonctions
 **/
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Compte le nombre d'objets associés pour chaque type d'objet, liés
 * à un mot clé donné.
 *
 * @pipeline_appel afficher_nombre_objets_associes_a
 *
 * @param int $id_xiti_niveau
 *     Identifiant du mot niveau deux
 * @return string[]
 *     Tableau de textes indiquant le nombre d'éléments tel que '3 articles'
 **/
function filtre_objets_associes_xiti_niveau_dist($id_xiti_niveau) {
	static $occurrences = array();

	// calculer tous les liens du groupe d'un coup
	if (!isset($occurrences[$id_xiti_niveau])) {
		$occurrences[$id_xiti_niveau] = calculer_utilisations_xiti_niveaux($id_xiti_niveau);
	}

	$associes = array();
	$tables = lister_tables_objets_sql();
	foreach ($tables as $table_objet_sql => $infos) {
		$nb = (isset($occurrences[$id_xiti_niveau][$table_objet_sql][$id_xiti_niveau]) ?
					$occurrences[$id_xiti_niveau][$table_objet_sql][$id_xiti_niveau] : 0);
		if ($nb) {
			$associes[] = objet_afficher_nb($nb, $infos['type']);
		}
	}

	$associes = pipeline(
		'afficher_nombre_objets_associes_a',
		array('args' => array('objet' => 'xiti_niveau', 'id_objet' => $id_xiti_niveau),
			'data' => $associes)
	);
	return $associes;
}

/**
 * Calculer les nombres d'éléments (articles, etc.) liés à chaque niveau deux
 *
 * @param int $id_xiti_niveau
 *     Identifiant du niveau deux
 * @return array
 *     Couples (tables de liaison => xiti_niveaux).
 *     xiti_niveaux est un tableau de couples (id_xiti_niveau => nombre d'utilisation)
 */
function calculer_utilisations_xiti_niveaux($id_xiti_niveau) {
	$retour = array();
	$objets = sql_allfetsel(
		'DISTINCT objet',
		array('spip_xiti_niveaux_liens AS L', 'spip_xiti_niveaux AS M'),
		array('L.id_xiti_niveau=M.id_xiti_niveau', 'M.id_xiti_niveau=' . intval($id_xiti_niveau))
	);

	foreach ($objets as $o) {
		$objet = $o['objet'];
		$_id_objet = id_table_objet($objet);
		$table_objet_sql = table_objet_sql($objet);
		$infos = lister_tables_objets_sql($table_objet_sql);
		if (isset($infos['field']) and $infos['field']) {
			// uniquement certains statut d'objet,
			// et uniquement si la table dispose du champ statut.
			$statuts = '';
			if (isset($infos['field']['statut']) or isset($infos['statut'][0]['champ'])) {
				// on s'approche au mieux de la declaration de l'objet.
				// il faudrait ameliorer ce point.
				$c_statut = isset($infos['statut'][0]['champ']) ? $infos['statut'][0]['champ'] : 'statut';

				// bricoler les statuts d'apres la declaration de l'objet (champ previsu a defaut de mieux)
				if (array_key_exists('previsu', $infos['statut'][0]) and strlen($infos['statut'][0]['previsu']) > 1) {
					$str_statuts = $infos['statut'][0]['previsu'];
					if ($GLOBALS['connect_statut'] != '0minirezo') {
						$str_statuts = str_replace('prepa', '', $str_statuts);
					}
					$not = (substr($str_statuts, 0, 1) == '!' ? 'NOT' : '');
					$str_statuts = str_replace('!', '', $str_statuts);
					$Tstatuts = array_filter(explode(',', $str_statuts));
					$statuts = ' AND ' . sql_in("O.$c_statut", $Tstatuts, $not);
				} // objets sans champ previsu ou avec un previsu == '!' (par ex les rubriques)
				else {
					$statuts = ' AND ' . sql_in(
						"O.$c_statut",
						($GLOBALS['connect_statut'] == '0minirezo') ?
							array('prepa', 'prop', 'publie') : array('prop', 'publie')
					);
				}
			}
			$res = sql_allfetsel(
				'COUNT(*) AS cnt, L.id_xiti_niveau',
				'spip_xiti_niveaux_liens AS L
					LEFT JOIN spip_xiti_niveaux AS M ON L.id_xiti_niveau=M.id_xiti_niveau
					AND L.objet=' . sql_quote($objet) . '
					LEFT JOIN ' . $table_objet_sql . " AS O ON L.id_objet=O.$_id_objet",
				"M.id_xiti_niveau=$id_xiti_niveau$statuts",
				'L.id_xiti_niveau'
			);
			foreach ($res as $row) {
				$retour[$table_objet_sql][$row['id_xiti_niveau']] = $row['cnt'];
			}
		}
	}
	return $retour;
}
