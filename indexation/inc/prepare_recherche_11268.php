<?php

// VERSION POUR SPIP >= http://trac.rezo.net/trac/spip/changeset/11172


@define('_DELAI_CACHE_RECHERCHES',600);

// Preparer les listes id_article IN (...) pour les parties WHERE
// et points =  des requetes du moteur de recherche
function inc_prepare_recherche($recherche, $table='articles', $cond=false, $serveur='') {
	static $cache = array();

	// si recherche n'est pas dans le contexte, on va prendre en globals
	// ca permet de faire des inclure simple.
	if (!isset($recherche) AND isset($GLOBALS['recherche']))
		$recherche = $GLOBALS['recherche'];

	// traiter le cas {recherche?}
	if ($cond AND !strlen($recherche))
		return array("''" /* as points */, /* where */ '1');

	// si on n'a pas encore traite les donnees
	if (!isset($cache[$recherche])) {

		spip_timer('fulltext');

		include_spip('inc/indexation');
		$liste_index_tables = liste_index_tables();

		// tester/nettoyer le cache de cette recherche
		$hashes = array();
		foreach ($liste_index_tables as $ttable) {
			$table_abreg = preg_replace("{^spip_}","",$ttable);
			$hash = substr(md5($recherche . $table_abreg),0,16);
			$cache[$recherche][$table_abreg] = array("resultats.points as points","recherche='$hash'");
			$hashes[] = $hash;
		}

		if (!$row = sql_fetsel('UNIX_TIMESTAMP(NOW())-UNIX_TIMESTAMP(maj) AS fraicheur','spip_resultats',
		sql_in('recherche', $hashes),'','fraicheur DESC','0,1','',$serveur)
		OR $row['fraicheur'] > _DELAI_CACHE_RECHERCHES) {

			$tab_couples = array();

			$points = Indexation_recherche_sql($recherche);
			foreach ($points as $type => $scores) {
				if ($ttable = $liste_index_tables[$type]) {
					$table_abreg = preg_replace("{^spip_}","",$ttable);
					$hash = substr(md5($recherche . $table_abreg),0,16);

					if (!count($scores)) {
						$cache[$recherche][$ttable] = array("''", '0');
					} else {
						foreach ($scores as $id => $score)
							$tab_couples[] = array(
								'recherche' => $hash,
								'id' => $id,
								'points' => $score
							);
					}
				}
			}

			// Aucune reponse : le noter
			if (!count($tab_couples))
				$tab_couples[] = array(
					'recherche' => $hashes[0],
					'id' => 0,
					'points' => 0
				);


			// supprimer les anciens resultats de cette recherche
			// et les resultats trop vieux avec une marge
			sql_delete('spip_resultats',
			'(maj<DATE_SUB(NOW(), INTERVAL '.(_DELAI_CACHE_RECHERCHES+100)." SECOND)) OR ". sql_in('recherche', $hashes),
			$serveur);

			// Inserer les reponses
			sql_insertq_multi('spip_resultats', $tab_couples, array(),$serveur);

			spip_log("recherche fulltext ($recherche) ".spip_timer("fulltext"));
		}
	}

	return $cache[$recherche][$table];
}



?>
