<?php
/**
 * Trad-lang v2
 * Plugin SPIP de traduction de fichiers de langue
 * © Florent Jugla, Fil, kent1
 *
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Fonction de vérification de la concordance d'une langue x par rapport à la langue mère
 *
 * @param int $id_tradlang_module
 * 		id du module concerne
 *    ou module (deprecated)
 * @param string $langue
 * 		La langue à comparer
 * @return array
 */
function inc_tradlang_verifier_langue_base_dist($id_tradlang_module, $langue) {

	// compat anciens appels avec module
	if (!is_numeric($id_tradlang_module)) {
		$module = $id_tradlang_module;
		$ids = sql_allfetsel('id_tradlang_module', 'spip_tradlang_modules', 'module='.sql_quote($module));
		$ids = array_column($ids, 'id_tradlang_module');
		$res = [];
		foreach ($ids as $id) {
			$res[] = inc_tradlang_verifier_langue_base_dist($id, $langue);
		}
		$inserees = array_sum(array_column($res, 0));
		$supprimees = array_sum(array_column($res, 1));
		$recuperees = array_sum(array_column($res, 2));

		return array($inserees,$supprimees,$recuperees);
	}

	if (!$row_module = sql_fetsel('*', 'spip_tradlang_modules', 'id_tradlang_module=' . intval($id_tradlang_module))) {
		return array(0,0,0);
	}
	$where_module = 'id_tradlang_module=' . intval($id_tradlang_module);
	$module = $row_module['module']; // pour les logs

	/**
	 * Quelle est la langue mère
	 */
	$langue_mere = $row_module['lang_mere'];
	$trad_langue_mere_id = $trad_langue_cible_id = $trad_langue_cible_id_attic = array();

	/**
	 * On crée trois tableaux:
	 * -* l'ensemble des id de la langue mère non supprimés
	 * -* l'ensemble des id de la langue cible non supprimés
	 * -* l'ensemble des id de la langue cible qui ont été supprimés par le passé
	 * (dans le cas où l'on doit en récupérer)
	 */
	$trad_langue_mere_id = sql_allfetsel('id', 'spip_tradlangs', "$where_module AND lang=" . sql_quote($langue_mere) . " AND statut!='attic'", '', 'id');
	$trad_langue_mere_id = array_column($trad_langue_mere_id, 'id');

	$trad_langue_cible_id  = sql_allfetsel('id', 'spip_tradlangs', "$where_module AND lang=" . sql_quote($langue) . " AND statut!='attic'", '', 'id');
	$trad_langue_cible_id = array_column($trad_langue_cible_id, 'id');

	$trad_langue_cible_id_attic  = sql_allfetsel('id', 'spip_tradlangs', "$where_module AND lang=" . sql_quote($langue) . " AND statut='attic'", '', 'id');
	$trad_langue_cible_id_attic = array_column($trad_langue_cible_id_attic, 'id');

	$inserees = $supprimees = $recuperees = 0;

	/**
	 * $diff1 est l'ensemble des chaines manquantes dans la langue cible
	 * et donc à insérer
	 *
	 * On met dans un tableau les chaines en question si on a au moins un résultat
	 */
	$diff1 = array_diff($trad_langue_mere_id, $trad_langue_cible_id);
	if (count($diff1) > 0) {
		$diff1_array = sql_allfetsel('*', 'spip_tradlangs', "$where_module AND lang=" . sql_quote($langue_mere) . ' AND ' . sql_in('id', $diff1));
		foreach ($diff1_array as $diff) {
			/**
			 * La chaine était préalablement supprimée
			 * Elle a le statut "attic"
			 * On la récupère donc en lui donnant le statut "MODIF"
			 */
			if (in_array($diff['id'], $trad_langue_cible_id_attic)) {
				$titre = $diff['id'].' : '.$diff['module'].' - '.$langue;
				sql_updateq('spip_tradlangs', array('statut' => 'MODIF', 'titre' => $titre), "$where_module AND id=" . sql_quote($diff['id']) . ' AND lang=' . sql_quote($langue) . " AND statut='attic'");
				$recuperees++;
			} else {
				$diff['orig'] = 0;
				$diff['lang'] = $langue;
				$diff['titre'] = $diff['id'].' : '.$diff['module'].' - '.$langue;
				$diff['statut'] = 'NEW';
				unset($diff['maj']);
				unset($diff['id_tradlang']);
				unset($diff['traducteur']);
				$id_tradlang = sql_insertq('spip_tradlangs', $diff);
				$inserees++;
			}
		}
	}

	/**
	 * $diff2 est l'ensemble des chaines en trop dans la langue fille
	 * et donc à supprimer
	 */
	$diff2 = array_diff($trad_langue_cible_id, $trad_langue_mere_id);
	if (count($diff2) > 0) {
		foreach ($diff2 as $id) {
			sql_updateq('spip_tradlangs', array('statut' => 'attic'), "$where_module AND id=" . sql_quote($id) . ' AND lang=' . sql_quote($langue));
			$supprimees++;
		}
	}

	if ($inserees + $supprimees + $recuperees > 0) {
		include_spip('inc/invalideur');
		suivre_invalideur('1');
		spip_log("$module: insert => $inserees - suppressions => $supprimees - recuperations => $recuperees", 'bilan.'._LOG_ERREUR);
		return array($inserees,$supprimees,$recuperees);
	}

	return array(0,0,0);
}
