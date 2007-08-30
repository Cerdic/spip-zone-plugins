<?php

// Preparer les listes id_article IN (...) pour les parties WHERE
// et points =  des requetes du moteur de recherche
function inc_prepare_recherche($recherche, $primary = 'id_article', $id_table='articles',$nom_table='spip_articles', $cond=false) {
	static $cache = array();
	static $fcache = array();

	// si recherche n'est pas dans le contexte, on va prendre en globals
	// ca permet de faire des inclure simple.
	if (!isset($recherche) AND isset($GLOBALS['recherche']))
		$recherche = $GLOBALS['recherche'];

	// traiter le cas {recherche?}
	if ($cond AND !strlen($recherche))
		return array("''" /* as points */, /* where */ '1');

	// Premier passage : chercher eventuel un cache des donnees sur le disque
	if (!$cache[$recherche]['hash']) {
		$dircache = sous_repertoire(_DIR_CACHE,'rech3');
		$fcache[$recherche] =
			$dircache . substr(md5($recherche),0,10).'.txt';
		if (lire_fichier($fcache[$recherche], $contenu))
			$cache[$recherche] = @unserialize($contenu);
	}

	// si on n'a pas encore traite les donnees dans une boucle precedente
	if (!$cache[$recherche][$primary]) {
		include_spip('inc/indexation');
		$points = array();

		$s = spip_query("SELECT id,type, MATCH (texte) AGAINST ("._q($recherche).") + MATCH (texte) AGAINST ("._q($recherche)." IN BOOLEAN MODE) AS points FROM spip_indexation WHERE type=".id_index_table($nom_table)." AND MATCH (texte) AGAINST ("._q($recherche)." IN BOOLEAN MODE)");

		while ($t = sql_fetch($s)) {
			$points[$t['id']] = array('score' => ceil(100*$t['points']));
		}

		# calculer le {id_article IN()} et le {... as points}
		if (!count($points)) {
			$cache[$recherche][$primary] = array("''", '0');
		} else {
			$listes_ids = array();
			$select = '0';
			foreach ($points as $id => $p)
				$listes_ids[$p['score']] .= ','.$id;
			foreach ($listes_ids as $p => $liste_ids)
				$select .= "+$p*(".
					calcul_mysql_in("$id_table.$primary", substr($liste_ids, 1))
					.") ";

			$cache[$recherche][$primary] = array($select,
				'('.calcul_mysql_in("$id_table.$primary",
					array_keys($points)).')'
				);
		}

		// ecrire le cache de la recherche sur le disque
		ecrire_fichier($fcache[$recherche], serialize($cache[$recherche]));
		// purger le petit cache
		nettoyer_petit_cache('rech3', 300);
	}

	return $cache[$recherche][$primary];
}


?>
